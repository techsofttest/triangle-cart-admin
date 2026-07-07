# delivery.md

Delivery module documentation: sessions, eligibility, compliance.

## Data model (key classes)
- `DeliverySession`
- `DeliverySessionOrder`
- `DeliveryComplianceLog`
- `DeliveryPostcode`
- `DeliveryDate` / `TimeSlot` (if used)

## Services
- `DeliveryEligibilityService` (validates whether delivery can be scheduled)
- `GoogleRoutesService` exists (planned routing integration)

## Workflows
See `docs/ai/workflows.md` for end-to-end delivery flow.

## Compliance invariants
- Every compliance decision should be logged into `DeliveryComplianceLog`.
- Compliance rules live in services (not UI).

## Search hints
- `delivery_session`
- `DeliveryComplianceLog`
- `DeliveryEligibilityService`
- `config/delivery.php`

## Update rule
When changing eligibility rules, session assignment, or compliance logs:
- Update module doc + workflows.
- Add search hints to `indexes.md`.

