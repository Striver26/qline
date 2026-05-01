<section class="rounded-[1.5rem] bg-[#0A0F1C] border border-white/5 overflow-hidden">
    <div class="flex items-center justify-between border-b border-white/5 px-6 py-5">
        <h2 class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Currently Serving</h2>
        <span class="rounded bg-teal-500/10 border border-teal-500/20 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-teal-400">
            {{ count($activeEntries) }} active
        </span>
    </div>

    <div class="p-4 space-y-3">
        @forelse($activeEntries as $entry)
            <div class="flex items-center justify-between gap-4 rounded-xl bg-white/5 border border-white/5 p-3 pr-4 transition hover:bg-white/10">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-14 shrink-0 items-center justify-center rounded-lg bg-teal-500 text-sm font-black text-teal-950 shadow-[0_0_15px_rgba(45,212,191,0.3)]">
                        {{ $entry['ticket_code'] }}
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="truncate text-[13px] font-bold text-white">{{ $entry['customer_label'] }}</p>
                            <span class="rounded bg-white/10 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-widest text-slate-300">
                                {{ $entry['status_label'] }}
                            </span>
                        </div>

                        <div class="mt-1 flex items-center gap-2 text-[11px] text-slate-500">
                            <span>{{ $entry['service_point_label'] ?? 'Unassigned' }}</span>
                            <span class="h-1 w-1 rounded-full bg-slate-700"></span>
                            <span class="flex items-center gap-1"><flux:icon.clock class="h-3 w-3" /> {{ $entry['called_human'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-1">
                    <button
                        wire:click="$dispatch('command-center.recall', { entryId: {{ $entry['id'] }} })"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/5 text-slate-400 transition hover:bg-white/10 hover:text-white"
                        title="Recall customer"
                    >
                        <flux:icon.speaker-wave class="h-4 w-4" />
                    </button>

                    <button
                        wire:click="$dispatch('command-center.mark-done', { entryId: {{ $entry['id'] }} })"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/5 text-slate-400 transition hover:bg-teal-500 hover:text-teal-950"
                        title="Mark as done"
                    >
                        <flux:icon.check class="h-4 w-4" />
                    </button>

                    <button
                        wire:click="$dispatch('command-center.skip', { entryId: {{ $entry['id'] }} })"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/5 text-slate-400 transition hover:bg-white/10 hover:text-white"
                        title="More options"
                    >
                        <flux:icon.ellipsis-vertical class="h-4 w-4" />
                    </button>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-slate-500">
                <flux:icon.bolt class="mx-auto h-6 w-6 opacity-50 mb-2" />
                <p class="text-sm font-semibold">No active tickets</p>
            </div>
        @endforelse
    </div>
</section>
