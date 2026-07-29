@extends('emails.layouts.master')

@section('title', 'Order Paid - ' . $order->order_number)

@section('content')
    <h2>Thanks, {{ $order->customer_name ?? 'Customer' }}.</h2>
    <p>Your payment for order <strong>{{ $order->order_number }}</strong> was successful.</p>
    <div class="info-box">
        <div class="info-row"><span class="info-label">Order date:</span> <span>{{ optional($order->created_at)->format('d M, Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Payment method:</span> <span>{{ $order->payment_method }}</span></div>
        <div class="info-row"><span class="info-label">Total:</span> <span>{{ number_format((float) $order->grand_total, 2) }}</span></div>
    </div>
@endsection
