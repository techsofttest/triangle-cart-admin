# Checkout, Address Management & Order Placement Module

## Objective

Implement a unified checkout flow supporting:

* Guest Checkout
* Customer Login (OTP)
* Saved Addresses
* New Address Entry
* Google Places Autocomplete
* Delivery Eligibility Validation
* Order Placement
* Admin Order Management

The checkout flow must support both Direct Delivery and Courier Delivery business rules.

---

# Customer Types

## Guest Customer

Guest users should be able to:

* Add products to cart
* Enter delivery address
* Use Google Address Autocomplete
* Place order
* Complete payment

Guest users should not be required to create an account.

---

## Logged-in Customer

Logged-in customers should be able to:

* Select saved address
* Add new address
* Edit existing address
* Set default shipping address
* Place order
* View order history

---

# Checkout Flow

## Step 1 - Cart Validation

Before checkout:

Validate:

* Stock availability
* Product status
* Product pricing
* Delivery eligibility

Return validation errors before proceeding.

---

## Step 2 - Customer Identification

Display:

### Existing Customer

```text
Continue with OTP Login
```

### Guest Customer

```text
Continue as Guest
```

No forced registration.

---

## Step 3 - Delivery Address

### Logged-in Customer

Show:

```text
Saved Addresses

○ Home
○ Work
○ Parents

+ Add New Address
```

Default shipping address should be pre-selected.

---

### Guest Customer

Display address form directly.

No address storage required.

---

# Address Form

Fields:

```text
Contact Name
Phone

Address Line 1
Address Line 2

Suburb
City
State
Postcode

Country

Delivery Notes
```

---

# Google Places Autocomplete

Integrate:

* Google Places Autocomplete API
* Place Details API

When address selected:

Auto-populate:

```text
Address Line 1
Suburb
City
State
Postcode
Country

Latitude
Longitude

Google Place ID
```

Users must still be allowed to edit fields manually.

---

# Delivery Validation

After postcode is available:

Call DeliveryEligibilityService.

Input:

```text
Postcode
Latitude
Longitude
Cart Items
```

Output:

```text
Serviceable
Delivery Type
Warehouse
Shipping Cost
Available Slots
```

---

# Shipping Method Resolution

Rules:

### Direct Delivery Area

If postcode is serviceable:

* Direct Delivery products allowed
* Courier products allowed
* Entire order delivered via Direct Delivery

---

### Non-Serviceable Area

If cart contains only courier-eligible products:

* Courier Delivery

If cart contains any direct-delivery-only products:

* Checkout blocked

Display clear error message.

---

# Delivery Slot Selection

Only display when:

```text
delivery_type = direct
```

Customer selects:

```text
9 AM - 12 PM
12 PM - 3 PM
3 PM - 6 PM
```

No slot selection for courier orders.

---

# Order Review

Display:

* Products
* Quantity
* Shipping Address
* Delivery Method
* Delivery Slot (if applicable)
* Shipping Cost
* Discounts
* Grand Total

---

# Payment

After successful payment:

Create order.

Order creation must be transactional.

Rollback if any failure occurs.

---

# Order Snapshot Storage

Never reference customer_addresses after order creation.

Store a complete snapshot inside the order.

Order fields:

```text
customer_name
customer_email
customer_phone

shipping_name
shipping_phone

shipping_address_line_1
shipping_address_line_2

shipping_suburb
shipping_city
shipping_state
shipping_postcode
shipping_country

shipping_latitude
shipping_longitude

delivery_notes
```

This ensures historical order accuracy.

---

# Order Delivery Fields

Store:

```text
delivery_type

warehouse_id

delivery_slot_id

shipping_method

shipping_cost

delivery_distance_km
```

Examples:

```text
Direct Delivery
Courier
```

---

# Guest Order Support

Guest orders must store:

```text
customer_name
customer_email
customer_phone
```

Guest users should be able to track orders using:

* Order Number
* Email Address

---

# Customer Order Support

Authenticated customers should see:

```text
My Orders
```

with:

* Order History
* Order Details
* Invoice Download
* Tracking Information

---

# Filament Admin Refactoring

## Customers

Guest customers should not appear as registered customers.

Create:

```text
Customers
Guests
```

separate views if required.

---

## Orders

Order resource must display:

### Customer

```text
Registered Customer
or
Guest Customer
```

---

### Delivery Information

```text
Delivery Type

Direct Delivery
Courier
```

---

### Address Snapshot

Display stored order address.

Do not load from customer_addresses.

---

### Delivery Slot

Display selected slot.

---

### Warehouse

Display assigned warehouse.

---

# Admin Order Workflow

Statuses:

```text
Pending

Paid

Processing

Packed

Out For Delivery

Delivered

Cancelled

Refund Requested

Refunded
```

---

# Future Compatibility

Architecture must support:

* Route optimization
* Navigation links
* Split shipments
* Multiple delivery methods

No implementation required yet.

---

# Acceptance Criteria

1. Guest checkout works.
2. OTP customer checkout works.
3. Saved addresses can be selected.
4. New addresses can be created.
5. Google Autocomplete populates address fields.
6. Delivery eligibility is validated before payment.
7. Direct Delivery and Courier rules are enforced.
8. Orders store address snapshots.
9. Filament displays historical order data correctly.
10. Architecture supports future delivery routing features.
