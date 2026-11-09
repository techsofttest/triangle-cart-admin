<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Promotion;

class BuyXGetXPromotionService
{
    /**
     * Fetch all active Buy X Get X offers.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Promotion>
     */
    public function getActiveOffers()
    {
        return Promotion::query()
            ->active()
            ->where('type', 'buy_x_get_x')
            ->orderBy('id')
            ->with(['eligibleProducts', 'eligibleCategories', 'eligibleBrands', 'freeProducts.variants', 'freeProducts.brand', 'freeProducts.category'])
            ->get();
    }

    /**
     * Fetch active Buy X Get X offer.
     *
     * @deprecated Use getActiveOffers() for multi-promotion support.
     */
    public function getActiveOffer(): ?Promotion
    {
        return $this->getActiveOffers()->first();
    }

    /**
     * Recalculate promotion state and return structured result.
     * Evaluates all active Buy X Get X promotions and returns the first one
     * for which the cart qualifies (or the one with the best partial progress).
     *
     * @param array $rawCartItems
     * @return array
     */
    public function calculatePromotion(array $rawCartItems): array
    {
        // Filter out existing promotional/free items (Buy X Get X items) submitted by frontend or previous calculation,
        // but preserve free gift items so FreeGiftPromotionService can process them.
        $userPaidCartItems = array_values(array_filter($rawCartItems, function ($item) {
            $isFreeGift = !empty($item['is_free_gift']);
            $itemId = (string)($item['id'] ?? $item['product_id'] ?? '');
            if ($isFreeGift || str_starts_with($itemId, 'free-gift-')) {
                return true;
            }
            if (!empty($item['is_free'])) {
                return false;
            }
            if (str_starts_with($itemId, 'free-')) {
                return false;
            }
            return true;
        }));

        $offers = $this->getActiveOffers();

        // Resolve the best matching offer: prefer one that fully qualifies,
        // then fall back to the one with the highest partial progress.
        $offer    = null;
        $bestQty  = -1;
        foreach ($offers as $candidate) {
            $qty = $this->countEligibleQty($candidate, $userPaidCartItems);
            if ($qty >= (int) $candidate->eligible_quantity) {
                // Fully qualified — use this offer immediately
                $offer = $candidate;
                break;
            }
            if ($qty > $bestQty) {
                $bestQty = $qty;
                $offer   = $candidate;
            }
        }

        if (!$offer) {
            return [
                'promotion'  => null,
                'free_item'  => null,
                'cart_items' => $userPaidCartItems,
                'qualified'  => false,
            ];
        }

        $eligibleProductIds  = array_map('intval', $offer->eligibleProducts->pluck('id')->toArray());
        $eligibleCategoryIds = array_map('intval', $offer->eligibleCategories->pluck('id')->toArray());
        $eligibleBrandIds    = array_map('intval', $offer->eligibleBrands->pluck('id')->toArray());

        if (!empty($eligibleCategoryIds)) {
            $childCategoryIds    = \App\Models\Category::whereIn('parent_id', $eligibleCategoryIds)->pluck('id')->toArray();
            $eligibleCategoryIds = array_unique(array_merge($eligibleCategoryIds, array_map('intval', $childCategoryIds)));
        }

        $hasRestrictions = !empty($eligibleProductIds) || !empty($eligibleCategoryIds) || !empty($eligibleBrandIds);

        // Calculate qualifying quantity from customer-purchased products
        $currentQuantity = 0;

        foreach ($userPaidCartItems as $item) {
            $rawId        = $item['product_id'] ?? $item['id'] ?? null;
            $rawVariantId = $item['variant_id'] ?? $item['variantId'] ?? null;

            if (is_string($rawId) && str_starts_with($rawId, 'free-gift-')) {
                $rawId = (int) str_replace('free-gift-', '', $rawId);
            } elseif (is_string($rawId) && str_starts_with($rawId, 'free-')) {
                $rawId = (int) str_replace('free-', '', $rawId);
            }

            $product = null;
            if ($rawId && is_numeric($rawId)) {
                $product = Product::find((int) $rawId);
            }

            if (! $product && $rawVariantId && is_numeric($rawVariantId)) {
                $variant = \App\Models\ProductVariant::with('product')->find((int) $rawVariantId);
                $product = $variant?->product;
            }

            if (! $product && $rawId && is_numeric($rawId)) {
                $variant = \App\Models\ProductVariant::with('product')->find((int) $rawId);
                $product = $variant?->product;
            }

            if (! $product || (! $product->is_active && ! $product->is_free_gift)) {
                continue;
            }

            $productBrandId    = $product->brand_id    ? (int) $product->brand_id    : null;
            $productCategoryId = $product->category_id ? (int) $product->category_id : null;

            $isEligible = ! $hasRestrictions ||
                in_array((int) $product->id, $eligibleProductIds, true) ||
                ($productCategoryId && in_array($productCategoryId, $eligibleCategoryIds, true)) ||
                ($productBrandId && in_array($productBrandId, $eligibleBrandIds, true));

            // Free gifts do not count towards qualifying Buy X Get X quantity
            if (!empty($item['is_free_gift']) || (is_string($item['id'] ?? null) && str_starts_with($item['id'], 'free-gift-')) || (is_string($item['product_id'] ?? null) && str_starts_with($item['product_id'], 'free-gift-'))) {
                continue;
            }

            if ($isEligible) {
                $currentQuantity += max(0, (int) ($item['quantity'] ?? 1));
            }
        }

        $eligibleQty = (int) $offer->eligible_quantity;

        if ($currentQuantity < $eligibleQty) {
            $needed = max(0, $eligibleQty - $currentQuantity);
            return [
                'promotion' => [
                    'type'               => 'buy_x_get_x',
                    'offer_id'           => $offer->id,
                    'name'               => $offer->name,
                    'qualified'          => false,
                    'eligible_quantity'  => $eligibleQty,
                    'current_quantity'   => $currentQuantity,
                    'remaining_quantity' => $needed,
                    'message'            => "Buy {$needed} more eligible item" . ($needed > 1 ? 's' : '') . " to get a free product!",
                ],
                'free_item'  => null,
                'cart_items' => $userPaidCartItems,
                'qualified'  => false,
            ];
        }

        // Cart qualifies! Evaluate every configured free product independently
        $qualifyingFreeItems = [];

        foreach ($offer->freeProducts as $freeProduct) {
            if (!$freeProduct->is_active && !$freeProduct->is_free_gift) {
                continue;
            }

            // Check stock of variants
            $availableVariant = $freeProduct->variants
                ->filter(fn ($v) => (int)$v->stock > 0)
                ->sortBy(fn ($v) => [(float)$v->selling_price, (string)($v->sku ?? '')])
                ->first();

            $hasVariants = $freeProduct->variants->isNotEmpty();

            // Product qualifies if:
            // 1. It has variants and at least one variant has stock > 0
            // 2. It has no variants (stock is considered > 0 or product level)
            if ($hasVariants && !$availableVariant) {
                continue;
            }

            $qualifyingFreeItems[] = [
                'id'           => 'free-' . $freeProduct->id,
                'product_id'   => $freeProduct->id,
                'variant_id'   => $availableVariant?->id ?? null,
                'name'         => $freeProduct->name,
                'price'        => 0,
                'quantity'     => 1,
                'is_free'      => true,
                'promotion_id' => $offer->id,
                'image'        => $freeProduct->featured_image ?? $freeProduct->image ?? '',
                'brand'        => $freeProduct->brand?->name ?? 'General',
                'weight'       => $availableVariant ? trim("{$availableVariant->size} {$availableVariant->unit}") : '1 unit',
                'category'     => $freeProduct->category?->slug ?? 'General',
                'inStock'      => true,
            ];
        }

        if (empty($qualifyingFreeItems)) {
            return [
                'promotion' => [
                    'type'               => 'buy_x_get_x',
                    'offer_id'           => $offer->id,
                    'name'               => $offer->name,
                    'qualified'          => false,
                    'eligible_quantity'  => $eligibleQty,
                    'current_quantity'   => $currentQuantity,
                    'remaining_quantity' => 0,
                    'message'            => 'Free product is currently unavailable.',
                ],
                'free_item'  => null,
                'free_items' => [],
                'cart_items' => $userPaidCartItems,
                'qualified'  => false,
            ];
        }

        $finalCartItems = array_merge($userPaidCartItems, $qualifyingFreeItems);

        return [
            'promotion' => [
                'type'               => 'buy_x_get_x',
                'offer_id'           => $offer->id,
                'name'               => $offer->name,
                'qualified'          => true,
                'eligible_quantity'  => $eligibleQty,
                'current_quantity'   => $currentQuantity,
                'remaining_quantity' => 0,
                'free_quantity'      => count($qualifyingFreeItems),
                'free_products'      => array_map(fn ($item) => [
                    'id'    => $item['product_id'],
                    'name'  => $item['name'],
                    'image' => $item['image'],
                ], $qualifyingFreeItems),
                'message'            => $offer->name . ' applied!',
            ],
            'free_item'  => $qualifyingFreeItems[0] ?? null,
            'free_items' => $qualifyingFreeItems,
            'cart_items' => $finalCartItems,
            'qualified'  => true,
        ];
    }

    /**
     * Count how many eligible units in the cart qualify for the given promotion.
     * This is used to pick the best-matching offer when multiple are active.
     */
    protected function countEligibleQty(Promotion $offer, array $userPaidCartItems): int
    {
        $eligibleProductIds  = array_map('intval', $offer->eligibleProducts->pluck('id')->toArray());
        $eligibleCategoryIds = array_map('intval', $offer->eligibleCategories->pluck('id')->toArray());
        $eligibleBrandIds    = array_map('intval', $offer->eligibleBrands->pluck('id')->toArray());

        if (!empty($eligibleCategoryIds)) {
            $childCategoryIds    = \App\Models\Category::whereIn('parent_id', $eligibleCategoryIds)->pluck('id')->toArray();
            $eligibleCategoryIds = array_unique(array_merge($eligibleCategoryIds, array_map('intval', $childCategoryIds)));
        }

        $hasRestrictions = !empty($eligibleProductIds) || !empty($eligibleCategoryIds) || !empty($eligibleBrandIds);

        $qty = 0;
        foreach ($userPaidCartItems as $item) {
            // Skip free gifts
            if (!empty($item['is_free_gift'])) {
                continue;
            }
            $rawId = $item['product_id'] ?? $item['id'] ?? null;
            if (is_string($rawId) && str_starts_with($rawId, 'free-gift-')) {
                continue;
            }
            if (is_string($rawId) && str_starts_with($rawId, 'free-')) {
                $rawId = (int) str_replace('free-', '', $rawId);
            }

            $product      = null;
            $rawVariantId = $item['variant_id'] ?? $item['variantId'] ?? null;

            if ($rawId && is_numeric($rawId)) {
                $product = Product::find((int) $rawId);
            }
            if (!$product && $rawVariantId && is_numeric($rawVariantId)) {
                $variant = \App\Models\ProductVariant::with('product')->find((int) $rawVariantId);
                $product = $variant?->product;
            }
            if (!$product && $rawId && is_numeric($rawId)) {
                $variant = \App\Models\ProductVariant::with('product')->find((int) $rawId);
                $product = $variant?->product;
            }

            if (!$product || (!$product->is_active && !$product->is_free_gift)) {
                continue;
            }

            $productBrandId    = $product->brand_id    ? (int) $product->brand_id    : null;
            $productCategoryId = $product->category_id ? (int) $product->category_id : null;

            $isEligible = !$hasRestrictions
                || in_array((int) $product->id, $eligibleProductIds, true)
                || ($productCategoryId && in_array($productCategoryId, $eligibleCategoryIds, true))
                || ($productBrandId    && in_array($productBrandId,    $eligibleBrandIds,    true));

            if ($isEligible) {
                $qty += max(0, (int) ($item['quantity'] ?? 1));
            }
        }

        return $qty;
    }
}
