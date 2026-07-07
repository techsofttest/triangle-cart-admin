<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Advertisement;
use App\Models\Banner;
use App\Models\Category;
use App\Models\HomePageSection;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorefrontController extends Controller
{
    private function assetUrl(?string $path): ?string
    {
        return $path ? asset('storage/' . $path) : null;
    }

    private function variantPayload($variant): array
    {
        return [
            'id' => $variant->id,
            'sku' => $variant->sku,
            'unit' => $variant->unit,
            'size' => $variant->size,
            'price' => (float) $variant->selling_price,
            'stock' => (int) $variant->stock,
        ];
    }

    private function productPayload(Product $product): array
    {
        $variant = $product->variants->first();
        $price = $variant ? (float) $variant->selling_price : 0.0;
        $reviews = $product->reviews;
        $rating = round((float) ($reviews->avg('review_rating') ?: 0), 1);

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'sku' => $product->sku,
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
                'slug' => $product->brand->slug,
            ] : null,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'featured_image' => $this->assetUrl($product->featured_image),
            'gallery' => $product->images->map(fn ($image) => $this->assetUrl($image->image_path))->values(),
            'price' => $price,
            'min_price' => (float) ($product->min_price ?? $price),
            'max_price' => (float) ($product->max_price ?? $price),
            'is_featured' => (bool) $product->is_featured,
            'is_active' => (bool) $product->is_active,
            'rating' => $rating,
            'review_count' => $reviews->count(),
            'variants' => $product->variants->map(fn ($variant) => $this->variantPayload($variant))->values(),
            'description' => $product->description,
            'key_features' => $product->key_features,
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
        ];
    }

    private function categoryPayload(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'image_url' => $this->assetUrl($category->image),
            'icon_url' => $this->assetUrl($category->icon),
            'href' => '/category/' . $category->slug,
        ];
    }

    private function brandPayload(Brand $brand): array
    {
        $firstProduct = $brand->products->first();

        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'slug' => $brand->slug,
            'logo_url' => $this->assetUrl($brand->logo),
            'product_image_url' => $firstProduct ? $this->assetUrl($firstProduct->featured_image) : null,
            'link' => '/brand/' . $brand->slug,
        ];
    }

    private function topOfferPayloads(int $limit = 20)
    {
        return Product::query()
            ->with(['brand', 'category', 'variants', 'images', 'reviews'])
            ->where('is_active', true)
            ->whereHas('variants')
            ->get()
            ->filter(function (Product $product) {
                $minPrice = (float) ($product->min_price ?? 0);
                $maxPrice = (float) ($product->max_price ?? 0);

                return $minPrice > 0 && $maxPrice > $minPrice;
            })
            ->map(function (Product $product) {
                $minPrice = (float) ($product->min_price ?? 0);
                $maxPrice = (float) ($product->max_price ?? 0);
                $discountPercent = (int) round((1 - ($minPrice / $maxPrice)) * 100);

                return [
                    'id' => $product->id,
                    'title' => $product->name,
                    'label' => $discountPercent . '% OFF',
                    'discount_percent' => $discountPercent,
                    'image_url' => $this->assetUrl($product->featured_image),
                    'href' => '/product/' . $product->slug,
                ];
            })
            ->sortByDesc('discount_percent')
            ->take($limit)
            ->values();
    }

    private function homeSectionPayload(HomePageSection $section): array
    {
        $limit = max(1, min((int) $section->item_limit, 48));
        $items = collect();

        if ($section->source === HomePageSection::SOURCE_CUSTOM_PRODUCTS && filled($section->product_ids)) {
            $products = Product::query()
                ->with(['brand', 'category', 'variants', 'reviews', 'images'])
                ->whereIn('id', $section->product_ids)
                ->where('is_active', true)
                ->get()
                ->sortBy(fn (Product $product) => array_search($product->id, $section->product_ids, true));

            $items = $products->map(fn (Product $product) => $this->productPayload($product))->values();
        } elseif ($section->source === HomePageSection::SOURCE_FEATURED_PRODUCTS) {
            $items = Product::query()
                ->with(['brand', 'category', 'variants', 'reviews', 'images'])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->take($limit)
                ->get()
                ->map(fn (Product $product) => $this->productPayload($product))
                ->values();
        } elseif ($section->source === HomePageSection::SOURCE_LATEST_PRODUCTS) {
            $items = Product::query()
                ->with(['brand', 'category', 'variants', 'reviews', 'images'])
                ->where('is_active', true)
                ->latest()
                ->take($limit)
                ->get()
                ->map(fn (Product $product) => $this->productPayload($product))
                ->values();
        } elseif ($section->source === HomePageSection::SOURCE_CUSTOM_CATEGORIES && filled($section->category_ids)) {
            $categories = Category::query()
                ->whereIn('id', $section->category_ids)
                ->where('is_active', true)
                ->get()
                ->sortBy(fn (Category $category) => array_search($category->id, $section->category_ids, true));

            $items = $categories->map(fn (Category $category) => $this->categoryPayload($category))->values();
        } elseif ($section->source === HomePageSection::SOURCE_CATEGORIES) {
            $items = Category::query()
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->take($limit)
                ->get()
                ->map(fn (Category $category) => $this->categoryPayload($category))
                ->values();
        } elseif ($section->source === HomePageSection::SOURCE_CUSTOM_BRANDS && filled($section->brand_ids)) {
            $brands = Brand::query()
                ->with(['products' => fn ($q) => $q->where('is_active', true)->take(1)])
                ->whereIn('id', $section->brand_ids)
                ->get()
                ->sortBy(fn (Brand $brand) => array_search($brand->id, $section->brand_ids, true));

            $items = $brands->map(fn (Brand $brand) => $this->brandPayload($brand))->values();
        } elseif ($section->source === HomePageSection::SOURCE_BRANDS) {
            $items = Brand::query()
                ->with(['products' => fn ($q) => $q->where('is_active', true)->take(1)])
                ->take($limit)
                ->get()
                ->map(fn (Brand $brand) => $this->brandPayload($brand))
                ->values();
        } elseif ($section->source === HomePageSection::SOURCE_BANNERS) {
            $items = Banner::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->take($limit)
                ->get()
                ->map(fn (Banner $banner) => [
                    'id' => $banner->id,
                    'name' => $banner->name,
                    'image_url' => $this->assetUrl($banner->image),
                    'url' => $banner->url,
                ])->values();
        } elseif ($section->source === HomePageSection::SOURCE_ADVERTISEMENT) {
            $advertisement = Advertisement::query()->first();

            $items = $advertisement ? collect([[
                'id' => $advertisement->id,
                'name' => $advertisement->name,
                'title' => $advertisement->title,
                'banner_url' => $this->assetUrl($advertisement->banner),
                'url' => $advertisement->url,
            ]]) : collect();
        } elseif ($section->source === HomePageSection::SOURCE_TOP_OFFERS) {
            $items = $this->topOfferPayloads($limit);
        }

        return [
            'id' => $section->id,
            'name' => $section->name,
            'type' => $section->type,
            'source' => $section->source,
            'title' => $section->title,
            'subtitle' => $section->subtitle,
            'link_label' => $section->link_label,
            'link_url' => $section->link_url,
            'background_color' => $section->background_color,
            'settings' => $section->settings,
            'items' => $items,
        ];
    }

    public function categories(): JsonResponse
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->withCount('products')
            ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories->map(function (Category $category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image_url' => $this->assetUrl($category->image),
                'icon_url' => $this->assetUrl($category->icon),
                'product_count' => $category->products_count,
                'children' => $category->children->map(fn (Category $child) => [
                    'id' => $child->id,
                    'name' => $child->name,
                    'slug' => $child->slug,
                    'image_url' => $this->assetUrl($child->image),
                    'icon_url' => $this->assetUrl($child->icon),
                ])->values(),
            ];
        }));
    }

    public function header(): JsonResponse
    {
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return response()->json([
            'brand' => [
                'name' => config('app.name'),
                'logo' => asset('images/logo/brand-logo-nobg.png'),
            ],
            'links' => [
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'Products', 'href' => '/products'],
                ['label' => 'Categories', 'href' => '/categories'],
            ],
            'categories' => $categories->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'href' => '/category/' . $category->slug,
                'image_url' => $this->assetUrl($category->image),
                'icon_url' => $this->assetUrl($category->icon),
            ])->values(),
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 12);
        $perPage = max(1, min($perPage, 48));

        $query = Product::query()
            ->with(['brand', 'category', 'variants', 'reviews', 'images'])
            ->where('is_active', true);

        if ($categorySlug = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($brandSlug = $request->string('brand')->toString()) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $brandSlug));
        }

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%')
                    ->orWhereHas('brand', fn ($brandQuery) => $brandQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%' . $search . '%'));
            });
        }

        $sort = $request->string('sort', 'latest')->toString();
        $query->when($sort === 'price_low', function ($q) {
                return $q->orderBy(
                    \App\Models\ProductVariant::select('selling_price')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->orderBy('selling_price', 'asc')
                        ->limit(1),
                    'asc'
                );
            })
            ->when($sort === 'price_high', function ($q) {
                return $q->orderBy(
                    \App\Models\ProductVariant::select('selling_price')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->orderBy('selling_price', 'desc')
                        ->limit(1),
                    'desc'
                );
            })
            ->when($sort === 'name_asc', fn ($q) => $q->orderBy('name'))
            ->when($sort === 'name_desc', fn ($q) => $q->orderByDesc('name'))
            ->when($sort === 'featured', fn ($q) => $q->orderByDesc('is_featured'))
            ->when($sort === 'latest' || ! in_array($sort, ['price_low', 'price_high', 'name_asc', 'name_desc', 'featured'], true), fn ($q) => $q->latest());

        $products = $query->paginate($perPage);

        return response()->json([
            'data' => $products->getCollection()->map(fn (Product $product) => $this->productPayload($product))->values(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function product(Product $product): JsonResponse
    {
        $product->load(['brand', 'category', 'variants', 'reviews', 'images']);

        return response()->json($this->productPayload($product));
    }

    public function home(): JsonResponse
    {
        $products = Product::query()
            ->with(['brand', 'category', 'variants', 'reviews', 'images'])
            ->where('is_active', true)
            ->latest()
            ->take(18)
            ->get();

        $homeAdvertisement = Advertisement::query()
            ->find(1);

        $banners = Banner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Banner $banner) => [
                'id' => $banner->id,
                'name' => $banner->name,
                'image_url' => $this->assetUrl($banner->image),
                'url' => $banner->url,
            ])->values();

        $brands = Brand::query()
            ->with(['products' => fn ($q) => $q->where('is_active', true)->take(1)])
            ->get()
            ->map(function (Brand $brand, int $index) {
                $payload = $this->brandPayload($brand);

                return $payload + ['order' => $index + 1];
            })->values();

        $sections = HomePageSection::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (HomePageSection $section) => $this->homeSectionPayload($section))
            ->values();

        $featuredCategories = Category::query()
            ->where('home_featured', true)
            ->where('is_active', true)
            ->get()
            ->map(function (Category $category) {
                $categoryIds = Category::query()
                    ->where('id', $category->id)
                    ->orWhere('parent_id', $category->id)
                    ->pluck('id');

                $products = Product::query()
                    ->with(['brand', 'category', 'variants', 'reviews', 'images'])
                    ->whereIn('category_id', $categoryIds)
                    ->where('is_active', true)
                    ->latest()
                    ->take(4)
                    ->get();

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products' => $products->map(fn (Product $product) => $this->productPayload($product))->values(),
                ];
            })
            ->values();

        return response()->json([
            'home_advertisement' => $homeAdvertisement ? [
                'id' => $homeAdvertisement->id,
                'name' => $homeAdvertisement->name,
                'title' => $homeAdvertisement->title,
                'banner_url' => $this->assetUrl($homeAdvertisement->banner),
                'url' => $homeAdvertisement->url,
            ] : null,
            'banners' => $banners,
            'products' => $products->map(fn (Product $product) => $this->productPayload($product))->values(),
            'brands' => $brands,
            'sections' => $sections,
            'featured_categories' => $featuredCategories,
        ]);
    }

    public function topOffers(): JsonResponse
    {
        return response()->json($this->topOfferPayloads());
    }
}
