<section class="w-full max-w-4xl mx-auto">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Business settings') }}</flux:heading>

    <x-settings.layout :heading="__('Business Profile')" :subheading="__('Configure how customers interact with your queue system.')">

        {{-- Alert --}}
        @if(!auth()->user()->profile_completed)
            <div
                class="mb-6 p-5 rounded-2xl border bg-gradient-to-r from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-900/10 border-amber-200 dark:border-amber-800 shadow-sm">
                <div class="flex items-start gap-3">
                    <flux:icon.exclamation-triangle class="h-5 w-5 text-amber-500 mt-0.5" />

                    <div>
                        <flux:heading size="sm" class="font-semibold text-amber-800 dark:text-amber-400">
                            Action Required
                        </flux:heading>

                        <flux:subheading class="mt-1 text-sm text-amber-700 dark:text-amber-400/80">
                            Complete your business profile to unlock the command center and billing features.
                        </flux:subheading>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form wire:submit="save" class="space-y-8">

            {{-- Section: Basic Info --}}
            <div class="space-y-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ __('Basic Information') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Your public business details.') }}
                    </p>
                </div>

                <flux:input wire:model="name" :label="__('Business Name')" placeholder="e.g. Warung Ahmad" type="text"
                    required autofocus />
            </div>

            {{-- Section: Queue Settings --}}
            <div class="space-y-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ __('Queue Configuration') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Customize how customers join your queue.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    {{-- WhatsApp Code --}}
                    <flux:field>
                        <flux:label>{{ __('WhatsApp Join Code') }}</flux:label>

                        <flux:input.group>
                            <flux:input.group.prefix class="font-bold text-teal-500">
                                JOIN
                            </flux:input.group.prefix>

                            <flux:input wire:model="join_code" placeholder="AHMAD"
                                class="font-bold uppercase tracking-wider" required />
                        </flux:input.group>

                        <flux:description class="mt-1">
                            {{ __('Customers send this keyword to join your queue.') }}
                        </flux:description>

                        <flux:error name="join_code" />
                    </flux:field>

                    {{-- Ticket Prefix --}}
                    <flux:field>
                        <flux:label>{{ __('Ticket Prefix') }}</flux:label>

                        <flux:input wire:model="queue_prefix" placeholder="A" maxlength="3"
                            class="font-bold uppercase tracking-wider" required />

                        <flux:description class="mt-1">
                            {{ __('Example: A001, B002') }}
                        </flux:description>

                        <flux:error name="queue_prefix" />
                    </flux:field>
                </div>
            </div>

            {{-- Section: Contact --}}
            <div class="space-y-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ __('Contact Details') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Optional but recommended.') }}
                    </p>
                </div>

                <flux:input wire:model="phone" :label="__('Support Phone Number')" placeholder="+60123456789"
                    type="text" />

                <flux:textarea wire:model="address" :label="__('Business Address')" rows="3"
                    placeholder="Your business location..." />
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">

                <x-action-message on="profile-updated" class="text-sm text-gray-500">
                    {{ __('Saved successfully.') }}
                </x-action-message>

                <flux:button variant="primary" type="submit" class="px-6 py-2.5 rounded-xl shadow-sm">
                    {{ auth()->user()->profile_completed ? __('Save Changes') : __('Complete Setup') }}
                </flux:button>
            </div>

        </form>
    </x-settings.layout>
</section>