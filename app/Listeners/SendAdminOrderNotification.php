<?php

namespace App\Listeners;

use App\Events\OrderPaymentSuccessful;
use App\Mail\AdminOrderPaidMail;
use App\Services\MailService;

class SendAdminOrderNotification
{
    public function __construct(private MailService $mailService)
    {
    }

    public function handle(OrderPaymentSuccessful $event): void
    {
        $recipients = $this->mailService->adminRecipients();
        if (! $recipients) {
            return;
        }

        $adminUrl = url('/admin/orders/' . $event->order->id);
        $this->mailService->sendToMany($recipients, new AdminOrderPaidMail($event->order->fresh(['items', 'customer']), $adminUrl));
    }
}
