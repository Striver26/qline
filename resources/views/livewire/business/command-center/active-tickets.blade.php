<section class="glass-card !p-0 overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
        <div>
            <p class="metric-label">Active Tickets</p>
            <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Currently serving</h2>
        </div>

        <span class="badge-pill badge-pill--brand">{{ count($activeEntries) }} active</span>
    </div>

    <div class="space-y-3 p-5">
        @forelse($activeEntries as $entry)
            <div class="soft-card flex items-center justify-between gap-4">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="mesh-accent flex h-14 w-14 shrink-0 items-center justify-center rounded-[1.15rem] text-sm font-bold text-white shadow-[0_24px_60px_-32px_rgba(15,159,124,0.65)]">
                        {{ $entry['ticket_code'] }}
                    </div>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $entry['customer_label'] }}</p>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[0.62rem] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                {{ $entry['status_label'] }}
                            </span>
                        </div>

                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs uppercase tracking-[0.2em] text-slate-400">
                            <span>{{ $entry['service_point_label'] ?? 'Unassigned' }}</span>
                            <span>{{ $entry['called_human'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <flux:button
                        wire:click="$dispatch('command-center.recall', { entryId: {{ $entry['id'] }} })"
                        size="sm"
                        variant="ghost"
                        class="rounded-full bg-slate-50 px-3 text-slate-600"
                        data-flux-tooltip="Recall customer"
                    >
                        <flux:icon.speaker-wave class="h-4 w-4" />
                    </flux:button>

                    <flux:button
                        wire:click="$dispatch('command-center.mark-done', { entryId: {{ $entry['id'] }} })"
                        size="sm"
                        variant="ghost"
                        class="rounded-full bg-brand-50 px-3 text-brand-700"
                    >
                        <flux:icon.check class="h-4 w-4" />
                    </flux:button>

                    <flux:button
                        wire:click="$dispatch('command-center.skip', { entryId: {{ $entry['id'] }} })"
                        size="sm"
                        variant="ghost"
                        class="rounded-full bg-rose-50 px-3 text-rose-600"
                    >
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
</section>
