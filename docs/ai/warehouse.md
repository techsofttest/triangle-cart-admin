# warehouse.md

Warehouse / operations module documentation.

## Responsibilities
- Operational tasks + stock movement (as implemented in database migrations under delivery operations).
- Staff assignment for operations.

## Search hints
- `delivery_operations`
- `Operation` models (if any)
- Filament resources under `app/Filament/Resources/*Operations*`

## Compliance tie-in
Warehouse/ops outputs feed into delivery sessions and delivery compliance logging.

## Update rule
When adding operations endpoints or admin screens:
- Update `docs/ai/api.md` and this module doc.

