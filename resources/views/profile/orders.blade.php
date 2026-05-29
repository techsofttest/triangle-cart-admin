@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: var(--c-linen); min-height: 80vh;">
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm p-4 h-100" style="background-color: var(--c-white);">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; border: 1px solid var(--c-gold);">
                            <i class="fa-solid fa-user fa-2x text-gold"></i>
                        </div>
                        <h5 class="font-heading mb-1">{{ explode(' ', $customer->name)[0] }}</h5>
                        <p class="text-muted small mb-0">{{ $customer->email }}</p>
                    </div>
                    
                    <hr class="my-4 opacity-10">
                    
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <a href="{{ route('profile.dashboard') }}" class="d-flex align-items-center gap-3 text-dark text-decoration-none">
                                <i class="fa-solid fa-gauge-high text-gold" style="width: 20px;"></i>
                                <span class="fw-bold">Dashboard</span>
                            </a>
                        </li>
                        <li class="mb-3">
                            <a href="{{ route('profile.orders') }}" class="d-flex align-items-center gap-3 text-muted text-decoration-none hover-gold transition-all">
                                <i class="fa-solid fa-box" style="width: 20px;"></i>
                                <span>My Orders</span>
                            </a>
                        </li>
                        <li class="mb-3">
                            <a href="{{ route('profile.addresses') }}" class="d-flex align-items-center gap-3 text-muted text-decoration-none hover-gold transition-all">
                                <i class="fa-solid fa-location-dot" style="width: 20px;"></i>
                                <span>My Addresses</span>
                            </a>
                        </li>
                        <li class="mt-5">
                            <form action="/customer/logout" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item p-0 d-flex align-items-center gap-3 text-danger border-0 bg-transparent">
                                    <i class="fa-solid fa-right-from-bracket" style="width: 20px;"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="mb-4">
                    <h2 class="font-heading mb-2">My Orders</h2>
                    <p class="text-muted">Review your past and current physical and digital orders.</p>
                </div>

                <div class="card border-0 shadow-sm p-4" style="background-color: var(--c-white);">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Order #</th>
                                        <th class="border-0">Date</th>
                                        <th class="border-0">Products</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Total</th>
                                        <th class="border-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="fw-bold">#{{ $order->order_number }}</td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="d-flex flex-column gap-2">
                                                    @foreach($order->items as $item)
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="rounded border bg-light overflow-hidden" style="width: 40px; height: 40px; flex-shrink: 0;">
                                                                <img src="{{ asset('storage/' . ($item->product->prod_image ?? 'placeholder.jpg')) }}" class="w-100 h-100 object-fit-cover" alt="{{ $item->product_name }}">
                                                            </div>
                                                            <div class="small text-truncate" style="max-width: 150px;">
                                                                <span class="d-block text-truncate fw-medium">{{ $item->product_name }}</span>
                                                                <span class="text-muted">Qty: {{ $item->quantity }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill @if($order->status == 'processing') bg-info-subtle text-info @elseif($order->status == 'completed') bg-success-subtle text-success @elseif($order->status == 'pending') bg-warning-subtle text-warning @else bg-secondary-subtle text-secondary @endif" style="font-size: 11px;">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="fw-bold">₹{{ number_format($order->grand_total, 2) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-dark px-3 rounded-0 border-gold-hover" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                                    View
                                                </button>
                                                
                                                @php
                                                    $canCancel = !in_array($order->status, ['delivered', 'completed', 'cancelled', 'shipped']);
                                                @endphp

                                                <button type="button" 
                                                    class="btn btn-sm btn-outline-danger px-3 rounded-0 border-danger-hover ms-1 cancel-order-btn" 
                                                    data-order-id="{{ $order->id }}"
                                                    data-order-number="{{ $order->order_number }}"
                                                    @if(!$canCancel) disabled title="This order cannot be cancelled" @endif>
                                                    Cancel
                                                </button>

                                                <!-- Order Detail Modal -->
                                                <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow" style="background-color: var(--c-white);">
                                                            <div class="modal-header border-bottom-light p-4">
                                                                <h5 class="modal-title font-heading">Order Details #{{ $order->order_number }}</h5>
                                                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="row g-4 mb-4">
                                                                    <div class="col-md-6">
                                                                        <h6 class="uppercase-tracking text-gold mb-3">Shipping Address</h6>
                                                                        <p class="small mb-0">
                                                                            <strong>{{ $order->first_name }} {{ $order->last_name }}</strong><br>
                                                                            {{ $order->address }}<br>
                                                                            @if($order->apartment) {{ $order->apartment }}<br> @endif
                                                                            {{ $order->city }}, {{ $order->state }} - {{ $order->pin_code }}<br>
                                                                            {{ $order->country }}<br>
                                                                            Phone: {{ $order->phone }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6 class="uppercase-tracking text-gold mb-3">Order Info</h6>
                                                                        <p class="small mb-1"><span class="text-muted">Date:</span> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                                                                        <p class="small mb-1"><span class="text-muted">Payment:</span> {{ strtoupper($order->payment_method) }}</p>
                                                                        <p class="small mb-1">
                                                                            <span class="text-muted">Payment Status:</span> 
                                                                            <span class="badge @if($order->payment_status == 'paid') bg-success-subtle text-success @else bg-warning-subtle text-warning @endif rounded-pill" style="font-size: 10px;">{{ ucfirst($order->payment_status) }}</span>
                                                                        </p>
                                                                        <p class="small mb-0">
                                                                            <span class="text-muted">Order Status:</span> 
                                                                            <span class="badge bg-dark-subtle text-dark rounded-pill" style="font-size: 10px;">{{ ucfirst($order->status) }}</span>
                                                                        </p>
                                                                    </div>
                                                                </div>

                                                                <h6 class="uppercase-tracking text-gold mb-3">Order Items</h6>
                                                                <div class="table-responsive border rounded overflow-hidden">
                                                                    <table class="table table-borderless align-middle mb-0">
                                                                        <thead class="bg-light">
                                                                            <tr class="small text-muted">
                                                                                <th class="px-3 py-2">Product</th>
                                                                                <th class="px-3 py-2 text-center">Price</th>
                                                                                <th class="px-3 py-2 text-center">Qty</th>
                                                                                <th class="px-3 py-2 text-end">Total</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($order->items as $item)
                                                                                <tr class="border-bottom-light">
                                                                                    <td class="px-3 py-3">
                                                                                        <div class="d-flex align-items-center gap-3">
                                                                                            <div class="rounded border overflow-hidden" style="width: 50px; height: 50px; flex-shrink: 0;">
                                                                                                <img src="{{ asset('storage/' . ($item->product->prod_image ?? 'placeholder.jpg')) }}" class="w-100 h-100 object-fit-cover">
                                                                                            </div>
                                                                                            <div>
                                                                                                <span class="fw-medium d-block" style="font-size: 13px;">{{ $item->product_name }}</span>
                                                                                                @if($item->variant_details)
                                                                                                    <span class="text-muted tiny">{{ $item->variant_details }}</span>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td class="px-3 py-3 text-center small">₹{{ number_format($item->price, 2) }}</td>
                                                                                    <td class="px-3 py-3 text-center small">{{ $item->quantity }}</td>
                                                                                    <td class="px-3 py-3 text-end fw-bold small">₹{{ number_format($item->line_total, 2) }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                <div class="row justify-content-end mt-4">
                                                                    <div class="col-md-5">
                                                                        <div class="d-flex justify-content-between mb-2 small">
                                                                            <span class="text-muted">Subtotal</span>
                                                                            <span>₹{{ number_format($order->subtotal, 2) }}</span>
                                                                        </div>
                                                                        @if($order->shipping_cost > 0)
                                                                            <div class="d-flex justify-content-between mb-2 small">
                                                                                <span class="text-muted">Shipping</span>
                                                                                <span>₹{{ number_format($order->shipping_cost, 2) }}</span>
                                                                            </div>
                                                                        @endif
                                                                        @if($order->discount > 0)
                                                                            <div class="d-flex justify-content-between mb-2 small text-success">
                                                                                <span>Discount ({{ $order->coupon_code }})</span>
                                                                                <span>-₹{{ number_format($order->discount, 2) }}</span>
                                                                            </div>
                                                                        @endif
                                                                        <hr class="my-2 opacity-10">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span class="fw-bold">Grand Total</span>
                                                                            <span class="fw-bold text-gold fs-5">₹{{ number_format($order->grand_total, 2) }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">You haven't placed any orders yet.</h5>
                            <a href="{{ route('home') }}" class="btn btn-primary-custom2 mt-3">Start Shopping</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

                <style>
                    .modal-header { border-color: rgba(0,0,0,0.05) !important; }
                    .border-bottom-light { border-bottom: 1px solid rgba(0,0,0,0.03) !important; }
                    .uppercase-tracking { text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; font-size: 11px; }
                    .tiny { font-size: 10px; }
                    .border-gold-hover:hover { background-color: var(--c-gold) !important; color: #fff !important; border-color: var(--c-gold) !important; }
                    .bg-success-subtle { background-color: #e6f7ef; color: #198754 !important; }
                    .bg-warning-subtle { background-color: #fff8eb; color: #ffc107 !important; }
                    .bg-info-subtle { background-color: #e7f6f8; color: #0dcaf0 !important; }
                    .bg-secondary-subtle { background-color: #f8f9fa; color: #6c757d !important; }
                    .bg-dark-subtle { background-color: #e9ecef; color: #212529 !important; }
                    .object-fit-cover { object-fit: cover; }
                </style>

@section('footer_extras')
<script>
    document.querySelectorAll('.cancel-order-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderNumber = this.getAttribute('data-order-number');
            
            alertify.confirm('Cancel Order', `Are you sure you want to cancel order #${orderNumber}?`, 
                function() {
                    fetch(`/profile/orders/${orderId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alertify.success(data.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            alertify.error(data.message || 'Something went wrong');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alertify.error('An error occurred. Please try again.');
                    });
                },
                function() {
                    // Cancel clicked
                }
            );
        });
    });
</script>
@endsection
@endsection
