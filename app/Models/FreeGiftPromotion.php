<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class FreeGiftPromotion extends Model
{
    protected $table = 'free_gift_promotions';

    protected $fillable = [
        'name',
        'status',
        'starts_at',
        'ends_at',
        'minimum_cart_amount',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'minimum_cart_amount' => 'decimal:2',
    ];

    public function freeProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'free_gift_promotion_products',
            'free_gift_promotion_id',
            'product_id'
        )->withTimestamps();
    }

    public function excludedCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'free_gift_promotion_excluded_categories',
            'free_gift_promotion_id',
            'category_id'
        )->withTimestamps();
    }

    public function getIsActiveAttribute(): bool
    {
        return strtolower($this->status ?? '') === 'active';
    }

    public function setIsActiveAttribute($value): void
    {
        $this->attributes['status'] = $value ? 'active' : 'inactive';
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }
}
