<section class="rounded-2xl border border-white/[0.06] bg-white/[0.02] overflow-hidden backdrop-blur-sm flex flex-col">
    <div class="flex items-center justify-between border-b border-white/[0.06] px-5 py-4 shrink-0">
        <h2 class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Waiting Queue</h2>
        <span class="text-[11px] font-semibold text-slate-400">{{ count($waitingEntries) }} waiting</span>
    </div>

    <div data-queue-waiting-list class="divide-y divide-white/[0.04] flex-1 overflow-y-auto max-h-[320px]">
        @forelse($waitingEntries as $entry)
            <div
                data-queue-entry
                data-entry-id="{{ $entry['id'] }}"
                class="flex items-center gap-3 px-5 py-3 cursor-grab hover:bg-white/[0.02] transition-colors {{ $entry['is_next'] ? 'bg-teal-500/[0.03]' : '' }}"
            >
                <div class="flex items-center gap-3 min-w-[100px]">
                    <span class="flex h-5 w-7 shrink-0 items-center justify-center rounded bg-white/5 ring-1 ring-white/[0.06] text-[10px] font-bold text-slate-400">
                        #{{ $entry['queue_position'] }}
                    </span>
                    <span class="text-[13px] font-black text-white tracking-tight">{{ $entry['ticket_code'] }}</span>
                </div>

                <div class="flex-1 text-[12px] font-medium text-slate-400 truncate">
                    {{ $entry['customer_label'] }}
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-[11px] font-medium text-slate-500 tabular-nums">
                        ~{{ $entry['estimated_wait_mins'] }}m
                    </span>

                    @if($entry['is_next'])
                        <span class="flex h-5 items-center rounded-md bg-teal-500/10 ring-1 ring-teal-500/20 px-2 text-[9px] font-bold uppercase tracking-widest text-teal-400">
                            Next
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="flex h-32 flex-col items-center justify-center text-slate-500">
                <flux:icon.users class="h-5 w-5 opacity-40 mb-2" />
                <p class="text-[12px] font-medium">Queue is empty</p>
            </div>
        @endforelse
    </div>
    
    @if(count($waitingEntries) > 0)
        <div class="border-t border-white/[0.06] bg-white/[0.01] px-5 py-2.5 text-center shrink-0">
            <button class="text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:text-white transition flex items-center justify-center w-full gap-1.5">
                View all <flux:icon.chevron-down class="h-3 w-3" />
            </button>
        </div>
    @endif
</section>
