## Important

This is a reimplementation of the existing Delivery Session map.

Do not rebuild the page or the route optimisation workflow.

Only replace the route rendering mechanism while preserving all existing functionality and UI where possible.

The existing session creation, optimization logic, and stored route data should remain unchanged.


---

# Custom Route Markers

Do not use the default markers rendered by Google Maps.

Configure the DirectionsRenderer with

```javascript
suppressMarkers: true
```

This allows the application to render custom markers while still using Google's calculated driving route.

---

## Warehouse Marker

Display a custom green marker for the warehouse.

Label

```
Warehouse
```

This marker represents the starting point of the delivery route.

---

## Delivery Markers

Render custom numbered markers for every delivery stop.

Example

```
Warehouse

↓

①

↓

②

↓

③

↓

④
```

The numbers must represent the optimized delivery sequence stored in the delivery session.

Do NOT display

- Order ID
- Database ID
- Customer ID

The numbering should exactly match the delivery order.

---

## Marker Styling

Recommended colours

```
Warehouse
Green

Delivery Stops
Blue

Completed Stops (Future)
Green with checkmark

Current Stop (Future)
Orange
```

The completed/current states do not need to be implemented now, but the marker implementation should be flexible enough to support them later.

---

## Marker Information Window

Clicking a delivery marker should display

```
Stop Number

Order Number

Customer Name

Delivery Address

View Order
```

Clicking the warehouse marker should display

```
Warehouse Name

Warehouse Address
```

---

# Route Rendering

The DirectionsRenderer should only be responsible for rendering Google's calculated driving route.

The application is responsible for rendering all markers.

Responsibilities

DirectionsRenderer

- Calculate road geometry
- Render driving path

Application

- Warehouse marker
- Numbered delivery markers
- Information windows
- Marker interactions

This provides complete control over the appearance of the map while still leveraging Google's routing engine.

---

# Future Compatibility

The custom marker implementation should be designed so that future enhancements can be added without changing the rendering architecture.

Potential future enhancements include

- Completed delivery markers
- Current active stop indicator
- Failed delivery indicator
- Delayed delivery indicator
- Driver live location
- Clustered markers for dense delivery areas

These features are out of scope for this implementation but should not require replacing the existing marker implementation.

---

# Expected Result

The final map should display:

- Google's actual driving route following roads
- A custom green warehouse marker
- Custom numbered delivery markers matching the optimized stop order
- Interactive information windows for each stop
- Automatic map bounds fitting
- A clean, professional dispatch view suitable for operational use