# TriangleCart - Staff Access Control Implementation

## Overview

Currently, the system has a single Filament administration panel used by the main administrator. The requirement is **not** to create a separate Filament panel or authentication system. Instead, staff members should log into the same admin panel while being restricted to only the modules and actions they are authorized to access.

This implementation will introduce a scalable Role-Based Access Control (RBAC) system that supports future operational modules such as delivery routing, warehouse management, and returns without requiring major architectural changes.

---

# Objectives

* Maintain a single Filament Admin Panel.
* Use the existing authentication system.
* Allow staff to log in using the same `/admin/login` page.
* Restrict navigation based on permissions.
* Restrict direct URL access to unauthorized resources.
* Restrict individual actions within resources.
* Support future operational modules without refactoring.

---

# Current Architecture

```
Laravel 11
│
├── Filament Admin
│     ├── Products
│     ├── Categories
│     ├── Orders
│     ├── Customers
│     ├── CMS
│     ├── Reports
│     ├── Settings
│     └── Users
│
└── Next.js Storefront
```

After implementation, all users will continue using the same Filament panel.

```
Admin Login

        │
        ▼

   Authentication

        │
        ▼

Permission Check

        │
        ├───────────────┐
        │               │
        ▼               ▼

 Administrator      Staff

 Full Access      Restricted Access
```

---

# Authentication

The existing authentication system will remain unchanged.

* Continue using the existing `users` table.
* Continue using the existing Filament login page.
* No additional guards.
* No additional panels.
* No duplicate user tables.

---

# Role & Permission System

Instead of relying solely on a simple `role` field, implement a proper permission-based architecture using **Spatie Laravel Permission**.

## Roles

Initially create the following roles:

* Super Admin
* Staff

Future roles may include:

* Operations Manager
* Warehouse Staff
* Delivery Staff
* Customer Support

---

# Permission Structure

Permissions should be module-based and action-based.

## Orders

```
orders.view
orders.create
orders.update
orders.delete
orders.export
orders.print
orders.assign
orders.cancel
orders.refund
```

## Products

```
products.view
products.create
products.update
products.delete
```

## Categories

```
categories.view
categories.create
categories.update
categories.delete
```

## Customers

```
customers.view
customers.update
```

## Reports

```
reports.view
```

## CMS

```
cms.view
cms.update
```

## Advertisements

```
advertisements.view
advertisements.update
```

## Settings

```
settings.manage
```

## Users

```
users.view
users.create
users.update
users.delete
```

Future modules should follow the same naming convention.

---

# Initial Staff Permissions

During the first implementation phase, the Staff role should only receive the following permissions:

```
orders.view
orders.update
orders.print
```

No additional permissions should be granted.

---

# Navigation Restrictions

Navigation should be generated dynamically based on permissions.

## Staff Navigation

```
Dashboard

Orders
```

Only these menu items should be visible.

The following modules must not appear:

* Products
* Categories
* Brands
* CMS
* Home Page Sections
* Advertisements
* Coupons
* Customers
* Reports
* Settings
* Users
* Payment Configuration

---

# Resource Authorization

Every Filament Resource must enforce authorization before allowing access.

Each resource should verify permissions for:

* View
* Create
* Edit
* Delete

This ensures users cannot access resources by manually entering URLs.

Example:

```
ProductResource

View      -> products.view
Create    -> products.create
Update    -> products.update
Delete    -> products.delete
```

---

# Dashboard Restrictions

The dashboard should display widgets according to permissions.

## Staff Dashboard

Operational widgets only.

Examples:

* Pending Orders
* Processing Orders
* Ready for Dispatch
* Delivered Today
* Failed Deliveries

Do **not** display:

* Revenue
* Sales Reports
* Profit
* Customer Statistics
* Financial Analytics

---

# Orders Module

Staff should have access to:

* View Orders
* View Customer Information
* View Delivery Address
* View Order Timeline
* Print Invoice
* Print Packing Slip
* Update Order Status

Staff must **not** be able to:

* Delete Orders
* Edit Product Prices
* Modify Order Totals
* Change Payment Information
* Refund Payments
* Edit Purchased Products

---

# Action-Level Authorization

Buttons should only be displayed when the corresponding permission exists.

Examples:

| Action        | Permission      |
| ------------- | --------------- |
| View Orders   | `orders.view`   |
| Update Status | `orders.update` |
| Print Invoice | `orders.print`  |
| Export Orders | `orders.export` |
| Cancel Order  | `orders.cancel` |
| Refund Order  | `orders.refund` |

Hidden buttons should also be protected by backend authorization.

---

# Future Warehouse Restrictions

When warehouse management is implemented, each staff member may be assigned to a warehouse.

Future enhancement:

```
User
    ↓
Warehouse

Orders automatically filtered by warehouse_id
```

No manual warehouse selection should be required.

---

# Future Modules

The permission system should support adding new modules without architectural changes.

Examples:

```
Delivery

Routing

Warehouse

Driver Management

Returns

Packing

Picking

Dispatch

Inventory
```

Each module should introduce its own permissions.

---

# Audit Logging

All operational actions should be logged.

Examples:

* Order status updated
* Invoice printed
* Packing slip generated
* Driver assigned
* Delivery completed

Each log should contain:

* User
* Action
* Previous Value
* New Value
* Timestamp

This provides accountability and simplifies troubleshooting.

---

# Security Requirements

The implementation must ensure:

* Unauthorized menu items are hidden.
* Unauthorized URLs cannot be accessed directly.
* Unauthorized buttons are not displayed.
* Backend authorization validates every request.
* Permissions are enforced regardless of frontend visibility.

Security must never rely solely on hidden navigation.

---

# Implementation Phases

## Phase 1

* Install and configure Spatie Laravel Permission.
* Create Super Admin and Staff roles.
* Create initial permission set.
* Assign permissions to roles.
* Restrict Filament navigation.
* Protect all Filament Resources.
* Restrict dashboard widgets.
* Limit Staff access to Orders only.

---

## Phase 2

* Introduce Delivery module.
* Introduce Routing module.
* Add warehouse-based filtering.
* Add driver management.
* Expand operational permissions.

---

## Phase 3

* Warehouse assignment.
* Inventory picking.
* Packing workflows.
* Dispatch management.
* Returns processing.
* Advanced audit reports.

---

# Expected Outcome

After implementation:

* A single `/admin` login is maintained.
* No duplicate admin panels are created.
* No duplicate authentication systems are introduced.
* Staff members only see modules they are authorized to access.
* Every resource and action is protected by permissions.
* The permission architecture is scalable for future operational modules while keeping the administration interface secure and maintainable.
