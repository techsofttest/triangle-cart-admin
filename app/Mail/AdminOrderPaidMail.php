<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class AdminOrderPaidMail extends Mailable
{
    public function __construct(public Order $order, public ?string $adminUrl = null)
    {
    }

    public function build()
    {
        return $this->subject('Order paid: ' . $this->order->order_number)
            ->view('emails.order_paid_admin', [
                'order' => $this->order,
                'adminUrl' => $this->adminUrl,
            ]);
    }
}
