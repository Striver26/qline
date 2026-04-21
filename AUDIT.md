## 📥 Backlog
1. Title: [Billing] Replace the hard-coded BillPlz payer email with a real billing identity
Description: `BillPlzService::createBill()` always sends `admin@qline.local` to BillPlz instead of a verified business/account billing contact.
Why it matters: This creates reconciliation problems, weakens payment auditability, and can break gateway expectations or receipts in production.
Suggested fix: Store a billing contact per business or platform account, validate it, and send that value to BillPlz.
Status: 📥 Backlog
Priority: P2
Module: Billing

2. Title: [Dashboard] Paginate or cap unbounded history views before data volume grows
Description: Billing history and several admin/business lists are loaded without hard caps or archival strategy.
Why it matters: These screens will become slow and memory-heavy as tenants and payment volume grow.
Suggested fix: Paginate all history views, add date filters, and archive cold data.
Status: 📥 Backlog
Priority: P2
Module: Dashboard

3. Title: [Codebase] Remove dead tables, duplicate views, and unused status artifacts
Description: The `qr_codes` table is not used by the application flow, duplicate `resources/views/components/...` blade files exist, and some enums/features are only partially referenced.
Why it matters: Dead code raises maintenance cost and makes audits harder because it is unclear what is authoritative.
Suggested fix: Delete unused tables/files after verifying no live dependency, or wire them into real flows if they are intended.
Status: 📥 Backlog
Priority: P3
Module: System

4. Title: [Dependencies] Stop using wildcard Composer constraints for core infrastructure packages
Description: `composer.json` uses `laravel/reverb: *` and `predis/predis: *`.
Why it matters: Fresh installs can drift to unexpected versions and create production-only breakage even with the same codebase.
Suggested fix: Pin supported version ranges and keep upgrades intentional.
Status: 📥 Backlog
Priority: P2
Module: System

## 🧠 Ready
1. Title: [Admin/Auth] Restrict superadmin creation and role changes to superadmin-only workflows
Description: `UsersIndex` lets any admin-level user create `superadmin` accounts and change roles, while route protection only checks `RequireAdminRole`.
Why it matters: A compromised or over-privileged `platform_staff` account can escalate to full platform ownership immediately.
Suggested fix: Add a superadmin-only policy for creating/editing/deleting privileged accounts and validate allowed target roles server-side.
Status: 🧠 Ready
Priority: P0
Module: Auth

2. Title: [Billing] Block business staff from changing subscriptions
Description: `business/billing` is not owner-protected and `SubscriptionBilling::subscribe()` has no owner authorization.
Why it matters: Any business staff account can purchase, replace, or spam subscription/payment records.
Suggested fix: Put `RequireOwnerRole` on the billing route and authorize the action again inside the component/service.
Status: 🧠 Ready
Priority: P1
Module: Billing

3. Title: [Queue] Move admission checks inside the locked transaction
Description: `QueueService::join()` and `addManual()` validate `queue_status`, `daily_limit`, and customer limits against a stale `Business` model before locking the row.
Why it matters: Closed or full queues can still accept tickets from stale pages or concurrent requests.
Suggested fix: Re-read the business row with `lockForUpdate()`, then perform all limit/status checks inside the transaction before creating the ticket.
Status: 🧠 Ready
Priority: P0
Module: Queue

4. Title: [Queue] Enforce a real server-side queue state machine
Description: `callNext`, `addManual`, `markServing`, `markDone`, `skip`, and `cancel` do not validate allowed transitions on the server.
Why it matters: Livewire request tampering or operator mistakes can move tickets into impossible states and corrupt reporting/rewards.
Suggested fix: Centralize allowed transitions in `QueueService` and reject illegal operations by current status and queue state.
Status: 🧠 Ready
Priority: P0
Module: Queue

5. Title: [Queue] Stop resetting ticket counters on manual queue close
Description: `closeQueue()` zeroes `current_number` and `entries_today` immediately, not just at day rollover.
Why it matters: Reopening the queue the same day reuses ticket numbers/codes and breaks identity, analytics, and customer trust.
Suggested fix: Separate “close queue” from “reset day”; only reset numbering in a controlled day-boundary job.
Status: 🧠 Ready
Priority: P0
Module: Queue

6. Title: [Queue] Require completed status and one-time submission for customer feedback
Description: `FeedbackForm` accepts feedback for any tokenized ticket and only checks duplicates on mount, with no DB uniqueness.
Why it matters: Customers can submit feedback before service, after cancellation, or multiple times through retries/races.
Suggested fix: Enforce `completed` status at submit time, add a unique constraint on `queue_entry_id`, and reject repeat writes server-side.
Status: 🧠 Ready
Priority: P1
Module: Queue

7. Title: [Queue] Normalize and validate customer phone numbers before storing or using them
Description: Public join only trims the input and stores it as-is.
Why it matters: One customer can bypass daily limits and loyalty matching by changing formatting, and WhatsApp sends can fail on bad values.
Suggested fix: Normalize to E.164 or a single canonical format and validate length/country rules before persistence.
Status: 🧠 Ready
Priority: P1
Module: Queue

8. Title: [Queue] Add idempotency and deduplication for public joins and WhatsApp joins
Description: There is no request/message idempotency for Livewire joins or inbound WhatsApp `JOIN` messages.
Why it matters: Double-clicks, retries, or webhook re-deliveries can create duplicate tickets for the same customer.
Suggested fix: Store inbound message IDs, add join request idempotency keys, and reject duplicate creates inside a transaction.
Status: 🧠 Ready
Priority: P1
Module: Queue

9. Title: [Queue] Notify customers when bulk queue closure cancels their tickets
Description: `closeQueue()` updates rows in bulk and never emits per-ticket events or WhatsApp notifications.
Why it matters: Customers can be silently cancelled with no alert, then only discover it when they manually refresh.
Suggested fix: Process affected tickets in batches through a service/job that emits notifications and broadcasts per ticket.
Status: 🧠 Ready
Priority: P1
Module: Notification

10. Title: [Tenant] Add foreign keys and one authoritative ownership model
Description: `users.business_id` has no foreign key, `businesses.user_id` is nullable and not maintained in onboarding, and invitations also lack FK protection.
Why it matters: Deleting a business or user can leave orphaned accounts, broken tenant pointers, and inconsistent ownership state.
Suggested fix: Choose one ownership direction, backfill data, add FKs, and update onboarding/deletion flows to preserve integrity.
Status: 🧠 Ready
Priority: P0
Module: System

11. Title: [Subscription] Enforce subscription validity on every queue action
Description: Queue opening checks subscription status once, but joins keep working after expiry and `ExpireSubscriptions` does not close queues or downgrade entitlements.
Why it matters: Expired tenants can continue using the product, which is both a monetization leak and an access-control failure.
Suggested fix: Check active entitlement on every queue-mutating action and close/downgrade businesses when subscriptions expire.
Status: 🧠 Ready
Priority: P0
Module: Billing

12. Title: [Database] Add unique and composite indexes for hot paths and bearer tokens
Description: Core lookups like `queue_entries(business_id,status)`, `cancel_token`, `wa_id + date`, `payments.reference`, and single-subscription-per-business are not protected/indexed.
Why it matters: Performance degrades quickly and duplicate/ambiguous records remain possible under load.
Suggested fix: Add the missing indexes and uniqueness constraints, then backfill/fix conflicting data before rollout.
Status: 🧠 Ready
Priority: P1
Module: System

13. Title: [System] Add audit trails and replace hard deletes with retention-aware archival
Description: Admin screens can delete users, businesses, queue logs, and WhatsApp logs directly with no audit log or recovery path.
Why it matters: This is unsafe for SaaS operations, support, billing disputes, and compliance reviews.
Suggested fix: Introduce immutable admin action logs, soft deletes where appropriate, and archival/pruning jobs behind policy controls.
Status: 🧠 Ready
Priority: P1
Module: System

14. Title: [Security] Require step-up authentication for destructive admin and billing actions
Description: High-risk actions like role changes, user deletion, billing changes, and business deletion have no password re-confirmation or 2FA step-up.
Why it matters: A stolen session can perform irreversible platform-wide damage.
Suggested fix: Add password-confirm/2FA gates for privileged actions and log the actor, target, and reason.
Status: 🧠 Ready
Priority: P2
Module: Auth

15. Title: [Operations] Make scheduled jobs safe in multi-instance production
Description: Scheduled commands do not use `onOneServer()` or `withoutOverlapping()`, and reset logic is server-time based.
Why it matters: Duplicate runners can race, double-close queues, and produce inconsistent resets in horizontally scaled deployments.
Suggested fix: Add scheduler locks, chunked processing, and business-timezone-aware execution rules.
Status: 🧠 Ready
Priority: P1
Module: System

16. Title: [Notification] Persist inbound WhatsApp events and delivery status updates
Description: The app stores outbound messages only and ignores inbound message history/status callbacks as durable audit data.
Why it matters: Support teams cannot explain duplicate joins, missing deliveries, or customer disputes.
Suggested fix: Record inbound message IDs, status events, and delivery transitions in a normalized message log.
Status: 🧠 Ready
Priority: P1
Module: Notification

17. Title: [Testing] Add production-like CI coverage for MySQL, queues, schedules, and concurrency
Description: CI runs on SQLite with sync queues, which hides MySQL enum failures, schedule behavior, and race conditions.
Why it matters: The current green test suite materially overstates production readiness.
Suggested fix: Add MySQL-backed tests, queued listener tests, scheduler tests, and concurrency-focused integration cases.
Status: 🧠 Ready
Priority: P1
Module: System

18. Title: [User Lifecycle] Prevent orphaned tenants and last-superadmin self-deletion
Description: Generic self-delete flow can remove owners and superadmins without tenant transfer or platform safety checks.
Why it matters: One bad click can strand a paying business or leave the platform with no recovery administrator.
Suggested fix: Block self-delete for last-superadmin and business owners until ownership transfer or replacement is completed.
Status: 🧠 Ready
Priority: P0
Module: Auth

19. Title: [System] Enforce `is_active` flags throughout the application
Description: `users.is_active` and `businesses.is_active` exist in schema but are never enforced in auth or business access paths.
Why it matters: Suspension/deactivation is effectively a fake feature today.
Suggested fix: Add middleware/policies that block inactive users/businesses and update admin tooling to manage those states deliberately.
Status: 🧠 Ready
Priority: P1
Module: System

20. Title: [Admin/Feedback] Replace the placeholder feedback dashboard with a real admin feature
Description: `Admin\Feedback\FeedbackIndex` is a stub and the blade is just placeholder text.
Why it matters: A marketed admin capability is not actually present, which creates support and sales risk.
Suggested fix: Build the real feedback index with filtering, tenant context, and export/report support.
Status: 🧠 Ready
Priority: P1
Module: Dashboard

## 🚧 In Progress
1. Title: [Invitations] Fix business staff invitation creation and `invited_by` persistence
Description: `StaffManagement` tries to write `invited_by`, but `Invitation` mass assignment drops it and MySQL rejects the insert.
Why it matters: Business staff invitation flow is broken in production today.
Suggested fix: Add `invited_by` to the model, add proper foreign keys, and test the full invite path against MySQL.
Status: 🚧 In Progress
Priority: P0
Module: Auth

2. Title: [Invitations/Auth] Persist invited users as verified and make acceptance transactional
Description: `InviteController` writes `email_verified_at`, but `User` fillable rules drop it; acceptance also is not wrapped in a lock-safe transaction.
Why it matters: Invited users can be forced back into email verification unexpectedly, and concurrent accepts can race.
Suggested fix: Persist `email_verified_at`, lock the invitation row, and commit user creation plus invite acceptance atomically.
Status: 🚧 In Progress
Priority: P1
Module: Auth

3. Title: [Queue] Align schema, jobs, and UI around the `no_show` state
Description: PHP enum includes `no_show`, cleanup job writes it, but MySQL `queue_entries.status` does not allow it and UI filters do not support it.
Why it matters: Scheduled cleanup can fail in production and the state model remains half-implemented.
Suggested fix: Add the DB enum value, add filters/colors/reporting, and decide the real business rules for no-show handling.
Status: 🚧 In Progress
Priority: P0
Module: Queue

4. Title: [TV Display] Backfill and require TV access tokens for every business
Description: `TvDisplay` checks `request()->query('token')`, but `tv_token` is nullable and older businesses can have null tokens.
Why it matters: A null token means the TV page can be opened without any secret at all.
Suggested fix: Backfill tokens, make the column non-null and unique, and add token rotation support.
Status: 🚧 In Progress
Priority: P0
Module: Queue

5. Title: [Business Settings] Replace freeform business-hours text with a structured schedule and timezone
Description: The settings page stores human-written text, while `CleanupStaleQueues` expects machine-readable JSON and a business timezone.
Why it matters: Automatic open/close behavior cannot be trusted and will fail silently for real operators.
Suggested fix: Use structured per-day fields plus timezone selection, validate them, and cast them properly on the model.
Status: 🚧 In Progress
Priority: P1
Module: System

6. Title: [Business Settings] Make slugs stable and unique, and persist business name changes correctly
Description: Updating settings changes `slug` but not `name`, and slug creation has no uniqueness strategy.
Why it matters: Public URLs and QR codes can break, onboarding can 500 on duplicate names, and admin data becomes inconsistent.
Suggested fix: Persist name updates, stop changing public slugs casually, and add collision-safe slug generation when creation is intentional.
Status: 🚧 In Progress
Priority: P1
Module: Booking

7. Title: [Loyalty] Register and queue `TicketCompleted` reward processing
Description: `ProcessLoyaltyRewards` exists but is not registered in `EventServiceProvider`.
Why it matters: Loyalty visits and earned rewards never accrue even though the feature is advertised.
Suggested fix: Register the listener, make it queued/idempotent, and add end-to-end tests around completion and reward issuance.
Status: 🚧 In Progress
Priority: P1
Module: Notification

8. Title: [Plans] Finish advanced-tier and counter entitlements or remove them from monetization paths
Description: Counters exist in schema/models, advanced tier exists in config/enum, and the UI claims multi-counter support, but the workflow is incomplete.
Why it matters: The product is selling functionality that is not operationally delivered.
Suggested fix: Either ship counter management and entitlement enforcement fully, or remove advanced-tier promises from billing and marketing.
Status: 🚧 In Progress
Priority: P1
Module: Billing

9. Title: [Realtime] Replace polling with real Reverb subscriptions or remove the realtime claim
Description: Reverb and Echo are installed, but public and business pages use `wire:poll` and there are no Echo subscriptions.
Why it matters: Load scales linearly with active clients and the “realtime” architecture is not actually in use.
Suggested fix: Implement channel subscriptions on queue pages or simplify the stack and stop pretending polling is websocket realtime.
Status: 🚧 In Progress
Priority: P2
Module: System

10. Title: [Queue UI] Stop rendering stale `position` values and compute live order consistently
Description: `queue_entries.position` is initialized to `0` and never maintained, but TV/dashboard blades display it.
Why it matters: Staff and customers can see wrong position numbers on the most visible screens in the product.
Suggested fix: Either maintain positions transactionally or remove the stored column and render a computed order everywhere.
Status: 🚧 In Progress
Priority: P1
Module: Queue

11. Title: [Plans] Reconcile published prices and feature claims with actual configuration
Description: Billing UI advertises RM15/RM400 and features like loyalty/multi-counter, while config and shipped behavior do not match.
Why it matters: This creates billing disputes and damages trust the moment customers compare invoice, UI, and feature reality.
Suggested fix: Use a single plan source of truth for price/label/features and render billing from config or a plans table.
Status: 🚧 In Progress
Priority: P1
Module: Billing

12. Title: [Analytics] Correct misleading platform and business KPIs
Description: Examples include `activeTickets = QueueEntry::count()`, 30-day cash labeled as MRR, and “served today” derived from waiting counts.
Why it matters: Operators and management will make bad decisions from wrong numbers.
Suggested fix: Define metrics formally, compute them from the right statuses/time windows, and add tests for each KPI.
Status: 🚧 In Progress
Priority: P2
Module: Dashboard

## 👀 Review
1. Title: [Admin/Auth] Remove the invalid `user` role from admin UI and validate role changes server-side
Description: Admin blades expose a `user` role that does not exist in the `users.role` enum, and update flows do not validate against `UserRole`.
Why it matters: The UI can trigger write failures or invalid state attempts during live admin operations.
Suggested fix: Bind all role inputs to the enum list only and reject unknown values in the component.
Status: 👀 Review
Priority: P1
Module: Auth

2. Title: [Billing/Security] Fail closed when BillPlz secrets are missing
Description: `BillPlzService` creates mock bills when secrets are absent and webhook signature verification returns true when the signature key is unset.
Why it matters: A production misconfiguration can silently bypass billing security and create fake payment flows.
Suggested fix: Only allow mocks in explicit local/testing mode and fail hard in non-dev environments when required secrets are missing.
Status: 👀 Review
Priority: P0
Module: Billing

3. Title: [Billing] Roll back payment processing on activation failure and honor `processPayment()` result on redirect
Description: `BillPlzWebhookController` catches exceptions inside the DB transaction, which can commit partial writes, and the redirect path ignores the returned response entirely.
Why it matters: A user can be shown “payment confirmed” while the subscription was never activated or only half-updated.
Suggested fix: Let the transaction throw to force rollback, then map the final result into the redirect message explicitly.
Status: 👀 Review
Priority: P0
Module: Billing

4. Title: [Billing] Validate callback amount, currency, and bill identity, and make payment references unique
Description: Callback processing trusts `paid=true` plus reference lookup and does not verify expected amount/currency/collection against the payment record.
Why it matters: Underpayments, mismatched bills, and ambiguous references can still credit a subscription incorrectly.
Suggested fix: Add a unique constraint on payment reference and compare callback payload to the stored payment before activating anything.
Status: 👀 Review
Priority: P0
Module: Billing

5. Title: [Notification/Security] Fail closed when WhatsApp secrets or tokens are missing
Description: Webhook signature verification returns true when `META_APP_SECRET` is absent, and outbound sends record “sent” mock rows when the token is missing.
Why it matters: Production misconfiguration can accept forged webhooks and produce false-delivery audit logs.
Suggested fix: Allow bypasses only in explicit local/testing mode and fail loudly elsewhere.
Status: 👀 Review
Priority: P0
Module: Notification

6. Title: [Queue/Public] Replace shared plaintext action tokens with scoped, unique, hashed or signed tokens
Description: `cancel_token` is reused for status viewing, ticket cancellation, and feedback access, and it is stored in plaintext without uniqueness enforcement.
Why it matters: One leaked token grants multiple actions and a database leak exposes active customer actions directly.
Suggested fix: Split status/cancel/feedback scopes, enforce uniqueness, and store hashed or signed values where possible.
Status: 👀 Review
Priority: P1
Module: Queue

7. Title: [Queue/Public] Support multiple same-day tickets per business in local recovery
Description: Browser recovery stores one token per business slug, but the backend allows up to three tickets per customer per day.
Why it matters: The client can lose active tickets or clear the wrong one after completion/cancellation.
Suggested fix: Store an array of active tickets per business and key local recovery by ticket token, not just slug.
Status: 👀 Review
Priority: P2
Module: Queue

8. Title: [Loyalty] Add locking and uniqueness to reward redemption and visit accrual
Description: `RewardService::redeem()` and the reward accrual flow rely on read-then-write logic without locks or unique keys.
Why it matters: Parallel staff clicks or repeated completion processing can duplicate or double-redeem rewards.
Suggested fix: Use transactional updates with status predicates and add unique constraints around per-ticket visit creation.
Status: 👀 Review
Priority: P1
Module: Notification

9. Title: [Search/Admin] Group `OR` search clauses so filters return correct results
Description: Users, businesses, and payments admin queries use ungrouped `orWhere` conditions before applying status/role filters.
Why it matters: Admins can see incorrect result sets and make the wrong destructive action against the wrong record.
Suggested fix: Wrap search clauses in a grouped `where(function...)` block before applying additional filters.
Status: 👀 Review
Priority: P2
Module: Dashboard

10. Title: [Business Hours] Support overnight schedules instead of simple string comparison
Description: `CleanupStaleQueues::shouldCloseQueue()` compares `currentTime < openTime || > closeTime`, which breaks for windows like `18:00-02:00`.
Why it matters: Night businesses will be auto-closed at the wrong times.
Suggested fix: Parse schedule windows into real time ranges with overnight support.
Status: 👀 Review
Priority: P2
Module: System

11. Title: [Integrations] Add timeout, retry, and backoff policy to external HTTP calls
Description: BillPlz and WhatsApp HTTP clients use default behavior with no explicit timeout/retry/circuit-breaker strategy.
Why it matters: Slow third parties can stall worker throughput, create duplicate manual retries, and amplify outages.
Suggested fix: Set conservative timeouts, retry only idempotent calls, and capture structured failure metadata.
Status: 👀 Review
Priority: P1
Module: System

12. Title: [Notification] Fix WhatsApp welcome tracker URLs to use the public ticket token
Description: `SendWhatsAppWelcome` builds `/status/{entry->id}` while the route expects the queue token.
Why it matters: The most important customer notification currently sends a broken tracking link.
Suggested fix: Generate the URL from the public route with `cancel_token` until token scopes are split.
Status: 👀 Review
Priority: P1
Module: Notification

## 🚀 Deployed
None.

## ❌ Blocked
1. Title: [Operations] Define and codify the production runtime topology
Description: The repo does not define the intended production model for worker supervision, Redis usage, Reverb hosting, scheduler ownership, or rollback strategy.
Why it matters: Several fixes above depend on whether the platform will run single-node, autoscaled workers, Horizon, or managed websocket infrastructure.
Suggested fix: Publish the target production architecture and codify it in deployment manifests/runbooks before final hardening.
Status: ❌ Blocked
Priority: P1
Module: System

2. Title: [Compliance] Define the retention and legal policy before keeping admin-side prune/delete features
Description: The code exposes destructive pruning/deletion, but the repo provides no retention policy for payments, queue history, feedback, or message logs.
Why it matters: You cannot make archival and deletion behavior safe without a legal/product retention decision.
Suggested fix: Lock deletion features behind a documented retention policy, then implement archival and purge windows that match it.
Status: ❌ Blocked
Priority: P1
Module: System