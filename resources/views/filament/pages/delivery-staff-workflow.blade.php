<x-filament-panels::page>
    <div class="max-w-md mx-auto px-2 py-4">
        @if (!$activeSession)
            <!-- Start Session Screen -->
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-xl text-center">
                <div class="w-16 h-16 bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" width="32" height="32" style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Start Delivery Shift</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Select an assigned delivery session below to start your route guidance and compliance logging.</p>

                @if (count($this->availableSessions) > 0)
                    <div class="space-y-4">
                        @foreach ($this->availableSessions as $session)
                            <button wire:click="startSession({{ $session->id }})" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl shadow-md transition duration-200 flex items-center justify-between">
                                <span class="text-left">
                                    <span class="block text-sm font-bold">{{ $session->delivery_date->format('l, d M') }}</span>
                                    <span class="block text-xs text-indigo-200">{{ $session->timeSlot?->start_time }} - {{ $session->timeSlot?->end_time }}</span>
                                </span>
                                <span class="bg-indigo-500 px-3 py-1 rounded-lg text-xs">Start →</span>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 text-sm text-gray-500 dark:text-gray-400">
                        No pending delivery sessions scheduled. Check back later or create one in the admin panel.
                    </div>
                @endif
            </div>
        @elseif (!$currentStop)
            <!-- Active Session, All Stops Completed Screen -->
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-xl text-center">
                <div class="w-16 h-16 bg-success-50 dark:bg-success-950 text-success-600 dark:text-success-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" width="32" height="32" style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">All Stops Completed!</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">You have completed all scheduled deliveries for this session. Complete the session to finalize your shift logs.</p>

                <button wire:click="completeSession" class="w-full bg-success-600 hover:bg-success-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200">
                    Complete Session & Log Off
                </button>
            </div>
        @else
            <!-- Active Delivery Stop Detail Card -->
            <div class="space-y-4">
                <!-- Header / Progress bar -->
                <div class="flex items-center justify-between bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 px-4 py-3 rounded-xl shadow-sm">
                    <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                        Stop #{{ $currentStop->stop_sequence }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        ETA: <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $currentStop->eta ?? 'N/A' }}</span>
                    </span>
                </div>

                <!-- Customer info card -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-lg space-y-4">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider">Customer</div>
                        <div class="text-lg font-bold text-gray-900 dark:text-white mt-0.5">{{ $currentStop->order?->customer_name }}</div>
                        <a href="tel:{{ $currentStop->order?->customer_phone }}" class="inline-flex items-center text-sm text-indigo-600 dark:text-indigo-400 mt-1 font-semibold hover:underline">
                            <svg class="w-4 h-4 mr-1" width="16" height="16" style="width: 16px; height: 16px; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $currentStop->order?->customer_phone }}
                        </a>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800"/>

                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider">Delivery Address</div>
                        <div class="text-sm text-gray-800 dark:text-gray-200 mt-1 font-medium">
                            {{ $currentStop->order?->shipping_address_line_1 }}<br>
                            @if ($currentStop->order?->shipping_address_line_2)
                                {{ $currentStop->order?->shipping_address_line_2 }}<br>
                            @endif
                            {{ $currentStop->order?->shipping_suburb }}, {{ $currentStop->order?->shipping_city }}
                        </div>

                        <!-- Navigation Link -->
                        @php
                            $lat = $currentStop->order?->shipping_latitude;
                            $lng = $currentStop->order?->shipping_longitude;
                            $placeId = $currentStop->order?->shipping_google_place_id;
                            $navUrl = $placeId 
                                ? "https://www.google.com/maps/search/?api=1&query=Google&query_place_id={$placeId}"
                                : "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}";
                        @endphp
                        <a href="{{ $navUrl }}" target="_blank" class="mt-3 w-full bg-blue-50 hover:bg-blue-100 dark:bg-blue-950 dark:hover:bg-blue-900 text-blue-600 dark:text-blue-400 font-bold py-2.5 px-4 rounded-xl flex items-center justify-center transition duration-150 text-sm shadow-sm">
                            <svg class="w-5 h-5 mr-1.5" width="20" height="20" style="width: 20px; height: 20px; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Navigate with Google Maps
                        </a>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-800"/>

                    <!-- Items List -->
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-2">Order Items</div>
                        <ul class="space-y-2">
                            @foreach ($currentStop->order?->items ?? [] as $item)
                                <li class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/50 p-2.5 rounded-lg text-sm">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $item->product_name ?? 'Product' }}</span>
                                    <span class="text-xs font-bold bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 px-2.5 py-0.5 rounded-full">
                                        Qty: {{ $item->quantity }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($currentStop->order?->delivery_notes)
                        <hr class="border-gray-100 dark:border-gray-800"/>
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-wider">Delivery Notes</div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 italic">
                                "{{ $currentStop->order?->delivery_notes }}"
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Primary Action Buttons -->
                <div class="grid grid-cols-2 gap-4">
                    <button wire:click="openFailureModal" class="bg-red-50 hover:bg-red-100 dark:bg-red-950 dark:hover:bg-red-900 text-red-600 dark:text-red-400 font-bold py-3.5 px-4 rounded-xl transition duration-150 shadow-md text-center text-sm">
                        Mark Failed
                    </button>
                    
                    <button wire:click="openDeliveryModal" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-4 rounded-xl transition duration-150 shadow-md text-center text-sm">
                        Mark Delivered
                    </button>
                </div>
            </div>
        @endif

        <!-- Mark Delivered Modal Form -->
        @if ($showDeliveryModal)
            <div class="fixed inset-0 bg-gray-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Delivery</h3>

                    <!-- Compliance Section -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl space-y-3">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="tempComplianceEnabled" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Enable Temp Compliance</span>
                        </label>

                        @if ($tempComplianceEnabled)
                            <div class="space-y-3 pt-2">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Temperature Reading (°C)</label>
                                    <input type="number" step="0.1" wire:model="temperatureReading" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Thermometer Photo</label>
                                    <input type="file" accept="image/*" capture="environment" wire:model="thermometerPhoto" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-400 file:cursor-pointer">
                                    <div wire:loading wire:target="thermometerPhoto" class="text-xs text-gray-500 mt-1">Uploading image...</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Delivery Notes (Optional)</label>
                        <textarea wire:model="deliveryNotes" rows="2" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    </div>

                    <div class="flex space-x-3 pt-2">
                        <button type="button" wire:click="$set('showDeliveryModal', false)" class="w-1/2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold py-2.5 px-4 rounded-xl text-sm transition">
                            Cancel
                        </button>
                        
                        <button type="button" onclick="getLocationAndSubmit()" class="w-1/2 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-md">
                            Submit Delivery
                        </button>
                    </div>
                </div>
            </div>

            <!-- JavaScript helper to capture GPS coordinates -->
            <script>
                function getLocationAndSubmit() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                @this.set('gpsLatitude', position.coords.latitude);
                                @this.set('gpsLongitude', position.coords.longitude);
                                @this.call('submitDeliverySuccess');
                            },
                            (error) => {
                                console.warn('Geolocation capture failed: ', error);
                                // Fallback without GPS
                                @this.call('submitDeliverySuccess');
                            },
                            { timeout: 5000 }
                        );
                    } else {
                        // Fallback if not supported
                        @this.call('submitDeliverySuccess');
                    }
                }
            </script>
        @endif

        <!-- Mark Failed Modal Form -->
        @if ($showFailureModal)
            <div class="fixed inset-0 bg-gray-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Log Delivery Failure</h3>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Reason for Failure</label>
                        <select wire:model="failureReason" class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">-- Select Reason --</option>
                            <option value="Customer not home">Customer not home</option>
                            <option value="Incorrect address details">Incorrect address details</option>
                            <option value="Refused delivery">Refused delivery</option>
                            <option value="Safety/Access issue">Safety/Access issue</option>
                            <option value="Other / Damaged parcel">Other / Damaged parcel</option>
                        </select>
                        @error('failureReason') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex space-x-3 pt-2">
                        <button type="button" wire:click="$set('showFailureModal', false)" class="w-1/2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold py-2.5 px-4 rounded-xl text-sm transition">
                            Cancel
                        </button>
                        
                        <button type="button" wire:click="submitDeliveryFailure" class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-md">
                            Confirm Failure
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
