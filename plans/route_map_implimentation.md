# Feature: Delivery Session Route Map Preview

## Objective

Enhance the Delivery Session module by displaying an interactive Google Maps preview immediately after a delivery session has been created and route optimisation has completed.

The objective is to provide dispatchers with a visual representation of the generated delivery route without recalculating the route each time the session is viewed.

The route optimisation engine already generates the optimal stop sequence. This feature should only visualize that result.

---

# Functional Flow

```
Create Delivery Session
        │
        ▼
Select Staff
        │
        ▼
Orders Loaded
        │
        ▼
Generate Session
        │
        ▼
Route Optimisation
        │
        ▼
Store Optimised Route
        │
        ▼
Store Route Statistics
        │
        ▼
Redirect to Session Details
        │
        ▼
Display Interactive Route Map
```

The existing route optimisation logic must remain unchanged.

---

# Objectives

The Session Details page should provide dispatchers with:

- Route overview
- Visual map
- Delivery order
- Estimated distance
- Estimated travel duration
- Warehouse starting point
- Interactive order markers

This feature is intended for operational visibility only.

---

# Database Changes

## Delivery Sessions

Add the following fields.

```php
estimated_distance_km
estimated_duration_minutes
route_generated_at
```

### estimated_distance_km

Total distance returned by the optimisation engine.

Example

```
42.80
```

---

### estimated_duration_minutes

Total estimated driving duration.

Example

```
198
```

The UI can display

```
3 hr 18 min
```

---

### route_generated_at

Timestamp indicating when the route was generated.

This allows the system to know whether the displayed route is current.

---

# Route Storage

The optimised stop order should be stored when the delivery session is created.

Do not regenerate the optimisation every time the session page is opened.

The session should contain the delivery order exactly as returned by the optimisation engine.

Example

```
Warehouse

↓

Order 102

↓

Order 118

↓

Order 143

↓

Order 107

↓

Order 125
```

The map must always display this stored order.

---

# Session Details Page

After successful session creation, redirect to

```
Delivery Session Details
```

---

## Layout

### Left Panel

Display

```
Session Number

Assigned Staff

Warehouse

Total Orders

Estimated Distance

Estimated Duration

Route Generated

Status
```

---

### Statistics Cards

Display

```
Orders

Estimated Distance

Estimated Duration

Route Generated At
```

Example

```
Orders

24
```

```
Distance

42.8 km
```

```
Duration

3 hr 18 min
```

```
Generated

17-Jul-2026 09:45 AM
```

---

# Google Maps

Use the Google Maps JavaScript API.

Do NOT implement your own route drawing algorithm.

---

## Rendering Strategy

The optimisation engine already returns the correct stop sequence.

The map should simply visualize this sequence.

Use

- Google Maps JavaScript API
- DirectionsService (when within Google waypoint limits)

If the route exceeds Google waypoint limitations,

fallback to

- Existing optimised coordinates
- Polyline rendering

The optimisation itself must NOT be recalculated.

---

# Map Contents

Display

- Warehouse marker
- Numbered delivery markers
- Driving route
- Auto zoom to fit all markers

---

## Warehouse

Green marker

Label

```
Warehouse
```

---

## Delivery Stops

Blue numbered markers

Example

```
1

2

3

4

5
```

The numbering represents the optimised route sequence.

NOT

- Order ID
- Database ID

---

## Marker Popup

Clicking a marker should display

```
Order Number

Customer Name

Address

Estimated Position

View Order
```

---

# Route Summary

Below the map display

```
Warehouse

↓

1

↓

2

↓

3

↓

...

↓

Final Stop
```

Each stop displays

```
Route Position

Order Number

Customer

Suburb
```

Example

```
1

Order #1004

John Smith

Belconnen
```

---

# Automatic Zoom

When the page loads

Automatically fit the map bounds so every stop is visible.

No manual zoom required.

---

# Existing Optimisation

No changes should be made to the optimisation engine.

Continue using the existing optimisation process.

This feature only displays its output.

---

# Performance Requirements

The session page must NOT perform route optimisation.

The page should

- Read the stored route
- Read stored statistics
- Render the map

No optimisation should occur during page load.

---

# Route Consistency

The displayed route must always match the generated delivery session.

Even if

- Customer address changes
- Warehouse changes
- New orders are created

the stored session route remains unchanged.

---

# Benefits

This approach provides

- Faster page loading
- Consistent delivery routes
- No repeated optimisation
- Lower Google API usage
- Accurate historical session records

---

# Technical Notes

Use the existing optimisation output.

Render the route using

- Google Maps DirectionsService when waypoint limits permit

Otherwise

- Render using polylines created from the stored optimised coordinates.

Do not manually compute routes.

Do not re-run optimisation.

The session page is a visualization layer only.

---

# Out of Scope

Do NOT implement

- Live driver tracking
- Driver mobile navigation
- Route editing
- Drag-and-drop stops
- Re-optimisation
- Traffic-aware recalculation
- Real-time ETA updates
- Google Maps navigation launch
- GPX export
- Route printing
- Route sharing

These can be implemented in future versions.

---

# Expected Result

After creating a delivery session, dispatchers should immediately see:

- Delivery session information
- Assigned staff
- Route statistics
- Interactive Google Map
- Warehouse starting point
- Numbered delivery stops
- Optimised driving path
- Route summary

The displayed route must always be identical to the route generated during session creation.