# Stripe Payment Gateway Integration & Webhook Implementation
## Project
Headless E-commerce Platform

**Backend:** Laravel 11 + Filament 4/5  
**Frontend:** Next.js 16  
**Payment Gateway:** Stripe  
**Currency:** AUD (Configurable)  
**Architecture:** Headless API

---

# Objective

Implement a secure, production-ready Stripe payment integration using the PaymentIntent API and Stripe Webhooks to ensure payment statuses are verified server-side rather than relying on client-side callbacks.

The implementation must support:

- Guest Checkout
- Customer Checkout
- Secure payment verification
- Accurate payment status synchronization
- Payment retries
- Future refund support
- Complete transaction history
- Webhook logging
- Idempotent processing

---

# Implementation Goals

The payment flow should:

- Never trust frontend payment success
- Never reduce inventory before successful payment
- Never confirm an order before Stripe confirms payment
- Support webhook retries safely
- Maintain complete payment history
- Be extensible for future payment gateways

---

# Overall Payment Flow

```
Checkout

↓

Validate Cart
Validate Delivery Address
Validate Delivery Slot
Validate Coupon
Validate Inventory

↓

Create Order
Status:
Pending Payment

↓

Create Stripe PaymentIntent

↓

Return Client Secret

↓

Stripe Payment Element

↓

Customer Completes Payment

↓

Stripe Sends Webhook

↓

Webhook Verification

↓

Update Payment Status

↓

Update Order Status

↓

Reduce Inventory

↓

Generate Invoice

↓

Send Confirmation Email

↓

Customer Success Screen
```

---

# Phase 1 – Backend Preparation

## 1. Install Stripe SDK

Install the latest Stripe PHP SDK compatible with Laravel 11.

Configure Stripe using environment variables.

Required configuration:

- Publishable Key
- Secret Key
- Webhook Secret
- Currency
- Webhook tolerance

---

## 2. Environment Variables

```
STRIPE_KEY=

STRIPE_SECRET=

STRIPE_WEBHOOK_SECRET=

STRIPE_CURRENCY=AUD

STRIPE_WEBHOOK_TOLERANCE=300
```

Do not hardcode any credentials.

---

# Phase 2 – Database Changes

## Orders Table

Add payment-related columns.

Suggested fields:

```
payment_method

payment_status

payment_amount

payment_currency

stripe_payment_intent

stripe_charge_id

paid_at

payment_failure_reason

payment_metadata (JSON)
```

---

## Payment Status Enum

Supported values:

```
pending

processing

authorized

paid

failed

cancelled

refunded

partially_refunded
```

Payment status must remain independent of order status.

---

## Order Status

Suggested workflow:

```
Pending Payment

Confirmed

Processing

Packed

Ready

Out For Delivery

Delivered

Cancelled

Refund Requested

Refunded
```

---

## Payment Transactions Table

Create a dedicated table.

Purpose:

- Complete payment history
- Refund history
- Retry history
- Webhook tracking

Suggested structure:

```
id

order_id

gateway

transaction_type

payment_intent

charge_id

event_id

status

amount

currency

response (JSON)

created_at
```

Never overwrite transaction records.

Every Stripe event should create a new transaction entry.

---

## Webhook Logs Table

Create a webhook log table.

Suggested fields:

```
id

provider

event_id

event_type

payload

processed

error

created_at
```

Purpose:

- Debugging
- Auditing
- Retry detection

---

# Phase 3 – Payment Service Architecture

Create a dedicated payment service.

Suggested structure:

```
App\Services\Payments

PaymentGatewayInterface

StripePaymentService
```

The checkout process should never communicate directly with Stripe.

Instead:

```
Checkout Controller

↓

Payment Service

↓

Stripe SDK
```

This architecture allows future integration of:

- PayPal
- Square
- Razorpay
- Apple Pay
- Google Pay

without changing checkout logic.

---

# Phase 4 – Checkout Integration

When customer clicks:

```
Pay Now
```

Backend should:

## Validate

- Cart
- Inventory
- Product prices
- Delivery slot
- Coupon
- Delivery address

---

## Create Order

Within a database transaction:

Create:

```
Order

Status:
Pending Payment
```

---

## Create Stripe PaymentIntent

Use:

```
Automatic Payment Methods
```

Store:

- PaymentIntent ID
- Amount
- Currency

Return:

```
client_secret

order_number

payment_intent

amount
```

No inventory reduction at this stage.

---

# Phase 5 – Frontend Payment

Replace current payment step with Stripe Payment Element.

Flow:

```
Receive Client Secret

↓

Render Payment Element

↓

Customer Enters Card

↓

Confirm Payment

↓

Redirect to Waiting Screen
```

The frontend should never mark an order as paid.

It should simply wait for webhook confirmation.

---

# Phase 6 – Stripe Webhook

Create endpoint:

```
POST

/api/webhooks/stripe
```

This endpoint must be publicly accessible.

---

## Verify Signature

Before processing:

- Verify Stripe Signature
- Reject invalid signatures
- Reject malformed payloads

Never process unverified requests.

---

# Phase 7 – Supported Webhook Events

## payment_intent.succeeded

Actions:

- Update payment status
- Update order status
- Record transaction
- Reduce inventory
- Generate invoice
- Send confirmation email
- Notify customer

---

## payment_intent.processing

Update payment status:

```
Processing
```

---

## payment_intent.payment_failed

Update:

```
Payment Failed
```

Store:

- Failure reason
- Error code
- Stripe response

Customer can retry payment.

---

## payment_intent.canceled

Update payment status.

Do not confirm order.

---

## charge.refunded

Update:

```
Refunded

or

Partially Refunded
```

Create refund transaction.

---

## charge.dispute.created

(Optional)

Create admin notification.

---

# Phase 8 – Webhook Processing Flow

```
Receive Event

↓

Verify Signature

↓

Verify Event ID

↓

Already Processed?

↓

Yes

Return 200

↓

No

Begin Database Transaction

↓

Locate Order

↓

Update Payment

↓

Update Order

↓

Reduce Inventory

↓

Create Transaction Record

↓

Store Webhook Log

↓

Commit Transaction

↓

Return Success
```

---

# Phase 9 – Idempotency

Stripe retries webhook deliveries.

The implementation must prevent duplicate processing.

Before processing:

```
Check event_id

Already Exists?

Ignore
```

Inventory must never reduce twice.

Emails must never send twice.

Invoices must never generate twice.

---

# Phase 10 – Inventory Management

Inventory should only reduce after:

```
payment_intent.succeeded
```

Never reduce stock:

- During checkout
- During PaymentIntent creation
- During frontend success

Inventory reduction belongs exclusively inside webhook processing.

---

# Phase 11 – Customer Order History

Display:

Payment Status

Examples:

```
Pending Payment

Paid

Processing

Failed

Refunded
```

Order Status

Examples:

```
Pending Payment

Confirmed

Packed

Delivered

Cancelled
```

Both statuses should remain independent.

---

# Phase 12 – Retry Payment

If payment fails:

Customer should see:

```
Retry Payment
```

The retry process should:

- Reuse the same order
- Generate a new PaymentIntent
- Preserve previous payment history
- Record new transaction

Never create duplicate orders.

---

# Phase 13 – Admin Panel

Extend the Filament Order Resource.

Add Payment Information section.

Display:

```
Gateway

Payment Status

Payment Intent

Charge ID

Currency

Amount

Paid Date

Failure Reason

Refund Status
```

---

## Payment Timeline

Example:

```
Pending

↓

Processing

↓

Succeeded

↓

Refunded
```

---

## Transaction History

Display every payment event.

Examples:

```
Payment Created

Payment Processing

Payment Succeeded

Refunded

Retry Payment

Partial Refund
```

No records should be overwritten.

---

# Phase 14 – Webhook Logs

Create a Filament resource.

Display:

- Event ID
- Event Type
- Order
- Status
- Processing Result
- Timestamp

Allow viewing raw payload.

Useful for debugging failed webhook processing.

---

# Phase 15 – Error Handling

The implementation must correctly handle:

- Customer closes browser after payment
- Internet disconnects
- Payment succeeds but redirect fails
- Duplicate webhook deliveries
- Payment timeout
- Card declines
- Expired PaymentIntent
- Inventory changes during checkout
- Coupon expiration during payment

Orders must always reflect Stripe's verified payment status.

---

# Phase 16 – Security Requirements

Mandatory requirements:

- Never trust frontend payment success
- Never trust client-side amount
- Always verify Stripe signature
- Always verify event ID
- Use database transactions
- Log every webhook
- Log every payment event
- Validate order amount before confirmation
- Prevent duplicate webhook processing

---

# Phase 17 – Future Enhancements

Design the payment layer for future expansion.

Potential additions:

- Stripe Refund API
- Apple Pay
- Google Pay
- Link Payments
- Saved Payment Methods
- Subscription Payments
- Multiple Payment Gateways
- Split Payments
- Partial Captures
- Partial Refunds

No checkout refactoring should be required when introducing additional gateways.

---

# Deliverables

## Backend

- Stripe SDK Integration
- Payment Service
- PaymentIntent API
- Secure Webhook Endpoint
- Transaction Logging
- Webhook Logging
- Retry Payment API
- Payment Status API

---

## Frontend

- Stripe Payment Element
- Payment Processing Screen
- Success Screen
- Failure Screen
- Retry Payment
- Payment Status Display

---

## Admin

- Payment Details
- Transaction History
- Webhook Logs
- Refund Status
- Payment Timeline
- Searchable Payment Records

---

# Expected Result

The payment system should provide a secure, fault-tolerant, and production-ready checkout experience where payment verification is performed exclusively through Stripe webhooks. Orders are confirmed only after verified payment, inventory updates occur exactly once, all payment events are auditable, and the architecture remains extensible for future payment gateways without requiring changes to the checkout workflow.