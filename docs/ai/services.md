# services.md

Services contain business logic.

## Entry point
- Read `docs/ai/coding-rules.md` and `docs/ai/conventions.md` first.
- Services are the primary implementation detail for workflows.

## Service patterns (Triangle Cart)
- Namespace: `app\Services\...`
- Inject dependencies (repositories, gateways) via constructor.
- Keep methods small; for multi-step workflows, use Actions.

## Payments services
- Payment gateway implementations live under `app/Services/Payments/*`.
- Stripe gateway should be behind a `PaymentGatewayInterface` (as indicated by TODO + project intent).

## Delivery services
- `app/Services/DeliveryEligibilityService.php` is a key example.
- Delivery compliance/logging should be triggered by service methods and/or events.

## Actions vs Services
- Use Services for business logic orchestration.
- Use Actions for complex steps that are reused across controllers, Filament, and jobs.

