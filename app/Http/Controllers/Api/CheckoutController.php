<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payments\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        protected PaymentGatewayInterface $paymentGateway
    ) {
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array',
            'customer_id' => 'nullable|exists:customers,id',
            'delivery_details' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // This is a simplified example. In a real application, you would
        // have more complex logic to calculate totals, handle inventory, etc.
        $grandTotal = 0;
        foreach ($request->input('cart') as $item) {
            $grandTotal += $item['price'] * $item['quantity'];
        }

        $order = Order::create([
            'order_number' => 'ORD-' . Str::random(10),
            'customer_id' => $request->input('customer_id'),
            'grand_total' => $grandTotal,
            'payment_status' => 'pending',
            'status' => 'pending_payment',
            // Add other necessary fields from delivery_details
        ]);

        try {
            $paymentIntent = $this->paymentGateway->createPaymentIntent($order);

            return response()->json([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent' => $paymentIntent,
            ]);
        } catch (\Exception $e) {
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
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent' => $paymentIntent,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create payment intent: ' . $e->getMessage()], 500);
        }
    }
}
