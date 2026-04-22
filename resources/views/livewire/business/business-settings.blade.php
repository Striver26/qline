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
                <p class="metric-label">Business Information</p>
                <div class="mt-5 space-y-5">
                    <flux:input wire:model="form.name" :label="__('Business Name')" placeholder="e.g. Warung Ahmad" type="text" required autofocus />
                    
                    <div class="grid gap-5 sm:grid-cols-2">
                        <flux:input wire:model="form.phone" :label="__('Business Phone Number')" placeholder="+60123456789" type="text" />
                        <flux:input wire:model="form.postcode" :label="__('Postcode')" placeholder="e.g. 50000" type="text" />
                    </div>

                    <flux:textarea wire:model="form.address" :label="__('Business Address')" rows="2" placeholder="Your business location..." />

                    <div class="grid gap-5 sm:grid-cols-2">
                        <flux:input wire:model="form.city" :label="__('City')" placeholder="e.g. Kuala Lumpur" type="text" />
                        <flux:input wire:model="form.state" :label="__('State')" placeholder="e.g. WP" type="text" />
                    </div>
                </div>
            </div>

            <div class="soft-card">
                <p class="metric-label">Queue Configuration</p>
                
                @if(auth()->user()->profile_completed)
                    <div class="mt-5 space-y-4">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Customer Entry Materials</p>
                        <flux:button 
                            as="a" 
                            href="{{ route('business.qr', ['print' => 1]) }}" 
                            target="_blank" 
                            variant="ghost" 
                            class="!bg-brand-50 text-brand-700 w-full rounded-2xl border-2 border-dashed border-brand-200 py-6 flex flex-col items-center justify-center gap-2"
                        >
                            <flux:icon.printer class="h-6 w-6" />
                            <span class="font-bold uppercase tracking-widest text-xs">Print QR Standee</span>
                        </flux:button>
                    </div>
                @endif

                <div class="mt-8 grid gap-6 sm:grid-cols-2">
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

                @if($business = auth()->user()->getActiveBusiness())
                    <div class="mt-8 space-y-6 border-t border-slate-100 dark:border-white/5 pt-6">
                        <flux:field>
                            <flux:label>{{ __('Public Join URL') }}</flux:label>
                            <div class="flex items-center gap-2">
                                <flux:input readonly value="{{ route('public.join', $business->slug) }}" class="bg-slate-50 dark:bg-white/5" />
                                <flux:button icon="clipboard" variant="ghost" x-on:click="navigator.clipboard.writeText('{{ route('public.join', $business->slug) }}'); $flux.toast('URL Copied!')" />
                            </div>
                            <flux:description>{{ __('Direct link for customers to join your queue from their browser.') }}</flux:description>
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('TV Display URL') }}</flux:label>
                            <div class="flex items-center gap-2">
                                <flux:input readonly value="{{ route('public.tv', ['slug' => $business->slug, 'token' => $business->tv_token]) }}" class="bg-slate-50 dark:bg-white/5" />
                                <flux:button icon="clipboard" variant="ghost" x-on:click="navigator.clipboard.writeText('{{ route('public.tv', ['slug' => $business->slug, 'token' => $business->tv_token]) }}'); $flux.toast('TV URL Copied!')" />
                            </div>
                            <flux:description>{{ __('Private link for your big screen display. Keep the token secret.') }}</flux:description>
                        </flux:field>
                    </div>
                @endif
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
