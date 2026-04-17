<div class="space-y-8" wire:poll.10s>
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-500/30 dark:bg-rose-500/10">
            <div class="flex items-center gap-3">
                <flux:icon.exclamation-circle class="h-5 w-5 text-rose-600 dark:text-rose-400" />
                <p class="text-sm font-medium text-rose-800 dark:text-rose-300">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    @endif

    <div class="page-header">
        <div>
            <span class="page-kicker">{{ $business->name ?? 'Your Business' }}</span>
            <h1 class="page-title mt-4">Command Center</h1>
            <p class="page-description mt-3">
                Open or pause the queue, monitor the current load, and keep the next customer handoff clear for staff.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="badge-pill {{ $business->queue_status === 'open' ? 'badge-pill--brand' : ($business->queue_status === 'paused' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 border-amber-200' : '') }}">
                <span class="h-2.5 w-2.5 rounded-full {{ $business->queue_status === 'open' ? 'bg-brand-500' : ($business->queue_status === 'paused' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                {{ $business->queue_status === 'open' ? 'Queue Open' : ($business->queue_status === 'paused' ? 'Queue Paused' : 'Queue Closed') }}
            </div>

            @if($business->queue_status === 'open')
                <flux:modal.trigger name="pause-queue-modal">
                    <flux:button variant="subtle" class="rounded-full px-5 py-2.5 font-semibold text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10">
                        <flux:icon.pause class="mr-2 h-4 w-4" />
                        Pause
                    </flux:button>
                </flux:modal.trigger>
            @elseif($business->queue_status === 'paused')
                <flux:button wire:click="resumeQueue" variant="subtle" class="rounded-full px-5 py-2.5 font-semibold text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-500/10">
                    <flux:icon.play class="mr-2 h-4 w-4" />
                    Resume
                </flux:button>
            @endif

            <flux:button
                wire:click="toggleQueue"
                class="rounded-full px-5 py-2.5 font-semibold shadow-[0_24px_60px_-32px_rgba(15,23,42,0.4)]"
                style="{{ $business->queue_status !== 'closed' ? '' : 'background: linear-gradient(135deg, #149f7c, #0f7f66); border-color: #149f7c; color: white;' }}"
                :variant="$business->queue_status !== 'closed' ? 'danger' : 'primary'"
            >
                <flux:icon class="mr-2 h-4 w-4" :name="$business->queue_status !== 'closed' ? 'x-mark' : 'bolt'" />
                {{ $business->queue_status !== 'closed' ? 'Close Queue' : 'Open Queue' }}
            </flux:button>
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-3">
        <div class="metric-card">
            <p class="metric-label">Waiting Right Now</p>
            <div class="mt-5 flex items-end justify-between gap-4">
                <p class="metric-value mt-0">{{ count($this->waitingEntries) }}</p>
                <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] bg-brand-50 text-brand-700">
                    <flux:icon.users class="h-7 w-7" />
                </div>
            </div>
        </div>

        <div class="metric-card">
            <p class="metric-label">Served Today</p>
            <div class="mt-5 flex items-end justify-between gap-4">
                <p class="metric-value mt-0">{{ max(0, ($business->entries_today ?? 0) - count($this->waitingEntries)) }}</p>
                <div class="flex h-14 w-14 items-center justify-center rounded-[1.25rem] bg-coral-50 text-coral-700">
                    <flux:icon.check-circle class="h-7 w-7" />
                </div>
            </div>
        </div>

        <flux:button
            wire:click="callNext"
            :disabled="$business->queue_status !== 'open'"
            class="mesh-accent relative flex h-full min-h-[11rem] flex-col items-start justify-between rounded-[1.8rem] p-6 text-left text-white shadow-[0_34px_90px_-42px_rgba(15,159,124,0.7)] transition duration-300 hover:-translate-y-1 disabled:cursor-not-allowed disabled:opacity-60"
        >
            <div>
                <p class="text-[0.72rem] font-semibold uppercase tracking-[0.26em] text-white/70">Next Action</p>
                <p class="mt-4 text-4xl font-bold tracking-[-0.06em]">Call Next</p>
            </div>

            <div class="flex h-12 w-12 items-center justify-center rounded-[1.1rem] border border-white/15 bg-white/10 backdrop-blur-sm">
                <flux:icon.arrow-right class="h-6 w-6" />
            </div>
        </flux:button>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="glass-card !p-0">
            <div class="flex items-center justify-between border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
                <div>
                    <p class="metric-label">Active Tickets</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Currently Serving</h2>
                </div>
                <span class="badge-pill badge-pill--brand">{{ count($this->activeEntries) }} active</span>
            </div>

            <div class="space-y-3 p-5">
                @forelse($this->activeEntries as $entry)
                    <div class="soft-card flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="mesh-accent flex h-14 w-14 items-center justify-center rounded-[1.15rem] text-sm font-bold text-white shadow-[0_24px_60px_-32px_rgba(15,159,124,0.65)]">
                                {{ $entry->ticket_code }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ $entry->wa_id ? $entry->wa_id : 'Walk-in customer' }}
                                </p>
                                <p class="mt-1 text-xs uppercase tracking-[0.24em] text-slate-400">
                                    {{ ucfirst($entry->status) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($entry->wa_id)
                                @php
                                    $reward = \App\Models\Marketing\EarnedReward::where('business_id', $business->id)
                                        ->where('wa_id', $entry->wa_id)
                                        ->where('status', 'available')
                                        ->first();
                                @endphp

                                @if($reward)
                                    <flux:button
                                        wire:click="redeemReward({{ $reward->id }})"
                                        wire:confirm="Redeem {{ $reward->reward->reward_value }} for this customer?"
                                        size="sm"
                                        class="rounded-full border border-amber-200 bg-amber-50 px-3 text-amber-700"
                                    >
                                        Redeem
                                    </flux:button>
                                @endif
                            @endif

                            <flux:button wire:click="markDone({{ $entry->id }})" size="sm" variant="ghost" class="rounded-full bg-brand-50 px-3 text-brand-700">
                                <flux:icon.check class="h-4 w-4" />
                            </flux:button>

                            <flux:button wire:click="skip({{ $entry->id }})" size="sm" variant="ghost" class="rounded-full bg-rose-50 px-3 text-rose-600">
                                <flux:icon.forward class="h-4 w-4" />
                            </flux:button>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400">
                        No active tickets right now.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="glass-card !p-0">
            <div class="flex items-center justify-between border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
                <div>
                    <p class="metric-label">Upcoming</p>
                    <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Waiting Line</h2>
                </div>
                <span class="badge-pill">{{ count($this->waitingEntries) }} in queue</span>
            </div>

            <div class="space-y-3 p-5">
                @forelse($this->waitingEntries as $entry)
                    <div class="soft-card flex items-center gap-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-[1rem] {{ $loop->first ? 'mesh-accent text-white' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300' }}">
                            #{{ $entry->position }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $entry->ticket_code }}</p>
                            <p class="mt-1 text-xs uppercase tracking-[0.24em] text-slate-400">
                                {{ $entry->source === 'whatsapp' ? 'WhatsApp' : 'Walk-in' }}
                            </p>
                        </div>

                        <p class="text-xs text-slate-400">{{ $entry->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400">
                        Queue is empty.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <flux:modal name="pause-queue-modal" class="md:max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Pause Queue</flux:heading>
                <flux:subheading>Let your customers know why the queue is temporarily on hold.</flux:subheading>
            </div>

            <flux:input wire:model="pauseReason" label="Pause Reason" placeholder="e.g., On break for 15 minutes, Friday prayers..." required />

            <div class="flex items-center gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="pauseQueue" variant="primary">Confirm Pause</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
