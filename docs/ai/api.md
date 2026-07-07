# api.md

REST API contract guidance.

## Principles
- Controllers should be thin.
- Request validation belongs in Form Requests.
- Business logic belongs in Services.
- Use DTOs/resources for stable response shapes.

## Stripe endpoints
- `POST /api/webhooks/stripe` → StripeWebhookController@handle

## Payment endpoints
- `/api/checkout`
- `/api/checkout/retry`

## Customer endpoints (expected)
- `/api/me` (profile data)
- `/api/customer/dashboard-summary` (dashboard summary)

## Search hints
- `routes/api.php`
- `Http\Controllers\Api`
- `FormRequest`

## Update rule
When adding an endpoint:
- Add it here with request/response keys.
- Update module docs.
- Add search hint to `indexes.md`.

