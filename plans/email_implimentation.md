# TriangleCart – Email Notification System Implementation Specification

## Project Overview

Implement a centralized email notification system for TriangleCart that supports customer account lifecycle emails and order-related notifications.

The project uses a headless architecture:

* Backend: Laravel 12
* Admin: Filament 5.2
* Frontend: Next.js
* Authentication: Email/password
* Mail Driver: Gmail SMTP (already configured)
* Email Templates: Existing Blade templates located in `resources/views/emails/`

This implementation should integrate cleanly into the existing architecture without modifying unrelated functionality.

---

# Objectives

Implement the following email notifications:

### Customer Emails

1. Email Verification
2. Registration Successful
3. Forgot Password
4. Password Reset Successful
5. Order Payment Successful

### Admin Emails

1. Order Payment Successful Notification

---

# Existing Project Assumptions

The following are already available and **must not be reimplemented**:

* Gmail SMTP configuration
* Existing Blade email templates
* Customer authentication
* Payment workflow
* Order management
* Customer management
* Laravel password reset tables
* Customer `email_verified_at` column

Reuse all existing functionality wherever possible.

---

# Architecture Requirements

The implementation **must** follow the Event → Listener → Mailable architecture.

```
Controller / Service

        ↓

Dispatch Event

        ↓

Listener

        ↓

Mailable

        ↓

Mail::send()
```

Controllers should never send emails directly.

Business logic should remain separated from email logic.

---

# General Implementation Rules

## Use Native Laravel Features

Use Laravel native functionality wherever available.

Specifically:

* Native Email Verification
* Native Password Reset
* Native Notifications where appropriate
* Signed Verification URLs
* Password Reset Tokens

Avoid custom implementations unless absolutely required.

---

# Mail Sending Strategy

* Send emails immediately.
* Do not use queues.
* Do not introduce Redis.
* Do not introduce Horizon.
* Email failures must never interrupt business operations.

If an email fails:

* Log the exception.
* Continue the request.
* Never fail checkout because of email delivery issues.

---

# Admin Email Configuration

Admin recipients are configured using the environment file.

Example:

```
ADMIN_EMAIL=admin@example.com,sales@example.com
```

Requirements:

* Support one or more email addresses.
* Parse comma-separated values.
* Trim whitespace.
* Ignore empty entries.

---

# Email Templates

Existing templates located in

```
resources/views/emails/
```

must be reused.

The implementation should:

* Reuse layouts
* Reuse branding
* Reuse logo
* Reuse styling

Only update:

* wording
* variables
* buttons
* dynamic content

Do not redesign templates.

---

# Feature Specifications

---

## Feature 1 – Email Verification

### Trigger

Customer registration.

### Flow

```
Customer Registers

↓

Customer Record Created

↓

Verification Email Sent

↓

Customer Clicks Verification Link

↓

email_verified_at Updated

↓

Account Activated
```

### Requirements

Use Laravel's native email verification.

Do not implement custom verification tokens.

Do not create additional verification tables.

Signed URLs must be used.

---

## Feature 2 – Registration Successful Email

### Trigger

After successful email verification.

Do NOT send immediately after registration.

### Email Content

Include:

* Welcome message
* Customer name
* Login button
* Support contact information

No promotional content.

---

## Feature 3 – Forgot Password

Use Laravel standard password reset flow.

### Flow

```
Forgot Password

↓

Generate Reset Token

↓

Email Reset Link

↓

Customer Resets Password
```

Do not implement OTP-based password reset.

Do not replace Laravel's reset mechanism.

---

## Feature 4 – Password Reset Successful

### Trigger

After password has been successfully changed.

### Email Content

Include:

* Password changed confirmation
* Time of change (if available)
* Security reminder
* Contact support information

---

## Feature 5 – Customer Order Payment Successful

### Trigger

Only after payment has been confirmed successfully.

Never send this email when an order is merely created.

### Email Content

Include:

* Customer name
* Order number
* Order date
* Billing summary
* Delivery summary
* Product list
* Quantity
* Unit price
* Totals
* Payment method
* Support information

Do not attach PDF invoices.

Only display the order summary.

---

## Feature 6 – Admin Order Notification

### Trigger

Immediately after successful payment.

Recipients:

```
ADMIN_EMAIL
```

### Email Content

Include:

* Order number
* Customer name
* Customer email
* Order total
* Payment method
* Payment status
* Link to the Filament order page (if available)

---

# Events

Create dedicated events.

Suggested events:

```
CustomerRegistered

CustomerVerified

PasswordResetRequested

PasswordResetCompleted

OrderPaymentSuccessful
```

Events should contain only the data required by their listeners.

---

# Listeners

Create dedicated listeners.

Suggested listeners:

```
SendVerificationEmail

SendWelcomeEmail

SendPasswordResetEmail

SendPasswordChangedEmail

SendCustomerOrderConfirmation

SendAdminOrderNotification
```

Each listener should have a single responsibility.

---

# Mailables

Create dedicated Mailables.

Suggested Mailables:

```
VerifyEmailMail

WelcomeMail

ForgotPasswordMail

PasswordChangedMail

CustomerOrderPaidMail

AdminOrderPaidMail
```

Each Mailable should:

* Receive strongly typed data
* Contain minimal business logic
* Pass data to Blade templates

---

# Services

Create a reusable mail service.

Suggested location:

```
App\Services\MailService
```

Responsibilities:

* Parse admin recipients
* Shared mail helper methods
* Centralize future reusable mail functionality

Business rules should not be placed inside this service.

---

# Configuration

Verify or add the following environment variables.

```
MAIL_MAILER

MAIL_HOST

MAIL_PORT

MAIL_USERNAME

MAIL_PASSWORD

MAIL_ENCRYPTION

MAIL_FROM_ADDRESS

MAIL_FROM_NAME

ADMIN_EMAIL

FRONTEND_URL
```

Do not hardcode any email addresses.

---

# Error Handling

Email failures must:

* Be logged
* Not break checkout
* Not interrupt registration
* Not interrupt password reset
* Not interrupt verification

Wrap mail sending in appropriate exception handling.

---

# Logging

Log:

* Failed email sends
* Invalid admin email configuration
* Unexpected mail exceptions

Avoid excessive logging of successful deliveries.

---

# Security Requirements

* Use signed verification URLs.
* Use Laravel password reset tokens.
* Never expose internal exception messages to users.
* Never reveal whether an email address exists during password reset requests.
* Respect existing authentication middleware.

---

# Constraints

The implementation must NOT:

* Redesign email templates
* Replace authentication
* Replace payment flow
* Modify checkout logic
* Introduce queues
* Introduce Redis
* Introduce Horizon
* Modify existing API response formats
* Introduce unnecessary packages
* Duplicate Laravel native functionality

---

# Acceptance Criteria

The implementation is complete when all of the following are verified:

## Customer Registration

* Registration sends verification email.
* Verification link activates account.
* `email_verified_at` is updated.
* Welcome email is sent only after successful verification.

---

## Password Reset

* Forgot password sends reset email.
* Reset link works correctly.
* Password changed email is sent after successful reset.

---

## Orders

* Customer receives order confirmation only after successful payment.
* Admin receives payment notification.
* Multiple admin recipients are supported.

---

## Reliability

* Email failures do not interrupt checkout.
* Email failures do not interrupt registration.
* Exceptions are logged.
* Existing functionality remains unchanged.

---

# Future Extensibility

The architecture should allow future implementation of:

* Order Shipped
* Order Delivered
* Order Cancelled
* Refund Processed
* Low Stock Alerts
* Newsletter Emails
* Promotional Campaigns
* SMS Notifications
* WhatsApp Notifications

No architectural changes should be required to support these future features.

---

# Implementation Notes for the IDE

* Reuse existing project structure wherever possible.
* Minimize modifications to existing files.
* Prefer extending existing classes over replacing them.
* Follow Laravel best practices.
* Keep all components loosely coupled.
* Ensure the implementation remains maintainable, testable, and production-ready.
