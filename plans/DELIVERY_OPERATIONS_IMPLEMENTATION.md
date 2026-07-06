# Delivery Operations Implementation (MVP)

> Triangle Cart -- Laravel 11 + Filament 4/5 + Next.js (Headless)

## Purpose

Implement a lightweight Delivery Operations module focused on a **single
store**, **single delivery location**, and **time-slot based
deliveries**.

### Design Goals

-   Single warehouse/store
-   No driver management
-   No vehicle management
-   Delivery Session centric
-   Google Route Optimization
-   PWA-based delivery interface
-   Optional Delivery Compliance
-   Future extensible

------------------------------------------------------------------------

# Module Structure

``` text
Delivery Operations
├── Dashboard
├── Delivery Sessions
│   ├── Orders
│   ├── Route
│   ├── Compliance
│   └── Reports
├── Exceptions
└── Reports
```

## Workflow

``` text
Customer Checkout
→ Payment
→ Picking
→ Packing
→ Ready for Dispatch
→ Delivery Session
→ Route Optimisation
→ Start Session
→ Deliveries
→ Session Completed
```

## Delivery Session

A delivery session represents:

-   Delivery Date
-   Delivery Time Slot

Status:

-   Draft
-   Preparing
-   Ready
-   In Progress
-   Completed

## Dashboard

Cards:

-   Today's Orders
-   Ready for Dispatch
-   Morning Session
-   Afternoon Session
-   Delivered
-   Pending
-   Failed

Map:

-   Store
-   Delivery locations
-   Optimized route

## Delivery Staff Workflow

``` text
Login
↓
Dashboard
↓
Start Session
↓
Current Delivery
↓
Navigate
↓
Delivered / Failed
↓
(Optional) Delivery Compliance
↓
Complete Delivery
↓
Next Delivery
↓
Session Completed
```

## Delivery Screen

Displays:

-   Customer
-   Phone
-   Address
-   Items
-   Delivery Notes

Actions:

-   Navigate
-   Delivered
-   Failed

## Delivery Compliance

Optional.

When Delivered is selected:

-   Add Delivery Compliance (toggle)

If enabled:

-   Temperature Reading (optional)
-   Thermometer Photo

Automatically capture:

-   Timestamp
-   GPS (if permitted)

No record is created if skipped.

## Database

### delivery_sessions

-   id
-   delivery_date
-   delivery_slot_id
-   status
-   started_at
-   completed_at

### delivery_session_orders

-   id
-   delivery_session_id
-   order_id
-   stop_sequence
-   eta
-   delivered_at
-   status
-   failure_reason
-   notes

### delivery_compliance_logs

-   id
-   order_id
-   delivery_session_order_id
-   thermometer_photo
-   temperature_reading
-   latitude
-   longitude
-   captured_at
-   notes

## Filament Resources

-   Delivery Dashboard
-   Delivery Sessions
-   Exceptions
-   Reports

## PWA

-   Installable
-   Camera access
-   Geolocation
-   Google Maps deep links

## Google Integration

Use Google Routes API to optimize stop order.

Navigation uses Google Maps with Place ID or coordinates.

## Business Rules

-   Only paid orders appear in sessions.
-   Orders grouped by delivery date + time slot.
-   Route optimized before session starts.
-   Deliveries follow optimized sequence.
-   Delivery Compliance is optional.
-   GPS and timestamp captured automatically when compliance is used.

## Future Scope

-   Multiple warehouses
-   Driver assignment
-   Vehicles
-   Delivery photos
-   Customer signatures
-   OTP delivery verification
-   Bluetooth thermometers
-   IoT temperature sensors
-   Live tracking
