<x-filament::page>
    @php
        $session = $this->record;
        $stops = $session->sessionOrders()
            ->with('order')
            ->orderBy('stop_sequence')
            ->get();
        
        $stopsData = $stops->map(function ($so) {
            return [
                'sequence' => $so->stop_sequence,
                'eta' => $so->eta ?? '—',
                'order_number' => $so->order->order_number,
                'customer_name' => $so->order->customer_name ?? ($so->order->first_name . ' ' . $so->order->last_name),
                'address' => $so->order->address,
                'suburb' => $so->order->shipping_suburb ?? $so->order->shipping_city ?? $so->order->city ?? '—',
                'lat' => (float)$so->order->shipping_latitude,
                'lng' => (float)$so->order->shipping_longitude,
                'view_url' => \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $so->order_id]),
            ];
        })->toArray();
    @endphp

    <div class="delivery-session-detail-wrapper" style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <!-- Header Info / Stats Row -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
            <!-- Card 1: Orders -->
            <div class="stats-card" style="background: var(--card-bg, #fff); border-radius: 0.5rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border-color, #e5e7eb);">
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Total Orders</div>
                <div style="font-size: 1.875rem; font-weight: 700; color: var(--text-color, #111827); margin-top: 0.25rem;">{{ $stops->count() }}</div>
            </div>
            <!-- Card 2: Distance -->
            <div class="stats-card" style="background: var(--card-bg, #fff); border-radius: 0.5rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border-color, #e5e7eb);">
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Estimated Distance</div>
                <div style="font-size: 1.875rem; font-weight: 700; color: var(--text-color, #111827); margin-top: 0.25rem;">
                    {{ $session->estimated_distance_km ? number_format($session->estimated_distance_km, 1) . ' km' : '—' }}
                </div>
            </div>
            <!-- Card 3: Duration -->
            <div class="stats-card" style="background: var(--card-bg, #fff); border-radius: 0.5rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border-color, #e5e7eb);">
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Estimated Duration</div>
                <div style="font-size: 1.875rem; font-weight: 700; color: var(--text-color, #111827); margin-top: 0.25rem;">
                    @if ($session->estimated_duration_minutes)
                        @php
                            $hours = floor($session->estimated_duration_minutes / 60);
                            $mins = $session->estimated_duration_minutes % 60;
                        @endphp
                        {{ $hours > 0 ? "{$hours} hr " : "" }}{{ $mins }} min
                    @else
                        —
                    @endif
                </div>
            </div>
            <!-- Card 4: Generated At -->
            <div class="stats-card" style="background: var(--card-bg, #fff); border-radius: 0.5rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border-color, #e5e7eb);">
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 500;">Route Generated</div>
                <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-color, #111827); margin-top: 0.75rem;">
                    {{ $session->route_generated_at ? $session->route_generated_at->format('d-M-Y h:i A') : '—' }}
                </div>
            </div>
        </div>

        <div class="grid-layout" style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start;">
            
            <!-- Left Panel -->
            <div class="left-panel-card" style="background: var(--card-bg, #fff); border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border-color, #e5e7eb); display: flex; flex-direction: column; gap: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 600; border-bottom: 1px solid var(--border-color, #e5e7eb); padding-bottom: 0.5rem; margin: 0;">Session Details</h3>
                
                <div>
                    <span style="font-size: 0.75rem; text-transform: uppercase; color: #9ca3af; font-weight: 600;">Session ID</span>
                    <div style="font-weight: 600; font-size: 1.1rem; color: var(--text-color, #111827);">#{{ $session->id }}</div>
                </div>

                <div>
                    <span style="font-size: 0.75rem; text-transform: uppercase; color: #9ca3af; font-weight: 600;">Assigned Staff</span>
                    <div style="font-weight: 500; color: var(--text-color, #111827);">{{ $session->staff->name ?? '—' }}</div>
                </div>

                <div>
                    <span style="font-size: 0.75rem; text-transform: uppercase; color: #9ca3af; font-weight: 600;">Warehouse (Origin)</span>
                    <div style="font-weight: 500; font-size: 0.875rem; color: var(--text-color, #111827);">
                        {{ config('delivery.store_address', 'Main Warehouse') }}
                    </div>
                </div>

                <div>
                    <span style="font-size: 0.75rem; text-transform: uppercase; color: #9ca3af; font-weight: 600;">Status</span>
                    <div style="margin-top: 0.25rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; 
                            background: {{ $session->status === 'completed' ? '#d1fae5' : ($session->status === 'in_progress' ? '#dbeafe' : '#f3f4f6') }};
                            color: {{ $session->status === 'completed' ? '#065f46' : ($session->status === 'in_progress' ? '#1e40af' : '#374151') }};">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Panel (Map + Summary) -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Map Container -->
                <div style="background: var(--card-bg, #fff); border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border-color, #e5e7eb);">
                    <div id="route-map" style="width: 100%; height: 450px; background: #e5e7eb;"></div>
                </div>

                <!-- Route Summary -->
                <div style="background: var(--card-bg, #fff); border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border-color, #e5e7eb);">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color, #e5e7eb); padding-bottom: 0.5rem;">Route Summary</h3>
                    
                    <div class="route-summary-timeline" style="display: flex; flex-direction: column; gap: 1rem; position: relative; padding-left: 1.5rem;">
                        <div style="position: absolute; left: 6px; top: 12px; bottom: 12px; width: 2px; background: #d1d5db;"></div>
                        
                        <!-- Warehouse -->
                        <div style="display: flex; gap: 1rem; align-items: start; position: relative;">
                            <div style="position: absolute; left: -19px; width: 10px; height: 10px; border-radius: 50%; background: #10b981; border: 2px solid #fff; box-shadow: 0 0 0 2px #10b981;"></div>
                            <div>
                                <div style="font-weight: 600; font-size: 0.875rem; color: #10b981;">Warehouse (Starting Point)</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">{{ config('delivery.store_address', 'Main Warehouse') }}</div>
                            </div>
                        </div>

                        <!-- Stops -->
                        @foreach ($stopsData as $stop)
                            <div style="display: flex; gap: 1rem; align-items: start; position: relative;">
                                <div style="position: absolute; left: -19px; width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; border: 2px solid #fff; box-shadow: 0 0 0 2px #3b82f6;"></div>
                                <div style="display: flex; justify-content: space-between; width: 100%; align-items: center; border-bottom: 1px solid var(--border-color, #f3f4f6); padding-bottom: 0.5rem;">
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-color, #111827);">
                                            {{ $stop['sequence'] }}. Order #{{ $stop['order_number'] }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: #6b7280;">
                                            Customer: <span style="font-weight: 500;">{{ $stop['customer_name'] }}</span> | Suburb: <span style="font-weight: 500;">{{ $stop['suburb'] }}</span>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 0.813rem; font-weight: 600; color: #3b82f6;">ETA: {{ $stop['eta'] }}</div>
                                        <a href="{{ $stop['view_url'] }}" style="font-size: 0.75rem; color: #3b82f6; text-decoration: underline;">View Order</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Styles for Dark Mode / CSS variables -->
    <style>
        :root {
            --card-bg: #ffffff;
            --text-color: #111827;
            --border-color: #e5e7eb;
        }
        .dark {
            --card-bg: #161617;
            --text-color: #f3f4f6;
            --border-color: #374151;
        }
        .stats-card, .left-panel-card {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
        }
    </style>

    <!-- Google Maps JS Implementation -->
    <script>
        window.routeMapData = {
            origin: {
                lat: {{ (float)config('delivery.store_coordinates.latitude', -37.8136) }},
                lng: {{ (float)config('delivery.store_coordinates.longitude', 144.9631) }},
                name: '{{ config('delivery.store_name', 'Main Warehouse') }}',
                address: '{{ config('delivery.store_address', '12 Main Street, Sydney NSW 2000') }}'
            },
            stops: @json($stopsData)
        };

        // Helper to generate custom SVG markers
        function getMarkerIcon(type, number, status = 'pending') {
            if (type === 'warehouse') {
                const svg = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 40" width="100" height="40">
                  <rect x="5" y="2" width="90" height="24" rx="12" fill="#10B981" stroke="#FFFFFF" stroke-width="2"/>
                  <path d="M45 26 L50 34 L55 26 Z" fill="#10B981" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M45.5 26.1 L54.5 26.1" fill="none" stroke="#10B981" stroke-width="2"/>
                  <text x="50" y="17" fill="#FFFFFF" font-size="11" font-weight="bold" font-family="sans-serif" text-anchor="middle">Warehouse</text>
                </svg>
                `;
                return {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                    anchor: new google.maps.Point(50, 34)
                };
            } else {
                let fillColor = '#3B82F6'; // default blue
                let textColor = '#3B82F6';
                let innerCircleColor = '#FFFFFF';

                if (status === 'completed') {
                    fillColor = '#10B981'; // green
                    textColor = '#10B981';
                } else if (status === 'current') {
                    fillColor = '#F97316'; // orange
                    textColor = '#F97316';
                }

                const svg = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 42" width="32" height="42">
                  <path d="M16 2 C9.37 2 4 7.37 4 14 C4 23 16 38 16 38 C16 38 28 23 28 14 C28 7.37 22.63 2 16 2 Z" fill="${fillColor}" stroke="#FFFFFF" stroke-width="2"/>
                  <circle cx="16" cy="14" r="9" fill="${innerCircleColor}"/>
                  <text x="16" y="18" fill="${textColor}" font-size="11" font-weight="bold" font-family="sans-serif" text-anchor="middle">${number}</text>
                </svg>
                `;
                return {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
                    anchor: new google.maps.Point(16, 38)
                };
            }
        }

        function initMap() {
            const data = window.routeMapData;
            const map = new google.maps.Map(document.getElementById('route-map'), {
                zoom: 12,
                center: data.origin,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true
            });

            const bounds = new google.maps.LatLngBounds();
            bounds.extend(data.origin);

            // Warehouse Marker
            const warehouseMarker = new google.maps.Marker({
                position: data.origin,
                map: map,
                title: 'Warehouse',
                icon: getMarkerIcon('warehouse')
            });

            const infoWindow = new google.maps.InfoWindow();
            
            warehouseMarker.addListener('click', () => {
                infoWindow.setContent(`
                    <div style="padding: 0.5rem; font-family: sans-serif; min-width: 180px;">
                        <h4 style="margin: 0 0 0.25rem 0; color: #10b981; font-weight: 600; font-size: 1rem;">${data.origin.name}</h4>
                        <p style="margin: 0; font-size: 0.813rem; color: #4b5563;">${data.origin.address}</p>
                    </div>
                `);
                infoWindow.open(map, warehouseMarker);
            });

            const waypoints = [];
            
            data.stops.forEach((stop) => {
                const stopPos = { lat: stop.lat, lng: stop.lng };
                bounds.extend(stopPos);

                // Add Stop Marker
                const marker = new google.maps.Marker({
                    position: stopPos,
                    map: map,
                    title: `Stop #${stop.sequence}`,
                    icon: getMarkerIcon('delivery', stop.sequence, 'pending')
                });

                marker.addListener('click', () => {
                    infoWindow.setContent(`
                        <div style="padding: 0.5rem; font-family: sans-serif; min-width: 180px;">
                            <div style="font-size: 0.75rem; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 0.25rem;">Stop #${stop.sequence}</div>
                            <h4 style="margin: 0 0 0.5rem 0; color: #1e40af; font-weight: 600; font-size: 1rem;">Order #${stop.order_number}</h4>
                            <p style="margin: 0 0 0.25rem 0; font-size: 0.813rem; color: #374151;"><strong>Customer:</strong> ${stop.customer_name}</p>
                            <p style="margin: 0 0 0.5rem 0; font-size: 0.813rem; color: #374151;"><strong>Address:</strong> ${stop.address}</p>
                            <a href="${stop.view_url}" style="font-size: 0.75rem; color: #2563eb; text-decoration: underline; font-weight: 500; display: inline-block;">View Order</a>
                        </div>
                    `);
                    infoWindow.open(map, marker);
                });

                waypoints.push({
                    location: stopPos,
                    stopover: true
                });
            });

            // Adjust zoom to fit bounds
            if (data.stops.length > 0) {
                map.fitBounds(bounds);
            }

            // Draw Route
            // Check waypoints limit (Google Directions Service limit is 25 waypoints)
            if (data.stops.length > 0 && data.stops.length <= 25) {
                const directionsService = new google.maps.DirectionsService();
                const directionsRenderer = new google.maps.DirectionsRenderer({
                    map: map,
                    suppressMarkers: true, // We already draw our custom green and blue markers
                    polylineOptions: {
                        strokeColor: '#3b82f6',
                        strokeOpacity: 0.8,
                        strokeWeight: 4
                    }
                });

                directionsService.route({
                    origin: data.origin,
                    destination: data.origin, // round-trip
                    waypoints: waypoints,
                    optimizeWaypoints: false, // The sequence is already optimized!
                    travelMode: google.maps.TravelMode.DRIVING
                }, (response, status) => {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(response);
                    } else {
                        console.warn('Directions request failed due to ' + status + '. Falling back to Polyline.');
                        drawPolyline(map, data);
                    }
                });
            } else if (data.stops.length > 25) {
                // Fallback to simple straight polylines if waypoints exceed Directions API limit
                drawPolyline(map, data);
            }
        }

        function drawPolyline(map, data) {
            const routePath = [data.origin];
            data.stops.forEach(stop => {
                routePath.push({ lat: stop.lat, lng: stop.lng });
            });
            routePath.push(data.origin); // return trip to warehouse

            const polyline = new google.maps.Polyline({
                path: routePath,
                geodesic: true,
                strokeColor: '#3b82f6',
                strokeOpacity: 0.8,
                strokeWeight: 4,
                map: map
            });
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap" async defer></script>

</x-filament::page>
