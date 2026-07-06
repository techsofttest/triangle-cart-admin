<style>
    /* ===== Layout ===== */
    .app-container { max-width: 28rem; margin: 0 auto; padding: 1rem 0.5rem; }
    .stack-2 > * + * { margin-top: 0.5rem; }
    .stack-3 > * + * { margin-top: 0.75rem; }
    .stack-4 > * + * { margin-top: 1rem; }
    .pt-2 { padding-top: 0.5rem; }
    .mb-2 { margin-bottom: 0.5rem; }

    /* ===== Cards ===== */
    .card {
        background: #ffffff;
        border: 1px solid #f3f4f6;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
    }
    .dark .card { background: #111827; border-color: #1f2937; }
    .card-center { text-align: center; }

    .detail-card {
        background: #ffffff;
        border: 1px solid #f3f4f6;
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
    }
    .dark .detail-card { background: #111827; border-color: #1f2937; }

    /* ===== Icon circles ===== */
    .icon-circle {
        width: 4rem; height: 4rem;
        border-radius: 9999px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem auto;
    }
    .icon-circle-blue { background: #eff6ff; color: #2563eb; }
    .dark .icon-circle-blue { background: #172554; color: #60a5fa; }
    .icon-circle-success { background: #ecfdf5; color: #16a34a; }
    .dark .icon-circle-success { background: #052e1c; color: #4ade80; }

    /* ===== Text ===== */
    .card-title { font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; }
    .dark .card-title { color: #ffffff; }
    .card-subtitle { font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem; }
    .dark .card-subtitle { color: #9ca3af; }
    .field-label { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
    .field-label-form { display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; margin-bottom: 0.25rem; }

    /* ===== Empty state ===== */
    .empty-state {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 1rem;
        font-size: 0.875rem;
        color: #6b7280;
    }
    .dark .empty-state { background: #1f2937; color: #9ca3af; }

    /* ===== Session buttons ===== */
    .btn-session {
        width: 100%;
        background: #4f46e5;
        color: #ffffff;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        display: flex; align-items: center; justify-content: space-between;
        border: none; cursor: pointer;
        transition: background 0.2s;
    }
    .btn-session:hover { background: #4338ca; }
    .text-left { text-align: left; }
    .session-date { display: block; font-size: 0.875rem; font-weight: 700; }
    .session-time { display: block; font-size: 0.75rem; color: #c7d2fe; }
    .session-badge { background: #6366f1; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; }

    /* ===== Full-width success button ===== */
    .btn-success-full {
        width: 100%;
        background: #16a34a;
        color: #ffffff;
        font-weight: 700;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: none; cursor: pointer;
        transition: background 0.2s;
    }
    .btn-success-full:hover { background: #15803d; }

    /* ===== Stop header bar ===== */
    .stop-header {
        display: flex; align-items: center; justify-content: space-between;
        background: #ffffff;
        border: 1px solid #f3f4f6;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .dark .stop-header { background: #111827; border-color: #1f2937; }
    .stop-number { font-size: 0.875rem; font-weight: 700; color: #4f46e5; }
    .dark .stop-number { color: #818cf8; }
    .stop-eta-label { font-size: 0.75rem; color: #6b7280; }
    .dark .stop-eta-label { color: #9ca3af; }
    .stop-eta-value { font-weight: 600; color: #1f2937; }
    .dark .stop-eta-value { color: #e5e7eb; }

    /* ===== Customer / phone ===== */
    .customer-name { font-size: 1.125rem; font-weight: 700; color: #111827; margin-top: 0.125rem; }
    .dark .customer-name { color: #ffffff; }
    .phone-link {
        display: inline-flex; align-items: center;
        font-size: 0.875rem; color: #4f46e5; font-weight: 600;
        margin-top: 0.25rem; text-decoration: none;
    }
    .phone-link:hover { text-decoration: underline; }
    .dark .phone-link { color: #818cf8; }
    .icon-xs { width: 1rem; height: 1rem; margin-right: 0.25rem; }
    .icon-md { width: 1.25rem; height: 1.25rem; margin-right: 0.375rem; }

    /* ===== Divider ===== */
    .divider { border: none; border-top: 1px solid #f3f4f6; margin: 0; }
    .dark .divider { border-top-color: #1f2937; }

    /* ===== Address ===== */
    .address-text { font-size: 0.875rem; color: #1f2937; margin-top: 0.25rem; font-weight: 500; }
    .dark .address-text { color: #e5e7eb; }

    /* ===== Navigate button ===== */
    .btn-navigate {
        margin-top: 0.75rem;
        width: 100%;
        background: #eff6ff;
        color: #2563eb;
        font-weight: 700;
        padding: 0.625rem 1rem;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.875rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        text-decoration: none;
        transition: background 0.15s;
    }
    .btn-navigate:hover { background: #dbeafe; }
    .dark .btn-navigate { background: #172554; color: #60a5fa; }
    .dark .btn-navigate:hover { background: #1e3a8a; }

    /* ===== Order items ===== */
    .item-row {
        display: flex; justify-content: space-between; align-items: center;
        background: #f9fafb;
        padding: 0.625rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    .dark .item-row { background: rgba(31,41,55,0.5); }
    .item-name { font-weight: 500; color: #111827; }
    .dark .item-name { color: #ffffff; }
    .item-qty-badge {
        font-size: 0.75rem; font-weight: 700;
        background: #e0e7ff; color: #4f46e5;
        padding: 0.125rem 0.625rem;
        border-radius: 9999px;
    }
    .dark .item-qty-badge { background: #1e1b4b; color: #818cf8; }

    /* ===== Notes ===== */
    .notes-text { font-size: 0.875rem; color: #374151; margin-top: 0.25rem; font-style: italic; }
    .dark .notes-text { color: #d1d5db; }

    /* ===== Action buttons ===== */
    .action-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .btn-fail {
        background: #fef2f2; color: #dc2626;
        font-weight: 700; padding: 0.875rem 1rem;
        border-radius: 0.75rem; text-align: center; font-size: 0.875rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: none; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-fail:hover { background: #fee2e2; }
    .dark .btn-fail { background: #450a0a; color: #f87171; }
    .dark .btn-fail:hover { background: #7f1d1d; }

    .btn-deliver {
        background: #16a34a; color: #ffffff;
        font-weight: 700; padding: 0.875rem 1rem;
        border-radius: 0.75rem; text-align: center; font-size: 0.875rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: none; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-deliver:hover { background: #15803d; }

    /* ===== Modal ===== */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(3,7,18,0.7);
        backdrop-filter: blur(4px);
        z-index: 50;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem;
    }
    .modal-box {
        background: #ffffff;
        border-radius: 1rem;
        max-width: 28rem; width: 100%;
        padding: 1.5rem;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    .dark .modal-box { background: #111827; }
    .modal-title { font-size: 1.125rem; font-weight: 700; color: #111827; }
    .dark .modal-title { color: #ffffff; }

    .compliance-box { background: #f9fafb; padding: 1rem; border-radius: 0.75rem; }
    .dark .compliance-box { background: #1f2937; }
    .checkbox-label { display: flex; align-items: center; gap: 0.75rem; cursor: pointer; }
    .checkbox-input { width: 1.25rem; height: 1.25rem; border-radius: 0.25rem; border: 1px solid #d1d5db; accent-color: #4f46e5; }
    .dark .checkbox-input { border-color: #374151; }
    .checkbox-text { font-size: 0.875rem; font-weight: 600; color: #374151; }
    .dark .checkbox-text { color: #d1d5db; }

    .form-input {
        display: block; width: 100%;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #111827;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    .form-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.2); }
    .dark .form-input { border-color: #374151; background: #111827; color: #ffffff; }

    .file-input {
        display: block; width: 100%;
        font-size: 0.75rem; color: #6b7280;
    }
    .file-input::file-selector-button {
        margin-right: 1rem; padding: 0.5rem 1rem;
        border-radius: 0.5rem; border: none;
        font-size: 0.75rem; font-weight: 600;
        background: #eef2ff; color: #4338ca;
        cursor: pointer;
    }
    .dark .file-input::file-selector-button { background: #1e1b4b; color: #818cf8; }
    .upload-status { font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem; }

    .modal-actions { display: flex; gap: 0.75rem; padding-top: 0.5rem; }
    .btn-cancel {
        width: 50%;
        background: #f3f4f6; color: #374151;
        font-weight: 700; padding: 0.625rem 1rem;
        border-radius: 0.75rem; font-size: 0.875rem;
        border: none; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-cancel:hover { background: #e5e7eb; }
    .dark .btn-cancel { background: #1f2937; color: #d1d5db; }
    .dark .btn-cancel:hover { background: #374151; }

    .btn-submit {
        width: 50%;
        background: #16a34a; color: #ffffff;
        font-weight: 700; padding: 0.625rem 1rem;
        border-radius: 0.75rem; font-size: 0.875rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: none; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-submit:hover { background: #15803d; }

    .btn-confirm-fail {
        width: 50%;
        background: #dc2626; color: #ffffff;
        font-weight: 700; padding: 0.625rem 1rem;
        border-radius: 0.75rem; font-size: 0.875rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: none; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-confirm-fail:hover { background: #b91c1c; }

    .error-text { font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem; display: block; }
</style>

<x-filament-panels::page>
    <div class="app-container">
        @if (!$activeSession)
            <!-- Start Session Screen -->
            <div class="card card-center">
                <div class="icon-circle icon-circle-blue">
                    <svg width="32" height="32" style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <h2 class="card-title">Start Delivery Shift</h2>
                <p class="card-subtitle">Select an assigned delivery session below to start your route guidance and compliance logging.</p>

                @if (count($this->availableSessions) > 0)
                    <div class="stack-4">
                        @foreach ($this->availableSessions as $session)
                            <button wire:click="startSession({{ $session->id }})" class="btn-session">
                                <span class="text-left">
                                    <span class="session-date">{{ $session->delivery_date->format('l, d M') }}</span>
                                    <span class="session-time">{{ $session->timeSlot?->start_time }} - {{ $session->timeSlot?->end_time }}</span>
                                </span>
                                <span class="session-badge">Start →</span>
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        No pending delivery sessions scheduled. Check back later or create one in the admin panel.
                    </div>
                @endif
            </div>
        @elseif (!$currentStop)
            <!-- Active Session, All Stops Completed Screen -->
            <div class="card card-center">
                <div class="icon-circle icon-circle-success">
                    <svg width="32" height="32" style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="card-title">All Stops Completed!</h2>
                <p class="card-subtitle">You have completed all scheduled deliveries for this session. Complete the session to finalize your shift logs.</p>

                <button wire:click="completeSession" class="btn-success-full">
                    Complete Session & Log Off
                </button>
            </div>
        @else
            <!-- Active Delivery Stop Detail Card -->
            <div class="stack-4">
                <!-- Header / Progress bar -->
                <div class="stop-header">
                    <span class="stop-number">
                        Stop #{{ $currentStop->stop_sequence }}
                    </span>
                    <span class="stop-eta-label">
                        ETA: <span class="stop-eta-value">{{ $currentStop->eta ?? 'N/A' }}</span>
                    </span>
                </div>

                <!-- Customer info card -->
                <div class="detail-card stack-4">
                    <div>
                        <div class="field-label">Customer</div>
                        <div class="customer-name">{{ $currentStop->order?->customer_name }}</div>
                        <a href="tel:{{ $currentStop->order?->customer_phone }}" class="phone-link">
                            <svg class="icon-xs" width="16" height="16" style="width: 16px; height: 16px; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $currentStop->order?->customer_phone }}
                        </a>
                    </div>

                    <hr class="divider"/>

                    <div>
                        <div class="field-label">Delivery Address</div>
                        <div class="address-text">
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
                        <a href="{{ $navUrl }}" target="_blank" class="btn-navigate">
                            <svg class="icon-md" width="20" height="20" style="width: 20px; height: 20px; display: inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Navigate with Google Maps
                        </a>
                    </div>

                    <hr class="divider"/>

                    <!-- Items List -->
                    <div>
                        <div class="field-label mb-2">Order Items</div>
                        <ul class="stack-2">
                            @foreach ($currentStop->order?->items ?? [] as $item)
                                <li class="item-row">
                                    <span class="item-name">{{ $item->product_name ?? 'Product' }}</span>
                                    <span class="item-qty-badge">
                                        Qty: {{ $item->quantity }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($currentStop->order?->delivery_notes)
                        <hr class="divider"/>
                        <div>
                            <div class="field-label">Delivery Notes</div>
                            <p class="notes-text">
                                "{{ $currentStop->order?->delivery_notes }}"
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Primary Action Buttons -->
                <div class="action-grid">
                    <button wire:click="openFailureModal" class="btn-fail">
                        Mark Failed
                    </button>
                    
                    <button wire:click="openDeliveryModal" class="btn-deliver">
                        Mark Delivered
                    </button>
                </div>
            </div>
        @endif

        <!-- Mark Delivered Modal Form -->
        @if ($showDeliveryModal)
            <div class="modal-overlay">
                <div class="modal-box stack-4">
                    <h3 class="modal-title">Confirm Delivery</h3>

                    <!-- Compliance Section -->
                    <div class="compliance-box stack-3">
                        <label class="checkbox-label">
                            <input type="checkbox" wire:model.live="tempComplianceEnabled" class="checkbox-input">
                            <span class="checkbox-text">Enable Temp Compliance</span>
                        </label>

                        @if ($tempComplianceEnabled)
                            <div class="stack-3 pt-2">
                                <div>
                                    <label class="field-label-form">Temperature Reading (°C)</label>
                                    <input type="number" step="0.1" wire:model="temperatureReading" class="form-input">
                                </div>

                                <div>
                                    <label class="field-label-form">Thermometer Photo</label>
                                    <input type="file" accept="image/*" capture="environment" wire:model="thermometerPhoto" class="file-input">
                                    <div wire:loading wire:target="thermometerPhoto" class="upload-status">Uploading image...</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="field-label-form">Delivery Notes (Optional)</label>
                        <textarea wire:model="deliveryNotes" rows="2" class="form-input"></textarea>
                    </div>

                    <div class="modal-actions">
                        <button type="button" wire:click="$set('showDeliveryModal', false)" class="btn-cancel">
                            Cancel
                        </button>
                        
                        <button type="button" onclick="getLocationAndSubmit()" class="btn-submit">
                            Submit Delivery
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Mark Failed Modal Form -->
        @if ($showFailureModal)
            <div class="modal-overlay">
                <div class="modal-box stack-4">
                    <h3 class="modal-title">Log Delivery Failure</h3>

                    <div>
                        <label class="field-label-form">Reason for Failure</label>
                        <select wire:model="failureReason" class="form-input">
                            <option value="">-- Select Reason --</option>
                            <option value="Customer not home">Customer not home</option>
                            <option value="Incorrect address details">Incorrect address details</option>
                            <option value="Refused delivery">Refused delivery</option>
                            <option value="Safety/Access issue">Safety/Access issue</option>
                            <option value="Other / Damaged parcel">Other / Damaged parcel</option>
                        </select>
                        @error('failureReason') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="modal-actions">
                        <button type="button" wire:click="$set('showFailureModal', false)" class="btn-cancel">
                            Cancel
                        </button>
                        
                        <button type="button" wire:click="submitDeliveryFailure" class="btn-confirm-fail">
                            Confirm Failure
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Leaflet (loaded once, regardless of Livewire re-renders) -->
    @once
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endonce

    <!-- GPS capture helper (loaded once, reliably re-initializes via @script regardless of modal state) -->
    @once
        @script
        <script>
            window.getLocationAndSubmit = function () {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            $wire.set('gpsLatitude', position.coords.latitude);
                            $wire.set('gpsLongitude', position.coords.longitude);
                            $wire.call('submitDeliverySuccess');
                        },
                        (error) => {
                            console.warn('Geolocation capture failed: ', error);
                            // Fallback without GPS
                            $wire.call('submitDeliverySuccess');
                        },
                        { timeout: 5000 }
                    );
                } else {
                    // Fallback if not supported
                    $wire.call('submitDeliverySuccess');
                }
            }
        </script>
        @endscript
    @endonce
</x-filament-panels::page>