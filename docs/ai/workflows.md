# workflows.md

End-to-end workflows for core systems.

## Stripe payment workflow (high level)
1. Frontend requests PaymentIntent creation (endpoint under `/api/checkout` as per TODO).
2. Backend creates Stripe PaymentIntent and logs transaction.
3. Frontend confirms payment using Payment Element.
4. Stripe webhook calls `/api/webhooks/stripe`.
5. Webhook handler verifies signature and updates order/payment state.
6. Transaction/webhook logging stored for audit.
7. Order status drives user-facing payment status.

## Delivery workflow
1. Customer selects delivery session/time slot.
2. DeliveryEligibilityService validates availability.
3. Orders are assigned to DeliverySession.
4. DeliveryComplianceLog records outcomes.

## Auth workflow (OTP)
1. Customer requests OTP.
2. Backend issues/sends OTP.
3. Customer verifies OTP.
4. Backend establishes authenticated session/token.

## Docs update rule
Each time code changes a workflow:
- Update the owning module doc.
- Add the exact controller endpoints to `api.md`.
- Add code search hints to `indexes.md`.

