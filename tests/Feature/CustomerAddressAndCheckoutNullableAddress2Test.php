<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAddressAndCheckoutNullableAddress2Test extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_save_address_without_address_line_2(): void
    {
        $customer = Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0400000000',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'status' => 1,
        ]);

        $response = $this->actingAs($customer, 'customer')->postJson('/api/customer/addresses', [
            'contact_name' => 'John Doe',
            'phone' => '+61400000000',
            'email' => 'john@example.com',
            'address_line_1' => '100 George Street',
            'address_line_2' => null, // Optional!
            'city' => 'Sydney',
            'state' => 'NSW',
            'postcode' => '2000',
            'country' => 'Australia',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('customer_addresses', [
            'customer_id' => $customer->id,
            'address_line_1' => '100 George Street',
            'address_line_2' => null,
        ]);
    }

    public function test_checkout_allows_order_creation_without_address_line_2(): void
    {
        $category = \App\Models\Category::create(['name' => 'Groceries', 'slug' => 'groceries', 'is_active' => true]);
        $brand = \App\Models\Brand::create(['name' => 'Brand A', 'slug' => 'brand-a']);
        $product = Product::create([
            'sku' => 'PROD-100',
            'name' => 'Item 100',
            'slug' => 'item-100',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'VAR-100',
            'stock' => 10,
            'selling_price' => 20,
        ]);

        $payload = [
            'cart' => [
                [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'quantity' => 1,
                    'price' => 20,
                ],
            ],
            'customer_name' => 'Jane Smith',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+61411111111',
            'address' => [
                'contact_name' => 'Jane Smith',
                'phone' => '+61411111111',
                'address_line_1' => '50 Pitt Street',
                'address_line_2' => null, // Optional!
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

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Jane Smith',
            'shipping_address_line_1' => '50 Pitt Street',
            'shipping_address_line_2' => null,
        ]);
    }
}
