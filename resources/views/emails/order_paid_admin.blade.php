@extends('emails.layouts.master')

@section('title', 'Order Paid Notification - ' . $order->order_number)

@section('content')
    <h2>Order paid successfully</h2>
    <div class="info-box">
        <div class="info-row"><span class="info-label">Order #:</span> <span>{{ $order->order_number }}</span></div>
        <div class="info-row"><span class="info-label">Customer:</span> <span>{{ $order->customer_name }}</span></div>
        <div class="info-row"><span class="info-label">Email:</span> <span>{{ $order->customer_email }}</span></div>
        <div class="info-row"><span class="info-label">Total:</span> <span>{{ number_format((float) $order->grand_total, 2) }}</span></div>
        <div class="info-row"><span class="info-label">Payment:</span> <span>{{ $order->payment_method }} / {{ $order->payment_status }}</span></div>
    </div>
    @if($adminUrl)
        <p style="text-align: center;"><a href="{{ $adminUrl }}" class="button">View Order</a></p>
    @endif
@endsection
