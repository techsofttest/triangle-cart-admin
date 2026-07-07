# database.md

Database conventions and mapping notes.

## Naming
- Table prefix: `tc_`
- Connection/charset: `utf8mb4_0900_ai_ci`

## How to add/alter tables
1. Create migration in `database/migrations/*`
2. Update AI KB module doc that owns the tables.
3. If adding new domain concepts, consider adding an Enum and a Policy.

## Key tables (by module)
> Note: this file is a living index. Use `indexes.md` search hints to locate exact schema.

### Permissions / staff roles
- `permissions`, `roles`
- pivot tables (model_has_roles, model_has_permissions, role_has_permissions)
- Seed: `database/seeders/RolesAndPermissionsSeeder.php`

### Payments / Stripe
- `payment_transactions`
- `stripe_webhook_logs`

### Delivery
- `delivery_sessions` and related order/session pivot
- `delivery_compliance_logs`
- `delivery_postcodes`
- `delivery_dates` and `time_slots` (if used for scheduling)

### Warehouse / delivery operations
- tables created by migrations matching `*delivery_operations*`

## Index discipline
- Always add indexes for foreign keys and high-cardinality lookup columns.
- Follow `docs/ai/indexes.md` “search hints” and ensure queries are supported.

