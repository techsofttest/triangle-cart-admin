<?php

namespace App\Services;

use App\Models\DeliverySession;
use App\Models\Order;
use App\Models\DeliverySessionOrder;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliverySessionService
{
    public function __construct(
        protected GoogleRoutesService $routesService
    ) {}

    /**
     * Get eligible paid orders grouped into missed/previous, today's, and future orders.
     *
     * @param string|\Carbon\Carbon $deliveryDate
     * @param int|null $deliverySlotId
     * @param int|null $staffId
     * @return array{missed: \Illuminate\Support\Collection, today: \Illuminate\Support\Collection, future: \Illuminate\Support\Collection}
     */
    public function getEligibleOrders(mixed $deliveryDate, ?int $deliverySlotId = null, ?int $staffId = null): array
    {
        $targetDate = \Carbon\Carbon::parse($deliveryDate)->toDateString();

        // Orders currently in pending or delivered status in any session are not available.
        // Orders with status 'failed' (missed deliveries) or not in any session ARE available.
        $unavailableOrderIds = DeliverySessionOrder::query()
            ->whereIn('status', ['pending', 'delivered'])
            ->pluck('order_id');

        $query = Order::query()
            ->where(function ($q) {
                $q->where('payment_status', PaymentStatus::PAID)
                  ->orWhere('payment_status', 'paid');
            })
            ->whereNotIn('status', [
                \App\Enums\OrderStatus::DELIVERED,
                \App\Enums\OrderStatus::CANCELLED,
                \App\Enums\OrderStatus::REFUNDED,
            ])
            ->whereNotIn('id', $unavailableOrderIds);

        if ($staffId) {
            $query->where(function ($q) use ($staffId) {
                $q->whereNull('assigned_staff_id')
                  ->orWhere('assigned_staff_id', $staffId);
            });
        }

        $allEligible = $query->get();

        $missed = $allEligible->filter(function ($order) use ($targetDate) {
            $date = \Carbon\Carbon::parse($order->delivery_date)->toDateString();
            return $date < $targetDate;
        })->values();

        $today = $allEligible->filter(function ($order) use ($targetDate, $deliverySlotId) {
            $date = \Carbon\Carbon::parse($order->delivery_date)->toDateString();
            if ($date !== $targetDate) {
                return false;
            }
            if ($deliverySlotId !== null && (int)$order->delivery_slot_id !== (int)$deliverySlotId) {
                return false;
            }
            return true;
        })->values();

        $future = $allEligible->filter(function ($order) use ($targetDate) {
            $date = \Carbon\Carbon::parse($order->delivery_date)->toDateString();
            return $date > $targetDate;
        })->values();

        return [
            'missed' => $missed,
            'today'  => $today,
            'future' => $future,
        ];
    }

    /**
     * Attach a list of order IDs to a delivery session without changing their original delivery dates.
     *
     * @param DeliverySession $session
     * @param array $orderIds
     * @return int
     */
    public function attachOrdersToSession(DeliverySession $session, array $orderIds): int
    {
        $orderIds = array_values(array_unique(array_filter($orderIds)));
        if (empty($orderIds)) {
            return 0;
        }

        $orders = Order::whereIn('id', $orderIds)->get();
        $seq = DeliverySessionOrder::where('delivery_session_id', $session->id)->max('stop_sequence') ?? 0;
        $attachedCount = 0;

        foreach ($orders as $order) {
            $exists = $session->sessionOrders()->where('order_id', $order->id)->exists();
            if (!$exists) {
                $session->sessionOrders()->create([
                    'order_id' => $order->id,
                    'stop_sequence' => ++$seq,
                    'status' => 'pending',
                ]);

                // Update staff assignment while preserving original delivery_date
                $order->update([
                    'assigned_staff_id' => $session->staff_id,
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                ]);

                $attachedCount++;
            }
        }

        return $attachedCount;
    }

    /**
     * Create a delivery session with explicitly selected order IDs and optimize its route.
     *
     * @param array $sessionData
     * @param array $orderIds
     * @return DeliverySession
     */
    public function createSessionWithOrders(array $sessionData, array $orderIds): DeliverySession
    {
        $session = DeliverySession::create($sessionData);
        if (!empty($orderIds)) {
            $this->attachOrdersToSession($session, $orderIds);
            $this->optimizeRoute($session);
        }
        return $session;
    }

    /**
     * Pull new paid orders for a delivery session's date and slot.
     *
     * @param DeliverySession $session
     * @return int Number of pulled orders
     */
    public function pullOrders(DeliverySession $session): int
    {
        $eligible = $this->getEligibleOrders($session->delivery_date, $session->delivery_slot_id, $session->staff_id);
        $orderIds = $eligible['today']->pluck('id')->toArray();

        if (empty($orderIds)) {
            return 0;
        }

        return $this->attachOrdersToSession($session, $orderIds);
    }

    /**
     * Optimize the route sequence for all orders in a delivery session.
     *
     * @param DeliverySession $session
     * @return bool Success status
     */
    public function optimizeRoute(DeliverySession $session): bool
    {
        $orders = $session->sessionOrders()->with('order')->get();
        
        if ($orders->isEmpty()) {
            return false;
        }

        $destinations = [];
        foreach ($orders as $sessionOrder) {
            $order = $sessionOrder->order;
            $lat = $order->shipping_latitude;
            $lng = $order->shipping_longitude;
            
            if (!$lat || !$lng) {
                $lat = config('delivery.store_coordinates.latitude', -37.8136) + (rand(-50, 50) / 1000);
                $lng = config('delivery.store_coordinates.longitude', 144.9631) + (rand(-50, 50) / 1000);
            }

            $destinations[] = [
                'id' => $sessionOrder->id,
                'lat' => (float)$lat,
                'lng' => (float)$lng,
            ];
        }

        $origin = [
            'lat' => (float)config('delivery.store_coordinates.latitude', -37.8136),
            'lng' => (float)config('delivery.store_coordinates.longitude', 144.9631),
        ];

        $result = $this->routesService->optimizeRoute($origin, $destinations);
        $optimized = $result['destinations'] ?? [];

        foreach ($optimized as $opt) {
            DeliverySessionOrder::where('id', $opt['id'])->update([
                'stop_sequence' => $opt['stop_sequence'],
                'eta' => $opt['eta'],
            ]);
        }

        $session->update([
            'estimated_distance_km' => $result['distance_km'] ?? null,
            'estimated_duration_minutes' => $result['duration_minutes'] ?? null,
            'route_polyline' => $result['encoded_polyline'] ?? null,
            'route_generated_at' => now(),
        ]);

        return true;
    }

    /**
     * Pull slot orders and optimize the route for a delivery session.
     *
     * @param DeliverySession $session
     * @return void
     */
    public function pullAndOptimize(DeliverySession $session): void
    {
        $this->pullOrders($session);
        $this->optimizeRoute($session);
    }

    /**
     * Calculate and save route geometry on-the-fly for delivery sessions missing it.
     */
    public function calculateRouteGeometry(DeliverySession $session): void
    {
        $stops = $session->sessionOrders()->with('order')->orderBy('stop_sequence')->get();
        if ($stops->isEmpty()) {
            return;
        }

        $destinations = [];
        foreach ($stops as $so) {
            $order = $so->order;
            $lat = $order->shipping_latitude;
            $lng = $order->shipping_longitude;
            if (!$lat || !$lng) {
                $lat = config('delivery.store_coordinates.latitude', -37.8136);
                $lng = config('delivery.store_coordinates.longitude', 144.9631);
            }
            $destinations[] = [
                'lat' => (float)$lat,
                'lng' => (float)$lng,
            ];
        }

        $origin = [
            'lat' => (float)config('delivery.store_coordinates.latitude', -37.8136),
            'lng' => (float)config('delivery.store_coordinates.longitude', 144.9631),
        ];

        $apiKey = config('services.google.maps_api_key');
        if (!$apiKey) {
            return;
        }

        try {
            $originStr = "{$origin['lat']},{$origin['lng']}";
            
            $waypointParts = [];
            foreach ($destinations as $dest) {
                $waypointParts[] = "{$dest['lat']},{$dest['lng']}";
            }
            
            $waypointsStr = implode('|', $waypointParts);

            $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
                'origin' => $originStr,
                'destination' => $originStr, // round-trip
                'waypoints' => $waypointsStr,
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? '') === 'OK' && !empty($data['routes'][0])) {
                    $route = $data['routes'][0];
                    $legs = $route['legs'] ?? [];
                    $encodedPolyline = $route['overview_polyline']['points'] ?? null;
                    
                    $totalDistanceMeters = 0;
                    $totalDurationSeconds = 0;
                    foreach ($legs as $leg) {
                        $totalDistanceMeters += $leg['distance']['value'] ?? 0;
                        $totalDurationSeconds += $leg['duration']['value'] ?? 0;
                    }

                    $session->update([
                        'route_polyline' => $encodedPolyline,
                        'estimated_distance_km' => round($totalDistanceMeters / 1000, 2),
                        'estimated_duration_minutes' => (int)round($totalDurationSeconds / 60),
                        'route_generated_at' => now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to compute route geometry: ' . $e->getMessage());
        }
    }
}
