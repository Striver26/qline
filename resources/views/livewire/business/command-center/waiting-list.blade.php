<section class="glass-card !p-0 overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-200/70 px-6 py-5 dark:border-white/10">
        <div>
            <p class="metric-label">Waiting Queue</p>
            <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Drag from the live waiting list</h2>
        </div>

        <span class="badge-pill">{{ count($waitingEntries) }} shown</span>
    </div>

    <div class="border-b border-slate-200/70 bg-slate-50/70 px-6 py-4 text-sm text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400">
        Drag the next customer onto a free table or available counter. The first ticket is highlighted as the current priority.
        @if($hiddenCount > 0)
            <span class="font-semibold text-slate-700 dark:text-slate-200">Showing the next {{ count($waitingEntries) }} tickets with {{ $hiddenCount }} more still waiting.</span>
        @endif
    </div>

    <div data-queue-waiting-list class="space-y-3 p-5">
        @forelse($waitingEntries as $entry)
            <article
                data-queue-entry
                data-entry-id="{{ $entry['id'] }}"
                class="soft-card cursor-grab border {{ $entry['is_next'] ? 'border-brand-200 bg-brand-50/70 shadow-[0_22px_50px_-36px_rgba(15,159,124,0.45)]' : 'border-transparent' }}"
            >
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[1rem] {{ $entry['is_next'] ? 'mesh-accent text-white' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300' }}">
                        #{{ $entry['queue_position'] }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-3">
                            <p class="truncate text-base font-bold tracking-[-0.03em] text-slate-950 dark:text-white">{{ $entry['ticket_code'] }}</p>
                            @if($entry['is_next'])
                                <span class="rounded-full border border-brand-200 bg-white px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.22em] text-brand-700">
                                    Next
                                </span>
                            @endif
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-2 text-xs uppercase tracking-[0.2em] text-slate-400">
                            <span>{{ $entry['customer_label'] }}</span>
                            <span>{{ $entry['source_label'] }}</span>
                            <span>~{{ $entry['estimated_wait_mins'] }} min</span>
                            <span>{{ $entry['created_human'] }}</span>

                            <button 
                                wire:click="$dispatch('command-center.print-entry', { entryId: {{ $entry['id'] }} })"
                                class="ml-auto flex items-center gap-1.5 text-brand-600 hover:text-brand-700 transition-colors"
                            >
                                <flux:icon.printer class="h-3.5 w-3.5" />
                                <span>Print</span>
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="p-12 text-center text-slate-400">
                Queue is empty.
            </div>
        @endforelse
    </div>
</section>
