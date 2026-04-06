<x-layouts::auth :title="__('Accept Invitation')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Join the Team')"
            :description="__('You\'ve been invited as ') . str_replace('_', ' ', Str::title($invitation->role))"
        />

        {{-- Email badge --}}
        <div class="text-center">
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                {{ $invitation->email }}
            </span>
        </div>

        <form method="POST" action="{{ route('invite.process', $invitation->token) }}" class="flex flex-col gap-6">
            @csrf

            {{-- Name --}}
            <flux:input
                name="name"
                :label="__('Full Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Your full name"
            />

            {{-- Password --}}
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Choose a strong password')"
                viewable
            />

            {{-- Confirm Password --}}
            <flux:input
                name="password_confirmation"
                :label="__('Confirm Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Re-enter your password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">
                    {{ __('Accept Invitation & Join') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::auth>
