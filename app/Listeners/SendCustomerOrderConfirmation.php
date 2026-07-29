<?php

namespace App\Listeners;

use App\Events\OrderPaymentSuccessful;
use App\Mail\CustomerOrderPaidMail;
use App\Services\MailService;

class SendCustomerOrderConfirmation
{
    public function __construct(private MailService $mailService)
    {
    }

    public function handle(OrderPaymentSuccessful $event): void
    {
        if ($event->order->customer_email) {
            $this->mailService->send($event->order->customer_email, new CustomerOrderPaidMail($event->order->fresh(['items', 'customer'])));
        }
    }
}
