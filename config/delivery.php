<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Minimum Delivery Lead Time
    |--------------------------------------------------------------------------
    |
    | The minimum lead time in hours required before a direct delivery order
    | can be fulfilled. For example, 24 hours.
    |
    */
    'minimum_lead_hours' => 24,

    /*
    |--------------------------------------------------------------------------
    | Courier Flat Rate Options
    |--------------------------------------------------------------------------
    |
    | Default flat rate shipping fee and free shipping threshold for courier
    | delivery options.
    |
    */
    'courier' => [
        'fee' => 9.99,
        'free_threshold' => 50.00,
    ],
];
