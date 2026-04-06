<div class="space-y-8">

    {{-- ═══════════ Header ═══════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <flux:subheading class="text-sm font-bold uppercase tracking-widest mb-1" style="color: #14B8A6;">Loyalty Program</flux:subheading>
            <flux:heading size="xl" class="text-4xl font-black tracking-tight text-gray-900 dark:text-white leading-none">Rewards</flux:heading>
        </div>
        @if($isOwner)
            <flux:button wire:click="openForm" variant="primary"
                class="font-bold rounded-xl px-5"
                style="background: #14B8A6; border-color: #14B8A6;">
                <flux:icon.plus class="w-4 h-4 mr-2" />
                Add Reward Tier
            </flux:button>
        @endif
    </div>

    {{-- ═══════════ Create / Edit Form ═══════════ --}}
    @if($showForm && $isOwner)
        <flux:card class="border-teal-200 dark:border-teal-900/50 overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1" style="background: linear-gradient(90deg, #14B8A6, #2dd4bf);"></div>
            <form wire:submit="save" class="space-y-6 pt-2">
                <flux:heading size="lg" class="font-bold text-gray-900 dark:text-white">
                    {{ $editingId ? 'Edit Reward' : 'New Reward Tier' }}
                </flux:heading>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <flux:field>
                        <flux:label>Reward Type</flux:label>
                        <flux:select wire:model="reward_type">
                            <option value="freebie">🎁 Free Item</option>
                            <option value="discount_percent">💰 Discount (%)</option>
                            <option value="discount_fixed">💵 Discount (RM)</option>
                        </flux:select>
                        <flux:error name="reward_type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ $reward_type === 'freebie' ? 'What do they get?' : ($reward_type === 'discount_percent' ? 'Discount amount' : 'Discount amount') }}</flux:label>
                        <flux:input wire:model="reward_value"
                            placeholder="{{ $reward_type === 'freebie' ? 'e.g. Free cup of coffee' : ($reward_type === 'discount_percent' ? 'e.g. 10' : 'e.g. 5.00') }}"
                            required />
                        <flux:error name="reward_value" />
                    </flux:field>

                    <flux:field>
                        <flux:label>After how many visits?</flux:label>
                        <flux:input wire:model="required_visits" type="number" min="1" max="999" required />
                        <flux:error name="required_visits" />
                    </flux:field>
                </div>

                <div class="flex items-center gap-3">
                    <flux:button type="submit" variant="primary" class="font-bold rounded-xl"
                        style="background: #14B8A6; border-color: #14B8A6;">
                        {{ $editingId ? 'Update Reward' : 'Create Reward' }}
                    </flux:button>
                    <flux:button wire:click="cancelForm" variant="ghost" class="font-bold rounded-xl">Cancel</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    {{-- ═══════════ Reward Tiers ═══════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($rewards as $reward)
            <flux:card class="relative p-6 group overflow-hidden border-gray-100 dark:border-zinc-800 {{ !$reward->is_active ? 'opacity-50' : '' }}">
                {{-- Bottom accent bar --}}
                <div class="absolute bottom-0 left-0 right-0 h-1"
                    style="background: linear-gradient(90deg,
                        {{ $reward->reward_type === 'freebie' ? '#14B8A6, #2dd4bf' : ($reward->reward_type === 'discount_percent' ? '#6366f1, #818cf8' : '#f59e0b, #fbbf24') }});">
                </div>

                {{-- Icon + Badge --}}
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm"
                        style="background: {{ $reward->reward_type === 'freebie' ? '#f0fdfa' : ($reward->reward_type === 'discount_percent' ? '#eef2ff' : '#fffbeb') }};">
                        @if($reward->reward_type === 'freebie')
                            <flux:icon.gift class="w-6 h-6" style="color: #14B8A6;" />
                        @elseif($reward->reward_type === 'discount_percent')
                            <flux:icon.receipt-percent class="w-6 h-6 text-indigo-500" />
                        @else
                            <flux:icon.banknotes class="w-6 h-6 text-amber-500" />
                        @endif
                    </div>

                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg {{ $reward->is_active ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-gray-100 text-gray-400 dark:bg-zinc-800 dark:text-gray-500' }}">
                        {{ $reward->is_active ? 'Active' : 'Paused' }}
                    </span>
                </div>

                {{-- Content --}}
                <flux:heading size="sm" class="font-bold text-gray-900 dark:text-white mb-1">
                    @if($reward->reward_type === 'freebie')
                        {{ $reward->reward_value }}
                    @elseif($reward->reward_type === 'discount_percent')
                        {{ $reward->reward_value }}% Discount
                    @else
                        RM{{ $reward->reward_value }} Off
                    @endif
                </flux:heading>

                <div class="flex items-center mt-2 mb-4">
                    <div class="flex items-center space-x-1.5 text-sm font-bold" style="color: #14B8A6;">
                        <flux:icon.arrow-path class="w-4 h-4" />
                        <span>Every {{ $reward->required_visits }} visits</span>
                    </div>
                </div>

                {{-- Owner Actions --}}
                @if($isOwner)
                    <div class="flex items-center gap-2 pt-3 border-t border-gray-100 dark:border-zinc-800">
                        <flux:button wire:click="edit({{ $reward->id }})" size="sm" variant="ghost"
                            class="text-xs font-bold rounded-lg">
                            <flux:icon.pencil-square class="w-3.5 h-3.5 mr-1" /> Edit
                        </flux:button>
                        <flux:button wire:click="toggleActive({{ $reward->id }})" size="sm" variant="ghost"
                            class="text-xs font-bold rounded-lg">
                            @if($reward->is_active)
                                <flux:icon.pause class="w-3.5 h-3.5 mr-1" /> Pause
                            @else
                                <flux:icon.play class="w-3.5 h-3.5 mr-1" /> Resume
                            @endif
                        </flux:button>
                        <flux:button wire:click="delete({{ $reward->id }})" size="sm" variant="ghost"
                            wire:confirm="Are you sure you want to delete this reward?"
                            class="text-xs font-bold rounded-lg text-rose-600 hover:text-rose-700">
                            <flux:icon.trash class="w-3.5 h-3.5 mr-1" /> Delete
                        </flux:button>
                    </div>
                @endif
            </flux:card>
        @empty
            <div class="sm:col-span-2 lg:col-span-3">
                <flux:card class="border-gray-100 dark:border-zinc-800">
                    <div class="py-16 px-4 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                            <flux:icon.gift class="w-8 h-8 text-gray-300 dark:text-zinc-700" />
                        </div>
                        <flux:heading class="text-sm font-bold text-gray-600 dark:text-gray-400">No rewards configured</flux:heading>
                        <flux:subheading class="mt-1 text-sm text-gray-400 dark:text-gray-500">
                            @if($isOwner)
                                Create your first loyalty reward to start retaining customers.
                            @else
                                No loyalty rewards have been set up yet.
                            @endif
                        </flux:subheading>
                    </div>
                </flux:card>
            </div>
        @endforelse
    </div>

    {{-- ═══════════ Top Loyal Customers ═══════════ --}}
    <flux:card class="p-0 overflow-hidden border-gray-100 dark:border-zinc-800">
        <div class="px-7 py-5 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: #f0fdfa;">
                    <flux:icon.trophy class="w-4 h-4" style="color: #14B8A6;" />
                </div>
                <flux:heading class="text-base font-extrabold text-gray-900 dark:text-white">Top Customers</flux:heading>
            </div>
            <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="color: #14B8A6; background-color: #f0fdfa;">
                {{ count($topCustomers) }} tracked
            </span>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-zinc-800/50">
            @forelse($topCustomers as $index => $customer)
                <div class="flex items-center justify-between px-7 py-4 hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                    <div class="flex items-center space-x-4">
                        {{-- Rank --}}
                        <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black
                            {{ $index === 0 ? 'text-white' : 'bg-gray-100 text-gray-500 border border-gray-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-gray-400' }}"
                            style="{{ $index === 0 ? 'background: linear-gradient(135deg, #14B8A6, #0d9488);' : '' }}">
                            #{{ $index + 1 }}
                        </div>
                        <div>
                            <flux:text class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $customer->wa_id }}
                            </flux:text>
                            <flux:text class="text-[11px] text-gray-400 dark:text-gray-500 font-semibold">
                                Last visit: {{ \Carbon\Carbon::parse($customer->last_visit)->diffForHumans() }}
                            </flux:text>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $customer->total_visits }}</span>
                        <flux:text class="text-xs font-bold text-gray-400">visits</flux:text>
                    </div>
                </div>
            @empty
                <div class="py-16 px-4 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                        <flux:icon.users class="w-8 h-8 text-gray-300 dark:text-zinc-700" />
                    </div>
                    <flux:heading class="text-sm font-bold text-gray-600 dark:text-gray-400">No loyalty data yet</flux:heading>
                    <flux:subheading class="mt-1 text-sm text-gray-400 dark:text-gray-500">Customer visit tracking will appear here as tickets are completed.</flux:subheading>
                </div>
            @endforelse
        </div>
    </flux:card>

</div>
