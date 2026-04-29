<section class="glass-card overflow-hidden !p-0">
    <div class="grid gap-6 border-b border-slate-200/70 px-6 py-5 lg:grid-cols-[0.96fr_1.04fr] dark:border-white/10">
        <div>
            <p class="metric-label">Next Action</p>
            <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Preview before calling</h2>
            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                Send the next ticket to a specific service point or drop them onto a free service point without refreshing the page.
            </p>
        </div>

        @if($nextEntry)
            <div class="soft-card border border-brand-100 bg-brand-50/70">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-brand-600">Next in line</p>
                        <p class="mt-3 text-4xl font-bold tracking-[-0.08em] text-slate-950">{{ $nextEntry['ticket_code'] }}</p>
                    </div>
                    <div class="rounded-full border border-brand-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">
                        #{{ $nextEntry['queue_position'] }}
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/70 bg-white px-4 py-3">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.22em] text-slate-400">Customer</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $nextEntry['customer_label'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white px-4 py-3">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.22em] text-slate-400">Source</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $nextEntry['source_label'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white px-4 py-3">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.22em] text-slate-400">Estimated Wait</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">~{{ $nextEntry['estimated_wait_mins'] }} min</p>
                    </div>
                </div>
            </div>
        @else
            <div class="soft-card flex min-h-[12rem] items-center justify-center text-center text-slate-400">
                Queue is clear. New arrivals can be served immediately.
            </div>
        @endif
    </div>

    <div class="grid gap-6 px-6 py-6 lg:grid-cols-1">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="metric-label">Service Point Call</p>
                    <h3 class="mt-2 text-lg font-bold tracking-[-0.04em] text-slate-950 dark:text-white">Call the next ticket</h3>
                </div>
                @if($selectedServicePointId)
                    <span class="badge-pill badge-pill--brand">Selected</span>
                @endif
            </div>

            <button
                type="button"
                wire:click="$dispatch('command-center.call-next')"
                @disabled($businessState['queue_status'] !== 'open' || !$nextEntry)
                class="mesh-accent flex min-h-[10rem] w-full flex-col items-start justify-between rounded-[1.8rem] p-6 text-left text-white shadow-[0_34px_90px_-42px_rgba(15,159,124,0.7)] transition duration-300 hover:-translate-y-1 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <div>
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.26em] text-white/70">Primary action</p>
                    <p class="mt-4 text-3xl font-bold tracking-[-0.06em]">
                        {{ $selectedServicePointId ? 'Call To Selected Service Point' : 'Call Next Ticket' }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-[1.1rem] border border-white/15 bg-white/10 backdrop-blur-sm">
                    <flux:icon.arrow-right class="h-6 w-6" />
                </div>
            </button>

            @if($this->availableServicePoints)
                <div class="flex flex-wrap gap-3 mt-4">
                    @foreach($this->availableServicePoints as $servicePoint)
                        <button
                            type="button"
                            wire:click="$dispatch('command-center.call-entry', { entryId: {{ $nextEntry['id'] ?? 0 }}, servicePointId: {{ $servicePoint['id'] }} })"
                            @disabled($businessState['queue_status'] !== 'open' || !$nextEntry)
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-brand-200 hover:text-brand-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ $servicePoint['name'] }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
