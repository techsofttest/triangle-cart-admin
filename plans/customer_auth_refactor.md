# Customer Authentication, Profile & Checkout State Refactoring

## Objective

Refactor customer authentication, profile visibility, address handling, and checkout state management to properly separate:

* Guest Users
* Authenticated Customers
* Saved Addresses
* Temporary Checkout Addresses

The system must never expose customer account data to unauthenticated users.

---

# Current Issues

## Issue 1

Saved addresses remain visible after logout.

### Expected Behavior

After logout:

* All customer profile data must be removed from frontend state.
* Saved addresses must disappear immediately.
* Checkout must switch to Guest Checkout mode.

---

## Issue 2

Manual address entry is unavailable.

### Expected Behavior

Every checkout must support:

* Saved Address Selection (authenticated users)
* New Address Creation
* Temporary Address Entry (without saving)

---

## Issue 3

Profile dropdown is visible when not authenticated.

### Expected Behavior

Profile menu should only appear when customer authentication is active.

Guests should see:

```text
Login
Register
```

Authenticated customers should see:

```text
My Account
Orders
Addresses
Wishlist
Logout
```

---

# Authentication State Management

Create a single source of truth.

Example:

```typescript
customerAuthStore
```

or

```typescript
AuthContext
```

State:

```typescript
isAuthenticated

customer

addresses

wishlist

orders
```

---

# Logout Behavior

On logout:

Clear:

```typescript
customer

addresses

wishlist

orders

tokens

session
```

Remove:

```typescript
localStorage
sessionStorage
cookies
```

where applicable.

Refresh customer state.

---

# Header Refactoring

## Guest State

Display:

```text
Login

Cart
```

Do not display:

```text
Profile Dropdown
Orders
Addresses
Wishlist
```

---

## Authenticated State

Display:

```text
Profile Dropdown

Orders

Wishlist

Logout
```

---

# Checkout Address Modes

Checkout must support three address modes.

---

## Mode 1 - Saved Address

Authenticated customers only.

Display:

```text
○ Home

○ Work

○ Parents
```

Customer selects existing address.

---

## Mode 2 - New Saved Address

Authenticated customers only.

Display:

```text
+ Add New Address
```

Customer creates address.

Option:

```text
Save for future use
```

enabled by default.

Address saved to account.

---

## Mode 3 - Temporary Address

Available for:

* Guests
* Logged-in customers

Display:

```text
Use Different Address
```

or

```text
Deliver to a New Address
```

Customer enters address manually.

Address is used for current order only.

Address is NOT saved to account.

---

# Address Storage Rules

## Saved Address

Stored in:

```text
customer_addresses
```

Associated with customer.

Visible in profile.

---

## Temporary Address

Never stored in:

```text
customer_addresses
```

Only stored inside:

```text
checkout session

and

order snapshot
```

after order creation.

---

# Checkout Flow

## Guest User

Steps:

1. Cart
2. Address Entry
3. Delivery Validation
4. Payment
5. Order Creation

No account required.

---

## Logged-In Customer

Steps:

1. Cart
2. Select Saved Address OR Enter New Address
3. Delivery Validation
4. Payment
5. Order Creation

---

# API Refactoring

## Current Customer Endpoint

Must require authentication.

Example:

```http
GET /api/customer/profile
```

Unauthorized response:

```http
401 Unauthorized
```

---

## Addresses Endpoint

Must require authentication.

```http
GET /api/customer/addresses
```

Guests must never receive address data.

---

# Frontend Address Loading Rules

Current behavior likely:

```text
Load addresses on page load
```

This is incorrect.

Replace with:

```text
Load addresses only after successful authentication.
```

---

# Order Creation Rules

Regardless of address source:

* Saved Address
* New Saved Address
* Temporary Address

Always create:

```text
Order Address Snapshot
```

inside the order.

Orders must never depend on customer_addresses after placement.

---

# Customer Dashboard Refactoring

Dashboard visible only when authenticated.

Modules:

```text
Dashboard

Orders

Addresses

Wishlist

Account Settings

Logout
```

Guests should never access dashboard routes.

Redirect guests to:

```text
Login
```

---

# Acceptance Criteria

1. Logout clears customer state completely.
2. Saved addresses disappear after logout.
3. Profile dropdown only appears for authenticated customers.
4. Guests can place orders.
5. Guests can enter addresses manually.
6. Logged-in users can select saved addresses.
7. Logged-in users can enter temporary addresses without saving.
8. Customer addresses API requires authentication.
9. Orders store address snapshots.
10. Checkout works correctly for both guests and authenticated customers.
