# authorization.md

Staff roles & permissions.

## System used
- Spatie Permission style tables (see `database/migrations/2026_07_03_120425_create_permission_tables.php`).
- Seeded via `database/seeders/RolesAndPermissionsSeeder.php`.

## Policies
- `app/Policies/*` contains domain policies.

## Rules
- UI/Controllers must call authorization checks (`authorize`, gates, policy methods).
- Business logic stays in Services; services may call policy checks if it enforces invariants.

## Search hints
- `Policy::class`
- `->authorize`
- Permission strings from seeder: look in `RolesAndPermissionsSeeder.php`.

