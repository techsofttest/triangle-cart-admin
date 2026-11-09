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
     * ALL active Buy X Get X promotions are evaluated independently and their
     * free products are merged — if the cart qualifies for multiple offers,
     * all corresponding free items are added.
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

        if ($offers->isEmpty()) {
            return [
                'promotion'  => null,
                'free_item'  => null,
                'free_items' => [],
                'cart_items' => $userPaidCartItems,
                'qualified'  => false,
            ];
        }

        // Evaluate every offer independently and collect results
        $qualifiedOffers    = [];   // offers the cart fully qualifies for
        $unqualifiedOffers  = [];   // offers with partial progress (for the UI nudge)
        $allFreeItems       = [];   // merged free items from all qualified offers

        foreach ($offers as $offer) {
            $currentQty  = $this->countEligibleQty($offer, $userPaidCartItems);
            $eligibleQty = (int) $offer->eligible_quantity;

            if ($currentQty < $eligibleQty) {
                $unqualifiedOffers[] = [
                    'offer'       => $offer,
                    'currentQty'  => $currentQty,
                    'eligibleQty' => $eligibleQty,
                    'needed'      => $eligibleQty - $currentQty,
                ];
                continue;
            }

            // Offer qualifies — collect its free products
            $freeItemsForOffer = $this->buildFreeItems($offer);

            if (!empty($freeItemsForOffer)) {
                $qualifiedOffers[] = [
                    'offer'      => $offer,
                    'currentQty' => $currentQty,
                    'freeItems'  => $freeItemsForOffer,
                ];
                $allFreeItems = array_merge($allFreeItems, $freeItemsForOffer);
            } else {
                // Qualified but stock unavailable
                $unqualifiedOffers[] = [
                    'offer'       => $offer,
                    'currentQty'  => $currentQty,
                    'eligibleQty' => $eligibleQty,
                    'needed'      => 0,
                    'unavailable' => true,
                ];
            }
        }

        // Build the aggregated promotion summary
        if (!empty($qualifiedOffers)) {
            $qualifiedNames = implode(' + ', array_map(fn ($o) => $o['offer']->name, $qualifiedOffers));

            $promotionSummary = [
                'type'          => 'buy_x_get_x',
                'qualified'     => true,
                'free_quantity' => count($allFreeItems),
                'free_products' => array_map(fn ($item) => [
                    'id'         => $item['product_id'],
                    'name'       => $item['name'],
                    'image'      => $item['image'],
                    'offer_id'   => $item['promotion_id'],
                    'offer_name' => $item['promotion_name'],
                ], $allFreeItems),
                // Individual offer details for the frontend (progress bars, etc.)
                'offers'        => array_map(fn ($o) => [
                    'offer_id'          => $o['offer']->id,
                    'name'              => $o['offer']->name,
                    'qualified'         => true,
                    'eligible_quantity' => (int) $o['offer']->eligible_quantity,
                    'current_quantity'  => $o['currentQty'],
                    'remaining_quantity'=> 0,
                    'message'           => $o['offer']->name . ' applied!',
                ], $qualifiedOffers),
                // Legacy flat fields (kept for backward compat with the frontend)
                'offer_id'          => $qualifiedOffers[0]['offer']->id,
                'name'              => $qualifiedNames,
                'eligible_quantity' => (int) $qualifiedOffers[0]['offer']->eligible_quantity,
                'current_quantity'  => $qualifiedOffers[0]['currentQty'],
                'remaining_quantity'=> 0,
                'message'           => $qualifiedNames . ' applied!',
            ];

            $finalCartItems = array_merge($userPaidCartItems, $allFreeItems);

            return [
                'promotion'  => $promotionSummary,
                'free_item'  => $allFreeItems[0] ?? null,
                'free_items' => $allFreeItems,
                'cart_items' => $finalCartItems,
                'qualified'  => true,
            ];
        }

        // Nothing fully qualified — return progress info for the best partial offer
        usort($unqualifiedOffers, fn ($a, $b) => ($a['needed'] ?? PHP_INT_MAX) <=> ($b['needed'] ?? PHP_INT_MAX));
        $best  = $unqualifiedOffers[0];
        $offer = $best['offer'];

        $unavailable = $best['unavailable'] ?? false;
        $needed      = $best['needed'];

        $message = $unavailable
            ? 'Free product is currently unavailable.'
            : "Buy {$needed} more eligible item" . ($needed > 1 ? 's' : '') . " to get a free product!";

        return [
            'promotion' => [
                'type'               => 'buy_x_get_x',
                'offer_id'           => $offer->id,
                'name'               => $offer->name,
                'qualified'          => false,
                'eligible_quantity'  => $best['eligibleQty'],
                'current_quantity'   => $best['currentQty'],
                'remaining_quantity' => $needed,
                'offers'             => array_map(fn ($o) => [
                    'offer_id'           => $o['offer']->id,
                    'name'               => $o['offer']->name,
                    'qualified'          => false,
                    'eligible_quantity'  => $o['eligibleQty'],
                    'current_quantity'   => $o['currentQty'],
                    'remaining_quantity' => $o['needed'],
                    'message'            => "Buy {$o['needed']} more eligible item" . ($o['needed'] > 1 ? 's' : '') . " to get a free product!",
                ], $unqualifiedOffers),
                'message' => $message,
            ],
            'free_item'  => null,
            'free_items' => [],
            'cart_items' => $userPaidCartItems,
            'qualified'  => false,
        ];
    }

    /**
     * Build the free cart items for a fully-qualified offer.
     * Returns an empty array if no free products have stock.
     *
     * @param Promotion $offer
     * @return array
     */
    protected function buildFreeItems(Promotion $offer): array
    {
        $items = [];

        foreach ($offer->freeProducts as $freeProduct) {
            if (!$freeProduct->is_active && !$freeProduct->is_free_gift) {
                continue;
            }

            $availableVariant = $freeProduct->variants
                ->filter(fn ($v) => (int) $v->stock > 0)
                ->sortBy(fn ($v) => [(float) $v->selling_price, (string) ($v->sku ?? '')])
                ->first();

            $hasVariants = $freeProduct->variants->isNotEmpty();

            // Skip if all variants are out of stock
            if ($hasVariants && !$availableVariant) {
                continue;
            }

            $items[] = [
                'id'            => 'free-' . $freeProduct->id,
                'product_id'    => $freeProduct->id,
                'variant_id'    => $availableVariant?->id ?? null,
                'name'          => $freeProduct->name,
                'price'         => 0,
                'quantity'      => 1,
                'is_free'       => true,
                'promotion_id'  => $offer->id,
                'promotion_name'=> $offer->name,
                'image'         => $freeProduct->featured_image ?? $freeProduct->image ?? '',
                'brand'         => $freeProduct->brand?->name ?? 'General',
                'weight'        => $availableVariant ? trim("{$availableVariant->size} {$availableVariant->unit}") : '1 unit',
                'category'      => $freeProduct->category?->slug ?? 'General',
                'inStock'       => true,
            ];
        }

        return $items;
    }

    /**
     * Count how many eligible units in the cart qualify for the given promotion.
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
