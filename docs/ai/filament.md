# filament.md

Filament admin integration.

## Rules
- Never place business logic inside Filament Resources.
- Resources should delegate to Services / Actions.
- Schemas used in Filament forms should map cleanly to request DTOs.

## Search hints
- `app/Filament/Resources/*`
- `Schemas/*Form.php`

## Update rule
When modifying a Filament resource:
- Add or update module docs (e.g., payments/delivery/admin screens).
- Ensure `indexes.md` search hints remain correct.

