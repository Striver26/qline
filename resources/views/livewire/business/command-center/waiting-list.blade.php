<section class="rounded-[1.5rem] bg-[#0A0F1C] border border-white/5 overflow-hidden flex flex-col h-full">
    <div class="flex items-center justify-between border-b border-white/5 px-6 py-5 shrink-0">
        <h2 class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Waiting Queue</h2>
        <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-400">
            {{ count($waitingEntries) }} waiting <flux:icon.chevron-right class="h-3 w-3" />
        </div>
    </div>

    <div data-queue-waiting-list class="divide-y divide-white/5 flex-1 overflow-y-auto">
        @forelse($waitingEntries as $entry)
            <div
                data-queue-entry
                data-entry-id="{{ $entry['id'] }}"
                class="flex items-center gap-4 px-6 py-4 cursor-grab hover:bg-white/[0.02] transition-colors {{ $entry['is_next'] ? 'bg-teal-500/[0.02]' : '' }}"
            >
                <div class="flex items-center gap-4 w-1/3 min-w-[120px]">
                    <div class="flex h-6 w-8 shrink-0 items-center justify-center rounded bg-white/5 border border-white/5 text-[10px] font-bold text-slate-400">
                        #{{ $entry['queue_position'] }}
                    </div>
                    <span class="text-sm font-black text-white tracking-tight">{{ $entry['ticket_code'] }}</span>
                </div>

                <div class="flex-1 text-[13px] font-medium text-slate-300 truncate">
                    {{ $entry['customer_label'] }}
                </div>

                <div class="flex items-center justify-end gap-6 w-1/3 min-w-[140px]">
                    <span class="text-[12px] font-medium text-slate-500 tabular-nums">
                        ~{{ $entry['estimated_wait_mins'] }} min
                    </span>

                    @if($entry['is_next'])
                        <span class="flex h-5 items-center rounded-md bg-teal-500/10 border border-teal-500/20 px-2 text-[9px] font-bold uppercase tracking-widest text-teal-400">
                            Next
                        </span>
                    @else
                        <span class="flex h-5 items-center rounded-md bg-white/5 border border-white/5 px-2 text-[9px] font-bold uppercase tracking-widest text-slate-500">
                            Wait
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="flex h-40 flex-col items-center justify-center text-slate-500">
                <flux:icon.users class="h-6 w-6 opacity-40 mb-2" />
                <p class="text-[13px] font-medium">Queue is empty</p>
            </div>
        @endforelse
    </div>
    
    @if(count($waitingEntries) > 0)
        <div class="border-t border-white/5 bg-[#060913]/30 px-6 py-3 text-center shrink-0">
            <button class="text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:text-white transition flex items-center justify-center w-full gap-1.5">
                View all waiting <flux:icon.chevron-down class="h-3 w-3" />
            </button>
        </div>
    @endif
</section>
