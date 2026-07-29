<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class CustomerOrderPaidMail extends Mailable
{
    public function __construct(public Order $order)
    {
    }

    public function build()
    {
        return $this->subject('Order payment received - ' . $this->order->order_number)
            ->view('emails.order_paid_customer', ['order' => $this->order]);
    }
}
