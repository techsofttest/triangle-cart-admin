# payments.md

Stripe payments module documentation.

## Source of truth
- `docs/ai/workflows.md` for high-level sequence.
- `docs/ai/api.md` for endpoints.

## Backend components (expected)
- `PaymentGatewayInterface`
- `StripePaymentService`
- Webhook controller at `/api/webhooks/stripe`
- Logging models:
  - `StripeWebhookLog`
  - `PaymentTransaction`

## Enums
- `app/Enums/OrderStatus.php`
- `app/Enums/PaymentStatus.php`
- `app/Enums/TransactionStatus.php`
- `app/Enums/TransactionType.php`

## Webhook invariants
- Verify signature
- Idempotently handle repeated events
- Persist raw/parsed payload (as already supported by webhook logs)

## Search hints
- `stripe_webhook_logs`
- `payment_transactions`
- `StripeWebhookController`
- `PaymentGatewayInterface`

## Update rule
When Stripe logic changes (retry, failure handling, refunds):
- Update this file and `docs/ai/workflows.md`.
- Add search hints to `indexes.md`.

