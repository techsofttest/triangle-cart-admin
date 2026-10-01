<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'variant_id', 'product_name',
        'variant_details', 'quantity', 'price', 'line_total', 'is_picked',
        'is_free', 'promotion_id', 'is_free_gift', 'free_gift_promotion_id'
    ];

    protected $casts = [
        'is_picked' => 'boolean',
        'is_free'   => 'boolean',
        'is_free_gift' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function freeGiftPromotion()
    {
        return $this->belongsTo(FreeGiftPromotion::class, 'free_gift_promotion_id');
    }
}
