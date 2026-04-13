<div class="settings-shell">
    <div class="settings-nav-card">
        <div class="mb-4 px-3">
            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">
                {{ __('Workspace Settings') }}
            </p>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                {{ __('Keep your team details, account security, and brand setup in one place.') }}
            </p>
        </div>

        <flux:navlist aria-label="{{ __('Settings') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            @if(auth()->user()->isOwner())
                <flux:navlist.item :href="route('business.settings')" wire:navigate>{{ __('Business') }}</flux:navlist.item>
            @endif
            <flux:navlist.item :href="route('security.edit')" wire:navigate>{{ __('Security') }}</flux:navlist.item>
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <div class="settings-content-card">
        <div class="max-w-3xl">
            <span class="page-kicker">{{ __('Account Control') }}</span>
            <h2 class="mt-4 text-3xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">{{ $heading ?? '' }}</h2>
            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">{{ $subheading ?? '' }}</p>

            <div class="mt-8 w-full max-w-2xl">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
