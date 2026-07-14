<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Services\DeliveryEligibilityService;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        protected PaymentGatewayInterface $paymentGateway,
        protected DeliveryEligibilityService $deliveryEligibilityService
    ) {
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'coupon_code' => 'nullable|string',
            'address' => 'required_without:delivery_details|array',
            'delivery_details' => 'required_without:address|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $deliveryDetails = $request->input('address') ?? $request->input('delivery_details') ?? [];
        $postcode = $deliveryDetails['postcode'] ?? '2000';

        $subtotal = 0;
        foreach ($request->input('cart') as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shippingInfo = $this->deliveryEligibilityService->calculateShipping($postcode, $subtotal);
        $shippingCost = $shippingInfo['shipping_cost'] ?? 0;

        $discount = 0;
        $couponCode = $request->input('coupon_code');
        if ($couponCode) {
            $customer = null;
            $customerId = $request->input('customer_id') ?: (\Illuminate\Support\Facades\Auth::guard('customer')->id());
            if ($customerId) {
                $customer = \App\Models\Customer::find($customerId);
            }
            $couponResult = CouponController::checkValidity($couponCode, $subtotal, $customer);
            if (!$couponResult['valid']) {
                return response()->json(['error' => 'Coupon validation failed: ' . $couponResult['message']], 422);
            }
            $discount = (float) $couponResult['discount'];
        }

        $tax = 0;
        $grandTotal = max(0, $subtotal - $discount + $shippingCost);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_number' => 'TCT-' . Str::upper(Str::random(10)),
                'customer_id' => $request->input('customer_id'),
                'customer_name' => $request->input('customer_name') ?? ($deliveryDetails['contact_name'] ?? $deliveryDetails['name'] ?? null),
                'customer_email' => $request->input('customer_email') ?? ($deliveryDetails['email'] ?? null),
                'customer_phone' => $request->input('customer_phone') ?? ($deliveryDetails['phone'] ?? null),
                
                // billing details
                'first_name' => explode(' ', ($deliveryDetails['contact_name'] ?? $deliveryDetails['name'] ?? 'Guest'), 2)[0] ?? 'Guest',
                'last_name' => explode(' ', ($deliveryDetails['contact_name'] ?? $deliveryDetails['name'] ?? 'Guest'), 2)[1] ?? '',
                'email' => $request->input('customer_email') ?? ($deliveryDetails['email'] ?? null),
                'phone' => $request->input('customer_phone') ?? ($deliveryDetails['phone'] ?? ''),
                'country' => $deliveryDetails['country'] ?? 'Australia',
                'address' => $deliveryDetails['address_line_1'] ?? ($deliveryDetails['address'] ?? ''),
                'apartment' => $deliveryDetails['address_line_2'] ?? null,
                'city' => $deliveryDetails['city'] ?? 'Sydney',
                'state' => $deliveryDetails['state'] ?? 'NSW',
                'pin_code' => $deliveryDetails['postcode'] ?? '2000',

                // shipping snapshot
                'shipping_name' => $deliveryDetails['contact_name'] ?? $deliveryDetails['name'] ?? null,
                'shipping_phone' => $deliveryDetails['phone'] ?? null,
                'shipping_address_line_1' => $deliveryDetails['address_line_1'] ?? ($deliveryDetails['address'] ?? null),
                'shipping_address_line_2' => $deliveryDetails['address_line_2'] ?? null,
                'shipping_suburb' => $deliveryDetails['suburb'] ?? null,
                'shipping_city' => $deliveryDetails['city'] ?? null,
                'shipping_state' => $deliveryDetails['state'] ?? null,
                'shipping_postcode' => $deliveryDetails['postcode'] ?? null,
                'shipping_country' => $deliveryDetails['country'] ?? 'Australia',
                'shipping_latitude' => $deliveryDetails['latitude'] ?? null,
                'shipping_longitude' => $deliveryDetails['longitude'] ?? null,
                'shipping_google_place_id' => $deliveryDetails['google_place_id'] ?? null,

                // delivery fulfillment
                'delivery_type' => $request->input('delivery_type') ?? ($this->deliveryEligibilityService->isDirectDeliveryPostcode($postcode) ? 'direct' : 'courier'),
                'delivery_slot_id' => $request->input('delivery_slot_id'),
                'delivery_date' => $request->input('delivery_date'),
                'delivery_notes' => $deliveryDetails['delivery_notes'] ?? ($request->input('notes') ?? null),
                'notes' => $deliveryDetails['delivery_notes'] ?? ($request->input('notes') ?? null),

                // payment
                'shipping_method' => $request->input('delivery_type') ?? ($this->deliveryEligibilityService->isDirectDeliveryPostcode($postcode) ? 'direct' : 'courier'),
                'payment_method' => $request->input('payment_method', 'card'),
                'payment_status' => 'pending',
                'status' => 'pending_payment',
                
                // pricing totals
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'coupon_code' => $couponCode,
                'discount' => $discount,
                'grand_total' => $grandTotal,
            ]);

            $order->update([
                'order_number' => 'TC-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            ]);

            // Create order items
            foreach ($request->input('cart') as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                $variantDetails = null;
                if (!empty($item['variant_id'])) {
                    $variant = \App\Models\ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $variantDetails = $variant->name;
                    }
                }

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $product ? $product->name : 'Product #' . $item['product_id'],
                    'variant_details' => $variantDetails,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'line_total' => $item['price'] * $item['quantity'],
                ]);
            }

            $paymentIntent = $this->paymentGateway->createPaymentIntent($order);

            DB::commit();

            return response()->json([
                'valid' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent' => $paymentIntent,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create payment intent: ' . $e->getMessage()], 500);
        }
    }

    public function retry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = Order::find($request->input('order_id'));

        if ($order->payment_status === 'paid') {
            return response()->json(['error' => 'This order has already been paid.'], 400);
        }

        try {
            $paymentIntent = $this->paymentGateway->createPaymentIntent($order);

            return response()->json([
                'valid' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent' => $paymentIntent,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create payment intent: ' . $e->getMessage()], 500);
        }
    }
}
