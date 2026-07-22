# Migration Plan: Google Routes API for Route Optimisation & Delivery Session Route Preview

## Objective

Migrate the Delivery Session routing implementation from the deprecated Google Maps DirectionsService/DirectionsRenderer APIs to the modern Google Routes API.

The migration must preserve all existing business logic, delivery workflow, and route optimisation while replacing only the Google routing implementation.

This migration should also ensure that the exact same route generated during Delivery Session creation is displayed when viewing the session later.

---

# Background

The current implementation uses:

- google.maps.DirectionsService
- google.maps.DirectionsRenderer

These APIs have been deprecated by Google and are no longer recommended for new Google Cloud projects.

Google recommends migrating to:

- Routes API
- google.maps.routes.Route.computeRoutes
- AdvancedMarkerElement

The migration should future-proof the application without changing the existing workflow.

---

# Existing Workflow

```
Customer Orders

↓

Create Delivery Session

↓

Existing Route Optimisation

↓

Delivery Session Created

↓

View Session
```

---

# New Workflow

```
Customer Orders

↓

Create Delivery Session

↓

Existing Route Optimisation

↓

Optimised Stop Order

↓

Google Routes API

↓

Route Geometry

↓

Store Route

↓

Delivery Session Created

↓

View Session

↓

Render Stored Route
```

The business logic remains unchanged.

---

# Important Design Principle

The application's optimisation engine remains the source of truth.

Google must NEVER optimise the stop order.

Google should only calculate the road path between the already optimised stops.

---

# Route Optimisation

The existing optimisation process should continue exactly as it does today.

Example

```
Warehouse

↓

Order 104

↓

Order 121

↓

Order 118

↓

Order 135
```

This sequence is final.

Google must render this exact sequence.

---

# Google Routes API

Replace

```
DirectionsService
```

with

```
Routes API
```

Use

```
computeRoutes()
```

to generate

- Driving geometry
- Distance
- Duration

Do not allow Google to reorder waypoints.

---

# During Delivery Session Creation

Current

```
Optimise Orders

↓

Save Session
```

New

```
Optimise Orders

↓

Request Google Route

↓

Receive Route Geometry

↓

Store Geometry

↓

Store Statistics

↓

Save Session
```

Everything happens only once.

---

# Database Changes

## Delivery Sessions

Add

```
estimated_distance_km

estimated_duration_minutes

route_generated_at

route_polyline
```

---

### estimated_distance_km

Store total route distance.

Example

```
42.8
```

---

### estimated_duration_minutes

Store total duration.

Example

```
198
```

---

### route_generated_at

Timestamp of generation.

---

### route_polyline

Store encoded Google polyline returned by Routes API.

This avoids requesting Google again when viewing the session.

---

# Route Storage

The delivery session should permanently store

- Optimised stop order
- Encoded polyline
- Distance
- Duration

Viewing a session must never trigger route calculation.

---

# Session Details Page

Do not call Routes API.

Instead

Read

```
Stored Polyline

↓

Decode Polyline

↓

Display Map
```

This guarantees

- Faster page loads
- Lower API costs
- Consistent historical routes

---

# Map Rendering

Use

Google Maps JavaScript API

Only for rendering.

Do NOT calculate routes inside the browser.

---

# Marker Implementation

Replace

```
google.maps.Marker
```

with

```
AdvancedMarkerElement
```

---

## Warehouse

Green marker

---

## Stops

Custom numbered markers

```
1

2

3

4
```

Numbers represent delivery order.

Not Order IDs.

---

# Polyline Rendering

Decode the stored encoded polyline.

Render it using

```
google.maps.Polyline
```

The polyline follows actual roads because it was produced by Google Routes API.

---

# Statistics

Display

```
Orders

Estimated Distance

Estimated Duration

Generated At
```

Read directly from Delivery Session.

Do not calculate.

---

# Route Summary

Display

```
Warehouse

↓

Stop 1

↓

Stop 2

↓

Stop 3
```

Using stored stop sequence.

---

# API Usage

Routes API should only be called

During

```
Delivery Session Creation
```

Never

```
Viewing Session
```

---

# Route Consistency

Changing

- Customer Address

- Warehouse

- Order

must NOT change an existing delivery session.

Historical sessions remain unchanged.

---

# Performance Benefits

Old

```
Open Session

↓

Directions API

↓

Calculate Route

↓

Display
```

New

```
Open Session

↓

Read Database

↓

Decode Polyline

↓

Display
```

No network request to Google.

---

# Cost Benefits

Google Routes API

One request

Per Delivery Session

Instead of

One request

Every page load.

---

# Future Compatibility

This architecture supports

- Driver mobile app

- Navigation launch

- Live GPS

- Route replay

- Completed stop colouring

- Current stop highlighting

- ETA recalculation

without changing the stored delivery session.

---

# Existing Features That Must Continue Working

- Existing delivery optimisation
- Existing warehouse selection
- Existing staff assignment
- Existing delivery session creation
- Existing order sequencing
- Existing route optimisation algorithm
- Existing session management

Only the Google routing implementation should change.

---

# Out of Scope

Do NOT implement

- Google route optimisation
- Stop reordering
- Traffic-aware optimisation
- Dynamic rerouting
- Driver navigation
- Live GPS
- Route editing
- Manual waypoint changes

---

# AI Implementation Notes

This is a migration of the Google routing layer only.

Do not modify the application's optimisation algorithm.

Do not modify delivery session business logic.

Do not change order sequencing.

The application's optimiser determines the delivery order.

Google Routes API is used solely to generate the road-following geometry, total distance, and estimated duration.

The generated route, encoded polyline, and statistics must be stored with the Delivery Session and reused for all future views.

The Session Details page must function purely as a rendering layer, reading stored data without making any additional routing requests to Google.