## Architecture (service-first)

### High level pipeline
Next.js

↓

REST API (Laravel)

↓

Thin Controllers (request validation + orchestration only)

↓

Services (business logic)

↓

Models (data + relations)

↓

Database

### Admin (Filament)
Filament Resources → delegate to Services/Actions → Models

## Module flow
- **Payments**: checkout request → payment service → Stripe → webhook updates order/payment state
- **Delivery**: session selection → `DeliveryEligibilityService` → session/order assignment → `DeliveryComplianceLog`
- **Warehouse/ops**: operations tables feed delivery assignment/compliance
- **Auth**: customer OTP endpoints → `AuthenticationService` → session/token issuance

## Search entry point
Use `docs/ai/indexes.md` first, then the module doc for the exact workflow.

