<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleRoutesService
{
    /**
     * Optimize the sequence of delivery orders.
     *
     * @param array $origin ['lat' => float, 'lng' => float]
     * @param array $destinations Array of elements like ['id' => mixed, 'lat' => float, 'lng' => float]
     * @return array Array of destinations in optimized order, each containing 'stop_sequence' and 'eta'
     */
    public function optimizeRoute(array $origin, array $destinations): array
    {
        if (empty($destinations)) {
            return [];
        }

        $apiKey = config('services.google.maps_api_key');

        if ($apiKey) {
            try {
                return $this->optimizeWithGoogle($origin, $destinations, $apiKey);
            } catch (\Exception $e) {
                Log::warning('Google Directions API optimization failed: ' . $e->getMessage() . '. Falling back to heuristic.');
            }
        }

        return $this->optimizeWithHeuristic($origin, $destinations);
    }

    /**
     * Optimize using Google Directions API
     */
    protected function optimizeWithGoogle(array $origin, array $destinations, string $apiKey): array
    {
        // For Google Directions, the destination must be a specific point.
        // We will use the last waypoint as the final destination and optimize all others as intermediate waypoints.
        // To be safe and simple, we can set the final destination to be the same as the origin (round-trip),
        // or set the last destination in the list as the final destination and optimize the rest.
        // Let's use the origin as the destination (round-trip back to store) or the last waypoint.
        // Let's make it a round-trip back to origin to allow full waypoint optimization:
        $originStr = "{$origin['lat']},{$origin['lng']}";
        
        $waypointParts = [];
        foreach ($destinations as $dest) {
            $waypointParts[] = "{$dest['lat']},{$dest['lng']}";
        }
        
        $waypointsStr = 'optimize:true|' . implode('|', $waypointParts);

        $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
            'origin' => $originStr,
            'destination' => $originStr, // round-trip
            'waypoints' => $waypointsStr,
            'key' => $apiKey,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (($data['status'] ?? '') === 'OK' && isset($data['routes'][0]['legs'])) {
                $route = $data['routes'][0];
                $waypointOrder = $route['waypoint_order'] ?? []; // Array of indices mapping to the order of waypoints

                $optimizedDestinations = [];
                $currentTime = now()->startOfHour()->addHours(9); // Start delivery at 9:00 AM by default

                // Map waypoint order
                foreach ($waypointOrder as $seq => $originalIndex) {
                    if (isset($destinations[$originalIndex])) {
                        $dest = $destinations[$originalIndex];
                        
                        // Estimate ETA based on leg durations
                        // leg 0 is origin to waypoint 0, leg 1 is waypoint 0 to waypoint 1, etc.
                        $legDuration = $route['legs'][$seq]['duration']['value'] ?? 600; // in seconds
                        // Add some buffer time (e.g. 5 mins per stop)
                        $currentTime = $currentTime->addSeconds($legDuration + 300);

                        $optimizedDestinations[] = array_merge($dest, [
                            'stop_sequence' => $seq + 1,
                            'eta' => $currentTime->format('h:i A'),
                        ]);
                    }
                }

                // If any waypoint was missed by waypoint_order, append them
                $usedIndices = array_flip($waypointOrder);
                $seq = count($optimizedDestinations) + 1;
                foreach ($destinations as $index => $dest) {
                    if (!isset($usedIndices[$index])) {
                        $currentTime = $currentTime->addMinutes(15);
                        $optimizedDestinations[] = array_merge($dest, [
                            'stop_sequence' => $seq++,
                            'eta' => $currentTime->format('h:i A'),
                        ]);
                    }
                }

                return $optimizedDestinations;
            }
        }

        throw new \Exception('Invalid Directions API response: ' . $response->body());
    }

    /**
     * Heuristic Nearest-Neighbor Optimization (fallback)
     */
    protected function optimizeWithHeuristic(array $origin, array $destinations): array
    {
        $unvisited = $destinations;
        $currentPoint = $origin;
        $optimized = [];
        $seq = 1;
        $currentTime = now()->startOfHour()->addHours(9); // Start at 9:00 AM

        while (!empty($unvisited)) {
            $nearestIndex = null;
            $minDistance = null;

            foreach ($unvisited as $index => $dest) {
                $dist = $this->calculateDistance(
                    $currentPoint['lat'], $currentPoint['lng'],
                    $dest['lat'], $dest['lng']
                );

                if ($minDistance === null || $dist < $minDistance) {
                    $minDistance = $dist;
                    $nearestIndex = $index;
                }
            }

            if ($nearestIndex !== null) {
                $nearestDest = $unvisited[$nearestIndex];
                
                // Assume average speed of 30 km/h: time (mins) = (distance / 30) * 60 = distance * 2
                // Limit distance to a reasonable calculation
                $travelTimeMins = max(3, round($minDistance * 2));
                // Add 5 minutes buffer for delivery
                $currentTime = $currentTime->addMinutes($travelTimeMins + 5);

                $optimized[] = array_merge($nearestDest, [
                    'stop_sequence' => $seq++,
                    'eta' => $currentTime->format('h:i A'),
                ]);

                $currentPoint = $nearestDest;
                unset($unvisited[$nearestIndex]);
            } else {
                break;
            }
        }

        return $optimized;
    }

    /**
     * Calculate geodesic distance (Haversine formula) in kilometers
     */
    protected function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
