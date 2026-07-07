# authentication.md

Customer authentication flows (including OTP).

## Current objective
- Customer OTP authentication (per task scope).

## How to implement (recommended pattern)
1. API controller receives request DTO/input.
2. Delegate to an AuthenticationService.
3. AuthenticationService:
   - validates OTP lifecycle rules
   - issues session/token (whatever the project uses)
   - writes audit logs if present
4. Return a response DTO.

## Search hints (to align with existing code)
- `otp`
- `one-time`
- `verification`
- `customer`
- `auth`

## Docs update rule
Whenever OTP endpoints are created/refactored:
- Update `docs/ai/api.md` with endpoint names and request/response keys.
- Update `docs/ai/workflows.md` with the sequence diagram.

