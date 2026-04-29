<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    @php($title = __('welcome.title'))
    @include('partials.head')
    {{-- Add Inter font for a cleaner body type --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        .queue-avatar {
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s;
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-200 antialiased">

    {{-- ─── Background texture (dot grid + subtle gradient) ─── --}}
    <div class="fixed inset-0 z-0 opacity-40">
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(20,184,166,0.15),transparent_50%),radial-gradient(circle_at_70%_60%,rgba(255,116,73,0.1),transparent_50%)]">
        </div>
        <div class="h-full w-full bg-[url('data:image/svg+xml;utf8,<svg xmlns=" http://www.w3.org/2000/svg" width="24"
            height="24" viewBox="0 0 24 24">
            <circle cx="2" cy="2" r="1.5" fill="rgba(255,255,255,0.05)" /></svg>')] bg-[size:24px_24px]">
        </div>
    </div>

    {{-- ─── Navigation ─────────────────────────── --}}
    <header class="relative z-50 border-b border-white/5 bg-slate-950/70 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <x-app-logo href="{{ route('home') }}" />

            <nav class="hidden items-center gap-8 text-sm font-medium text-slate-400 md:flex">
                <a href="#features" class="transition hover:text-white">{{ __('welcome.nav_features') }}</a>
                <a href="#workflow" class="transition hover:text-white">{{ __('welcome.nav_workflow') }}</a>
                <a href="#pricing" class="transition hover:text-white">{{ __('welcome.nav_pricing') }}</a>
            </nav>

            <div class="flex items-center gap-3">
                {{-- Language switcher --}}
                <div class="hidden items-center gap-1 sm:flex">
                    <a href="{{ route('lang.switch', ['locale' => 'en']) }}"
                        class="rounded-full px-3 py-1 text-xs font-semibold transition {{ app()->getLocale() === 'en' ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }}">EN</a>
                    <a href="{{ route('lang.switch', ['locale' => 'ms']) }}"
                        class="rounded-full px-3 py-1 text-xs font-semibold transition {{ app()->getLocale() === 'ms' ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }}">BM</a>
                </div>
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-400 transition hover:text-white">
                    {{ __('welcome.nav_login') }}
                </a>
                <a href="{{ route('register') }}"
                    class="rounded-full bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_0_20px_rgba(20,184,166,0.4)] transition hover:bg-brand-400 hover:shadow-[0_0_30px_rgba(20,184,166,0.6)]">
                    {{ __('welcome.register_now') }}
                </a>
            </div>
        </div>
    </header>

    <main class="relative z-10">

        {{-- ─── Hero + Live Queue Visualisation ──── --}}
        <section class="mx-auto max-w-7xl px-6 pb-20 pt-16 lg:pt-24">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="space-y-8">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-brand-500/20 bg-brand-500/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-300">
                        {{ __('welcome.hero_kicker') }}
                    </span>
                    <h1 class="text-5xl font-extrabold tracking-[-0.06em] text-white lg:text-7xl">
                        {{ __('welcome.hero_title') }}
                    </h1>
                    <p class="max-w-xl text-lg text-slate-400">
                        {{ __('welcome.hero_subtitle') }}
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-8 py-4 text-sm font-bold text-white shadow-[0_0_30px_rgba(20,184,166,0.4)] transition hover:bg-brand-400">
                            {{ __('welcome.hero_cta_primary') }}
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="#workflow"
                            class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-8 py-4 text-sm font-semibold text-white backdrop-blur transition hover:border-white/20 hover:bg-white/10">
                            {{ __('welcome.hero_cta_secondary') }}
                        </a>
                    </div>
                    <div class="flex items-center gap-6 text-sm text-slate-500">
                        <div class="flex -space-x-2">
                            <div
                                class="h-8 w-8 rounded-full border-2 border-slate-950 bg-gradient-to-br from-brand-400 to-coral-400">
                            </div>
                            <div
                                class="h-8 w-8 rounded-full border-2 border-slate-950 bg-gradient-to-br from-blue-400 to-purple-400">
                            </div>
                            <div
                                class="h-8 w-8 rounded-full border-2 border-slate-950 bg-gradient-to-br from-amber-400 to-orange-400">
                            </div>
                            <div
                                class="h-8 w-8 rounded-full border-2 border-slate-950 bg-gradient-to-br from-pink-400 to-rose-400">
                            </div>
                        </div>
                        <span>{{ __('welcome.stat_join_value') }} {{ __('welcome.stat_join_label') }}</span>
                    </div>
                </div>

                {{-- Interactive Queue Visual --}}
                <div class="relative" x-data="queueVisual()" x-init="startSimulation()">
                    <div class="overflow-hidden rounded-3xl border border-white/5 bg-slate-900/70 p-6 backdrop-blur-xl">
                        <div class="flex items-center justify-between text-sm text-slate-400">
                            <span class="flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span
                                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-400 opacity-75"></span>
                                    <span class="relative inline-flex h-3 w-3 rounded-full bg-brand-500"></span>
                                </span>
                                {{ __('welcome.preview_open') }}
                            </span>
                            <span
                                class="rounded-full bg-white/5 px-3 py-1 text-xs font-semibold">{{ __('welcome.preview_avg_wait') }}
                                12 min</span>
                        </div>

                        {{-- Service desk + avatar queue --}}
                        <div class="mt-10">
                            <div class="mb-6 flex items-end justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-slate-500">
                                        {{ __('welcome.preview_serving') }}</p>
                                    <p class="text-4xl font-bold text-white" x-text="serving"></p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-right backdrop-blur">
                                    <p class="text-xs uppercase tracking-widest text-slate-500">
                                        {{ __('welcome.preview_waiting') }}</p>
                                    <p class="text-2xl font-bold text-white" x-text="waiting"></p>
                                </div>
                            </div>

                            {{-- Animated queue line --}}
                            <div class="relative h-20 rounded-2xl border border-dashed border-white/10 bg-white/5">
                                <div class="absolute left-4 top-1/2 flex -translate-y-1/2 items-center gap-6">
                                    {{-- Desk icon --}}
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500/20 text-brand-400">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                        </svg>
                                    </div>
                                    {{-- Queue avatars --}}
                                    <template x-for="(user, index) in queue" :key="index">
                                        <div class="queue-avatar flex h-10 w-10 items-center justify-center rounded-full border-2 border-slate-800 bg-gradient-to-br text-xs font-bold text-white shadow-lg"
                                            :style="'background-image: linear-gradient(135deg, ' + user.color1 + ', ' + user.color2 + '); transform: translateX(' + (index * 52) + 'px)'">
                                            <span x-text="user.initials"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Feature teasers --}}
                        <div class="mt-8 grid grid-cols-3 gap-3 text-center">
                            <div class="rounded-xl bg-white/5 p-3">
                                <p class="text-xs font-semibold text-brand-300">{{ __('welcome.preview_join') }}</p>
                                <p class="mt-1 text-[0.65rem] text-slate-500">{{ __('welcome.preview_join_desc') }}</p>
                            </div>
                            <div class="rounded-xl bg-white/5 p-3">
                                <p class="text-xs font-semibold text-blue-300">{{ __('welcome.preview_track') }}</p>
                                <p class="mt-1 text-[0.65rem] text-slate-500">{{ __('welcome.preview_track_desc') }}</p>
                            </div>
                            <div class="rounded-xl bg-white/5 p-3">
                                <p class="text-xs font-semibold text-coral-300">{{ __('welcome.preview_call') }}</p>
                                <p class="mt-1 text-[0.65rem] text-slate-500">{{ __('welcome.preview_call_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─── Features (Bento Grid) ──────────── --}}
        <section id="features" class="mx-auto max-w-7xl px-6 pb-20 pt-12">
            <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                        {{ __('welcome.features_kicker') }}
                    </span>
                    <h2 class="mt-4 text-4xl font-bold tracking-[-0.04em] text-white lg:text-5xl">
                        {{ __('welcome.features_title') }}
                    </h2>
                </div>
                <p class="max-w-md text-slate-400">{{ __('welcome.features_subtitle') }}</p>
            </div>

            <div class="grid gap-4 md:grid-cols-4 md:grid-rows-2">
                {{-- Card 1 (tall) --}}
                <div
                    class="group relative overflow-hidden rounded-3xl border border-white/5 bg-slate-900/70 p-6 backdrop-blur transition hover:border-brand-500/30 hover:bg-slate-900/90 md:col-span-2 md:row-span-2">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-brand-500/10 blur-3xl"></div>
                    <span class="badge-pill badge-pill--brand">{{ __('welcome.feature_1_badge') }}</span>
                    <h3 class="mt-6 text-2xl font-bold text-white">{{ __('welcome.feature_1_title') }}</h3>
                    <p class="mt-4 max-w-sm text-slate-400">{{ __('welcome.feature_1_desc') }}</p>
                    <div class="mt-8 flex items-center gap-4 text-sm text-brand-400">
                        <span>Learn more</span>
                        <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div
                    class="group relative overflow-hidden rounded-3xl border border-white/5 bg-slate-900/70 p-6 backdrop-blur transition hover:border-coral-500/30 md:col-span-2">
                    <span class="badge-pill">{{ __('welcome.feature_2_badge') }}</span>
                    <h3 class="mt-4 text-xl font-bold text-white">{{ __('welcome.feature_2_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-400">{{ __('welcome.feature_2_desc') }}</p>
                </div>

                {{-- Card 3 --}}
                <div
                    class="group relative overflow-hidden rounded-3xl border border-white/5 bg-slate-900/70 p-6 backdrop-blur transition hover:border-blue-500/30 md:col-span-2">
                    <span class="badge-pill">{{ __('welcome.feature_3_badge') }}</span>
                    <h3 class="mt-4 text-xl font-bold text-white">{{ __('welcome.feature_3_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-400">{{ __('welcome.feature_3_desc') }}</p>
                </div>
            </div>
        </section>

        {{-- ─── Workflow / How It Works ───────────── --}}
        <section id="workflow" class="mx-auto max-w-7xl px-6 pb-20 pt-12">
            <div class="rounded-3xl border border-white/5 bg-slate-900/60 p-8 backdrop-blur-xl sm:p-12">
                <div class="grid items-center gap-10 lg:grid-cols-[1fr_2fr]">
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                            {{ __('welcome.workflow_kicker') }}
                        </span>
                        <h2 class="mt-4 text-4xl font-bold tracking-[-0.04em] text-white lg:text-5xl">
                            {{ __('welcome.workflow_title') }}
                        </h2>
                        <p class="mt-4 text-slate-400">{{ __('welcome.workflow_subtitle') }}</p>
                    </div>

                    <div class="relative flex flex-col gap-4 sm:flex-row">
                        <div
                            class="absolute left-4 top-0 h-full w-px bg-gradient-to-b from-brand-500/50 to-transparent sm:left-0 sm:top-8 sm:h-px sm:w-full sm:bg-gradient-to-r">
                        </div>
                        <div
                            class="relative z-10 flex-1 rounded-2xl border border-white/5 bg-slate-800/50 p-6 backdrop-blur">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500/20 text-lg font-bold text-brand-400">1</span>
                            <h4 class="mt-4 text-lg font-semibold text-white">{{ __('welcome.step_1_title') }}</h4>
                            <p class="mt-2 text-sm text-slate-400">{{ __('welcome.step_1_desc') }}</p>
                        </div>
                        <div
                            class="relative z-10 flex-1 rounded-2xl border border-white/5 bg-slate-800/50 p-6 backdrop-blur">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500/20 text-lg font-bold text-brand-400">2</span>
                            <h4 class="mt-4 text-lg font-semibold text-white">{{ __('welcome.step_2_title') }}</h4>
                            <p class="mt-2 text-sm text-slate-400">{{ __('welcome.step_2_desc') }}</p>
                        </div>
                        <div
                            class="relative z-10 flex-1 rounded-2xl border border-white/5 bg-slate-800/50 p-6 backdrop-blur">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500/20 text-lg font-bold text-brand-400">3</span>
                            <h4 class="mt-4 text-lg font-semibold text-white">{{ __('welcome.step_3_title') }}</h4>
                            <p class="mt-2 text-sm text-slate-400">{{ __('welcome.step_3_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─── Pricing ──────────────────────────────── --}}
        <section id="pricing" class="mx-auto max-w-7xl px-6 pb-20 pt-12">
            <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                        {{ __('welcome.pricing_kicker') }}
                    </span>
                    <h2 class="mt-4 text-4xl font-bold tracking-[-0.04em] text-white lg:text-5xl">
                        {{ __('welcome.pricing_title') }}
                    </h2>
                </div>
                <p class="max-w-md text-slate-400">{{ __('welcome.pricing_subtitle') }}</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Daily Pass --}}
                <div
                    class="rounded-3xl border border-white/5 bg-slate-900/60 p-8 backdrop-blur-xl transition hover:border-white/10">
                    <span class="badge-pill">{{ __('welcome.plan_daily_badge') }}</span>
                    <h3 class="mt-6 text-3xl font-bold text-white">{{ __('welcome.plan_daily_name') }}</h3>
                    <p class="mt-3 text-slate-400">{{ __('welcome.plan_daily_desc') }}</p>
                    <p class="mt-8 text-6xl font-extrabold text-white">
                        {{ __('welcome.plan_daily_price') }}
                        <span
                            class="ml-2 text-base font-semibold text-slate-500">{{ __('welcome.plan_daily_per') }}</span>
                    </p>
                    <ul class="mt-8 space-y-3 text-sm text-slate-400">
                        <li class="flex items-center gap-2">
                            <svg class="h-5 w-5 shrink-0 text-brand-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('welcome.plan_daily_f1') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-5 w-5 shrink-0 text-brand-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('welcome.plan_daily_f2') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-5 w-5 shrink-0 text-brand-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('welcome.plan_daily_f3') }}
                        </li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="mt-8 flex w-full items-center justify-center rounded-full border border-white/10 bg-white/5 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10">
                        {{ __('welcome.plan_daily_cta') }}
                    </a>
                </div>

                {{-- Monthly Ultimate (Highlighted) --}}
                <div
                    class="relative rounded-3xl border border-brand-500/30 bg-gradient-to-br from-brand-500/10 to-slate-900/70 p-8 backdrop-blur-xl">
                    <div
                        class="absolute right-6 top-6 rounded-full bg-brand-500 px-4 py-1 text-xs font-bold text-white shadow-[0_0_20px_rgba(20,184,166,0.5)]">
                        {{ __('welcome.plan_monthly_popular') ?? 'Most Popular' }}
                    </div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-brand-500/30 bg-brand-500/10 px-3 py-1 text-xs font-semibold text-brand-300">
                        {{ __('welcome.plan_monthly_badge') }}
                    </span>
                    <h3 class="mt-5 text-3xl font-bold text-white">{{ __('welcome.plan_monthly_name') }}</h3>
                    <p class="mt-3 text-slate-300">{{ __('welcome.plan_monthly_desc') }}</p>
                    <p class="mt-8 text-6xl font-extrabold text-white">
                        {{ __('welcome.plan_monthly_price') }}
                        <span
                            class="ml-2 text-base font-semibold text-slate-400">{{ __('welcome.plan_monthly_per') }}</span>
                    </p>
                    <ul class="mt-8 space-y-3 text-sm text-slate-300">
                        <li class="flex items-center gap-2">
                            <svg class="h-5 w-5 shrink-0 text-brand-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('welcome.plan_monthly_f1') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-5 w-5 shrink-0 text-brand-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('welcome.plan_monthly_f2') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-5 w-5 shrink-0 text-brand-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('welcome.plan_monthly_f3') }}
                        </li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="mt-8 flex w-full items-center justify-center rounded-full bg-brand-500 py-3 text-sm font-bold text-white shadow-[0_0_30px_rgba(20,184,166,0.4)] transition hover:bg-brand-400">
                        {{ __('welcome.plan_monthly_cta') }}
                    </a>
                </div>
            </div>
        </section>

        {{-- ─── CTA Banner ─────────────────────────── --}}
        <section class="mx-auto max-w-7xl px-6 pb-20">
            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 p-10 text-center text-white sm:p-16">
                <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 h-48 w-48 rounded-full bg-coral-500/20 blur-3xl"></div>
                <h2 class="relative text-4xl font-extrabold tracking-[-0.04em] sm:text-5xl">
                    {{ __('welcome.cta_title') }}
                </h2>
                <p class="relative mx-auto mt-4 max-w-xl text-brand-100">
                    {{ __('welcome.cta_subtitle') }}
                </p>
                <div class="relative mt-8 flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-4 text-sm font-bold text-brand-700 shadow-xl transition hover:bg-slate-100">
                        {{ __('welcome.cta_primary') }}
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-8 py-4 text-sm font-semibold backdrop-blur transition hover:bg-white/20">
                        {{ __('welcome.cta_secondary') }}
                    </a>
                </div>
            </div>
        </section>
    </main>

    {{-- ─── Footer ─────────────────────────────────── --}}
    <footer class="relative z-10 border-t border-white/5 py-8 text-center text-sm text-slate-500">
        {{ __('welcome.footer_copy', ['year' => date('Y')]) }}
    </footer>

    {{-- ─── Alpine: Queue Animation ────────────────── --}}
    <script>
        function queueVisual() {
            return {
                serving: 'A102',
                waiting: 18,
                queue: [
                    { initials: 'JD', color1: '#3B82F6', color2: '#8B5CF6' },
                    { initials: 'MK', color1: '#F59E0B', color2: '#EF4444' },
                    { initials: 'AL', color1: '#EC4899', color2: '#F97316' },
                    { initials: 'SR', color1: '#10B981', color2: '#06B6D4' },
                    { initials: 'BP', color1: '#8B5CF6', color2: '#EC4899' },
                ],
                ticketPool: ['A102', 'B045', 'C218', 'D099', 'E150', 'F003', 'G082'],
                currentTicketIndex: 0,
                simulationId: null,
                startSimulation() {
                    this.simulationId = setInterval(() => {
                        // Advance ticket
                        this.currentTicketIndex = (this.currentTicketIndex + 1) % this.ticketPool.length;
                        this.serving = this.ticketPool[this.currentTicketIndex];
                        // Move first avatar out, shift queue
                        this.queue.push(this.queue.shift());
                        // Random waiting fluctuation
                        this.waiting = Math.max(5, this.waiting + Math.floor(Math.random() * 5) - 2);
                    }, 3000);
                }
            }
        }
    </script>
</body>

</html>