# Checkout Improvements & Direct Delivery Implementation

## Objective

Enhance the checkout experience by implementing Google Maps Address Autocomplete as the mandatory address selection method, integrating Direct Delivery logic, improving delivery slot selection, and resolving the current checkout issues.

---

# 1. Google Maps Address Autocomplete (Mandatory)

## Address Selection

The delivery address must be selected using Google Maps Address Autocomplete.

Customers will not be permitted to manually enter the complete address.

### Checkout Flow

1. Customer searches for the delivery address using Google Autocomplete.
2. Customer selects an address from Google's suggestions.
3. Address fields are automatically populated.
4. Customer clicks **Confirm Address**.
5. Shipping methods, delivery availability, and delivery slots are calculated.

---

## Editable Fields

| Field                               | Editable         |
| ----------------------------------- | ---------------- |
| Phone Number                        | Yes              |
| Street Address Line 1               | No (Auto-filled) |
| Apartment / Unit / Suite / Building | Yes              |
| Suburb                              | No               |
| City                                | No               |
| State                               | No               |
| Postcode                            | No               |
| Country                             | No               |
| Delivery Instructions               | Yes              |

Hidden fields:

* Google Place ID
* Latitude
* Longitude
* Formatted Address

These values will be used for route optimisation and delivery calculations.

---

# 2. Fix Google Autocomplete After Editing Address

## Current Issue

After the customer:

* Selects an address
* Clicks **Confirm Address**
* Returns to edit the address

Google Autocomplete stops working.

Symptoms:

* No suggestions displayed.
* No API requests sent to Google.
* Autocomplete component is not reinitialised.

---

## Required Fix

When the customer edits or changes the delivery address:

* Properly destroy/reset the existing Google Autocomplete instance.
* Re-initialise the Google Autocomplete component.
* Ensure API requests resume normally.
* Allow unlimited address changes before checkout.

The component should behave exactly like a fresh page load every time the address is edited.

---

# 3. Online Payment Only

## Current Behaviour

Checkout currently displays:

* Cash on Delivery (COD)
* Online Payment

---

## Required Behaviour

The store currently accepts **Online Payments only**.

Therefore:

* Hide the Cash on Delivery payment option.
* Remove any COD-related UI and logic.
* Make Online Payment the default and only payment method.
* Change the checkout button text from:

```
Place Order
```

to

```
Pay Now
```

---

# 4. Delivery Slot Selection

## Current Behaviour

Checkout currently displays hardcoded (dummy) delivery slots.

---

## Required Behaviour

Delivery slots must be loaded dynamically from the database.

Use the existing tables:

* `tc_delivery_dates`
* `tc_time_slots`

No dummy data should be displayed.

---

## Slot Availability Rules

Only display slots that:

* Are active.
* Belong to the selected delivery date.
* Have available capacity (if capacity management exists).
* Are enabled by the administrator.

If no slots are available:

Display:

```
No delivery slots available for the selected date.
```

The customer must not be allowed to proceed without selecting a valid delivery slot.

---

# 5. Next-Day Delivery Logic

For Direct Delivery orders:

Customers may only select delivery slots after the configured minimum delivery lead time.

Current requirement:

* Minimum lead time: 24 hours.

Example:

| Order Time     | Earliest Delivery       |
| -------------- | ----------------------- |
| Monday 9:00 AM | Tuesday 9:00 AM onwards |
| Monday 5:00 PM | Tuesday 5:00 PM onwards |

Only qualifying delivery dates and slots should be displayed.

---

## Configurable Lead Time

The minimum delivery lead time must **not** be hardcoded.

Create a configurable constant or configuration value.

Example:

```
DELIVERY_MINIMUM_LEAD_HOURS = 24
```

or

```
config('delivery.minimum_lead_hours')
```

Changing this single value should automatically affect delivery date calculations throughout the application.

---

# 6. Direct Delivery Validation

When the customer confirms the delivery address:

1. Validate the postcode against the Direct Delivery master table.
2. Determine whether Direct Delivery is available.
3. Calculate shipping charges.
4. Determine free shipping eligibility.
5. Display only valid delivery dates.
6. Load available delivery slots.

---

# 7. Route Optimisation Requirements

Store the following information with every order:

* Google Place ID
* Latitude
* Longitude
* Selected Delivery Date
* Selected Delivery Slot

The Admin Dashboard will use these values to:

* Generate delivery routes.
* Optimise delivery order.
* Display graphical route maps.
* Integrate with Google Maps navigation.

Coordinates should always be used for routing instead of text addresses.

---

# 8. Expected Checkout Flow

1. Customer searches for their address using Google Maps Autocomplete.
2. Customer selects an address.
3. Customer clicks **Confirm Address**.
4. System validates the postcode.
5. System determines the available delivery method.
6. Shipping charges are calculated.
7. Available delivery dates are loaded.
8. Available delivery slots are loaded from the database.
9. Customer selects a delivery slot.
10. Customer proceeds to payment.
11. Customer clicks **Pay Now** to complete the order.

---

# 9. Acceptance Criteria

* Google Address Autocomplete is mandatory.
* Google Autocomplete continues to function correctly after editing the address.
* Cash on Delivery is completely removed.
* Online Payment is the only available payment method.
* Checkout button displays **Pay Now**.
* Delivery slots are loaded dynamically from the database.
* Dummy delivery slots are removed.
* If no slots are available, an appropriate message is displayed.
* Only valid next-day delivery slots are shown.
* Minimum delivery lead time is configurable through a single configuration value.
* Direct Delivery eligibility is determined using the postcode master table.
* Latitude, longitude, and Google Place ID are stored for every order to support future delivery route optimisation.
