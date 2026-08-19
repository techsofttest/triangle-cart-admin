<?php

namespace Tests\Feature;

use App\Models\DeliveryPostcode;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryFreeShippingTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $brand = Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand']);
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);

        $this->product = Product::create([
            'sku' => 'FREE-DEL-01',
            'name' => 'Free Delivery Product',
            'slug' => 'free-delivery-product',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'is_active' => true,
            'allows_courier' => true,
            'requires_direct_delivery' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'sku' => 'FREE-DEL-01-V1',
            'unit' => 'pcs',
            'size' => '1',
            'buying_price' => 5.00,
            'selling_price' => 10.00,
            'stock' => 100,
        ]);
    }

    public function test_managed_postcode_below_threshold(): void
    {
        DeliveryPostcode::create([
            'postcode' => '2000',
            'suburb' => 'Sydney',
            'state' => 'NSW',
            'delivery_fee' => 5.00,
            'free_shipping_threshold' => 50.00,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/delivery/check', [
            'postcode' => '2000',
            'cart' => [
                ['product_id' => $this->product->id, 'quantity' => 3, 'price' => 10.00] // subtotal = $30
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'delivery_type' => 'postcode',
                'delivery_charge' => 5.00,
                'free_shipping_threshold' => 50.00,
                'amount_until_free_delivery' => 20.00,
                'is_free_delivery' => false,
            ]);
    }

    public function test_managed_postcode_exactly_at_threshold(): void
    {
        DeliveryPostcode::create([
            'postcode' => '2000',
            'suburb' => 'Sydney',
            'state' => 'NSW',
            'delivery_fee' => 5.00,
            'free_shipping_threshold' => 50.00,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/delivery/check', [
            'postcode' => '2000',
            'cart' => [
                ['product_id' => $this->product->id, 'quantity' => 5, 'price' => 10.00] // subtotal = $50
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'delivery_type' => 'postcode',
                'delivery_charge' => 0.00,
                'free_shipping_threshold' => 50.00,
                'amount_until_free_delivery' => 0.00,
                'is_free_delivery' => true,
            ]);
    }

    public function test_managed_postcode_above_threshold(): void
    {
        DeliveryPostcode::create([
            'postcode' => '2000',
            'suburb' => 'Sydney',
            'state' => 'NSW',
            'delivery_fee' => 5.00,
            'free_shipping_threshold' => 50.00,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/delivery/check', [
            'postcode' => '2000',
            'cart' => [
                ['product_id' => $this->product->id, 'quantity' => 6, 'price' => 10.00] // subtotal = $60
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'delivery_type' => 'postcode',
                'delivery_charge' => 0.00,
                'free_shipping_threshold' => 50.00,
                'amount_until_free_delivery' => 0.00,
                'is_free_delivery' => true,
            ]);
    }

    public function test_managed_postcode_with_no_free_shipping_threshold(): void
    {
        DeliveryPostcode::create([
            'postcode' => '2000',
            'suburb' => 'Sydney',
            'state' => 'NSW',
            'delivery_fee' => 5.00,
            'free_shipping_threshold' => null,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/delivery/check', [
            'postcode' => '2000',
            'cart' => [
                ['product_id' => $this->product->id, 'quantity' => 10, 'price' => 10.00] // subtotal = $100
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'delivery_type' => 'postcode',
                'delivery_charge' => 5.00,
                'free_shipping_threshold' => null,
                'amount_until_free_delivery' => null,
                'is_free_delivery' => false,
            ]);
    }

    public function test_courier_below_threshold(): void
    {
        // Unmanaged postcode 3000 -> courier
        $courierThreshold = (float) config('delivery.courier.free_threshold', 150.00);

        $response = $this->postJson('/api/delivery/check', [
            'postcode' => '3000',
            'cart' => [
                ['product_id' => $this->product->id, 'quantity' => 5, 'price' => 10.00] // subtotal = $50
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'delivery_type' => 'courier',
                'delivery_charge' => (float) config('delivery.courier.fee', 17.00),
                'free_shipping_threshold' => $courierThreshold,
                'amount_until_free_delivery' => $courierThreshold - 50.00,
                'is_free_delivery' => false,
            ]);
    }

    public function test_courier_at_threshold(): void
    {
        $courierThreshold = (float) config('delivery.courier.free_threshold', 150.00);

        $response = $this->postJson('/api/delivery/check', [
            'postcode' => '3000',
            'cart' => [
                ['product_id' => $this->product->id, 'quantity' => 15, 'price' => 10.00] // subtotal = $150
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'delivery_type' => 'courier',
                'delivery_charge' => 0.00,
                'free_shipping_threshold' => $courierThreshold,
                'amount_until_free_delivery' => 0.00,
                'is_free_delivery' => true,
            ]);
    }

    public function test_courier_above_threshold(): void
    {
        $courierThreshold = (float) config('delivery.courier.free_threshold', 150.00);

        $response = $this->postJson('/api/delivery/check', [
            'postcode' => '3000',
            'cart' => [
                ['product_id' => $this->product->id, 'quantity' => 20, 'price' => 10.00] // subtotal = $200
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'delivery_type' => 'courier',
                'delivery_charge' => 0.00,
                'free_shipping_threshold' => $courierThreshold,
                'amount_until_free_delivery' => 0.00,
                'is_free_delivery' => true,
            ]);
    }

    public function test_switching_postcode_and_courier_addresses(): void
    {
        DeliveryPostcode::create([
            'postcode' => '2000',
            'suburb' => 'Sydney',
            'state' => 'NSW',
            'delivery_fee' => 5.00,
            'free_shipping_threshold' => 50.00,
            'is_active' => true,
        ]);

        // Postcode check
        $resPostcode = $this->postJson('/api/delivery/check', [
            'postcode' => '2000',
            'cart' => [['product_id' => $this->product->id, 'quantity' => 3, 'price' => 10.00]]
        ]);
        $resPostcode->assertJson(['delivery_type' => 'postcode', 'delivery_charge' => 5.00]);

        // Switch to Courier postcode
        $resCourier = $this->postJson('/api/delivery/check', [
            'postcode' => '4000',
            'cart' => [['product_id' => $this->product->id, 'quantity' => 3, 'price' => 10.00]]
        ]);
        $resCourier->assertJson(['delivery_type' => 'courier', 'delivery_charge' => (float) config('delivery.courier.fee', 17.00)]);
    }

    public function test_order_creation_enforces_backend_calculated_shipping_charge(): void
    {
        DeliveryPostcode::create([
            'postcode' => '2000',
            'suburb' => 'Sydney',
            'state' => 'NSW',
            'delivery_fee' => 8.00,
            'free_shipping_threshold' => 100.00,
            'is_active' => true,
        ]);

        // Create date & slot for direct delivery
        $date = \App\Models\DeliveryDate::create(['date' => now()->addDays(2)->toDateString()]);
        $slot = \App\Models\TimeSlot::create([
            'delivery_date_id' => $date->id,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'capacity' => 10,
        ]);

        $response = $this->postJson('/api/checkout', [
            'cart' => [
                ['product_id' => $this->product->id, 'variant_id' => $this->variant->id, 'quantity' => 2, 'price' => 10.00] // subtotal = $20
            ],
            'address' => [
                'name' => 'John Doe',
                'phone' => '0400000000',
                'email' => 'john@example.com',
                'address_line_1' => '123 Main St',
                'postcode' => '2000',
                'suburb' => 'Sydney',
                'city' => 'Sydney',
                'state' => 'NSW',
            ],
            'delivery_type' => 'postcode',
            'delivery_date' => $date->date,
            'delivery_slot_id' => $slot->id,
            'payment_method' => 'card',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'subtotal' => 20.00,
            'shipping_cost' => 8.00,
            'grand_total' => 28.00,
        ]);
    }
}
