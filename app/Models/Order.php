<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'first_name', 'last_name', 'email', 'phone',
        'country', 'address', 'apartment', 'city', 'state', 'pin_code', 'billing_details',
        'shipping_method', 'payment_method', 'payment_status', 'status',
        'subtotal', 'shipping_cost', 'discount', 'coupon_code', 'grand_total', 'notes'
    ];
    
    protected $casts = [
        'billing_details' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
