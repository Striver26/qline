# Changelog

All notable changes to the **Qline** project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Subscription Management Controls** — Administrative interface to manually edit billing details and toggle subscription statuses
- **User Impersonation** — Allow administrators to securely simulate user sessions for debugging and support
- **Command Center Enhancements** — Thermal printing workflow for "Quick Add", optimized event broadcasting for TV Display, and robust modal state management
- **Customer Recall Workflow** — Dedicated workflow for managing customer callbacks in the queue
- **Automated Proximity Notifications** — Location-aware alerts to minimize no-shows
- **QR Standee Print Layout** — Refined A4 print design with rounded corners and optimized spacing for physical standees
- **Quick Add Feature** — Streamlined registration for anonymous walk-in customers
- **Public Queue Security** — Tokenized status URLs and date-aware tab recovery for secure ticket tracking
- **Admin Panel** — Full platform administration with 7 data dashboards (Users, Businesses, Subscriptions, Payments, WA Messages, Queue Entries, Feedback)
- **Admin Analytics Dashboard** — Platform-wide analytics and KPIs
- **Business Analytics Dashboard** — Business-level queue performance and traffic insights
- **Redis Integration** — Predis client for caching and queue infrastructure
- **Laravel Reverb** — Real-time WebSocket broadcasting for live queue updates
- **Loyalty Rewards System** — Configurable reward programs with auto-earned rewards on visit milestones
- **Earned Rewards Tracking** — Track and manage customer reward redemptions
- **Staff Invitation System** — Email-based invitations for business staff and platform staff
- **Platform Staff Invitations** — Super admins can invite platform-level staff members
- **WhatsApp Notifications** — Event-driven WhatsApp messages for ticket joined and status updates
- **WhatsApp Webhook** — Inbound/outbound webhook integration with WhatsApp Business API
- **TV Display Mode** — Large-screen queue display for business waiting areas
- **Customer Feedback** — Post-service feedback collection with ratings
- **Business Settings & Onboarding** — Profile completion flow for new business owners
- **QR Code Generation** — Branded QR codes for queue join URLs
- **Subscription Billing** — Daily/Monthly tiers with BillPlz payment gateway
- **Two-Factor Authentication** — via Laravel Fortify
- **Public Queue Pages** — Join queue, ticket status, TV display, and feedback form
- **Business Hours Configuration** — Configurable operating hours per business
- **CI/CD Pipelines** — GitHub Actions for automated testing (Pest) and linting (Pint)

### Changed
- **Architecture Refactoring** — Decoupled business logic into dedicated Service classes (Billing, Marketing, Analytics)
- **ServicePoint Migration** — Unified legacy Counter/Table architecture into a singular ServicePoint model
- **Livewire State Management** — Standardized component state using Form Objects
- **Terminology Updates** — Standardized "Walk-in" to "Anonymous" across the platform
- **Dynamic Timezone Support** — Improved timezone handling for business operations

### Security
- Eliminated mass-assignment vulnerabilities on the User model
- Refactored authentication actions for secure role assignment
- Hardened service configurations for production cache safety
- Secured tenant deletion through strict staff access cleanup
- Fixed enum comparison logic to prevent silent bugs

### Infrastructure
- Laravel 13 framework with PHP 8.3+ requirement
- Livewire 4 + Flux UI component library
- Tailwind CSS 4 with Vite 8 build pipeline
- Multi-tenant architecture with role-based access control
- Event-driven architecture with listeners for cross-cutting concerns

---

## Version History Format

Future releases will follow this template:

```
## [X.Y.Z] - YYYY-MM-DD

### Added
- New features

### Changed
- Changes to existing functionality

### Deprecated
- Features that will be removed in future versions

### Removed
- Removed features

### Fixed
- Bug fixes

### Security
- Vulnerability patches
```
