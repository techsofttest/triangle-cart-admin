# repositories.md

Repository layer (where applicable).

## Principles
- The project may use repositories only when it reduces duplication or centralizes query logic.
- If repositories exist, they should live in `app/Repositories/*`.

## Typical responsibilities
- Encapsulate complex Eloquent queries.
- Provide stable query interfaces for services.

## Rule
- Never query unrelated models directly from UI components.
- If UI needs data, create a service method (and repository query inside if needed).

