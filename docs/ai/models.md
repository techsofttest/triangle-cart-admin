# models.md

Model responsibilities and naming rules.

## Principles
- Models represent data and relationships.
- No business logic in models beyond domain invariants or small helpers already used in the codebase.
- Business logic must live in `app/Services/*`.

## Model naming
- Use singular, Eloquent-idiomatic naming: `Order`, `DeliverySession`, `CustomerAddress`.
- Follow existing naming for relationships.

## Where to look
- `app/Models/*`
- Related enums in `app/Enums/*`

## Common relationship patterns
- Orders → OrderItems
- Order → PaymentTransaction(s) (indirectly via status/log tables)
- DeliverySession → DeliverySessionOrder (or similar)
- Staff/User → roles/permissions (via Spatie Permission)

## Required docs update rule
If you introduce a new model:
1. Add/extend module doc under `docs/ai/modules.md` and the owning module file.
2. Add search hints to `indexes.md`.

