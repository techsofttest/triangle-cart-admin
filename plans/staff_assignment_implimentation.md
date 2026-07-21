# Feature: Staff Order Assignment & Delivery Session Integration

## Objective

Implement a simple staff assignment workflow for orders.

The goal is to allow an administrator to assign orders to delivery staff before creating a delivery session. When a delivery session is created, only the orders assigned to the selected staff member should be included.

This is the initial implementation only. Do not implement assignment history, workload balancing, auto-assignment, or any advanced dispatch features.

---

# Functional Flow

```
Customer places order
        │
        ▼
Admin reviews orders
        │
        ▼
Assign Staff
        │
        ▼
Order remains assigned
        │
        ▼
Create Delivery Session
        │
        ▼
Select Staff
        │
        ▼
System fetches assigned orders
        │
        ▼
Create Session
        │
        ▼
Continue existing delivery workflow
```

The existing delivery workflow, route optimisation, session management, and delivery process should remain unchanged.

---

# Database Changes

## Orders

Add the following nullable fields to the orders table.

```php
assigned_staff_id
assigned_at
assigned_by
```

### assigned_staff_id

Foreign key to users table.

Nullable because existing orders and new orders are initially unassigned.

---

### assigned_at

Timestamp indicating when the order was assigned.

Nullable.

---

### assigned_by

User ID of the administrator who assigned the order.

Nullable.

---

# Eloquent Relationships

## Order

```php
assignedStaff()
```

belongsTo(User::class)

---

```php
assignedBy()
```

belongsTo(User::class)

---

# Orders Resource

## Table Changes

Add a new column

```
Assigned Staff
```

Display

- Staff Name
- "-" if not assigned

---

## Filters

Add filter

```
Assigned Staff
```

Allow selecting staff member.

---

Add filter

```
Unassigned Orders
```

Shows

```
assigned_staff_id IS NULL
```

---

## Single Action

Add a table action

```
Assign Staff
```

Workflow

- Open modal
- Select staff
- Save assignment

Update

- assigned_staff_id
- assigned_at
- assigned_by

---

## Bulk Action

Add bulk action

```
Assign Staff
```

Workflow

- Select multiple orders
- Choose staff
- Update all selected orders

Update

- assigned_staff_id
- assigned_at
- assigned_by

---

# Staff Selection

Only users eligible for deliveries should appear.

Use the existing user configuration/roles already implemented in the project.

Do not display unrelated admin accounts.

---

# Delivery Session

Modify the Create Delivery Session form.

Current flow

```
Create Session
↓
Generate Orders
```

New flow

```
Create Session
↓
Select Staff
↓
Generate Orders
```

---

## New Field

Required field

```
Staff
```

Dropdown listing eligible delivery staff.

---

# Order Retrieval

The existing order retrieval logic should be updated.

Instead of retrieving every eligible order, retrieve only orders satisfying all existing delivery conditions AND

```
assigned_staff_id = selected staff
```

No other delivery session logic should change.

---

# Existing Route Optimisation

No changes required.

The route optimisation should continue exactly as it does now.

The only difference is that the input dataset now consists only of orders assigned to the selected staff member.

---

# Validation Rules

Do not include

- Unassigned orders
- Orders assigned to another staff member

Continue respecting all existing delivery session eligibility rules already implemented.

---

# Permissions

Reuse the existing permission system.

Only users who currently have permission to assign/manage orders should be able to perform assignment.

---

# UI Summary

## Orders Page

Add

- Assigned Staff column
- Assigned Staff filter
- Unassigned filter
- Assign Staff action
- Bulk Assign Staff action

---

## Delivery Session

Add

```
Staff
```

dropdown before generating the order list.

---

# Out of Scope

Do NOT implement

- Assignment history
- Reassignment logs
- Driver workload indicators
- Automatic assignment
- Dispatch module
- Driver mobile app
- Notifications
- Session locking
- Route changes
- Delivery performance metrics

These may be implemented in future versions.

---

# Notes

This implementation should introduce the smallest possible change to the existing delivery workflow.

The only behavioural difference should be:

1. Orders are first assigned to a staff member.
2. Delivery session creation requires selecting a staff member.
3. Only orders assigned to that staff member are included in the session.
4. Everything else continues to work exactly as it does today.