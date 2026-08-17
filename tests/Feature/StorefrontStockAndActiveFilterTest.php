<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontStockAndActiveFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_featured_category_products_exclude_zero_stock_and_inactive_products(): void
    {
        $category = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'is_active' => true,
            'home_featured' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Brand A',
            'slug' => 'brand-a',
        ]);

        // Active product with stock > 0
        $activeProduct = Product::create([
            'sku' => 'ACT-01',
            'name' => 'Active In-Stock Product',
            'slug' => 'active-in-stock-product',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);
        ProductVariant::create([
            'product_id' => $activeProduct->id,
            'sku' => 'VAR-ACT',
            'stock' => 10,
            'selling_price' => 50,
        ]);

        // Product with 0 stock
        $outOfStockProduct = Product::create([
            'sku' => 'OOS-01',
            'name' => 'Out of Stock Product',
            'slug' => 'out-of-stock-product',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);
        ProductVariant::create([
            'product_id' => $outOfStockProduct->id,
            'sku' => 'VAR-OOS',
            'stock' => 0,
            'selling_price' => 50,
        ]);

        // Inactive product
        $inactiveProduct = Product::create([
            'sku' => 'INACT-01',
            'name' => 'Inactive Product',
            'slug' => 'inactive-product',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'is_active' => false,
        ]);
        ProductVariant::create([
            'product_id' => $inactiveProduct->id,
            'sku' => 'VAR-INACT',
            'stock' => 10,
            'selling_price' => 50,
        ]);

        $response = $this->getJson('/api/storefront/home');
        $response->assertOk();

        $featuredCategories = $response->json('featured_categories');
        $this->assertNotEmpty($featuredCategories);

        $fashionCategory = collect($featuredCategories)->firstWhere('id', $category->id);
        $this->assertNotNull($fashionCategory);

        $productIds = collect($fashionCategory['products'])->pluck('id')->all();
        $this->assertContains($activeProduct->id, $productIds);
        $this->assertNotContains($outOfStockProduct->id, $productIds);
        $this->assertNotContains($inactiveProduct->id, $productIds);
    }

    public function test_inactive_or_out_of_stock_product_returns_404_on_detail_endpoint(): void
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat', 'is_active' => true]);
        $brand = Brand::create(['name' => 'Brand', 'slug' => 'brand']);

        $outOfStockProduct = Product::create([
            'sku' => 'OOS-02',
            'name' => 'OOS Item',
            'slug' => 'oos-item',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);
        ProductVariant::create([
            'product_id' => $outOfStockProduct->id,
            'sku' => 'VAR-OOS2',
            'stock' => 0,
            'selling_price' => 20,
        ]);

        $inactiveProduct = Product::create([
            'sku' => 'INACT-02',
            'name' => 'Inactive Item',
            'slug' => 'inactive-item',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'is_active' => false,
        ]);
        ProductVariant::create([
            'product_id' => $inactiveProduct->id,
            'sku' => 'VAR-INACT2',
            'stock' => 10,
            'selling_price' => 20,
        ]);

        $this->getJson('/api/storefront/products/' . $outOfStockProduct->slug)->assertStatus(404);
        $this->getJson('/api/storefront/products/' . $inactiveProduct->slug)->assertStatus(404);
    }
}
