<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'type',
        'eligible_quantity',
        'free_quantity',
        'start_at',
        'end_at',
        'offer_image',
        'is_active',
    ];

    protected $casts = [
        'eligible_quantity' => 'integer',
        'free_quantity' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function eligibleProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promotion_eligible_products');
    }

    public function eligibleCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'promotion_eligible_categories');
    }

    public function eligibleBrands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'promotion_eligible_brands');
    }

    public function freeProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promotion_free_products');
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('start_at')->orWhere('start_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('end_at')->orWhere('end_at', '>=', $now));
    }
}
