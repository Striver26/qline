# Contributing to Qline

Thank you for your interest in contributing to **Qline**! This document provides guidelines and instructions to help you get started.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Development Setup](#development-setup)
- [Branching Strategy](#branching-strategy)
- [Coding Standards](#coding-standards)
- [Commit Messages](#commit-messages)
- [Pull Request Process](#pull-request-process)
- [Testing](#testing)
- [Architecture Guidelines](#architecture-guidelines)

---

## Code of Conduct

By contributing, you agree to maintain a respectful and inclusive environment. Be kind, constructive, and professional in all interactions.

## Development Setup

1. Follow the [Getting Started](README.md#getting-started) section in the README
2. Ensure all tests pass before making changes:
   ```bash
   php artisan test
   ```
3. Ensure code style passes:
   ```bash
   composer lint
   ```

## Branching Strategy

We follow a **Git Flow** inspired branching model:

| Branch | Purpose |
|--------|---------|
| `main` | Production-ready code |
| `develop` | Integration branch for features |
| `feature/*` | New features (branch off `develop`) |
| `bugfix/*` | Bug fixes (branch off `develop`) |
| `hotfix/*` | Critical production fixes (branch off `main`) |

### Creating a Branch

```bash
# For features
git checkout develop
git pull origin develop
git checkout -b feature/your-feature-name

# For bug fixes
git checkout develop
git pull origin develop
git checkout -b bugfix/short-description
```

## Coding Standards

### PHP

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use [Laravel Pint](https://laravel.com/docs/pint) for automatic formatting:
  ```bash
  composer lint
  ```
- Use strict typing where possible
- Prefer typed properties and return types
- Use PHP 8.3+ features (enums, readonly properties, etc.)

### Blade / Livewire

- Keep Livewire components focused and single-responsibility
- Use Flux UI components where applicable
- Prefer Livewire properties over Alpine.js state when possible

### General

- **No `dd()` or `dump()`** in committed code
- **No hardcoded credentials** — use `.env` variables
- Add docblocks for non-trivial methods
- Keep methods short and focused (< 30 lines ideally)

## Commit Messages

Follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <description>

[optional body]
[optional footer]
```

### Types

| Type | Description |
|------|-------------|
| `feat` | A new feature |
| `fix` | A bug fix |
| `docs` | Documentation changes |
| `style` | Code style changes (formatting, semicolons, etc.) |
| `refactor` | Code refactoring (no feature or fix) |
| `test` | Adding or updating tests |
| `chore` | Build process, CI, or tooling changes |
| `perf` | Performance improvements |

### Examples

```
feat(queue): add priority queue support
fix(whatsapp): handle webhook timeout gracefully
docs(readme): update installation instructions
refactor(models): extract loyalty logic into service
```

## Pull Request Process

1. **Create your branch** from `develop`
2. **Make your changes** following the coding standards
3. **Write or update tests** for your changes
4. **Run all checks locally:**
   ```bash
   composer lint
   php artisan test
   ```
5. **Push your branch** and create a Pull Request to `develop`
6. **Fill in the PR template** with:
   - Description of changes
   - Related issue number (if applicable)
   - Screenshots for UI changes
   - Testing steps taken
7. **Request a review** from at least one team member
8. **Address review feedback** promptly

### PR Checklist

- [ ] Code follows the project's style guidelines
- [ ] Self-reviewed the code changes
- [ ] Added/updated tests as needed
- [ ] All tests pass locally
- [ ] Updated documentation if needed
- [ ] No sensitive data or credentials committed

## Testing

We use [Pest PHP](https://pestphp.com/) for testing.

### Running Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run a specific test file
php artisan test --filter=QueueServiceTest

# Run only unit tests
php artisan test --testsuite=Unit

# Run only feature tests
php artisan test --testsuite=Feature
```

### Writing Tests

- Place **unit tests** in `tests/Unit/`
- Place **feature/integration tests** in `tests/Feature/`
- Use Pest-style syntax:

```php
it('can join an open queue', function () {
    $business = Business::factory()->create(['queue_status' => 'open']);

    $entry = app(QueueService::class)->join($business, '60123456789');

    expect($entry)
        ->status->toBe('waiting')
        ->position->toBe(1);
});
```

### Test Database

Tests use an **in-memory SQLite** database (configured in `phpunit.xml`). No external database setup is needed for testing.

## Architecture Guidelines

### Adding New Features

1. **Models** — Add to the appropriate namespace under `app/Models/` (`Queue/`, `Tenant/`, `Marketing/`, `Platform/`)
2. **Business logic** — Encapsulate in **Service classes** under `app/Services/`
3. **UI components** — Create **Livewire components** under the appropriate panel (`Admin/`, `Business/`, `PublicQueue/`)
4. **Events** — Dispatch domain events for cross-cutting concerns (e.g., notifications)
5. **Routes** — Add to `routes/web.php` under the correct middleware group

### Key Principles

- **Fat Services, Thin Controllers** — Keep controllers/Livewire components lean; delegate logic to services
- **Event-Driven Side Effects** — Use events + listeners for cross-cutting concerns (WhatsApp, analytics, etc.)
- **Tenant Isolation** — Always scope queries by `business_id` in the tenant context
- **Role-Based Access** — Use the `UserRole` enum and middleware for authorization

---

Questions? Reach out to the team or open a Discussion on GitHub. 🚀
