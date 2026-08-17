<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontPriceSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_product_uses_cheapest_available_variant_for_default_price(): void
    {
        $category = Category::create([
            'name' => 'Groceries',
            'slug' => 'groceries',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Brand A',
            'slug' => 'brand-a',
        ]);

        $product = Product::create([
            'sku' => 'SKU-001',
            'name' => 'Sample Product',
            'slug' => 'sample-product',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $highestVariant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'VAR-HIGH',
            'stock' => 5,
            'selling_price' => 25,
        ]);

        $lowestVariant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'VAR-LOW',
            'stock' => 4,
            'selling_price' => 12,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'VAR-OUT',
            'stock' => 0,
            'selling_price' => 18,
        ]);

        $response = $this->getJson('/api/storefront/products/' . $product->slug);

        $response->assertOk();
        $this->assertEquals(12, $response->json('price'));
        $response->assertJsonPath('variants.0.id', $lowestVariant->id);
        $this->assertEquals(12, $response->json('variants.0.price'));
        $this->assertNotSame($highestVariant->id, $response->json('variants.0.id'));
    }
}
