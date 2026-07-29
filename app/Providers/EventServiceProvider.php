<?php

namespace App\Providers;

use App\Events\CustomerRegistered;
use App\Events\CustomerVerified;
use App\Events\OrderPaymentSuccessful;
use App\Events\PasswordResetCompleted;
use App\Events\PasswordResetRequested;
use App\Listeners\SendAdminOrderNotification;
use App\Listeners\SendCustomerOrderConfirmation;
use App\Listeners\SendPasswordChangedEmail;
use App\Listeners\SendPasswordResetEmail;
use App\Listeners\SendVerificationEmail;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CustomerRegistered::class => [
            SendVerificationEmail::class,
        ],
        CustomerVerified::class => [
            SendWelcomeEmail::class,
        ],
        PasswordResetCompleted::class => [
            SendPasswordChangedEmail::class,
        ],
        PasswordResetRequested::class => [
            SendPasswordResetEmail::class,
        ],
        OrderPaymentSuccessful::class => [
            SendCustomerOrderConfirmation::class,
            SendAdminOrderNotification::class,
        ],
    ];
}
