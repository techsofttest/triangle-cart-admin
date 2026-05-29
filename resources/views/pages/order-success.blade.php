@extends('layouts.app')

@section('content')
<section class="py-5" style="background-color: var(--c-white); min-height: 60vh; display: flex; align-items: center;">
    <div class="container text-center">
        <div class="mb-4">
            <i class="fa-solid fa-circle-check" style="font-size: 80px; color: #2e7d32;"></i>
        </div>
        <h1 class="font-heading mb-3" style="color: var(--c-primary); font-size: 32px;">Order Placed Successfully!</h1>
        <p class="text-muted mb-4" style="font-family: var(--f-body); font-size: 16px;">
            Thank you for your purchase. Your order number is <strong>{{ $order->order_number }}</strong>.
        </p>
        <p class="text-muted mb-5" style="font-family: var(--f-body); font-size: 14px;">
            We've sent a confirmation email to <strong>{{ $order->email }}</strong>.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ url('/') }}" class="btn-luxury-solid px-5 py-3 text-decoration-none" style="font-size: 13px; letter-spacing: 1.5px;">
                <span>Continue Shopping</span>
            </a>
        </div>
    </div>
</section>
@endsection
