# glossary.md

Canonical terms used across the AI-first Knowledge Base.

## Domain terms
- **Delivery Session**: A scheduled operational window (typically for deliveries) represented by `DeliverySession` and related models.
- **Delivery Compliance**: Proof/logging that a delivery followed required rules, stored in `DeliveryComplianceLog`.
- **OTP**: One-Time Password. A short-lived code used for customer authentication.
- **Staff**: Authenticated back-office users using role/permission checks.
- **Warehouse / Operations**: Operational scheduling + task assignment (delivery operations tables).

## System terms
- **KB (Knowledge Base)**: This `docs/ai/*` documentation.
- **Service**: Laravel class that contains business logic.
- **Repository**: (Where applicable) data access abstraction.
- **Action**: Encapsulated workflow step used for complex operations.
- **API Contract**: Request/response shape exposed by REST endpoints.

## Conventions
- Prefer terms exactly as used in this glossary when naming code, docs, and tickets.

