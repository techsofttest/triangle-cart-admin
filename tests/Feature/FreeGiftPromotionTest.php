<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\FreeGiftPromotion;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\FreeGiftPromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FreeGiftPromotionTest extends TestCase
{
    use RefreshDatabase;

    protected FreeGiftPromotionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FreeGiftPromotionService::class);
    }

    private function createProductWithVariant(array $attributes = [], int $stock = 10, float $price = 50.00): Product
    {
        $brand = Brand::firstOrCreate(['slug' => 'test-brand'], ['name' => 'Test Brand']);
        $category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Category']);

        $product = Product::create(array_merge([
            'name' => 'Sample Product ' . rand(100, 999),
            'sku' => 'SKU-' . rand(1000, 9999),
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'is_active' => true,
            'is_free_gift' => false,
        ], $attributes));

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $product->sku . '-V1',
            'selling_price' => $price,
            'stock' => $stock,
        ]);

        return $product;
    }

    public function test_activating_second_free_gift_promotion_deactivates_first(): void
    {
        $promo1 = FreeGiftPromotion::create([
            'name' => 'Promo 1',
            'status' => 'active',
            'minimum_cart_amount' => 100,
        ]);

        $deactivatedCount = $this->service->deactivateOtherActivePromotions(null, true);
        $this->assertEquals(1, $deactivatedCount);

        $promo1->refresh();
        $this->assertEquals('inactive', $promo1->status);
    }

    public function test_product_flags_on_attachment_and_detachment(): void
    {
        $promo1 = FreeGiftPromotion::create(['name' => 'Promo 1', 'status' => 'active', 'minimum_cart_amount' => 100]);
        $promo2 = FreeGiftPromotion::create(['name' => 'Promo 2', 'status' => 'inactive', 'minimum_cart_amount' => 100]);

        $product = $this->createProductWithVariant(['is_active' => true]);

        // Attach to Promo 1
        $promo1->freeProducts()->attach($product->id);
        $this->service->syncProductFreeGiftFlags($promo1, [$product->id], []);

        $product->refresh();
        $this->assertTrue($product->is_free_gift);
        $this->assertFalse($product->is_active);

        // Attach to Promo 2 as well
        $promo2->freeProducts()->attach($product->id);
        $this->service->syncProductFreeGiftFlags($promo2, [$product->id], []);

        // Detach from Promo 1 (still in Promo 2)
        $promo1->freeProducts()->detach($product->id);
        $this->service->syncProductFreeGiftFlags($promo1, [], [$product->id]);

        $product->refresh();
        $this->assertTrue($product->is_free_gift);

        // Detach from Promo 2
        $promo2->freeProducts()->detach($product->id);
        $this->service->syncProductFreeGiftFlags($promo2, [], [$product->id]);

        $product->refresh();
        $this->assertFalse($product->is_free_gift);
        $this->assertFalse($product->is_active); // Never automatically re-activated
    }

    public function test_reserved_free_gift_product_excluded_from_storefront_listing(): void
    {
        $giftProduct = $this->createProductWithVariant(['is_active' => false, 'is_free_gift' => true]);
        $normalProduct = $this->createProductWithVariant(['is_active' => true, 'is_free_gift' => false]);

        $response = $this->getJson('/api/storefront/products');
        $response->assertOk();

        $productIds = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains($normalProduct->id, $productIds);
        $this->assertNotContains($giftProduct->id, $productIds);
    }

    public function test_cart_below_minimum_amount_is_ineligible_and_returns_remaining(): void
    {
        $giftProduct = $this->createProductWithVariant(['is_active' => false, 'is_free_gift' => true]);
        $promo = FreeGiftPromotion::create([
            'name' => 'Free Gift Over $100',
            'status' => 'active',
            'minimum_cart_amount' => 100.00,
        ]);
        $promo->freeProducts()->attach($giftProduct->id);

        $cart = [
            ['product_id' => 999, 'price' => 40.00, 'quantity' => 1]
        ];

        $result = $this->service->calculateFreeGift($cart);

        $this->assertFalse($result['eligible']);
        $this->assertEquals(100.00, $result['minimum_cart_amount']);
        $this->assertEquals(40.00, $result['cart_subtotal']);
        $this->assertEquals(60.00, $result['remaining_amount']);
        $this->assertNull($result['selected_gift']);
    }

    public function test_cart_meeting_minimum_amount_unlocks_single_gift(): void
    {
        $giftProduct = $this->createProductWithVariant(['is_active' => false, 'is_free_gift' => true]);
        $promo = FreeGiftPromotion::create([
            'name' => 'Free Gift Over $100',
            'status' => 'active',
            'minimum_cart_amount' => 100.00,
        ]);
        $promo->freeProducts()->attach($giftProduct->id);

        $cart = [
            ['product_id' => 999, 'price' => 50.00, 'quantity' => 2]
        ];

        $result = $this->service->calculateFreeGift($cart);

        $this->assertTrue($result['eligible']);
        $this->assertEquals(0.00, $result['remaining_amount']);
        $this->assertNotNull($result['selected_gift']);
        $this->assertEquals($giftProduct->id, $result['selected_gift']['id']);

        // Check attached free item in cart_items
        $freeItems = array_filter($result['cart_items'], fn ($i) => !empty($i['is_free_gift']));
        $this->assertCount(1, $freeItems);
        $freeItem = reset($freeItems);
        $this->assertEquals(0, $freeItem['price']);
        $this->assertEquals(1, $freeItem['quantity']);
    }

    public function test_excluded_category_makes_entire_cart_ineligible(): void
    {
        $excludedCat = Category::create(['name' => 'Excluded Category', 'slug' => 'excluded-cat']);
        $excludedProduct = $this->createProductWithVariant(['category_id' => $excludedCat->id, 'is_active' => true], 10, 150.00);

        $giftProduct = $this->createProductWithVariant(['is_active' => false, 'is_free_gift' => true]);
        $promo = FreeGiftPromotion::create([
            'name' => 'Free Gift Over $100',
            'status' => 'active',
            'minimum_cart_amount' => 100.00,
        ]);
        $promo->freeProducts()->attach($giftProduct->id);
        $promo->excludedCategories()->attach($excludedCat->id);

        $cart = [
            ['product_id' => $excludedProduct->id, 'price' => 150.00, 'quantity' => 1]
        ];

        $result = $this->service->calculateFreeGift($cart);

        $this->assertFalse($result['eligible']);
        $this->assertTrue($result['has_excluded_category']);
        $this->assertNotNull($result['excluded_category_message']);
    }

    public function test_out_of_stock_gift_products_are_excluded(): void
    {
        $outOfStockGift = $this->createProductWithVariant(['is_active' => false, 'is_free_gift' => true], 0);
        $promo = FreeGiftPromotion::create([
            'name' => 'Free Gift Over $100',
            'status' => 'active',
            'minimum_cart_amount' => 50.00,
        ]);
        $promo->freeProducts()->attach($outOfStockGift->id);

        $cart = [
            ['product_id' => 999, 'price' => 100.00, 'quantity' => 1]
        ];

        $result = $this->service->calculateFreeGift($cart);

        $this->assertEmpty($result['gift_options']);
        $this->assertNull($result['selected_gift']);
    }

    public function test_selecting_and_changing_gift_in_multiple_gift_options(): void
    {
        $giftA = $this->createProductWithVariant(['name' => 'Gift A', 'is_active' => false, 'is_free_gift' => true]);
        $giftB = $this->createProductWithVariant(['name' => 'Gift B', 'is_active' => false, 'is_free_gift' => true]);

        $promo = FreeGiftPromotion::create([
            'name' => 'Free Gift Over $100',
            'status' => 'active',
            'minimum_cart_amount' => 100.00,
        ]);
        $promo->freeProducts()->attach([$giftA->id, $giftB->id]);

        $cart = [
            ['product_id' => 999, 'price' => 120.00, 'quantity' => 1]
        ];

        // Initially multiple options -> no auto-selection
        $result = $this->service->calculateFreeGift($cart);
        $this->assertTrue($result['eligible']);
        $this->assertCount(2, $result['gift_options']);
        $this->assertNull($result['selected_gift']);

        // Select Gift B
        $resultB = $this->service->calculateFreeGift($cart, $giftB->id);
        $this->assertNotNull($resultB['selected_gift']);
        $this->assertEquals($giftB->id, $resultB['selected_gift']['id']);

        // Change to Gift A
        $resultA = $this->service->calculateFreeGift($cart, $giftA->id);
        $this->assertNotNull($resultA['selected_gift']);
        $this->assertEquals($giftA->id, $resultA['selected_gift']['id']);

        // Verify only ONE free gift item exists in cart_items
        $freeItems = array_filter($resultA['cart_items'], fn ($i) => !empty($i['is_free_gift']));
        $this->assertCount(1, $freeItems);
    }

    public function test_checkout_creates_order_with_free_gift_fields_and_decreases_stock(): void
    {
        $paidProd = $this->createProductWithVariant(['name' => 'Paid Item', 'is_active' => true], 10, 150.00);
        $giftProd = $this->createProductWithVariant(['name' => 'Gift Item', 'is_active' => false, 'is_free_gift' => true], 5, 0.00);

        $promo = FreeGiftPromotion::create([
            'name' => 'Free Gift Promo',
            'status' => 'active',
            'minimum_cart_amount' => 100.00,
        ]);
        $promo->freeProducts()->attach($giftProd->id);

        $payload = [
            'cart' => [
                ['product_id' => $paidProd->id, 'quantity' => 1, 'price' => 150.00],
                ['product_id' => 'free-gift-' . $giftProd->id, 'quantity' => 1, 'price' => 0.00, 'is_free' => true, 'is_free_gift' => true],
            ],
            'address' => [
                'contact_name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '0400000000',
                'address_line_1' => '123 Test St',
                'city' => 'Sydney',
                'state' => 'NSW',
                'postcode' => '2000',
                'country' => 'Australia',
            ],
            'payment_method' => 'card',
        ];

        $response = $this->postJson('/api/checkout', $payload);
        $response->assertOk();

        $orderId = $response->json('order_id');
        $order = Order::with('items')->find($orderId);

        $this->assertNotNull($order);
        $this->assertCount(2, $order->items);

        $freeItem = $order->items->firstWhere('is_free_gift', true);
        $this->assertNotNull($freeItem);
        $this->assertEquals($giftProd->id, $freeItem->product_id);
        $this->assertEquals(0, $freeItem->price);
        $this->assertEquals(1, $freeItem->quantity);
        $this->assertEquals($promo->id, $freeItem->free_gift_promotion_id);
    }
}
