<p align="center">
  <h1 align="center">🚀 Qline</h1>
  <p align="center">
    <strong>Smart Queue Management for Modern Businesses</strong>
  </p>
  <p align="center">
    A multi-tenant SaaS platform that digitizes walk-in queues with real-time updates, WhatsApp notifications, and powerful analytics.
  </p>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#tech-stack">Tech Stack</a> •
  <a href="#getting-started">Getting Started</a> •
  <a href="#architecture">Architecture</a> •
  <a href="#contributing">Contributing</a> •
  <a href="#license">License</a>
</p>

---

## 📋 Overview

**Qline** is a full-featured, multi-tenant queue management system designed for Malaysian businesses. Customers join virtual queues via QR code or web link, receive real-time WhatsApp notifications, and track their position live — no more standing in physical lines.

Business owners get a powerful dashboard with live queue controls, analytics, staff management, loyalty rewards, and subscription billing — all in one place.

## ✨ Features

### 🏪 For Businesses (Tenant Panel)
- **Live Queue Dashboard** — Open, pause, or close queues with real-time ticket management
- **Call / Serve / Skip / Cancel** — Full ticket lifecycle with counter assignment
- **Queue Entries Log** — Searchable history of all tickets with status tracking
- **Staff Management** — Invite & manage staff via email with role-based access
- **Customer Feedback** — Collect and view customer ratings and comments
- **Analytics Dashboard** — Insights into queue performance, wait times, and traffic patterns
- **QR Code Generator** — Generate branded QR codes for customers to join queues
- **Loyalty Rewards** — Create reward programs that auto-trigger on visit milestones
- **Subscription & Billing** — Daily/Monthly subscription tiers with BillPlz integration

### 👥 For Customers (Public)
- **Join Queue** — Scan a QR code or visit a link to join a queue instantly
- **Live Ticket Status** — Real-time position tracking with estimated wait time
- **WhatsApp Notifications** — Automatic alerts when called or status changes
- **TV Display Mode** — Large-screen display for waiting areas showing current queue
- **Feedback Form** — Post-service feedback submission
- **Self-Cancel** — Cancel tickets via a unique token link

### 🛡️ For Platform Admins
- **Admin Dashboard** — Platform-wide overview and KPIs
- **Platform Analytics** — Cross-business performance insights
- **User Management** — View and manage all registered users
- **Business Management** — Oversee all tenant businesses on the platform
- **Subscription Management** — Monitor active subscriptions across tenants
- **Payment Records** — Track all billing and payment transactions
- **WhatsApp Message Logs** — Audit trail for all outbound WA messages
- **Queue Entry Logs** — Platform-wide queue ticket history
- **Customer Feedback Overview** — Aggregate feedback across all businesses

## 🛠️ Tech Stack

| Layer          | Technology                                                    |
|----------------|---------------------------------------------------------------|
| **Framework**  | [Laravel 13](https://laravel.com) (PHP 8.3+)                 |
| **Frontend**   | [Livewire 4](https://livewire.laravel.com) + [Flux UI](https://fluxui.dev) |
| **Styling**    | [Tailwind CSS 4](https://tailwindcss.com)                     |
| **Build Tool** | [Vite 8](https://vitejs.dev)                                  |
| **Database**   | MySQL (production) / SQLite (testing)                         |
| **Caching**    | Redis via [Predis](https://github.com/predis/predis)          |
| **Real-time**  | [Laravel Reverb](https://reverb.laravel.com) (WebSockets)     |
| **Auth**       | [Laravel Fortify](https://laravel.com/docs/fortify) + 2FA     |
| **Messaging**  | WhatsApp Business API (via webhook)                           |
| **Payments**   | [BillPlz](https://www.billplz.com/)                           |
| **QR Codes**   | [chillerlan/php-qrcode](https://github.com/chillerlan/php-QRCode) |
| **Testing**    | [Pest PHP](https://pestphp.com)                               |
| **Linting**    | [Laravel Pint](https://laravel.com/docs/pint)                 |
| **CI/CD**      | GitHub Actions                                                |

## 📐 Architecture

```
app/
├── Actions/           # Fortify auth action classes
├── Concerns/          # Shared traits
├── Enums/             # UserRole, QueueStatus, SubTier
├── Events/            # TicketJoined, TicketStatusUpdated
├── Http/
│   ├── Controllers/   # InviteController, WhatsApp Webhook
│   └── Middleware/     # RequireOwnerRole, RequireProfileCompleted
├── Listeners/         # WhatsApp notification listeners
├── Livewire/
│   ├── Actions/       # Reusable Livewire actions
│   ├── Admin/         # Platform admin panel components
│   ├── Business/      # Business tenant panel components
│   ├── PublicQueue/   # Public-facing queue components
│   └── Settings/      # User & business settings components
├── Mail/              # Staff & platform invitation mailers
├── Models/
│   ├── Marketing/     # CustomerFeedback, LoyaltyReward, LoyaltyVisit, EarnedReward, WhatsappMessage
│   ├── Platform/      # Invitation
│   ├── Queue/         # QueueEntry, Counter, QrCode
│   ├── Tenant/        # Business, Subscription, Payment, Counter
│   └── User.php
├── Providers/         # Service providers
└── Services/
    ├── Billing/       # BillPlzService
    ├── Queue/         # QueueService (core queue logic)
    └── WhatsApp/      # WhatsAppService
```

### Key Concepts

- **Multi-Tenancy** — Each business is a tenant. Users belong to a business via `business_id`. Role-based access (`superadmin`, `platform_staff`, `business_owner`, `business_staff`) controls visibility.
- **Event-Driven** — Ticket lifecycle events (`TicketJoined`, `TicketStatusUpdated`) trigger WhatsApp notifications via listeners.
- **Real-time** — Laravel Reverb broadcasts queue state changes to connected clients via WebSockets.
- **Queue Status Flow** — `waiting → called → serving → completed` (or `skipped` / `cancelled` at any point).

## 🚀 Getting Started

### Prerequisites

- **PHP** ≥ 8.3
- **Composer** ≥ 2.x
- **Node.js** ≥ 22.x
- **MySQL** 8.x (or SQLite for quick start)
- **Redis** (optional, for caching/queuing)
- [Laravel Herd](https://herd.laravel.com) (recommended for local dev on Windows/macOS)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/Striver26/queue.git
cd queue

# 2. Install PHP dependencies
#    Note: Flux UI requires credentials — see "Flux UI Setup" below
composer install

# 3. Install Node dependencies
npm install

# 4. Set up environment
cp .env.example .env
php artisan key:generate

# 5. Configure your database in .env
#    Update DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Run migrations
php artisan migrate

# 7. (Optional) Seed demo data
php artisan db:seed
```

### Flux UI Setup

This project uses [Flux UI](https://fluxui.dev) (a paid Livewire component library). You need valid credentials:

```bash
composer config http-basic.composer.fluxui.dev "{YOUR_EMAIL}" "{YOUR_LICENSE_KEY}"
```

### Running Locally

```bash
# Start all services concurrently (server + queue worker + Vite)
composer dev
```

This starts:
- **PHP dev server** at `http://localhost:8000`
- **Queue worker** listening for background jobs
- **Vite** dev server for hot-reloading assets

Or if using **Laravel Herd**, just run:

```bash
npm run dev
```

And visit your configured Herd domain (e.g., `http://queue.test`).

### Running Tests

```bash
# Run all tests with Pest
php artisan test

# Or directly
./vendor/bin/pest

# Run linter
composer lint
```

## 🔐 Environment Variables

| Variable | Description | Default |
|---|---|---|
| `APP_NAME` | Application name | `Qline` |
| `DB_CONNECTION` | Database driver | `mysql` |
| `QUEUE_CONNECTION` | Queue driver | `database` |
| `CACHE_STORE` | Cache driver | `database` |
| `BROADCAST_CONNECTION` | Broadcasting driver | `reverb` |
| `REDIS_CLIENT` | Redis client library | `predis` |
| `REVERB_APP_ID` | Laravel Reverb app ID | — |
| `REVERB_APP_KEY` | Laravel Reverb app key | — |
| `REVERB_APP_SECRET` | Laravel Reverb app secret | — |
| `REVERB_HOST` | Reverb WebSocket host | `localhost` |
| `REVERB_PORT` | Reverb WebSocket port | `8080` |
| `MAIL_MAILER` | Mail driver | `smtp` |
| `MAIL_HOST` | SMTP host | — |

> See [`.env.example`](.env.example) for all available variables.

## 🧪 CI/CD

GitHub Actions workflows run on every push/PR to `develop`, `main`, and `master` branches:

| Workflow | Description |
|---|---|
| **tests.yml** | Runs Pest tests across PHP 8.3, 8.4, 8.5 |
| **lint.yml** | Runs Laravel Pint code style checks |

## 📄 License

This project is proprietary software. All rights reserved.

---

<p align="center">
  Built with ❤️ in Malaysia 🇲🇾
</p>
