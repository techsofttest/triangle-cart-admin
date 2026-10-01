<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\DeliveryDate;
use App\Models\DeliverySession;
use App\Models\DeliverySessionOrder;
use App\Models\Order;
use App\Models\TimeSlot;
use App\Models\User;
use App\Services\DeliverySessionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliverySessionCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function createDummyOrder(array $attributes = []): Order
    {
        return Order::create(array_merge([
            'order_number'    => 'TC' . rand(1000, 9999),
            'customer_name'   => 'Test Customer',
            'first_name'      => 'Test',
            'last_name'       => 'Customer',
            'email'           => 'test@example.com',
            'phone'           => '1234567890',
            'country'         => 'Australia',
            'address'         => '123 Test St',
            'city'            => 'Melbourne',
            'state'           => 'VIC',
            'pin_code'        => '3000',
            'shipping_method' => 'standard',
            'payment_method'  => 'stripe',
            'subtotal'        => 100,
            'grand_total'     => 100,
            'payment_status'  => PaymentStatus::PAID,
            'status'          => OrderStatus::CONFIRMED,
        ], $attributes));
    }

    public function test_categorizes_eligible_orders_correctly(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $sessionDate = '2026-09-08';

        $deliveryDate = DeliveryDate::create(['date' => $sessionDate]);
        $slot = TimeSlot::create([
            'delivery_date_id' => $deliveryDate->id,
            'start_time'       => '10:00:00',
            'end_time'         => '14:00:00',
        ]);

        $missedOrder = $this->createDummyOrder([
            'order_number'   => 'TC1001',
            'delivery_date'  => '2026-09-05',
            'delivery_slot_id' => $slot->id,
        ]);

        $todayOrder = $this->createDummyOrder([
            'order_number'   => 'TC1010',
            'delivery_date'  => $sessionDate,
            'delivery_slot_id' => $slot->id,
        ]);

        $futureOrder = $this->createDummyOrder([
            'order_number'   => 'TC1015',
            'delivery_date'  => '2026-09-10',
            'delivery_slot_id' => $slot->id,
        ]);

        $service = app(DeliverySessionService::class);
        $eligible = $service->getEligibleOrders($sessionDate, $slot->id, $staff->id);

        $this->assertTrue($eligible['missed']->contains('id', $missedOrder->id));
        $this->assertTrue($eligible['today']->contains('id', $todayOrder->id));
        $this->assertTrue($eligible['future']->contains('id', $futureOrder->id));
    }

    public function test_creates_session_and_preserves_original_delivery_date(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $sessionDate = '2026-09-08';
        $originalMissedDate = '2026-09-05';

        $deliveryDate = DeliveryDate::create(['date' => $sessionDate]);
        $slot = TimeSlot::create([
            'delivery_date_id' => $deliveryDate->id,
            'start_time'       => '10:00:00',
            'end_time'         => '14:00:00',
        ]);

        $missedOrder = $this->createDummyOrder([
            'order_number'   => 'TC1001',
            'delivery_date'  => $originalMissedDate,
            'delivery_slot_id' => $slot->id,
        ]);

        $todayOrder = $this->createDummyOrder([
            'order_number'   => 'TC1010',
            'delivery_date'  => $sessionDate,
            'delivery_slot_id' => $slot->id,
        ]);

        $service = app(DeliverySessionService::class);
        $session = $service->createSessionWithOrders([
            'delivery_date'    => $sessionDate,
            'delivery_slot_id' => $slot->id,
            'staff_id'         => $staff->id,
            'status'           => 'in_progress',
        ], [$missedOrder->id, $todayOrder->id]);

        $this->assertDatabaseHas('delivery_sessions', [
            'id'       => $session->id,
            'staff_id' => $staff->id,
        ]);
        $this->assertEquals($sessionDate, Carbon::parse($session->fresh()->delivery_date)->toDateString());

        $this->assertDatabaseHas('delivery_session_orders', [
            'delivery_session_id' => $session->id,
            'order_id'            => $missedOrder->id,
            'status'              => 'pending',
        ]);

        $this->assertDatabaseHas('delivery_session_orders', [
            'delivery_session_id' => $session->id,
            'order_id'            => $todayOrder->id,
            'status'              => 'pending',
        ]);

        // Key requirement check: original delivery_date MUST NOT change
        $missedOrderRefresh = $missedOrder->fresh();
        $this->assertEquals($originalMissedDate, Carbon::parse($missedOrderRefresh->delivery_date)->toDateString());
        $this->assertEquals($staff->id, $missedOrderRefresh->assigned_staff_id);

        // Verify that orders attached to active session are no longer returned as unassigned eligible orders
        $eligibleAfter = $service->getEligibleOrders($sessionDate, $slot->id, $staff->id);
        $this->assertFalse($eligibleAfter['missed']->contains('id', $missedOrder->id));
        $this->assertFalse($eligibleAfter['today']->contains('id', $todayOrder->id));
    }
}
