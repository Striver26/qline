<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Business settings') }}</flux:heading>

    <x-settings.layout :heading="__('Business Profile')" :subheading="__('Set up your queue preferences. This controls how customers interact with your portal.')">
        @if(!auth()->user()->profile_completed)
            <div class="mb-6 p-4 rounded-xl border relative overflow-hidden bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-900/50">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-0.5">
                        <flux:icon.exclamation-triangle class="h-5 w-5 text-amber-500" />
                    </div>
                    <div class="ml-3">
                        <flux:heading size="sm" class="font-bold text-amber-800 dark:text-amber-400">Action Required</flux:heading>
                        <flux:subheading class="mt-1 text-sm font-medium text-amber-700 dark:text-amber-500/80">
                            Please complete your business profile before accessing the command center or viewing billing.
                        </flux:subheading>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="save" class="my-6 w-full space-y-6">
            
            <flux:input wire:model="name" :label="__('Business Name')" placeholder="e.g. Warung Ahmad" type="text" required autofocus />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>{{ __('WhatsApp Join Code') }}</flux:label>
                    <flux:input.group>
                        <flux:input.group.prefix class="font-bold text-[#14B8A6]">JOIN</flux:input.group.prefix>
                        <flux:input wire:model="join_code" placeholder="AHMAD" class="font-bold uppercase" required />
                    </flux:input.group>
                    <flux:description class="mt-1">{{ __('Customers will text this to join queue.') }}</flux:description>
                    <flux:error name="join_code" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Ticket Prefix') }}</flux:label>
                    <flux:input wire:model="queue_prefix" placeholder="A" maxlength="3" class="font-bold uppercase" required />
                    <flux:description class="mt-1">{{ __('e.g. A001') }}</flux:description>
                    <flux:error name="queue_prefix" />
                </flux:field>
            </div>

            <flux:input wire:model="phone" :label="__('Support Phone Number')" placeholder="+60123456789" type="text" />
            
            <flux:textarea wire:model="address" :label="__('Business Address')" rows="3" placeholder="Your business location..." />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ auth()->user()->profile_completed ? __('Save Changes') : __('Complete Setup') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
