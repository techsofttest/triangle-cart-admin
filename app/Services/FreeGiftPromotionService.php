<?php

namespace App\Services;

use App\Models\Category;
use App\Models\FreeGiftPromotion;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class FreeGiftPromotionService
{
    /**
     * Deactivate any other active free gift promotion so only one promotion is active at a time.
     */
    public function deactivateOtherActivePromotions(?int $ignoreId = null, bool $isActive = true): int
    {
        if (! $isActive) {
            return 0;
        }

        $query = FreeGiftPromotion::where('status', 'active');
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->update(['status' => 'inactive']);
    }

    /**
     * Validate that only one Free Gift Promotion is active at a time.
     * (Deprecated: Use deactivateOtherActivePromotions instead)
     */
    public function validateSingleActivePromotion(?int $ignoreId = null, bool $isActive = true): void
    {
        $this->deactivateOtherActivePromotions($ignoreId, $isActive);
    }

    /**
     * Synchronize product free_gift and active flags when products are attached or detached.
     */
    public function syncProductFreeGiftFlags(FreeGiftPromotion $promotion, array $currentProductIds, array $previousProductIds = []): void
    {
        $currentProductIds = array_map('intval', $currentProductIds);
        $previousProductIds = array_map('intval', $previousProductIds);

        $addedIds = array_diff($currentProductIds, $previousProductIds);
        $removedIds = array_diff($previousProductIds, $currentProductIds);

        // For added products: is_free_gift = true, is_active = false
        if (! empty($addedIds)) {
            Product::whereIn('id', $addedIds)->update([
                'is_free_gift' => true,
                'is_active' => false,
            ]);
        }

        // For removed products: if not attached to any other free gift promotion, is_free_gift = false
        if (! empty($removedIds)) {
            foreach ($removedIds as $productId) {
                $stillAttached = \Illuminate\Support\Facades\DB::table('free_gift_promotion_products')
                    ->where('product_id', $productId)
                    ->where('free_gift_promotion_id', '!=', $promotion->id)
                    ->exists();

                if (! $stillAttached) {
                    Product::where('id', $productId)->update([
                        'is_free_gift' => false,
                    ]);
                }
            }
        }
    }

    /**
     * Calculate free gift eligibility, remaining amount, options, and cart items.
     */
    public function calculateFreeGift(array $cartItems, ?int $selectedGiftId = null): array
    {
        // Separate existing free gift items from base cart items (paid items + BuyXGetX free items)
        $baseCartItems = [];
        $existingSelectedGiftId = $selectedGiftId;

        foreach ($cartItems as $item) {
            $rawId = $item['product_id'] ?? $item['id'] ?? null;
            $isFreeGiftItem = ! empty($item['is_free_gift']) || (is_string($rawId) && str_starts_with($rawId, 'free-gift-'));

            if ($isFreeGiftItem) {
                if (! $existingSelectedGiftId) {
                    $cleanId = is_string($rawId) ? (int) str_replace('free-gift-', '', $rawId) : (int) $rawId;
                    if ($cleanId) {
                        $existingSelectedGiftId = $cleanId;
                    }
                }
            } else {
                $baseCartItems[] = $item;
            }
        }

        // Calculate paid items subtotal (excluding both BuyXGetX free items and free gifts)
        $subtotal = 0;
        $paidProductIds = [];
        foreach ($baseCartItems as $item) {
            $isFree = ! empty($item['is_free']) || ! empty($item['is_free_gift']);
            if (! $isFree) {
                $price = (float) ($item['price'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 1);
                $subtotal += ($price * $qty);

                $prodId = $item['product_id'] ?? $item['id'] ?? null;
                if ($prodId && is_numeric($prodId)) {
                    $paidProductIds[] = (int) $prodId;
                }
            }
        }

        // Load active promotion
        $activePromotion = FreeGiftPromotion::active()
            ->with(['freeProducts.variants', 'freeProducts.brand', 'freeProducts.category', 'excludedCategories'])
            ->first();

        if (! $activePromotion) {
            return [
                'eligible' => false,
                'minimum_cart_amount' => 0.00,
                'cart_subtotal' => round($subtotal, 2),
                'remaining_amount' => 0.00,
                'has_excluded_category' => false,
                'excluded_category_message' => null,
                'gift_options' => [],
                'selected_gift' => null,
                'promotion_id' => null,
                'promotion_name' => null,
                'cart_items' => $baseCartItems,
            ];
        }

        // Collect excluded category IDs (including subcategories)
        $excludedCategoryIds = $activePromotion->excludedCategories->pluck('id')->all();
        if (! empty($excludedCategoryIds)) {
            $childCategoryIds = Category::whereIn('parent_id', $excludedCategoryIds)->pluck('id')->all();
            $excludedCategoryIds = array_unique(array_merge($excludedCategoryIds, $childCategoryIds));
        }

        // Check if any cart item belongs to an excluded category
        $hasExcludedCategory = false;
        if (! empty($paidProductIds) && ! empty($excludedCategoryIds)) {
            $cartProducts = Product::with('category')->whereIn('id', $paidProductIds)->get();
            foreach ($cartProducts as $product) {
                $catId = $product->category_id;
                $parentCatId = $product->category?->parent_id;
                if (($catId && in_array($catId, $excludedCategoryIds)) || ($parentCatId && in_array($parentCatId, $excludedCategoryIds))) {
                    $hasExcludedCategory = true;
                    break;
                }
            }
        }

        $minAmount = (float) $activePromotion->minimum_cart_amount;
        $remainingAmount = max(0.00, round($minAmount - $subtotal, 2));
        $isEligible = ($subtotal >= $minAmount) && ! $hasExcludedCategory;

        // Build valid gift options
        $validGiftOptions = [];
        foreach ($activePromotion->freeProducts as $giftProd) {
            // Must have is_free_gift = true, is_active = false, and stock > 0
            $totalStock = $giftProd->variants->sum('stock');
            if (! $giftProd->is_free_gift || $giftProd->is_active || $totalStock <= 0) {
                continue;
            }

            $cheapestVariant = $giftProd->variants
                ->filter(fn ($v) => (int) $v->stock > 0)
                ->sortBy(fn ($v) => [(float) $v->selling_price, (string) ($v->sku ?? '')])
                ->first();

            $validGiftOptions[] = [
                'id' => $giftProd->id,
                'name' => $giftProd->name,
                'slug' => $giftProd->slug,
                'sku' => $giftProd->sku,
                'brand_name' => $giftProd->brand?->name,
                'brand' => $giftProd->brand ? ['id' => $giftProd->brand->id, 'name' => $giftProd->brand->name] : null,
                'category_name' => $giftProd->category?->name,
                'featured_image' => $giftProd->featured_image ? asset('storage/' . $giftProd->featured_image) : null,
                'stock' => $totalStock,
                'variant_id' => $cheapestVariant?->id,
            ];
        }

        // Determine selected gift
        $selectedGiftOption = null;
        if ($isEligible && ! empty($validGiftOptions)) {
            if (count($validGiftOptions) === 1) {
                // Single gift -> auto select
                $selectedGiftOption = $validGiftOptions[0];
            } elseif ($existingSelectedGiftId) {
                foreach ($validGiftOptions as $opt) {
                    if ($opt['id'] === $existingSelectedGiftId) {
                        $selectedGiftOption = $opt;
                        break;
                    }
                }
            }
        }

        // Construct final cart items list
        $finalCartItems = $baseCartItems;
        if ($selectedGiftOption) {
            $finalCartItems[] = [
                'id' => 'free-gift-' . $selectedGiftOption['id'],
                'product_id' => $selectedGiftOption['id'],
                'variant_id' => $selectedGiftOption['variant_id'],
                'name' => $selectedGiftOption['name'],
                'brand' => $selectedGiftOption['brand_name'] ?? '',
                'category' => $selectedGiftOption['category_name'] ?? '',
                'image' => $selectedGiftOption['featured_image'] ?? '',
                'price' => 0.00,
                'quantity' => 1,
                'is_free' => true,
                'is_free_gift' => true,
                'free_gift_promotion_id' => $activePromotion->id,
            ];
        }

        return [
            'eligible' => $isEligible,
            'minimum_cart_amount' => round($minAmount, 2),
            'cart_subtotal' => round($subtotal, 2),
            'remaining_amount' => round($remainingAmount, 2),
            'has_excluded_category' => $hasExcludedCategory,
            'excluded_category_message' => $hasExcludedCategory
                ? 'This order is not eligible for a free product because your cart contains an excluded product category.'
                : null,
            'gift_options' => $validGiftOptions,
            'selected_gift' => $selectedGiftOption,
            'promotion_id' => $activePromotion->id,
            'promotion_name' => $activePromotion->name,
            'cart_items' => $finalCartItems,
        ];
    }
}
