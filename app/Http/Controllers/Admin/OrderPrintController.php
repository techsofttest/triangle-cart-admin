<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderPrintController extends Controller
{
    /**
     * Display a printable invoice for the given order.
     */
    public function show(Order $order)
    {
        // Eager load required relations for the invoice
        $order->load([
            'items.product.brand',
            'items.variant',
            'customer',
            'deliverySlot',
            'assignedStaff',
        ]);

        return view('invoices.invoice', ['order' => $order]);
    }
}
