<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen overflow-x-hidden text-slate-900 dark:text-slate-100">
        <div class="pointer-events-none fixed inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_top_left,rgba(20,159,124,0.22),transparent_42%),radial-gradient(circle_at_top_right,rgba(255,116,73,0.18),transparent_26%)]"></div>
        <div class="pointer-events-none fixed bottom-[-180px] left-[-120px] h-96 w-96 rounded-full bg-brand-300/20 blur-3xl"></div>

        <div class="relative mx-auto grid min-h-screen max-w-7xl gap-6 px-4 py-5 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
            <section class="mesh-accent relative hidden overflow-hidden rounded-[2.5rem] p-8 text-white shadow-[0_45px_120px_-48px_rgba(15,23,42,0.75)] lg:flex lg:flex-col lg:justify-between">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.2),transparent_30%),linear-gradient(180deg,rgba(255,255,255,0.04),transparent)]"></div>

                <div class="relative z-10">
                    <x-app-logo :inverse="true" />
                </div>

                <div class="relative z-10 max-w-xl">
                    <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-white/90 backdrop-blur-sm">
                        Built for busy service teams
                    </span>
                    <h1 class="mt-6 text-5xl font-bold tracking-[-0.07em] text-white">
                        Make the next customer action obvious.
                    </h1>
                    <p class="mt-5 max-w-lg text-base text-white/80">
                        Qline gives your team a calmer operating rhythm with live queue control, effortless customer updates, and cleaner handoffs.
                    </p>

                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        <div class="auth-metric">
                            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.26em] text-white/70">Live Updates</p>
                            <p class="mt-3 text-3xl font-bold tracking-[-0.05em]">24/7</p>
                        </div>
                        <div class="auth-metric">
                            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.26em] text-white/70">Less Chaos</p>
                            <p class="mt-3 text-3xl font-bold tracking-[-0.05em]">Faster</p>
                        </div>
                        <div class="auth-metric">
                            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.26em] text-white/70">Customer UX</p>
                            <p class="mt-3 text-3xl font-bold tracking-[-0.05em]">Clear</p>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 grid gap-3 text-sm text-white/78">
                    <div class="flex items-center gap-3 rounded-[1.4rem] border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-sm">
                        <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                        Customers know their position instantly.
                    </div>
                    <div class="flex items-center gap-3 rounded-[1.4rem] border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-sm">
                        <span class="h-2.5 w-2.5 rounded-full bg-coral-200"></span>
                        Staff can focus on service instead of crowd control.
                    </div>
                </div>
            </section>

            <section class="flex min-h-[calc(100vh-2.5rem)] items-center justify-center">
                <div class="w-full max-w-xl">
                    <div class="mb-6 flex justify-center lg:hidden">
                        <x-app-logo href="{{ route('home') }}" wire:navigate />
                    </div>

                    <div class="auth-card">
                        {{ $slot }}
                    </div>
                </div>
            </section>
        </div>

        @fluxScripts
    </body>
</html>
