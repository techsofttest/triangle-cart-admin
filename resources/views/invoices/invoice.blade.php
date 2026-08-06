<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Invoice - {{ $order->order_number }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        html,body{
            background: #fff;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .container{
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }

        header{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom: 12px;
        }

        .company{
            text-align:left;
        }

        .company .name{
            font-weight:700;
            font-size:16px;
            margin-bottom:4px;
        }

        .meta{
            text-align:right;
            font-size:13px;
        }

        h2{font-size:14px;margin:8px 0}

        .section{margin:8px 0 14px;}

        .two-col{display:flex;justify-content:space-between;gap:12px}
        .col{width:48%}

        table{width:100%;border-collapse:collapse}
        th,td{padding:6px 8px;border:1px solid #000;text-align:left;vertical-align:top}
        th{font-weight:700}

        .no-border td{border:0}

        tfoot td{font-weight:700}

        .right{text-align:right}

        .small{font-size:11px;color:#222}

        .product-extra{font-size:11px;color:#222;margin-top:6px}
        .product-extra div{margin-top:2px}

        footer{margin-top:18px;text-align:center;font-size:12px}

        /* Prevent page breaks inside table rows where possible */
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="company">
            <div class="name">{{ config('app.name', 'Company Name') }}</div>
            
        </div>

        <div class="meta">
            
            <div>Order #: {{ $order->order_number }}</div>
            <div>Order Date: {{ optional($order->created_at)->format('d M Y \a\t g:i A') }}</div>
            <div>Payment: {{ optional($order->payment_status)->value ?? $order->payment_status }}</div>
        </div>
    </header>

    <div class="section two-col">
        <div class="col">
            <h2>Customer</h2>
            <div>{{ trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? '')) ?: ($order->customer?->name ?? '—') }}</div>
            <div class="small">{{ $order->email ?? $order->customer?->email ?? '—' }}</div>
            <div class="small">{{ $order->phone ?? $order->customer?->phone ?? '—' }}</div>
        </div>

        <div class="col">
            <h2>Delivery Address</h2>
            <div>
                @if($order->address)
                    {{ $order->first_name }} {{ $order->last_name }}<br>
                    {{ $order->address }}<br>
                    @if($order->apartment){{ $order->apartment }}<br>@endif
                    {{ $order->city }}, {{ $order->state }}<br>
                    {{ $order->country }} {{ $order->pin_code }}
                @else
                    —
                @endif
            </div>
        </div>
    </div>

    @if($order->delivery_type === 'direct')
        <div class="section">
            <h2>Delivery Information</h2>
            <div class="small">
                <div>Type: Direct</div>
                @if($order->delivery_date)
                    <div>Date: {{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</div>
                @endif
                @if($order->deliverySlot)
                    <div>Time: {{ optional($order->deliverySlot)->start_time ? \Carbon\Carbon::parse($order->deliverySlot->start_time)->format('g:i A') : '' }} - {{ optional($order->deliverySlot)->end_time ? \Carbon\Carbon::parse($order->deliverySlot->end_time)->format('g:i A') : '' }}</div>
                @endif
                @if($order->delivery_notes)
                    <div>Notes: {{ $order->delivery_notes }}</div>
                @endif
            </div>
        </div>
    @endif

    <div class="section">
        <h2>Products</h2>
        <table>
            <thead>
                <tr>
                    <th style="width:48%">Product</th>
                    <th>Variant</th>
                    <th style="width:12%">Unit Price</th>
                     <th style="width:8%">Qty</th>
                    <th style="width:12%">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            <div class="product-extra">
                                @if($item->product?->brand?->name)
                                <div>Brand: {{ $item->product->brand->name }}</div>
                                @endif
                            </div>
                        </td>
                        <td>@if($item->variant?->size){{ $item->variant->size }}@endif @if($item->variant?->unit){{ $item->variant->unit }}@endif</td>
                        
                        <td class="right">${{ number_format($item->price, 2) }}</td>
                        <td class="right">{{ $item->quantity }}</td>
                        <td class="right">${{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section" style="margin-top:8px;">
        <table class="no-border" style="width:40%;float:right">
            <tbody>
                <tr>
                    <td>Subtotal</td>
                    <td class="right">${{ number_format($order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Shipping</td>
                    <td class="right">${{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
                @if($order->discount > 0)
                    <tr>
                        <td>Discount ({{ $order->coupon_code }})</td>
                        <td class="right">-${{ number_format($order->discount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Grand Total</strong></td>
                    <td class="right">${{ number_format($order->grand_total, 2) }}</td>
                </tr>
            </tbody>
        </table>
        <div style="clear:both"></div>
    </div>

    

</div>

<script>
    window.onload = function () {
        window.print();
    };
</script>
</body>
</html>
