<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen text-slate-900 dark:text-slate-100">
    <div
        class="pointer-events-none fixed inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top_left,rgba(20,159,124,0.24),transparent_52%),radial-gradient(circle_at_top_right,rgba(255,116,73,0.16),transparent_38%)]">
    </div>
    <div
        class="pointer-events-none fixed bottom-[-140px] right-[-120px] h-80 w-80 rounded-full bg-coral-400/20 blur-3xl">
    </div>

    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-white/60 bg-white/78 backdrop-blur-xl dark:border-white/10 dark:bg-slate-950/82">
        <flux:sidebar.header class="px-4 pt-4">
            <div class="glass-card !rounded-[1.6rem] !p-4">
                <div class="flex items-center justify-between gap-3">
                    <x-app-logo :sidebar="true"
                        href="{{ route(in_array(auth()->user()->role, [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::PLATFORM_STAFF]) ? 'admin.dashboard' : 'business.dashboard') }}"
                        wire:navigate />
                    <flux:sidebar.collapse class="lg:hidden" />
                </div>
            </div>
        </flux:sidebar.header>

        @if(in_array(auth()->user()->role, [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::PLATFORM_STAFF]))
            <flux:sidebar.nav class="px-3 pt-4">
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="command-line" :href="route('admin.dashboard')"
                        :current="request()->routeIs('admin.dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-storefront" :href="route('admin.businesses')"
                        :current="request()->routeIs('admin.businesses')" wire:navigate>
                        {{ __('Tenants') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chart-bar" :href="route('admin.analytics')"
                        :current="request()->routeIs('admin.analytics')" wire:navigate>
                        {{ __('Analytics') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('admin.users')"
                        :current="request()->routeIs('admin.users')" wire:navigate>
                        {{ __('Users') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-left-ellipsis" :href="route('admin.wa-messages')"
                        :current="request()->routeIs('admin.wa-messages')" wire:navigate>
                        {{ __('WhatsApp Log') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:sidebar.nav class="px-3 pt-2">
                <flux:sidebar.group :heading="__('Revenue & Settings')" class="grid">
                    <flux:sidebar.item icon="banknotes" :href="route('admin.payments')"
                        :current="request()->routeIs('admin.payments')" wire:navigate>
                        {{ __('Payments') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-path-rounded-square" :href="route('admin.subscriptions')"
                        :current="request()->routeIs('admin.subscriptions')" wire:navigate>
                        {{ __('Subscriptions') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clock" :href="route('admin.queue-entries')"
                        :current="request()->routeIs('admin.queue-entries')" wire:navigate>
                        {{ __('Queue Dump') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>
        @else
            <flux:sidebar.nav class="px-3 pt-4">
                <flux:sidebar.group :heading="__('Operations')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('business.dashboard')"
                        :current="request()->routeIs('business.dashboard')" wire:navigate>
                        {{ __('Queue Center') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clock" :href="route('business.entries')"
                        :current="request()->routeIs('business.entries')" wire:navigate>
                        {{ __('Queue History') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-bottom-center-text" :href="route('business.feedback')"
                        :current="request()->routeIs('business.feedback')" wire:navigate>
                        {{ __('Feedback') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="computer-desktop" :href="route('business.counters')"
                        :current="request()->routeIs('business.counters')" wire:navigate>
                        {{ __('Counters') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:sidebar.nav class="px-3 pt-2">
                <flux:sidebar.group :heading="__('Growth')" class="grid">
                    <flux:sidebar.item icon="gift" :href="route('business.rewards')"
                        :current="request()->routeIs('business.rewards')" wire:navigate>
                        {{ __('Rewards') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chart-pie" :href="route('business.analytics')"
                        :current="request()->routeIs('business.analytics')" wire:navigate>
                        {{ __('Analytics') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('business.staff')"
                        :current="request()->routeIs('business.staff')" wire:navigate>
                        {{ __('Staff') }}
                    </flux:sidebar.item>
                    @if(auth()->user()->isOwner())
                        <flux:sidebar.item icon="credit-card" :href="route('business.billing')"
                            :current="request()->routeIs('business.billing')" wire:navigate>
                            {{ __('Billing') }}
                        </flux:sidebar.item>
                    @endif
                    <flux:sidebar.item icon="cog-8-tooth" :href="route('profile.edit')"
                        :current="request()->routeIs('profile.edit')" wire:navigate>
                        {{ __('Settings') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>
        @endif

        <flux:spacer />

        @php
            $quote = cache()->remember('daily_quote', now()->endOfDay(), function () {
                return \Illuminate\Foundation\Inspiring::quote();
            });
        @endphp
        <div
            class="mx-3 mb-3 rounded-[1.5rem] border border-brand-200/70 bg-brand-50/85 p-4 text-sm text-brand-900 shadow-sm dark:border-brand-400/20 dark:bg-brand-500/10 dark:text-brand-100">
            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-brand-700 dark:text-brand-200">
                Quote
            </p>
            <p class="mt-2 text-sm leading-relaxed text-brand-900/85 dark:text-brand-50/85">
                {!! $quote !!}
            </p>
        </div>

        <x-desktop-user-menu class="hidden lg:block px-3 pb-4" :name="auth()->user()->name" />
    </flux:sidebar>

    <flux:header
        class="border-b border-white/60 bg-white/72 backdrop-blur-xl dark:border-white/10 dark:bg-slate-950/78 lg:hidden">
        <flux:sidebar.toggle class="lg:hidden mr-3" icon="bars-2" inset="left" />

        <x-app-logo
            href="{{ route(in_array(auth()->user()->role, [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::PLATFORM_STAFF]) ? 'admin.dashboard' : 'business.dashboard') }}"
            wire:navigate />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>