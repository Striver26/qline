<div class="space-y-6">
    <div class="page-header">
        <div>
            <span class="page-kicker">Retention</span>
            <h1 class="page-title mt-4">Loyalty Rewards</h1>
            <p class="page-description mt-3">
                Turn repeat visits into something customers can see and redeem, without making the workflow harder for staff.
            </p>
        </div>

        @if($isOwner)
            <flux:button wire:click="openForm" variant="primary" class="mesh-accent rounded-full px-5 py-2.5 font-semibold text-white">
                <flux:icon.plus class="mr-2 h-4 w-4" />
                Add Reward Tier
            </flux:button>
        @endif
    </div>

    @if($showForm && $isOwner)
        <div class="glass-card">
            <span class="page-kicker">{{ $editingId ? __('Edit reward') : __('Create reward') }}</span>
            <h2 class="mt-4 text-3xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">
                {{ $editingId ? 'Update reward settings' : 'Build a new reward tier' }}
            </h2>

            <form wire:submit="save" class="mt-6 space-y-6">
                <div class="grid gap-6 lg:grid-cols-3">
                    <flux:field>
                        <flux:label>Reward Type</flux:label>
                        <flux:select wire:model="reward_type">
                            <option value="freebie">Free Item</option>
                            <option value="discount_percent">Discount (%)</option>
                            <option value="discount_fixed">Discount (RM)</option>
                        </flux:select>
                        <flux:error name="reward_type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ $reward_type === 'freebie' ? 'Reward Name' : 'Reward Value' }}</flux:label>
                        <flux:input
                            wire:model="reward_value"
                            placeholder="{{ $reward_type === 'freebie' ? 'Free drink' : ($reward_type === 'discount_percent' ? '10' : '5.00') }}"
                            required
                        />
                        <flux:error name="reward_value" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Visits Required</flux:label>
                        <flux:input wire:model="required_visits" type="number" min="1" max="999" required />
                        <flux:error name="required_visits" />
                    </flux:field>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <flux:button type="submit" variant="primary" class="mesh-accent rounded-full px-5 py-2.5 font-semibold text-white">
                        {{ $editingId ? 'Update Reward' : 'Create Reward' }}
                    </flux:button>
                    <flux:button wire:click="cancelForm" variant="ghost" class="rounded-full px-5 py-2.5 font-semibold">
                        Cancel
                    </flux:button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($rewards as $reward)
            <div class="glass-card {{ !$reward->is_active ? 'opacity-65' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-[1rem]
                        {{ $reward->reward_type === 'freebie' ? 'bg-brand-50 text-brand-700' : ($reward->reward_type === 'discount_percent' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700') }}">
                        @if($reward->reward_type === 'freebie')
                            <flux:icon.gift class="h-6 w-6" />
                        @elseif($reward->reward_type === 'discount_percent')
                            <flux:icon.receipt-percent class="h-6 w-6" />
                        @else
                            <flux:icon.banknotes class="h-6 w-6" />
                        @endif
                    </div>

                    <span class="badge-pill {{ $reward->is_active ? 'badge-pill--brand' : '' }}">
                        {{ $reward->is_active ? 'Active' : 'Paused' }}
                    </span>
                </div>

                <h2 class="mt-6 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">
                    @if($reward->reward_type === 'freebie')
                        {{ $reward->reward_value }}
                    @elseif($reward->reward_type === 'discount_percent')
                        {{ $reward->reward_value }}% Discount
                    @else
                        RM{{ $reward->reward_value }} Off
                    @endif
                </h2>

                <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
                    Unlock after {{ $reward->required_visits }} visit{{ $reward->required_visits > 1 ? 's' : '' }}.
                </p>

                @if($isOwner)
                    <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-slate-200/70 pt-4 dark:border-white/10">
                        <flux:button wire:click="edit({{ $reward->id }})" size="sm" variant="ghost" class="rounded-full px-3 font-semibold">
                            Edit
                        </flux:button>
                        <flux:button wire:click="toggleActive({{ $reward->id }})" size="sm" variant="ghost" class="rounded-full px-3 font-semibold">
                            {{ $reward->is_active ? 'Pause' : 'Resume' }}
                        </flux:button>
                        <flux:button
                            wire:click="removeReward({{ $reward->id }})"
                            size="sm"
                            variant="ghost"
                            onclick="return confirm('Are you sure you want to delete this reward?')"
                            class="rounded-full px-3 font-semibold text-rose-600"
                        >
                            Delete
                        </flux:button>
                    </div>
                @endif
            </div>
        @empty
            <div class="glass-card md:col-span-2 xl:col-span-3">
                <div class="py-12 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.25rem] bg-slate-100 dark:bg-slate-800">
                        <flux:icon.gift class="h-8 w-8 text-slate-300 dark:text-slate-600" />
                    </div>
                    <h2 class="mt-5 text-2xl font-bold tracking-[-0.05em] text-slate-700 dark:text-slate-200">No rewards configured yet</h2>
                    <p class="mt-3 text-sm text-slate-400">
                        {{ $isOwner ? 'Create your first reward tier to encourage repeat visits.' : 'Rewards will appear here when the owner sets them up.' }}
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="glass-card !p-0 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
            <div>
                <p class="metric-label">Most Engaged Customers</p>
                <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Top Customers</h2>
            </div>
            <span class="badge-pill">{{ count($topCustomers) }} tracked</span>
        </div>

        <div class="divide-y divide-slate-200/70 dark:divide-white/10">
            @forelse($topCustomers as $index => $customer)
                <div class="flex items-center justify-between gap-4 px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-[0.95rem] {{ $index === 0 ? 'mesh-accent text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                            #{{ $index + 1 }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $customer->wa_id }}</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.24em] text-slate-400">
                                Last visit {{ \Carbon\Carbon::parse($customer->last_visit)->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <p class="text-lg font-bold tracking-[-0.04em] text-slate-950 dark:text-white">{{ $customer->total_visits }} visits</p>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.25rem] bg-slate-100 dark:bg-slate-800">
                        <flux:icon.users class="h-8 w-8 text-slate-300 dark:text-slate-600" />
                    </div>
                    <h3 class="mt-5 text-xl font-bold tracking-[-0.04em] text-slate-700 dark:text-slate-200">No loyalty data yet</h3>
                    <p class="mt-2 text-sm text-slate-400">Completed visits will start building the leaderboard here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
