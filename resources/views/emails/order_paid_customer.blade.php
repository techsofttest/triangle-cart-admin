@extends('emails.layouts.master')

@section('title', 'Order Placed & Payment Successful - ' . $order->order_number)

@section('content')
    <h2 style="color: #2b9346; margin-bottom: 5px;">Payment Successful & Order Placed!</h2>
    <p>Hi {{ $order->customer_name ?? 'Customer' }},</p>
    <p>Thank you for your order! Your payment for order <strong>{{ $order->order_number }}</strong> was successful, and your order has been placed.</p>

    <!-- Order Summary -->
    <div style="margin: 25px 0; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #f8fafc; padding: 12px 15px; border-bottom: 1px solid #e2e8f0; font-weight: bold; font-size: 15px;">
            Order Information
        </div>
        <div style="padding: 15px; font-size: 14px; line-height: 1.5;">
            <div style="margin-bottom: 6px;"><strong>Order Number:</strong> {{ $order->order_number }}</div>
            <div style="margin-bottom: 6px;"><strong>Order Date:</strong> {{ optional($order->created_at)->format('d M, Y H:i') }}</div>
            @if($order->delivery_date)
                <div style="margin-bottom: 6px;"><strong>Delivery Date:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('l, d M, Y') }}</div>
            @endif
            @if($order->deliverySlot)
                <div style="margin-bottom: 6px;"><strong>Delivery Slot:</strong> {{ \Carbon\Carbon::parse($order->deliverySlot->start_time)->format('g A') }} - {{ \Carbon\Carbon::parse($order->deliverySlot->end_time)->format('g A') }}</div>
            @endif
        </div>
    </div>

    <!-- Delivery Address -->
    <div style="margin: 25px 0; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #f8fafc; padding: 12px 15px; border-bottom: 1px solid #e2e8f0; font-weight: bold; font-size: 15px;">
            Delivering To
        </div>
        <div style="padding: 15px; font-size: 14px; line-height: 1.5;">
            <strong>{{ $order->shipping_name }}</strong><br>
            {{ $order->shipping_address_line_1 }}
            @if($order->shipping_address_line_2)<br>{{ $order->shipping_address_line_2 }}@endif
            <br>{{ $order->shipping_suburb }}, {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postcode }}
            @if($order->shipping_phone)<br>Phone: {{ $order->shipping_phone }}@endif
        </div>
    </div>

    <!-- Items Ordered -->
    <h3 style="margin-top: 25px; margin-bottom: 10px; font-size: 16px; border-bottom: 2px solid #2b9346; padding-bottom: 5px;">Items Ordered</h3>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 14px;">
        <thead>
            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                <th style="padding: 10px; font-weight: bold;">Item</th>
                <th style="padding: 10px; font-weight: bold;">Variant</th>
                <th style="padding: 10px; font-weight: bold; text-align: center;">Qty</th>
                <th style="padding: 10px; font-weight: bold; text-align: right;">Price</th>
                <th style="padding: 10px; font-weight: bold; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
            <tr style="border-bottom: 1px solid #edf2f7;">
                <td style="padding: 12px 10px; font-weight: 500;">{{ $item->product_name }}</td>
                <td style="padding: 12px 10px; color: #64748b; font-size: 13px;">{{ $item->variant_details ?: 'N/A' }}</td>
                <td style="padding: 12px 10px; text-align: center;">{{ $item->quantity }}</td>
                <td style="padding: 12px 10px; text-align: right;">${{ number_format((float) $item->price, 2) }}</td>
                <td style="padding: 12px 10px; text-align: right; font-weight: 500;">${{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Price Breakdown -->
    <div style="width: 280px; margin-left: auto; margin-bottom: 30px; font-size: 14px; line-height: 2.0; border-top: 1px solid #e2e8f0; padding-top: 15px;">
        <div style="display: flex; justify-content: space-between;">
            <span style="color: #64748b;">Subtotal:</span>
            <span style="font-weight: 500;">${{ number_format((float) $order->subtotal, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="color: #64748b;">Shipping:</span>
            <span style="font-weight: 500;">{{ $order->shipping_cost > 0 ? '$' . number_format((float) $order->shipping_cost, 2) : 'Free' }}</span>
        </div>
        @if($order->discount > 0)
        <div style="display: flex; justify-content: space-between; color: #dc2626;">
            <span>Discount ({{ $order->coupon_code }}):</span>
            <span>-${{ number_format((float) $order->discount, 2) }}</span>
        </div>
        @endif
        <div style="display: flex; justify-content: space-between; border-top: 2px solid #e2e8f0; padding-top: 10px; margin-top: 10px; font-size: 16px; font-weight: bold; color: #1e293b;">
            <span>Grand Total:</span>
            <span>${{ number_format((float) $order->grand_total, 2) }}</span>
        </div>
    </div>
@endsection
