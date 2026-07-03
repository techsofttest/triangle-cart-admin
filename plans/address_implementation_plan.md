# Implementation Plan - Customer Address Management Module

Implement a complete customer address management system spanning the Laravel backend (`trianglecart`) and Next.js frontend (`trianglecart-front`).

## User Review Required

> [!IMPORTANT]
> The database migration will drop and recreate the `customer_addresses` table to support the Australia-based address schema, coordinates (`latitude`, `longitude`), and soft deletes. Any existing local addresses in the database will be reset.
>
> Google Places Autocomplete API keys should be configured in `NEXT_PUBLIC_GOOGLE_MAPS_API_KEY` on the frontend. If the key is not set, the frontend will automatically fallback to manual address entry.

## Proposed Changes

### Laravel Backend (`trianglecart`)

#### [NEW] [2026_06_19_172650_recreate_customer_addresses_and_update_customers_and_orders.php](file:///c:/laragon/www/trianglecart/database/migrations/2026_06_19_172650_recreate_customer_addresses_and_update_customers_and_orders.php)
- Add new migration to drop & recreate the `customer_addresses` table with the requested fields (soft deletes, lat/long, label, contact_name, default flags).
- Add `default_shipping_address_id` and `default_billing_address_id` columns to `customers` table as foreign keys referencing `customer_addresses`.
- Add address snapshot columns (`shipping_*`) to `orders` table to preserve address information at the time of purchase.

#### [MODIFY] [Customer.php](file:///c:/laragon/www/trianglecart/app/Models/Customer.php)
- Add `$fillable` fields for `default_shipping_address_id` and `default_billing_address_id`.
- Update the `addresses` relation to point to `CustomerAddress` with foreign key `customer_id` (instead of `user_id`).
- Define `defaultShippingAddress` and `defaultBillingAddress` relationships.

#### [MODIFY] [CustomerAddress.php](file:///c:/laragon/www/trianglecart/app/Models/CustomerAddress.php)
- Update `$fillable` to include new address fields: `customer_id`, `label`, `contact_name`, `phone`, `address_line_1`, `address_line_2`, `suburb`, `city`, `state`, `postcode`, `country`, `latitude`, `longitude`, `google_place_id`, `delivery_notes`, `is_default_shipping`, `is_default_billing`.
- Add `SoftDeletes` trait support.
- Update `customer` relationship to point to `customer_id`.

#### [MODIFY] [Order.php](file:///c:/laragon/www/trianglecart/app/Models/Order.php)
- Update `$fillable` to include address snapshot fields: `shipping_name`, `shipping_phone`, `shipping_address_line_1`, `shipping_address_line_2`, `shipping_city`, `shipping_state`, `shipping_postcode`, `shipping_country`, `shipping_latitude`, `shipping_longitude`.

#### [NEW] [CustomerAddressController.php](file:///c:/laragon/www/trianglecart/app/Http/Controllers/Api/CustomerAddressController.php)
- Implement endpoints for:
  - `GET /api/customer/addresses`: Fetch addresses list (maximum 20).
  - `POST /api/customer/addresses`: Store a new address. If it's the first address, automatically mark it as default shipping and billing.
  - `PUT /api/customer/addresses/{id}`: Update an address.
  - `DELETE /api/customer/addresses/{id}`: Soft delete address. Ensure default addresses cannot be deleted unless another is set first.
  - `POST /api/customer/addresses/{id}/default-shipping`: Set default shipping and update customer profile references.
  - `POST /api/customer/addresses/{id}/default-billing`: Set default billing and update customer profile references.
- Implement session helper fallback for dynamic customer authentication retrieval.

#### [MODIFY] [routes/api.php](file:///c:/laragon/www/trianglecart/routes/api.php)
- Add login and registration routes for testing and production auth:
  - `POST /api/customer/login`
  - `POST /api/customer/register`
- Define routes for the new `CustomerAddressController` address management APIs.

---

### Next.js Frontend (`trianglecart-front`)

#### [MODIFY] [AuthCard.tsx](file:///c:/laragon/www/trianglecart-front/components/auth/AuthCard.tsx)
- Connect login and registration form submissions to backend endpoints (`/api/customer/login` and `/api/customer/register`).
- Keep mock logins as fallback if backend auth fails to ensure development stability.

#### [MODIFY] [AddressBook.tsx](file:///c:/laragon/www/trianglecart-front/components/profile/AddressBook.tsx)
- Re-route address listings, additions, edits, defaults setting, and deletions to use the backend API.
- Support Google Places Autocomplete script initialization and field populating when inputting addresses.
- Support manual inputs in case the Autocomplete API is unavailable or fails.

#### [MODIFY] [page.tsx](file:///c:/laragon/www/trianglecart-front/app/checkout/page.tsx)
- Fetch customer address list from `/api/customer/addresses`.
- Auto-select default shipping address.
- Support address selection radio buttons and adding new addresses via a modal/drawer.

## Verification Plan

### Automated Tests
- Propose migration execution and run `php artisan migrate` on the database.
- Run `npm run lint` and `npx tsc --noEmit` on frontend to verify type-safety.

### Manual Verification
- Test creating up to 20 addresses and verify they save to the MySQL database.
- Test setting default billing/shipping address.
- Test Google Autocomplete field populate in the address drawer.
- Test checkout flow selecting default shipping address and adding a new address in-checkout.
