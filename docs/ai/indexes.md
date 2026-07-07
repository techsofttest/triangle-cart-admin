# indexes.md

AI-first navigation index for Triangle Cart.

Use this file as the primary entry point for “where is X implemented?” questions.

---

## Quick start (read in this order)
1. [`docs/ai/README.md`](README.md) (how to use the KB)
2. [`docs/ai/indexes.md`](indexes.md) (search-oriented index)
3. [`docs/ai/project.md`](project.md) (stack + boundaries)
4. [`docs/ai/architecture.md`](architecture.md) (end-to-end flow)
5. Module docs: `payments.md`, `delivery.md`, `warehouse.md`, `authentication.md`, `authorization.md`, etc.

---

## Repository map (by responsibility)

### API
- `routes/api.php`
- `app/Http/Controllers/Api/*`
- Form requests: `app/Http/Requests/*` (if present)
- Response DTOs / resources (if present)

### Services / business logic
- `app/Services/*`

### Policies / authorization
- `app/Policies/*`
- `database/seeders/RolesAndPermissionsSeeder.php`

### Enums / domain states
- `app/Enums/*`

### Stripe
- `routes/api.php` → `StripeWebhookController@handle`
- `app/Http/Controllers/Api/StripeWebhookController.php` (if present)
- `app/Models/StripeWebhookLog.php`
- `app/Services/Payments/*` (payment gateway implementations)
- `database/migrations/*payment*` and `*stripe*`

### Delivery / sessions / compliance
- `app/Models/DeliverySession*.php`
- `app/Models/DeliveryComplianceLog.php`
- `app/Services/DeliveryEligibilityService.php`
- `config/delivery.php`
- `database/migrations/*delivery*`

### Warehouse / operations
- `database/migrations/*delivery_operations*` (see delivery operations tables)
- `app/Filament/Resources/*` for operations UI (admin)

### Customer OTP authentication
- Search for OTP usage across:
  - `app/Http/Controllers/Api/*`
  - `app/Services/*`
  - `app/Models/*` (otp/verification)

---

## Search cheats (copy/paste into repo search)

### Stripe
- `StripeWebhookController`
- `payment_intent`
- `webhooks/stripe`
- `StripePaymentService`
- `stripe_webhook_logs`

### Roles / permissions
- `spatie`
- `PermissionRegistrar`
- `roles.view` (or permissions names in seeder)
- `can(` / `authorize(`

### Delivery sessions
- `DeliverySession`
- `delivery_session`
- `DeliveryComplianceLog`

### Filament
- `Filament\Resources`
- `Schemas\*Form`
- `->form(` and `->table(`

---

## Where to put what
- Business logic: `app/Services/*`
- Complex workflows: `app/Actions/*` (or dedicated workflow services)
- Authorization: `app/Policies/*` + `config/permission.php` seeded permissions
- Side effects: Prefer events / listeners where the codebase already uses them

