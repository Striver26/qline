<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen text-slate-900">
    <div class="pointer-events-none fixed inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top_left,rgba(20,159,124,0.24),transparent_46%),radial-gradient(circle_at_top_right,rgba(255,116,73,0.16),transparent_28%)]"></div>
    <div class="pointer-events-none fixed bottom-[-120px] right-[-110px] h-72 w-72 rounded-full bg-brand-300/25 blur-3xl"></div>

    <header class="relative z-10 border-b border-white/60 bg-white/66 backdrop-blur-xl">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6">
            <x-app-logo href="{{ route('home') }}" wire:navigate />

            <div class="hidden items-center gap-3 sm:flex">
                <span class="badge-pill">No app needed</span>
                <span class="badge-pill badge-pill--brand">Live updates</span>
            </div>
        </div>
    </header>

    <main class="relative z-10 px-4 py-8 sm:px-6 sm:py-10">
        <div class="mx-auto max-w-5xl space-y-6">
            <div class="flex flex-col gap-3 text-center sm:text-left">
                <span class="page-kicker mx-auto sm:mx-0">{{ __('Customer Queue') }}</span>
                <h1 class="text-3xl font-bold tracking-[-0.06em] text-slate-950 sm:text-4xl">Fast to join, easy to follow.</h1>
                <p class="max-w-2xl text-sm text-slate-600 sm:text-base">
                    Customers can hop into the line, track their turn in real time, and keep moving without crowding the counter.
                </p>
            </div>

            <div class="public-panel">
                {{ $slot }}
            </div>
        </div>
    </main>

    <footer class="relative z-10 px-4 pb-8 pt-2 text-center text-xs font-medium uppercase tracking-[0.24em] text-slate-400 sm:px-6">
        Powered by Qline
    </footer>

    @fluxScripts
</body>
</html>
