# integrations.md

External integrations: Stripe, Google Maps, SMS/OTP (if applicable).

## Stripe
- See `docs/ai/payments.md` and `docs/ai/workflows.md`.

## Google Maps routing (planned)
- `app/Services/GoogleRoutesService.php` is present.
- Routing integration should not become UI logic; use services.

## OTP delivery integration (planned/if applicable)
- Search for SMS/email provider usage.

## Update rule
When adding new integrations:
- Add provider credentials locations (env keys) to this file.
- Add service entry points in module docs.

