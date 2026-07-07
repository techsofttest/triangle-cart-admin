# frontend.md

Next.js frontend integration guidance.

## Principles
- Treat Next.js as a consumer of the Laravel REST API.
- Avoid duplicating business rules on the frontend.

## Typical flow
1. Next.js page/component triggers fetch to `/api/*`.
2. Use auth token/session established by the backend.
3. Render based on API response.

## Search hints
- `trianglecart-front` directory
- `app/checkout/page.tsx`
- `localStorage('user')` (if used)

## Update rule
When endpoints change, update:
- module docs (`api.md` + owning module)
- frontend integration notes in this file.

