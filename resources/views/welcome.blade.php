<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php($title = __('Welcome'))
    @include('partials.head')
</head>
<body>
    <div class="pointer-events-none fixed inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_top_left,rgba(20,159,124,0.24),transparent_40%),radial-gradient(circle_at_top_right,rgba(255,116,73,0.18),transparent_24%)]"></div>

    <header class="relative z-10 px-4 pt-5 sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl items-center justify-between rounded-full border border-white/60 bg-white/70 px-5 py-3 shadow-[0_24px_60px_-34px_rgba(15,23,42,0.24)] backdrop-blur-xl">
            <x-app-logo href="{{ route('home') }}" />

            <nav class="hidden items-center gap-8 text-sm font-medium text-slate-500 md:flex">
                <a href="#features" class="hover:text-slate-950">Features</a>
                <a href="#workflow" class="hover:text-slate-950">Workflow</a>
                <a href="#pricing" class="hover:text-slate-950">Pricing</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-slate-600 hover:text-slate-950 sm:inline-flex">
                    Log in
                </a>
                <a href="{{ route('register') }}" class="btn-link-primary">
                    Start Free Trial
                </a>
            </div>
        </div>
    </header>

    <main class="relative z-10 px-4 pb-16 pt-8 sm:px-6 lg:px-8">
        <section class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div class="space-y-6">
                <span class="page-kicker">Queue management for modern service businesses</span>
                <div class="space-y-4">
                    <h1 class="text-5xl font-bold tracking-[-0.08em] text-slate-950 sm:text-6xl lg:text-7xl">
                        Your front desk feels calmer when the queue runs itself.
                    </h1>
                    <p class="max-w-2xl text-lg text-slate-600">
                        Qline helps customers join from a QR code or WhatsApp, track their turn live, and arrive only when they are actually needed.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn-link-primary">
                        Launch Your Queue
                    </a>
                    <a href="#workflow" class="btn-link-secondary">
                        See How It Works
                    </a>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="soft-card">
                        <p class="metric-label">Best For</p>
                        <p class="mt-3 text-lg font-bold text-slate-950">Clinics, salons, counters</p>
                    </div>
                    <div class="soft-card">
                        <p class="metric-label">Customer Join</p>
                        <p class="mt-3 text-lg font-bold text-slate-950">QR code or WhatsApp</p>
                    </div>
                    <div class="soft-card">
                        <p class="metric-label">Team Benefit</p>
                        <p class="mt-3 text-lg font-bold text-slate-950">Less shouting, less confusion</p>
                    </div>
                </div>
            </div>

            <div class="glass-card overflow-hidden !p-0">
                <div class="mesh-accent p-8 text-white">
                    <div class="flex items-center justify-between">
                        <span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-white/90">
                            Live Queue Preview
                        </span>
                        <span class="rounded-full bg-white/15 px-3 py-1 text-sm font-semibold backdrop-blur-sm">
                            Queue Open
                        </span>
                    </div>

                    <div class="mt-10 rounded-[1.8rem] border border-white/15 bg-white/10 p-6 backdrop-blur-sm">
                        <p class="text-sm text-white/70">Now serving</p>
                        <p class="mt-3 text-7xl font-bold tracking-[-0.08em]">A102</p>

                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-[1.3rem] border border-white/12 bg-black/10 p-4">
                                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-white/60">Waiting</p>
                                <p class="mt-2 text-3xl font-bold tracking-[-0.05em]">18</p>
                            </div>
                            <div class="rounded-[1.3rem] border border-white/12 bg-black/10 p-4">
                                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-white/60">Avg. Wait</p>
                                <p class="mt-2 text-3xl font-bold tracking-[-0.05em]">12 min</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-3">
                    <div class="rounded-[1.4rem] bg-brand-50 p-4">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-brand-700">Join</p>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Customers scan or message a code.</p>
                    </div>
                    <div class="rounded-[1.4rem] bg-slate-100 p-4">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-slate-500">Track</p>
                        <p class="mt-2 text-sm font-semibold text-slate-800">They watch their turn live from the phone.</p>
                    </div>
                    <div class="rounded-[1.4rem] bg-coral-50 p-4">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-coral-700">Call</p>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Staff move the line with one tap.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto mt-16 max-w-7xl" id="features">
            <div class="page-header">
                <div>
                    <span class="page-kicker">Why teams switch</span>
                    <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em] text-slate-950">Designed to be obvious for both staff and customers.</h2>
                </div>
                <p class="page-description">
                    The interface is built so customers know where to go next and staff can act without digging through cluttered controls.
                </p>
            </div>

            <div class="mt-8 grid gap-5 lg:grid-cols-3">
                <article class="feature-card">
                    <span class="badge-pill badge-pill--brand">Live control</span>
                    <h3 class="mt-5 text-2xl font-bold tracking-[-0.05em] text-slate-950">A cleaner command center for staff.</h3>
                    <p class="mt-3 text-sm text-slate-600">
                        Open or close the queue, call the next customer, and keep the line moving from one focused dashboard.
                    </p>
                </article>
                <article class="feature-card">
                    <span class="badge-pill">WhatsApp ready</span>
                    <h3 class="mt-5 text-2xl font-bold tracking-[-0.05em] text-slate-950">Customers get updates where they already are.</h3>
                    <p class="mt-3 text-sm text-slate-600">
                        Let people join and receive timely notifications without forcing them to install another app.
                    </p>
                </article>
                <article class="feature-card">
                    <span class="badge-pill">Built for retention</span>
                    <h3 class="mt-5 text-2xl font-bold tracking-[-0.05em] text-slate-950">Feedback and loyalty stay connected.</h3>
                    <p class="mt-3 text-sm text-slate-600">
                        Capture service quality, reward repeat visits, and turn queue traffic into returning customers.
                    </p>
                </article>
            </div>
        </section>

        <section class="mx-auto mt-16 max-w-7xl" id="workflow">
            <div class="glass-card">
                <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                    <div>
                        <span class="page-kicker">Workflow</span>
                        <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em] text-slate-950">Three steps from crowded counter to smooth handoff.</h2>
                        <p class="mt-4 text-sm text-slate-600">
                            Qline gives customers confidence while your team stays focused on service instead of managing uncertainty.
                        </p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="soft-card">
                            <p class="text-sm font-semibold text-brand-700">1. Join instantly</p>
                            <p class="mt-3 text-sm text-slate-600">A QR standee or WhatsApp keyword gets customers into the queue in seconds.</p>
                        </div>
                        <div class="soft-card">
                            <p class="text-sm font-semibold text-brand-700">2. Wait clearly</p>
                            <p class="mt-3 text-sm text-slate-600">They see position, estimated wait time, and live status without asking staff.</p>
                        </div>
                        <div class="soft-card">
                            <p class="text-sm font-semibold text-brand-700">3. Serve smoothly</p>
                            <p class="mt-3 text-sm text-slate-600">Your team calls the next customer with one tap and keeps momentum steady.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto mt-16 max-w-7xl" id="pricing">
            <div class="page-header">
                <div>
                    <span class="page-kicker">Simple pricing</span>
                    <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em] text-slate-950">Pick the pace that matches your business.</h2>
                </div>
                <p class="page-description">
                    Start light for short bursts or go all-in with the monthly plan when queues are a daily part of your operation.
                </p>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <article class="pricing-card">
                    <span class="badge-pill">Starter</span>
                    <h3 class="mt-6 text-3xl font-bold tracking-[-0.05em] text-slate-950">Daily Pass</h3>
                    <p class="mt-3 text-sm text-slate-600">Perfect for businesses that want flexibility without a long commitment.</p>
                    <p class="mt-8 text-5xl font-bold tracking-[-0.07em] text-slate-950">RM 15<span class="ml-2 text-base font-semibold text-slate-400">/day</span></p>
                    <ul class="mt-8 space-y-3 text-sm text-slate-600">
                        <li>Unlimited queue entries for the day</li>
                        <li>WhatsApp notifications</li>
                        <li>Printable QR standee</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-link-secondary mt-8 w-full">Try the Daily Pass</a>
                </article>

                <article class="pricing-card mesh-accent text-white">
                    <div class="absolute right-6 top-6 rounded-full bg-white/15 px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.24em] backdrop-blur-sm">
                        Best Value
                    </div>
                    <h3 class="text-3xl font-bold tracking-[-0.05em]">Monthly Ultimate</h3>
                    <p class="mt-3 text-sm text-white/78">For service teams that run queues every day and want all the growth tools included.</p>
                    <p class="mt-8 text-5xl font-bold tracking-[-0.07em]">RM 400<span class="ml-2 text-base font-semibold text-white/65">/month</span></p>
                    <ul class="mt-8 space-y-3 text-sm text-white/82">
                        <li>Everything in Daily Pass</li>
                        <li>Feedback dashboard</li>
                        <li>Loyalty rewards and retention tools</li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 inline-flex w-full items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-[0_24px_60px_-30px_rgba(0,0,0,0.45)] transition duration-300 hover:-translate-y-0.5 hover:bg-slate-100">
                        Start the Monthly Plan
                    </a>
                </article>
            </div>
        </section>

        <section class="mx-auto mt-16 max-w-7xl">
            <div class="glass-card mesh-accent text-center text-white">
                <span class="rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-white/90 backdrop-blur-sm">
                    Ready when you are
                </span>
                <h2 class="mt-6 text-4xl font-bold tracking-[-0.06em] sm:text-5xl">
                    Stop managing crowds. Start managing flow.
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-white/80 sm:text-base">
                    Give your customers a smoother wait and your team a calmer service rhythm with a queue system that actually feels modern.
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-brand-700 transition duration-300 hover:-translate-y-0.5 hover:bg-slate-100">
                        Start Free Trial
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:bg-white/15">
                        Log In
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="relative z-10 px-4 pb-8 text-center text-sm text-slate-500 sm:px-6 lg:px-8">
        © 2026 Qline. Designed to keep queues moving with less stress.
    </footer>
</body>
</html>
