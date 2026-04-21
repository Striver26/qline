<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Business settings') }}</flux:heading>

    <x-settings.layout :heading="__('Business Profile')" :subheading="__('Configure how customers discover, join, and recognize your queue.')">
        @if(!auth()->user()->profile_completed)
            <div class="mb-6 rounded-[1.5rem] border border-amber-200 bg-amber-50 p-5">
                <p class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-amber-700">{{ __('Action required') }}</p>
                <p class="mt-3 text-sm text-amber-700">
                    Complete your business profile to unlock the command center, billing, and public queue experience.
                </p>
            </div>
        @endif

        <form wire:submit="save" class="space-y-8">
            <div class="soft-card">
                <p class="metric-label">Basic Information</p>
                <div class="mt-5 space-y-5">
                    <flux:input wire:model="form.name" :label="__('Business Name')" placeholder="e.g. Warung Ahmad" type="text" required autofocus />
                </div>
            </div>

            <div class="soft-card">
                <p class="metric-label">Queue Configuration</p>
                <div class="mt-5 grid gap-6 sm:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('WhatsApp Join Code') }}</flux:label>

                        <flux:input.group>
                            <flux:input.group.prefix class="font-bold text-brand-700">
                                JOIN
                            </flux:input.group.prefix>

                            <flux:input wire:model="form.join_code" placeholder="AHMAD" class="font-bold uppercase tracking-wider" required />
                        </flux:input.group>

                        <flux:description>{{ __('Customers send this keyword to join your queue.') }}</flux:description>
                        <flux:error name="join_code" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Ticket Prefix') }}</flux:label>
                        <flux:input wire:model="form.queue_prefix" placeholder="A" maxlength="3" class="font-bold uppercase tracking-wider" required />
                        <flux:description>{{ __('Example: A001, B002') }}</flux:description>
                        <flux:error name="queue_prefix" />
                    </flux:field>
                </div>
            </div>

            <div class="soft-card">
                <p class="metric-label">Contact Details</p>
                <div class="mt-5 space-y-5">
                    <flux:input wire:model="form.phone" :label="__('Support Phone Number')" placeholder="+60123456789" type="text" />
                    <flux:textarea wire:model="form.address" :label="__('Business Address')" rows="2" placeholder="Your business location..." />
                    <div class="grid gap-5 sm:grid-cols-3">
                        <flux:input wire:model="form.city" :label="__('City')" placeholder="e.g. Kuala Lumpur" type="text" />
                        <flux:input wire:model="form.state" :label="__('State')" placeholder="e.g. WP" type="text" />
                        <flux:input wire:model="form.postcode" :label="__('Postcode')" placeholder="e.g. 50000" type="text" />
                    </div>
                </div>
            </div>

            <div class="soft-card">
                <p class="metric-label">Operating Hours & Timezone</p>
                <div class="mt-5 space-y-6">
                    <flux:field>
                        <flux:label>{{ __('Business Timezone') }}</flux:label>
                        <flux:select wire:model="form.timezone" filterable>
                            @foreach(\DateTimeZone::listIdentifiers() as $tz)
                                <flux:select.option :value="$tz">{{ $tz }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:description>{{ __('All automated open/close logic will follow this timezone.') }}</flux:description>
                    </flux:field>

                    <div class="space-y-4">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Weekly Schedule</p>
                        <div class="grid gap-4 sm:max-w-xl">
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                <div class="flex items-center gap-4">
                                    <div class="w-24 text-sm font-semibold capitalize text-slate-600 dark:text-slate-400">{{ __($day) }}</div>
                                    <div class="flex flex-1 items-center gap-3">
                                        <flux:input type="time" wire:model="form.business_hours.{{ $day }}.0" class="flex-1" />
                                        <span class="text-slate-400">to</span>
                                        <flux:input type="time" wire:model="form.business_hours.{{ $day }}.1" class="flex-1" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <flux:description>{{ __('Automated cleanup jobs will use these windows to close your queue if left open.') }}</flux:description>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-200/70 pt-4 dark:border-white/10">
                <x-action-message on="profile-updated" class="text-sm text-slate-500">
                    {{ __('Saved successfully.') }}
                </x-action-message>

                <flux:button variant="primary" type="submit" class="mesh-accent rounded-full px-5 py-2.5 font-semibold text-white">
                    {{ auth()->user()->profile_completed ? __('Save Changes') : __('Complete Setup') }}
                </flux:button>
            </div>
        </form>
    </x-settings.layout>
</section>
