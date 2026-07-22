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
     * @return array Array containing 'destinations', 'distance_km', and 'duration_minutes'
     */
    public function optimizeRoute(array $origin, array $destinations): array
    {
        if (empty($destinations)) {
            return [
                'destinations' => [],
                'distance_km' => 0,
                'duration_minutes' => 0,
            ];
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
     * Optimize using Google Routes API (computeRoutes)
     */
    protected function optimizeWithGoogle(array $origin, array $destinations, string $apiKey): array
    {
        // Build waypoints (intermediates) from destinations
        $intermediates = [];
        foreach ($destinations as $dest) {
            $intermediates[] = [
                'location' => [
                    'latLng' => [
                        'latitude' => $dest['lat'],
                        'longitude' => $dest['lng'],
                    ],
                ],
            ];
        }

        $body = [
            'origin' => [
                'location' => [
                    'latLng' => [
                        'latitude' => $origin['lat'],
                        'longitude' => $origin['lng'],
                    ],
                ],
            ],
            'destination' => [
                'location' => [
                    'latLng' => [
                        'latitude' => $origin['lat'],
                        'longitude' => $origin['lng'],
                    ],
                ],
            ],
            'intermediates' => $intermediates,
            'travelMode' => 'DRIVE',
            'optimizeWaypointOrder' => true,
            'routingPreference' => 'TRAFFIC_UNAWARE',
            'polylineEncoding' => 'ENCODED_POLYLINE',
        ];

        $fieldMask = 'routes.optimizedIntermediateWaypointIndex,routes.legs.duration,routes.legs.distanceMeters,routes.polyline.encodedPolyline';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $apiKey,
            'X-Goog-FieldMask' => $fieldMask,
        ])->post('https://routes.googleapis.com/directions/v2:computeRoutes', $body);

        if ($response->successful()) {
            $data = $response->json();

            if (!empty($data['routes'][0])) {
                $route = $data['routes'][0];
                $waypointOrder = $route['optimizedIntermediateWaypointIndex'] ?? [];
                $legs = $route['legs'] ?? [];
                $encodedPolyline = $route['polyline']['encodedPolyline'] ?? null;

                $optimizedDestinations = [];
                $currentTime = now()->startOfHour()->addHours(9); // Start delivery at 9:00 AM

                $totalDistanceMeters = 0;
                $totalDurationSeconds = 0;
                foreach ($legs as $leg) {
                    $totalDistanceMeters += $leg['distanceMeters'] ?? 0;
                    // Duration comes as a string like "1234s"
                    $durationStr = $leg['duration'] ?? '0s';
                    $totalDurationSeconds += (int)str_replace('s', '', $durationStr);
                }

                // Map waypoint order
                if (!empty($waypointOrder)) {
                    foreach ($waypointOrder as $seq => $originalIndex) {
                        if (isset($destinations[$originalIndex])) {
                            $dest = $destinations[$originalIndex];

                            // Estimate ETA based on leg durations
                            $durationStr = $legs[$seq]['duration'] ?? '600s';
                            $legDuration = (int)str_replace('s', '', $durationStr);
                            // Add some buffer time (e.g. 5 mins per stop)
                            $currentTime = $currentTime->addSeconds($legDuration + 300);

                            $optimizedDestinations[] = array_merge($dest, [
                                'stop_sequence' => $seq + 1,
                                'eta' => $currentTime->format('h:i A'),
                            ]);
                        }
                    }
                } else {
                    // No optimization returned, use original order
                    foreach ($destinations as $seq => $dest) {
                        $durationStr = $legs[$seq]['duration'] ?? '600s';
                        $legDuration = (int)str_replace('s', '', $durationStr);
                        $currentTime = $currentTime->addSeconds($legDuration + 300);

                        $optimizedDestinations[] = array_merge($dest, [
                            'stop_sequence' => $seq + 1,
                            'eta' => $currentTime->format('h:i A'),
                        ]);
                    }
                }

                // If any waypoint was missed by waypoint order, append them
                $usedIndices = !empty($waypointOrder) ? array_flip($waypointOrder) : [];
                $seq = count($optimizedDestinations) + 1;
                foreach ($destinations as $index => $dest) {
                    if (!empty($waypointOrder) && !isset($usedIndices[$index])) {
                        $currentTime = $currentTime->addMinutes(15);
                        $optimizedDestinations[] = array_merge($dest, [
                            'stop_sequence' => $seq++,
                            'eta' => $currentTime->format('h:i A'),
                        ]);
                        $totalDistanceMeters += 5000;
                        $totalDurationSeconds += 900;
                    }
                }

                return [
                    'destinations' => $optimizedDestinations,
                    'distance_km' => round($totalDistanceMeters / 1000, 2),
                    'duration_minutes' => (int)round($totalDurationSeconds / 60),
                    'encoded_polyline' => $encodedPolyline,
                ];
            }
        }

        throw new \Exception('Invalid Routes API response: ' . $response->body());
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

        $totalDistance = 0.0;
        $totalDuration = 0;

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
                $travelTimeMins = max(3, round($minDistance * 2));
                // Add 5 minutes buffer for delivery
                $currentTime = $currentTime->addMinutes($travelTimeMins + 5);

                $optimized[] = array_merge($nearestDest, [
                    'stop_sequence' => $seq++,
                    'eta' => $currentTime->format('h:i A'),
                ]);

                $totalDistance += $minDistance;
                $totalDuration += ($travelTimeMins + 5);

                $currentPoint = $nearestDest;
                unset($unvisited[$nearestIndex]);
            } else {
                break;
            }
        }

        if (!empty($optimized)) {
            $lastDest = end($optimized);
            $returnDist = $this->calculateDistance(
                $lastDest['lat'], $lastDest['lng'],
                $origin['lat'], $origin['lng']
            );
            $returnTimeMins = max(3, round($returnDist * 2));
            $totalDistance += $returnDist;
            $totalDuration += $returnTimeMins;
        }

        return [
            'destinations' => $optimized,
            'distance_km' => round($totalDistance, 2),
            'duration_minutes' => (int)$totalDuration,
        ];
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
