<?php

namespace App\Services\Import;

use App\Models\ProductVariant;

class VariantResolver
{
    /** @var array<string, ProductVariant> Keyed by lowercase SKU */
    protected array $cache = [];

    /**
     * Pre-load all existing variants into the cache.
     */
    public function warmCache(): void
    {
        $this->cache = [];
        foreach (ProductVariant::all() as $variant) {
            $this->cache[strtolower(trim($variant->sku))] = $variant;
        }
    }

    /**
     * Resolve a variant by SKU. Creates or updates.
     *
     * @param array<string, mixed> $attributes Must include 'sku' and 'product_id'
     * @return array{variant: ProductVariant, is_new: bool}
     */
    public function resolve(array $attributes): array
    {
        $sku = strtolower(trim($attributes['sku']));

        if (isset($this->cache[$sku])) {
            $variant = $this->cache[$sku];
            $variant->update($attributes);
            return ['variant' => $variant, 'is_new' => false];
        }

        $isNew = !ProductVariant::where('sku', trim($attributes['sku']))->exists();

        $variant = ProductVariant::updateOrCreate(
            ['sku' => trim($attributes['sku'])],
            $attributes
        );

        $this->cache[$sku] = $variant;

        return ['variant' => $variant, 'is_new' => $isNew];
    }
}
