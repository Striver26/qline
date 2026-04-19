<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php($title = __('welcome.title'))
    @include('partials.head')
</head>

<body>
    {{-- Ambient background gradients --}}
    <div
        class="pointer-events-none fixed inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_top_left,rgba(20,159,124,0.24),transparent_40%),radial-gradient(circle_at_top_right,rgba(255,116,73,0.18),transparent_24%)]">
    </div>
    <div
        class="pointer-events-none fixed bottom-[-160px] left-[-100px] h-96 w-96 rounded-full bg-brand-300/15 blur-3xl">
    </div>

    {{-- ─── Navigation ──────────────────────────────────── --}}
    <header class="relative z-10 px-4 pt-5 sm:px-6 lg:px-8">
        <div
            class="mx-auto flex max-w-7xl items-center justify-between rounded-full border border-white/60 bg-white/70 px-5 py-3 shadow-[0_24px_60px_-34px_rgba(15,23,42,0.24)] backdrop-blur-xl">

            <x-app-logo href="{{ route('home') }}" />

            <nav class="hidden items-center gap-8 text-sm font-medium text-slate-500 md:flex">
                <a href="#features" class="transition hover:text-slate-950">{{ __('welcome.nav_features') }}</a>
                <a href="#workflow" class="transition hover:text-slate-950">{{ __('welcome.nav_workflow') }}</a>
                <a href="#pricing" class="transition hover:text-slate-950">{{ __('welcome.nav_pricing') }}</a>
            </nav>

            <div class="flex items-center gap-2">
                {{-- Language switcher --}}
                <div class="hidden items-center gap-1 sm:flex">
                    <a href="{{ route('lang.switch', ['locale' => 'en']) }}"
                        class="rounded-full px-2.5 py-1 text-xs font-semibold transition {{ app()->getLocale() === 'en' ? 'bg-slate-950 text-white' : 'text-slate-500 hover:text-slate-950' }}">
                        EN
                    </a>
                    <a href="{{ route('lang.switch', ['locale' => 'ms']) }}"
                        class="rounded-full px-2.5 py-1 text-xs font-semibold transition {{ app()->getLocale() === 'ms' ? 'bg-slate-950 text-white' : 'text-slate-500 hover:text-slate-950' }}">
                        BM
                    </a>
                </div>

                <a href="{{ route('login') }}"
                    class="hidden text-sm font-semibold text-slate-600 transition hover:text-slate-950 sm:inline-flex">
                    {{ __('welcome.nav_login') }}
                </a>
                <a href="{{ route('register') }}" class="btn-link-primary">
                    {{ __('welcome.register_now') }}
                </a>
            </div>
        </div>
    </header>

    <main class="relative z-10 px-4 pb-16 pt-8 sm:px-6 lg:px-8">

        {{-- ─── Hero ────────────────────────────────────────── --}}
        <section class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div class="space-y-6">
                <span class="page-kicker">{{ __('welcome.hero_kicker') }}</span>

                <div class="space-y-4">
                    <h1 class="text-5xl font-bold tracking-[-0.08em] text-slate-950 sm:text-6xl lg:text-7xl">
                        {{ __('welcome.hero_title') }}
                    </h1>
                    <p class="max-w-2xl text-lg text-slate-600">
                        {{ __('welcome.hero_subtitle') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn-link-primary">
                        {{ __('welcome.hero_cta_primary') }}
                    </a>
                    <a href="#workflow" class="btn-link-secondary">
                        {{ __('welcome.hero_cta_secondary') }}
                    </a>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="soft-card">
                        <p class="metric-label">{{ __('welcome.stat_best_for_label') }}</p>
                        <p class="mt-3 text-lg font-bold text-slate-950">{{ __('welcome.stat_best_for_value') }}</p>
                    </div>
                    <div class="soft-card">
                        <p class="metric-label">{{ __('welcome.stat_join_label') }}</p>
                        <p class="mt-3 text-lg font-bold text-slate-950">{{ __('welcome.stat_join_value') }}</p>
                    </div>
                    <div class="soft-card">
                        <p class="metric-label">{{ __('welcome.stat_team_label') }}</p>
                        <p class="mt-3 text-lg font-bold text-slate-950">{{ __('welcome.stat_team_value') }}</p>
                    </div>
                </div>
            </div>

            {{-- Live Queue Preview Card --}}
            <div class="glass-card overflow-hidden !p-0">
                <div class="mesh-accent p-8 text-white">
                    <div class="flex items-center justify-between">
                        <span
                            class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-white/90 backdrop-blur-sm">
                            {{ __('welcome.preview_badge') }}
                        </span>
                        <span class="rounded-full bg-white/15 px-3 py-1 text-sm font-semibold backdrop-blur-sm">
                            {{ __('welcome.preview_open') }}
                        </span>
                    </div>

                    <div class="mt-10 rounded-[1.8rem] border border-white/15 bg-white/10 p-6 backdrop-blur-sm">
                        <p class="text-sm text-white/70">{{ __('welcome.preview_serving') }}</p>
                        <p class="mt-3 text-7xl font-bold tracking-[-0.08em]">A102</p>

                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-[1.3rem] border border-white/12 bg-black/10 p-4">
                                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-white/60">
                                    {{ __('welcome.preview_waiting') }}
                                </p>
                                <p class="mt-2 text-3xl font-bold tracking-[-0.05em]">18</p>
                            </div>
                            <div class="rounded-[1.3rem] border border-white/12 bg-black/10 p-4">
                                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-white/60">
                                    {{ __('welcome.preview_avg_wait') }}
                                </p>
                                <p class="mt-2 text-3xl font-bold tracking-[-0.05em]">12 {{ __('welcome.preview_min') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-3">
                    <div class="rounded-[1.4rem] bg-brand-50 p-4">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-brand-700">
                            {{ __('welcome.preview_join') }}
                        </p>
                        <p class="mt-2 text-sm font-semibold text-slate-800">{{ __('welcome.preview_join_desc') }}</p>
                    </div>
                    <div class="rounded-[1.4rem] bg-slate-100 p-4">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            {{ __('welcome.preview_track') }}
                        </p>
                        <p class="mt-2 text-sm font-semibold text-slate-800">{{ __('welcome.preview_track_desc') }}</p>
                    </div>
                    <div class="rounded-[1.4rem] bg-coral-50 p-4">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-coral-700">
                            {{ __('welcome.preview_call') }}
                        </p>
                        <p class="mt-2 text-sm font-semibold text-slate-800">{{ __('welcome.preview_call_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─── Features ────────────────────────────────────── --}}
        <section class="mx-auto mt-16 max-w-7xl" id="features">
            <div class="page-header">
                <div>
                    <span class="page-kicker">{{ __('welcome.features_kicker') }}</span>
                    <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em] text-slate-950">
                        {{ __('welcome.features_title') }}
                    </h2>
                </div>
                <p class="page-description">{{ __('welcome.features_subtitle') }}</p>
            </div>

            <div class="mt-8 grid gap-5 lg:grid-cols-3">
                <article class="feature-card">
                    <span class="badge-pill badge-pill--brand">{{ __('welcome.feature_1_badge') }}</span>
                    <h3 class="mt-5 text-2xl font-bold tracking-[-0.05em] text-slate-950">
                        {{ __('welcome.feature_1_title') }}
                    </h3>
                    <p class="mt-3 text-sm text-slate-600">{{ __('welcome.feature_1_desc') }}</p>
                </article>

                <article class="feature-card">
                    <span class="badge-pill">{{ __('welcome.feature_2_badge') }}</span>
                    <h3 class="mt-5 text-2xl font-bold tracking-[-0.05em] text-slate-950">
                        {{ __('welcome.feature_2_title') }}
                    </h3>
                    <p class="mt-3 text-sm text-slate-600">{{ __('welcome.feature_2_desc') }}</p>
                </article>

                <article class="feature-card">
                    <span class="badge-pill">{{ __('welcome.feature_3_badge') }}</span>
                    <h3 class="mt-5 text-2xl font-bold tracking-[-0.05em] text-slate-950">
                        {{ __('welcome.feature_3_title') }}
                    </h3>
                    <p class="mt-3 text-sm text-slate-600">{{ __('welcome.feature_3_desc') }}</p>
                </article>
            </div>
        </section>

        {{-- ─── Workflow ─────────────────────────────────────── --}}
        <section class="mx-auto mt-16 max-w-7xl" id="workflow">
            <div class="glass-card">
                <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                    <div>
                        <span class="page-kicker">{{ __('welcome.workflow_kicker') }}</span>
                        <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em] text-slate-950">
                            {{ __('welcome.workflow_title') }}
                        </h2>
                        <p class="mt-4 text-sm text-slate-600">{{ __('welcome.workflow_subtitle') }}</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="soft-card">
                            <p class="text-sm font-semibold text-brand-700">{{ __('welcome.step_1_title') }}</p>
                            <p class="mt-3 text-sm text-slate-600">{{ __('welcome.step_1_desc') }}</p>
                        </div>
                        <div class="soft-card">
                            <p class="text-sm font-semibold text-brand-700">{{ __('welcome.step_2_title') }}</p>
                            <p class="mt-3 text-sm text-slate-600">{{ __('welcome.step_2_desc') }}</p>
                        </div>
                        <div class="soft-card">
                            <p class="text-sm font-semibold text-brand-700">{{ __('welcome.step_3_title') }}</p>
                            <p class="mt-3 text-sm text-slate-600">{{ __('welcome.step_3_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─── Pricing ──────────────────────────────────────── --}}
        <section class="mx-auto mt-16 max-w-7xl" id="pricing">
            <div class="page-header">
                <div>
                    <span class="page-kicker">{{ __('welcome.pricing_kicker') }}</span>
                    <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em] text-slate-950">
                        {{ __('welcome.pricing_title') }}
                    </h2>
                </div>
                <p class="page-description">{{ __('welcome.pricing_subtitle') }}</p>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                {{-- Daily Pass --}}
                <article class="pricing-card">
                    <span class="badge-pill">{{ __('welcome.plan_daily_badge') }}</span>
                    <h3 class="mt-6 text-3xl font-bold tracking-[-0.05em] text-slate-950">
                        {{ __('welcome.plan_daily_name') }}
                    </h3>
                    <p class="mt-3 text-sm text-slate-600">{{ __('welcome.plan_daily_desc') }}</p>
                    <p class="mt-8 text-5xl font-bold tracking-[-0.07em] text-slate-950">
                        {{ __('welcome.plan_daily_price') }}<span
                            class="ml-2 text-base font-semibold text-slate-400">{{ __('welcome.plan_daily_per') }}</span>
                    </p>
                    <ul class="mt-8 space-y-3 text-sm text-slate-600">
                        <li class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700 text-xs font-bold">✓</span>
                            {{ __('welcome.plan_daily_f1') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700 text-xs font-bold">✓</span>
                            {{ __('welcome.plan_daily_f2') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700 text-xs font-bold">✓</span>
                            {{ __('welcome.plan_daily_f3') }}
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-link-secondary mt-8 w-full">
                        {{ __('welcome.plan_daily_cta') }}
                    </a>
                </article>

                {{-- Monthly Ultimate --}}
                <article class="pricing-card mesh-accent text-white">
                    <div
                        class="absolute right-6 top-6 rounded-full bg-white/15 px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.24em] backdrop-blur-sm">
                        {{ __('welcome.plan_monthly_badge') }}
                    </div>
                    <h3 class="text-3xl font-bold tracking-[-0.05em]">{{ __('welcome.plan_monthly_name') }}</h3>
                    <p class="mt-3 text-sm text-white/78">{{ __('welcome.plan_monthly_desc') }}</p>
                    <p class="mt-8 text-5xl font-bold tracking-[-0.07em]">
                        {{ __('welcome.plan_monthly_price') }}<span
                            class="ml-2 text-base font-semibold text-white/65">{{ __('welcome.plan_monthly_per') }}</span>
                    </p>
                    <ul class="mt-8 space-y-3 text-sm text-white/82">
                        <li class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white/20 text-white text-xs font-bold">✓</span>
                            {{ __('welcome.plan_monthly_f1') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white/20 text-white text-xs font-bold">✓</span>
                            {{ __('welcome.plan_monthly_f2') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <span
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white/20 text-white text-xs font-bold">✓</span>
                            {{ __('welcome.plan_monthly_f3') }}
                        </li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="mt-8 inline-flex w-full items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-[0_24px_60px_-30px_rgba(0,0,0,0.45)] transition duration-300 hover:-translate-y-0.5 hover:bg-slate-100">
                        {{ __('welcome.plan_monthly_cta') }}
                    </a>
                </article>
            </div>
        </section>

        {{-- ─── CTA Banner ───────────────────────────────────── --}}
        <section class="mx-auto mt-16 max-w-7xl">
            <div class="glass-card mesh-accent text-center text-white">
                <span
                    class="rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-white/90 backdrop-blur-sm">
                    {{ __('welcome.cta_kicker') }}
                </span>
                <h2 class="mt-6 text-4xl font-bold tracking-[-0.06em] sm:text-5xl">
                    {{ __('welcome.cta_title') }}
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-white/80 sm:text-base">
                    {{ __('welcome.cta_subtitle') }}
                </p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-brand-700 transition duration-300 hover:-translate-y-0.5 hover:bg-slate-100">
                        {{ __('welcome.cta_primary') }}
                    </a>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:bg-white/15">
                        {{ __('welcome.cta_secondary') }}
                    </a>
                </div>
            </div>
        </section>

    </main>

    {{-- ─── Footer ───────────────────────────────────────── --}}
    <footer class="relative z-10 px-4 pb-8 text-center text-sm text-slate-500 sm:px-6 lg:px-8">
        {{ __('welcome.footer_copy', ['year' => date('Y')]) }}
    </footer>

</body>

</html>