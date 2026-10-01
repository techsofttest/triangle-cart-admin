<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Services\BuyXGetXPromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyXGetXPromotionTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::create(['name' => 'Beverages', 'slug' => 'beverages', 'is_active' => true]);
        $this->brand    = Brand::create(['name' => 'Brand A', 'slug' => 'brand-a']);
    }

    protected function createTestProduct(string $name, float $price = 10.0, int $stock = 50, ?Category $cat = null): Product
    {
        $product = Product::create([
            'sku'         => 'SKU-' . rand(1000, 9999),
            'name'        => $name,
            'slug'        => \Illuminate\Support\Str::slug($name) . '-' . rand(100, 999),
            'brand_id'    => $this->brand->id,
            'category_id' => ($cat ?? $this->category)->id,
            'is_active'   => true,
        ]);

        ProductVariant::create([
            'product_id'    => $product->id,
            'sku'           => 'VAR-' . rand(1000, 9999),
            'stock'         => $stock,
            'selling_price' => $price,
        ]);

        return $product;
    }

    protected function createPromotion(array $attributes = []): Promotion
    {
        return Promotion::create(array_merge([
            'name'              => 'Buy 2 Get 1 Free',
            'type'              => 'buy_x_get_x',
            'eligible_quantity' => 2,
            'free_quantity'     => 1,
            'is_active'         => true,
        ], $attributes));
    }

    // 1. Offer is inactive
    public function test_inactive_offer_is_not_applied(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Juice', 4.0);

        $promo = $this->createPromotion(['is_active' => false]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 2, 'price' => 5.0]
        ]);

        $this->assertFalse($res['qualified']);
        $this->assertNull($res['free_item']);
    }

    // 2. Offer is outside date range
    public function test_offer_outside_date_range_is_not_applied(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Juice', 4.0);

        $promo = $this->createPromotion([
            'start_at' => now()->addDays(2),
            'end_at'   => now()->addDays(5),
        ]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 2, 'price' => 5.0]
        ]);

        $this->assertFalse($res['qualified']);
        $this->assertNull($res['free_item']);
    }

    // 3. Cart below qualifying quantity
    public function test_cart_below_qualifying_quantity_does_not_qualify(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Juice', 4.0);

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 1, 'price' => 5.0]
        ]);

        $this->assertFalse($res['qualified']);
        $this->assertEquals(1, $res['promotion']['remaining_quantity']);
    }

    // 4. Cart exactly reaches qualifying quantity
    public function test_cart_exactly_reaching_qualifying_quantity_qualifies(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Juice', 4.0);

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 2, 'price' => 5.0]
        ]);

        $this->assertTrue($res['qualified']);
        $this->assertNotNull($res['free_item']);
        $this->assertEquals($free->id, $res['free_item']['product_id']);
    }

    // 5. Cart contains more than qualifying quantity
    public function test_cart_exceeding_qualifying_quantity_qualifies(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Juice', 4.0);

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 5, 'price' => 5.0]
        ]);

        $this->assertTrue($res['qualified']);
        $this->assertNotNull($res['free_item']);
    }

    // 6. Free product is automatically selected
    public function test_free_product_is_automatically_selected(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Free Snack', 3.0);

        $promo = $this->createPromotion();
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 2, 'price' => 5.0]
        ]);

        $this->assertEquals('Free Snack', $res['free_item']['name']);
    }

    // 7. Free product quantity is exactly one
    public function test_free_product_quantity_is_exactly_one(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Free Snack', 3.0);

        $promo = $this->createPromotion();
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 10, 'price' => 5.0]
        ]);

        $this->assertEquals(1, $res['free_item']['quantity']);
    }

    // 8. Free item price is zero
    public function test_free_item_price_is_zero(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Free Snack', 3.0);

        $promo = $this->createPromotion();
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 2, 'price' => 5.0]
        ]);

        $this->assertEquals(0, $res['free_item']['price']);
    }

    // 9. Free item is not counted toward eligibility
    public function test_free_item_is_not_counted_towards_eligibility(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Soda', 5.0); // same product as free item

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        // Pass 1 paid + 1 free item
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 1, 'price' => 5.0],
            ['product_id' => $free->id, 'quantity' => 1, 'price' => 0, 'is_free' => true],
        ]);

        // Paid quantity is only 1, so it must not qualify
        $this->assertFalse($res['qualified']);
    }

    // 10. Free item cannot be duplicated during recalculation
    public function test_free_item_is_not_duplicated_during_recalculation(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Snack', 3.0);

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        // Cart already has free item from previous calculation
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 2, 'price' => 5.0],
            ['product_id' => $free->id, 'quantity' => 1, 'price' => 0, 'is_free' => true],
        ]);

        // Total free items in result must be exactly 1
        $freeItemsInResult = array_filter($res['cart_items'], fn ($i) => !empty($i['is_free']));
        $this->assertCount(1, $freeItemsInResult);
    }

    // 11. Qualifying product is removed and free item disappears
    public function test_qualifying_product_removed_causes_free_item_removal(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Snack', 3.0);

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        // Customer reduced quantity of qualifying product from 2 to 1
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 1, 'price' => 5.0],
            ['product_id' => $free->id, 'quantity' => 1, 'price' => 0, 'is_free' => true],
        ]);

        $this->assertFalse($res['qualified']);
        $this->assertNull($res['free_item']);
        $freeItemsInResult = array_filter($res['cart_items'], fn ($i) => !empty($i['is_free']));
        $this->assertCount(0, $freeItemsInResult);
    }

    // 12. Free product is out of stock
    public function test_free_product_out_of_stock_is_not_selected(): void
    {
        $prod = $this->createTestProduct('Soda', 5.0);
        $free = $this->createTestProduct('Snack', 3.0, 0); // 0 stock!

        $promo = $this->createPromotion();
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 2, 'price' => 5.0]
        ]);

        $this->assertFalse($res['qualified']);
        $this->assertNull($res['free_item']);
    }

    // 13. Multiple free products all active and in stock are all added
    public function test_multiple_active_in_stock_free_products_are_all_added(): void
    {
        $prod  = $this->createTestProduct('Soda', 5.0);
        $free1 = $this->createTestProduct('Chips', 3.0, 10); // active, stock 10
        $free2 = $this->createTestProduct('Nuts', 4.0, 5);   // active, stock 5

        $promo = $this->createPromotion(['eligible_quantity' => 1]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach([$free1->id, $free2->id]);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 1, 'price' => 5.0]
        ]);

        $this->assertTrue($res['qualified']);
        $freeItemsInResult = array_values(array_filter($res['cart_items'], fn ($i) => !empty($i['is_free'])));
        $this->assertCount(2, $freeItemsInResult);
        $this->assertEquals($free1->id, $freeItemsInResult[0]['product_id']);
        $this->assertEquals($free2->id, $freeItemsInResult[1]['product_id']);
    }

    // 14. Inactive or out of stock free products are excluded while available ones are included
    public function test_inactive_and_out_of_stock_free_products_are_excluded(): void
    {
        $prod  = $this->createTestProduct('Soda', 5.0);
        $freeA = $this->createTestProduct('Product A', 3.0, 10); // active, stock 10
        $freeB = $this->createTestProduct('Product B', 4.0, 5);  // active, stock 5
        $freeC = $this->createTestProduct('Product C', 2.0, 10); // inactive
        $freeC->update(['is_active' => false]);
        $freeD = $this->createTestProduct('Product D', 6.0, 0);  // active, stock 0

        $promo = $this->createPromotion(['eligible_quantity' => 1]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach([$freeA->id, $freeB->id, $freeC->id, $freeD->id]);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 1, 'price' => 5.0]
        ]);

        $this->assertTrue($res['qualified']);
        $freeItemsInResult = array_values(array_filter($res['cart_items'], fn ($i) => !empty($i['is_free'])));
        $this->assertCount(2, $freeItemsInResult);
        $productIds = array_column($freeItemsInResult, 'product_id');
        $this->assertContains($freeA->id, $productIds);
        $this->assertContains($freeB->id, $productIds);
        $this->assertNotContains($freeC->id, $productIds);
        $this->assertNotContains($freeD->id, $productIds);
    }

    // 15. Frontend attempts to submit an arbitrary free product
    public function test_frontend_submitting_arbitrary_free_product_is_overridden(): void
    {
        $prod  = $this->createTestProduct('Soda', 5.0);
        $free  = $this->createTestProduct('Legit Free Product', 3.0);
        $fake  = $this->createTestProduct('Expensive Laptop', 1000.0);

        $promo = $this->createPromotion();
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        // Frontend attempts to inject a free expensive laptop
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 2, 'price' => 5.0],
            ['product_id' => $fake->id, 'quantity' => 1, 'price' => 0, 'is_free' => true],
        ]);

        $this->assertTrue($res['qualified']);
        // Overridden to legitimate free product!
        $this->assertEquals($free->id, $res['free_item']['product_id']);
        $this->assertEquals('Legit Free Product', $res['free_item']['name']);
    }

    // 16. Checkout recalculates promotion server-side
    // 17. Order stores promotional item correctly
    // 18. Payment total excludes the free item's value
    public function test_checkout_recalculates_promotion_and_stores_free_order_item(): void
    {
        $prod = $this->createTestProduct('Soda', 10.0);
        $free = $this->createTestProduct('Free Cookie', 5.0);

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $payload = [
            'cart' => [
                ['product_id' => $prod->id, 'quantity' => 2, 'price' => 10.0]
            ],
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '1234567890',
            'address' => [
                'contact_name' => 'John Doe',
                'phone' => '1234567890',
                'address_line_1' => '123 Main St',
                'city' => 'Sydney',
                'state' => 'NSW',
                'postcode' => '2000',
                'country' => 'Australia',
            ],
            'payment_method' => 'card',
        ];

        $response = $this->postJson('/api/checkout', $payload);
        $response->assertOk();
        $response->assertJsonPath('valid', true);

        $orderId = $response->json('order_id');
        $order = Order::with('items')->find($orderId);

        // Subtotal = 2 * 10 = 20 (free item excluded)
        $this->assertEquals(20.0, (float)$order->subtotal);

        // Order contains 2 items: 1 paid, 1 free
        $this->assertCount(2, $order->items);

        $freeOrderItem = $order->items->firstWhere('is_free', true);
        $this->assertNotNull($freeOrderItem);
        $this->assertEquals($free->id, $freeOrderItem->product_id);
        $this->assertEquals(0, (float)$freeOrderItem->price);
        $this->assertEquals(0, (float)$freeOrderItem->line_total);
        $this->assertEquals($promo->id, $freeOrderItem->promotion_id);
    }

    // 19. Guest cart works via API
    public function test_guest_cart_calculation_endpoint(): void
    {
        $prod = $this->createTestProduct('Soda', 10.0);
        $free = $this->createTestProduct('Free Cookie', 5.0);

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $response = $this->postJson('/api/cart/calculate', [
            'cart' => [
                ['product_id' => $prod->id, 'quantity' => 2, 'price' => 10.0]
            ]
        ]);

        $response->assertOk();
        $response->assertJsonPath('promotion.qualified', true);
        $response->assertJsonPath('free_item.product_id', $free->id);
    }

    // 20. Authenticated cart works via API
    public function test_authenticated_customer_cart_calculation_endpoint(): void
    {
        $customer = Customer::create([
            'name'              => 'Jane Doe',
            'email'             => 'jane@example.com',
            'phone'             => '0400000000',
            'password'          => bcrypt('password'),
            'email_verified_at' => now(),
            'status'            => 1,
        ]);

        $prod = $this->createTestProduct('Soda', 10.0);
        $free = $this->createTestProduct('Free Cookie', 5.0);

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleProducts()->attach($prod->id);
        $promo->freeProducts()->attach($free->id);

        $response = $this->actingAs($customer, 'customer')->postJson('/api/cart/calculate', [
            'cart' => [
                ['product_id' => $prod->id, 'quantity' => 2, 'price' => 10.0]
            ]
        ]);

        $response->assertOk();
        $response->assertJsonPath('promotion.qualified', true);
        $response->assertJsonPath('free_item.product_id', $free->id);
    }

    public function test_cart_with_eligible_brand_qualifies(): void
    {
        $brandB = Brand::create(['name' => 'Brand B', 'slug' => 'brand-b']);
        $prod = Product::create([
            'sku' => 'SKU-BRAND-B',
            'name' => 'Brand B Product',
            'slug' => 'brand-b-product',
            'brand_id' => $brandB->id,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);
        ProductVariant::create([
            'product_id' => $prod->id,
            'sku' => 'VAR-BRAND-B',
            'stock' => 10,
            'selling_price' => 15.0,
        ]);

        $free = $this->createTestProduct('Free Cookie', 5.0);

        $promo = $this->createPromotion(['eligible_quantity' => 2]);
        $promo->eligibleBrands()->attach($brandB->id);
        $promo->freeProducts()->attach($free->id);

        $service = app(BuyXGetXPromotionService::class);
        $res = $service->calculatePromotion([
            ['product_id' => $prod->id, 'quantity' => 2, 'price' => 15.0]
        ]);

        $this->assertTrue($res['qualified']);
        $this->assertNotNull($res['free_item']);
        $this->assertEquals($free->id, $res['free_item']['product_id']);
    }
}
