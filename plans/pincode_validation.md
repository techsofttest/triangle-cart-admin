# Delivery Serviceability & Fulfillment Module

## Objective

Implement a delivery eligibility system that supports both Direct Delivery and Courier Delivery based on product fulfillment rules and customer postcode.

---

## Business Rules

### Direct Delivery Products

These products:

* Can only be delivered to approved Direct Delivery postcodes.
* Cannot be shipped via courier.
* Must be blocked from checkout if the customer's postcode is not serviceable.

Examples:

* Fresh produce
* Dairy
* Frozen goods
* Bakery items

---

### Courier Eligible Products

These products:

* Can be shipped anywhere via courier.
* Can also be delivered via Direct Delivery if the customer's postcode is within a Direct Delivery service area.

Examples:

* Packaged groceries
* Household items
* Cleaning products

---

## Delivery Decision Matrix

| Product Type             | Direct Delivery Postcode | Other Postcodes |
| ------------------------ | ------------------------ | --------------- |
| Direct Delivery Product  | Direct Delivery          | Not Available   |
| Courier Eligible Product | Direct Delivery          | Courier         |

---

## Database Changes

### products

Add:

```sql
requires_direct_delivery BOOLEAN DEFAULT FALSE
allows_courier BOOLEAN DEFAULT TRUE
```

---

### delivery_postcodes

Create table:

```sql
id
postcode
warehouse_id
delivery_fee
free_shipping_threshold
is_active
created_at
updated_at
```

Purpose:

* Stores all postcodes eligible for Direct Delivery.

---

## Backend Components

Create:

### Models

* DeliveryPostcode

### Services

* DeliveryEligibilityService

Responsibilities:

* Validate cart eligibility
* Determine shipping method
* Determine delivery type
* Return delivery availability messages

---

## Service API

### validateCart()

Input:

```php
customer_postcode
cart_items
```

Output:

```php
[
    'valid' => true,
    'delivery_type' => 'direct'
]
```

or

```php
[
    'valid' => false,
    'message' => 'Fresh Milk is not available for delivery to this postcode.'
]
```

---

### determineShippingMethod()

Returns:

```php
direct
```

or

```php
courier
```

---

## Checkout Logic

### Step 1

Retrieve customer postcode.

### Step 2

Check if postcode exists in delivery_postcodes.

### Step 3

Validate all products in cart.

Rules:

* Direct Delivery Product + Non-Serviceable Postcode = Block Checkout
* Courier Product + Non-Serviceable Postcode = Courier Delivery
* Any Product + Serviceable Postcode = Direct Delivery

---

## API Endpoint

### Check Delivery Eligibility

```http
POST /api/delivery/check
```

Request:

```json
{
  "postcode": "3000",
  "cart": [...]
}
```

Response:

```json
{
  "valid": true,
  "delivery_type": "direct"
}
```

or

```json
{
  "valid": false,
  "message": "Some products are not available for delivery to this postcode."
}
```

---

## Filament Admin

Create Delivery Postcodes Resource.

Features:

* List postcodes
* Add postcode
* Edit postcode
* Delete postcode
* Bulk CSV import
* Assign warehouse
* Enable/disable postcode

---

## Future Compatibility

Design all code to support:

* Multiple warehouses
* Distance-based validation
* Delivery slots
* Route optimization
* Driver assignment
* Navigation integration

No implementation required yet.

---

## Acceptance Criteria

1. Customer enters postcode.
2. System determines serviceability.
3. Direct Delivery products are blocked outside serviceable postcodes.
4. Courier products remain purchasable everywhere.
5. Serviceable postcodes receive Direct Delivery.
6. Checkout displays correct delivery method.
7. Admin can manage serviceable postcodes from Filament.
8. Architecture must support future warehouse and routing features.
