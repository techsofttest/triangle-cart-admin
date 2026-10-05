<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Services\DeliveryEligibilityService;
use App\Services\BuyXGetXPromotionService;
use App\Services\FreeGiftPromotionService;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(
        protected PaymentGatewayInterface $paymentGateway,
        protected DeliveryEligibilityService $deliveryEligibilityService,
        protected BuyXGetXPromotionService $promotionService,
        protected FreeGiftPromotionService $freeGiftService
    ) {
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'coupon_code' => 'nullable|string',
            'customer_email' => ['nullable', 'email', 'regex:/^[^\s@\/]+@[^\s@\/]+\.[^\s@\/]+$/'],
            'address' => 'required_without:delivery_details|array',
            'address.email' => ['nullable', 'email', 'regex:/^[^\s@\/]+@[^\s@\/]+\.[^\s@\/]+$/'],
            'address.address_line_2' => 'nullable|string|max:255',
            'delivery_details' => 'required_without:address|array',
            'delivery_details.email' => ['nullable', 'email', 'regex:/^[^\s@\/]+@[^\s@\/]+\.[^\s@\/]+$/'],
            'delivery_details.address_line_2' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate that each product in cart is active (or valid free gift) and has sufficient stock
        foreach ($request->input('cart', []) as $item) {
            $rawProductId = $item['product_id'] ?? $item['id'] ?? null;
            if (is_string($rawProductId) && str_starts_with($rawProductId, 'free-gift-')) {
                $rawProductId = (int) str_replace('free-gift-', '', $rawProductId);
            } elseif (is_string($rawProductId) && str_starts_with($rawProductId, 'free-')) {
                $rawProductId = (int) str_replace('free-', '', $rawProductId);
            }

            $isFreeItem = !empty($item['is_free']) || !empty($item['is_free_gift']);
            $product = $rawProductId ? \App\Models\Product::find($rawProductId) : null;
            if (! $product || (! $product->is_active && ! $isFreeItem && ! $product->is_free_gift)) {
                return response()->json([
                    'error' => 'Product ' . ($product ? "'{$product->name}'" : '#' . ($rawProductId ?? '')) . ' is inactive or unavailable.',
                ], 422);
            }

            $reqQty = max(1, (int) ($item['quantity'] ?? 1));
            $variantId = $item['variant_id'] ?? $item['variantId'] ?? null;
            if ($variantId) {
                $variant = \App\Models\ProductVariant::find($variantId);
                if (! $variant || (int) $variant->stock < $reqQty) {
                    return response()->json([
                        'error' => "Product '{$product->name}' is out of stock.",
                    ], 422);
                }
            } else {
                $hasStock = $product->variants()->where('stock', '>=', $reqQty)->exists();
                if (! $hasStock) {
                    return response()->json([
                        'error' => "Product '{$product->name}' is out of stock.",
                    ], 422);
                }
            }
        }

        $deliveryDetails = $request->input('address') ?? $request->input('delivery_details') ?? [];
        $postcode = $deliveryDetails['postcode'] ?? '2000';

        // Validate delivery availability for the cart and postcode
        $deliveryCheck = $this->deliveryEligibilityService->validateCart($postcode, $request->input('cart') ?? []);
        if (isset($deliveryCheck['valid']) && ! $deliveryCheck['valid']) {
            return response()->json(['error' => $deliveryCheck['message'] ?? 'Delivery not available for this postcode.'], 422);
        }

        // Determine delivery type (direct/courier)
        $rawDeliveryType = $request->input('delivery_type') ?? ($deliveryCheck['delivery_type'] ?? null);
        $deliveryType = ($rawDeliveryType === 'direct' || $rawDeliveryType === 'postcode') ? 'direct' : 'courier';

        // If direct delivery is required, ensure delivery_date and delivery_slot_id are provided and valid
        if ($deliveryType === 'direct') {
            $deliveryDate = $request->input('delivery_date');
            $deliverySlotId = $request->input('delivery_slot_id');

            $slotValid = false;
            $availableDates = $this->deliveryEligibilityService->getAvailableDatesAndSlots('direct');
            foreach ($availableDates as $d) {
                if (($d['date'] ?? null) === $deliveryDate) {
                    $slots = $d['slots'] ?? [];
                    foreach ($slots as $s) {
                        if (($s['id'] ?? null) == $deliverySlotId) {
                            $slotValid = true;
                            break 2;
                        }
                    }
                }
            }

            $errors = [];
            if (! $deliveryDate) $errors['delivery_date'] = ['Delivery date is required for direct delivery.'];
            if (! $deliverySlotId) $errors['delivery_slot_id'] = ['Delivery slot is required for direct delivery.'];
            if ($deliverySlotId && ! $slotValid) $errors['delivery_slot_id'] = ['Selected delivery slot is not available.'];

            if (! empty($errors)) {
                return response()->json(['errors' => $errors], 422);
            }
        }

        // Server-side Buy X Get X promotion recalculation
        $promoCalculation = $this->promotionService->calculatePromotion($request->input('cart', []));

        // Server-side Free Gift promotion recalculation
        $selectedGiftId = $request->input('selected_gift_id') ? (int) $request->input('selected_gift_id') : null;
        $freeGiftCalculation = $this->freeGiftService->calculateFreeGift($promoCalculation['cart_items'], $selectedGiftId);
        $recalculatedCartItems = $freeGiftCalculation['cart_items'];

        $subtotal = 0;
        foreach ($recalculatedCartItems as $item) {
            if (empty($item['is_free']) && empty($item['is_free_gift'])) {
                $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
            }
        }

        $shippingInfo = $this->deliveryEligibilityService->calculateShipping($postcode, $subtotal);
        $shippingCost = $shippingInfo['delivery_charge'] ?? $shippingInfo['shipping_cost'] ?? 0;

        $customerId = $request->input('customer_id');
        if (! $customerId) {
            $customerId = session()->get('customer_id');
        }
        if (! $customerId) {
            $customerId = \Illuminate\Support\Facades\Auth::guard('customer')->id();
        }

        $discount = 0;
        $couponCode = $request->input('coupon_code');
        if ($couponCode) {
            $customer = null;
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
                'order_number' => 'TEMP-' . Str::upper(Str::random(10)),
                'customer_id' => $customerId,
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
            foreach ($recalculatedCartItems as $item) {
                $rawProductId = $item['product_id'] ?? $item['id'] ?? null;
                if (is_string($rawProductId) && str_starts_with($rawProductId, 'free-gift-')) {
                    $productId = (int) str_replace('free-gift-', '', $rawProductId);
                } elseif (is_string($rawProductId) && str_starts_with($rawProductId, 'free-')) {
                    $productId = (int) str_replace('free-', '', $rawProductId);
                } else {
                    $productId = $rawProductId;
                }

                $product = $productId ? \App\Models\Product::find($productId) : null;

                $variantId = $item['variant_id'] ?? $item['variantId'] ?? null;
                if (is_string($variantId)) {
                    $variantId = trim($variantId);
                    if ($variantId === '' || strtolower($variantId) === 'null') {
                        $variantId = null;
                    }
                }

                $variantDetails = null;
                $variant = null;

                if ($variantId !== null) {
                    $variant = \App\Models\ProductVariant::find($variantId);
                }

                if (!$variant && $product) {
                    $product->loadMissing('variants');
                    $variant = $product->variants
                        ->filter(fn ($v) => (int) $v->stock > 0)
                        ->sortBy(fn ($v) => [(float) $v->selling_price, (string) ($v->sku ?? '')])
                        ->values()
                        ->first() ?? $product->variants
                            ->sortBy(fn ($v) => [(float) $v->selling_price, (string) ($v->sku ?? '')])
                            ->values()
                            ->first();
                    if ($variant) {
                        $variantId = $variant->id;
                    }
                }

                if ($variant) {
                    $variantDetails = $variant->name ?? $variant->sku ?? null;
                }

                $isFree = !empty($item['is_free']) || !empty($item['is_free_gift']);
                $isFreeGift = !empty($item['is_free_gift']);
                $itemPrice = $isFree ? 0 : (float)($item['price'] ?? 0);
                $itemQty = $isFreeGift ? 1 : max(1, (int)($item['quantity'] ?? 1));

                $order->items()->create([
                    'product_id'             => $productId,
                    'variant_id'             => $variantId !== null ? $variantId : null,
                    'product_name'           => $product ? $product->name : ($item['name'] ?? 'Product #' . $productId),
                    'variant_details'        => $variantDetails,
                    'quantity'               => $itemQty,
                    'price'                  => $itemPrice,
                    'line_total'             => $itemPrice * $itemQty,
                    'is_free'                => $isFree,
                    'promotion_id'           => ($isFree && !$isFreeGift) ? ($item['promotion_id'] ?? null) : null,
                    'is_free_gift'           => $isFreeGift,
                    'free_gift_promotion_id' => $isFreeGift ? ($item['free_gift_promotion_id'] ?? ($freeGiftCalculation['promotion_id'] ?? null)) : null,
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

    public function paymentStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'nullable|exists:orders,id',
            'order_number' => 'nullable|exists:orders,order_number',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = null;
        $orderId = $request->input('order_id');
        $orderNumber = $request->input('order_number');

        if ($orderId) {
            $order = Order::find($orderId);
        } elseif ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
        }

        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        // Check if this is the first time viewing this status
        $sessionKey = "payment_status_viewed_{$order->id}";
        $hasViewed = session()->has($sessionKey);

        // Mark as viewed in session
        session()->put($sessionKey, true);

        $paymentStatus = $order->payment_status instanceof \App\Enums\PaymentStatus ? $order->payment_status->value : $order->payment_status;

        $isSuccess = $paymentStatus === 'paid';
        $isFailed = $paymentStatus === 'failed';
        $isProcessing = $paymentStatus === 'processing';

        return response()->json([
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_status' => $paymentStatus,
            'status' => $order->status,
            'is_success' => $isSuccess,
            'is_failed' => $isFailed,
            'is_processing' => $isProcessing,
            'is_first_view' => !$hasViewed,
            'grand_total' => $order->grand_total,
            'paid_at' => $order->paid_at,
            'payment_failure_reason' => $order->payment_failure_reason ?? null,
            'message' => $isSuccess 
                ? 'Payment successful! Your order has been confirmed.'
                : ($isFailed 
                    ? 'Payment failed. Please try again or contact support.'
                    : 'Payment is being processed. Please wait...'),
        ]);
    }
}
