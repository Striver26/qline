# Security Policy

## Supported Versions

| Version | Supported          |
|---------|--------------------|
| Latest  | ✅ Yes              |
| Older   | ❌ No               |

Only the latest version of Qline receives security updates. We recommend always running the most recent release.

## Reporting a Vulnerability

We take security vulnerabilities seriously. If you discover a security issue, please report it responsibly.

### ⚠️ Do NOT

- Open a public GitHub issue for security vulnerabilities
- Post vulnerability details in discussions, comments, or social media
- Exploit the vulnerability beyond what is necessary to demonstrate it

### ✅ Do

1. **Email us** at **security@qline.my** with:
   - A description of the vulnerability
   - Steps to reproduce the issue
   - Potential impact assessment
   - Any suggested fixes (optional)

2. **Allow reasonable time** for us to investigate and patch before any public disclosure (we aim for 72 hours acknowledgment, 30 days for a fix).

3. **Include** the following in your report:
   - Affected component (e.g., authentication, queue management, webhook)
   - Attack vector and prerequisites
   - Proof of concept (if possible)
   - Your contact information for follow-up

## Security Measures

Qline implements the following security practices:

### Authentication & Authorization
- **Laravel Fortify** for robust authentication
- **Two-Factor Authentication (2FA)** support
- **Email verification** required for all users
- **Role-Based Access Control (RBAC)** with four distinct roles:
  - `superadmin` — Full platform access
  - `platform_staff` — Platform administration
  - `business_owner` — Tenant management
  - `business_staff` — Limited tenant access
- **Middleware-enforced** route protection (`RequireOwnerRole`, `RequireProfileCompleted`)

### Data Protection
- **Bcrypt hashing** for passwords (12 rounds in production)
- **Encrypted sessions** support
- **CSRF protection** on all forms (via Laravel)
- **Mass assignment protection** on models
- **Environment-based secrets** — No hardcoded credentials

### Infrastructure
- **Queue worker isolation** — Background jobs run in separate processes
- **Webhook verification** — WhatsApp webhook signature validation
- **Database transactions** — Atomic operations for queue state changes
- **Input validation** — Server-side validation on all user inputs

### CI/CD
- **Automated testing** on every push and pull request
- **Code style enforcement** via Laravel Pint
- **Dependency auditing** — Regular review of Composer and NPM packages

## Responsible Disclosure

We are committed to working with security researchers and will:

1. **Acknowledge** your report within 72 hours
2. **Provide updates** on our investigation progress
3. **Credit you** in our security advisories (unless you prefer anonymity)
4. **Not pursue legal action** against researchers who follow this responsible disclosure policy

Thank you for helping keep Qline and its users safe! 🔒
