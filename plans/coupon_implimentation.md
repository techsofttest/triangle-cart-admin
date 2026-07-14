# Coupon System Implementation Plan

**Project:** Triangle Cart
**Backend:** Laravel 12 + Filament 5.2
**Frontend:** Next.js (Headless Storefront)

---

# Objective

Implement a simple, scalable coupon system that supports the most common promotional campaigns while keeping the database and business logic clean.

The current project already contains a `tc_coupons` table and basic coupon functionality. The implementation should **refactor and extend the existing functionality instead of rebuilding it**, preserving compatibility with the existing admin panel wherever possible.

---

# Existing Functionality Review

Before beginning development, review the current implementation and identify what already exists.

Items likely already implemented:

* Coupon management in Filament
* Coupon code
* Coupon amount
* Coupon type
* Start date
* End date

These should be **refactored** where necessary rather than duplicated.

---

# Coupon Features

## General Information

| Field       | Type    | Notes                       |
| ----------- | ------- | --------------------------- |
| Coupon Code | String  | Must be unique              |
| Coupon Name | String  | Internal/admin display name |
| Active      | Boolean | Enable or disable coupon    |

---

## Discount

### Fixed Amount

Example:

```
SAVE10

$10 OFF
```

---

### Percentage

Example

```
SAVE20

20% OFF
```

---

## Validity

| Field      | Description           |
| ---------- | --------------------- |
| Start Date | Coupon becomes active |
| End Date   | Coupon expires        |

Coupons outside this date range cannot be redeemed.

---

## Restrictions

### Minimum Order Amount

Customer must spend at least the configured amount before the coupon can be applied.

Example

```
Minimum Order

$50
```

---

### Maximum Discount

Only applies to percentage coupons.

Example

```
20% OFF

Maximum Discount

$30
```

If the calculated percentage exceeds the maximum discount, the maximum discount value is used.

---

### Global Usage Limit

Maximum number of times the coupon may be redeemed across the entire store.

Example

```
1000 Uses
```

Once reached, the coupon becomes unavailable.

---

### Per Customer Usage Limit

Instead of multiple boolean options such as:

* One Time
* Unlimited
* Two Times
* Three Times

use a single integer field.

```
customer_usage_limit
```

Meaning:

```
0 = Unlimited

1 = One Use

2 = Two Uses

5 = Five Uses
```

This is significantly cleaner, easier to maintain, and allows future flexibility without additional database changes.

---

### First Order Only

Boolean

```
Yes

No
```

If enabled, only customers with **no previous successful orders** may redeem the coupon.

---

# Database Changes

## Refactor Existing tc_coupons Table

Retain the existing table and extend it where required.

Recommended fields:

| Field                | Purpose                                 |
| -------------------- | --------------------------------------- |
| id                   | Primary Key                             |
| coupon_code          | Coupon code                             |
| coupon_name          | Internal name                           |
| coupon_type          | Fixed / Percentage                      |
| coupon_amount        | Discount value                          |
| start_date           | Valid from                              |
| end_date             | Valid until                             |
| minimum_order_amount | Minimum cart value                      |
| maximum_discount     | Maximum discount for percentage coupons |
| global_usage_limit   | Total allowed redemptions               |
| customer_usage_limit | Per customer limit (0 = Unlimited)      |
| first_order_only     | Boolean                                 |
| active               | Boolean                                 |
| created_at           | Timestamp                               |
| updated_at           | Timestamp                               |

Rename existing fields where appropriate instead of creating duplicate columns.

---

# New Table

## tc_coupon_usages

Purpose:

Maintain a complete redemption history for every coupon used.

| Field           | Purpose                                   |
| --------------- | ----------------------------------------- |
| id              | Primary Key                               |
| coupon_id       | Coupon                                    |
| customer_id     | Customer (nullable for guest if required) |
| order_id        | Order in which coupon was redeemed        |
| discount_amount | Actual discount applied                   |
| created_at      | Redemption timestamp                      |

---

# Benefits of Redemption History

This table enables:

* Total coupon usage counting
* Per-customer usage validation
* Coupon redemption history
* Marketing reports
* Duplicate redemption prevention
* Future analytics and reporting

---

# Coupon Validation Flow

When a customer enters a coupon:

1. Coupon exists.
2. Coupon is active.
3. Current date is within the validity period.
4. Cart subtotal meets the minimum order amount.
5. Global usage limit has not been reached.
6. Customer usage limit has not been exceeded.
7. If First Order Only is enabled, verify the customer has no previous successful orders.
8. Calculate discount.
9. Apply maximum discount (percentage coupons only).
10. Return the updated cart totals.

Coupon validation should occur both:

* When the coupon is entered.
* Immediately before order creation/payment to prevent race conditions.

---

# Order Integration

Once an order is successfully completed:

* Save the coupon code on the order.
* Save the discount amount applied.
* Insert a record into `tc_coupon_usages`.
* Update coupon usage statistics if cached.

If payment fails or the order is cancelled before completion, no redemption record should be created.

---

# Backend (Laravel)

## Refactor Existing Coupon Module

Review the existing implementation and reuse wherever possible.

Tasks:

* Refactor Coupon model
* Refactor Coupon migration if required
* Add missing database fields
* Create CouponUsage model
* Create CouponUsage migration
* Implement coupon validation service
* Prevent duplicate coupon redemption
* Validate first-order restrictions
* Validate usage limits
* Apply discount calculations
* Store coupon usage after successful payment
* Return clear validation messages for invalid coupons

Avoid replacing working functionality unnecessarily; extend the existing implementation.

---

# Frontend (Next.js)

Update the checkout flow to integrate with the refactored backend.

Tasks:

* Coupon input field
* Apply Coupon button
* Remove Coupon button
* Display validation errors
* Display applied discount
* Update order summary dynamically
* Recalculate totals without refreshing the page
* Prevent multiple coupon applications
* Persist applied coupon during checkout
* Clear coupon when cart contents become invalid (if applicable)

No business logic should be duplicated in the frontend. Validation and discount calculations must remain server-side.

---

# Admin Panel (Filament)

Review the existing Coupon Resource and refactor where necessary.

Ensure support for:

* Coupon Code
* Coupon Name
* Active Status
* Fixed Amount
* Percentage
* Start Date
* End Date
* Minimum Order Amount
* Maximum Discount
* Global Usage Limit
* Customer Usage Limit
* First Order Only

Recommended enhancements:

* Display total redemptions
* Display remaining global uses
* Display coupon status (Active, Scheduled, Expired, Disabled)
* Add a relation manager or page to view coupon redemption history

Reuse existing resources wherever possible instead of creating new ones.

---

# Future Enhancements (Out of Scope)

The following features are intentionally excluded from this implementation but can be added later without changing the overall architecture:

* Free Shipping Coupons
* Product-specific Coupons
* Category-specific Coupons
* Brand-specific Coupons
* Buy X Get Y Promotions
* Automatic Promotions
* Customer Group Coupons
* Referral Coupons
* Birthday Coupons
* Coupon Stacking
* Coupon Scheduling by Time
* Auto-generated Unique Coupon Codes

---

# Expected Outcome

The completed implementation will provide a lightweight, maintainable, and scalable coupon system that integrates seamlessly with the existing Laravel 12 + Filament 5.2 backend and Next.js storefront.

The implementation should prioritize refactoring and extending the existing coupon module wherever possible, minimizing code duplication while ensuring all validation and discount calculations remain centralized in the backend.
