<section class="rounded-2xl border border-white/[0.06] bg-white/[0.02] overflow-hidden backdrop-blur-sm">
    <div class="flex items-center justify-between border-b border-white/[0.06] px-5 py-4">
        <h2 class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Currently Serving</h2>
        <span class="rounded-md bg-teal-500/10 ring-1 ring-teal-500/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-teal-400">
            {{ count($activeEntries) }} active
        </span>
    </div>

    <div data-queue-active-list class="p-3 space-y-2">
        @forelse($activeEntries as $entry)
            <div
                data-queue-entry
                data-entry-id="{{ $entry['id'] }}"
                class="flex items-center justify-between gap-3 rounded-xl bg-white/[0.03] ring-1 ring-white/[0.06] p-3 cursor-grab transition hover:bg-white/[0.06]"
            >
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-10 w-12 shrink-0 items-center justify-center rounded-lg bg-teal-500 text-[12px] font-black text-teal-950 shadow-[0_0_15px_rgba(45,212,191,0.3)]">
                        {{ $entry['ticket_code'] }}
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="truncate text-[13px] font-bold text-white">{{ $entry['customer_label'] }}</p>
                            <span class="rounded bg-white/10 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-widest text-slate-300 shrink-0">
                                {{ $entry['status_label'] }}
                            </span>
                        </div>

                        <div class="mt-0.5 flex items-center gap-2 text-[11px] text-slate-500">
                            <span>{{ $entry['service_point_label'] ?? 'Unassigned' }}</span>
                            <span class="h-1 w-1 rounded-full bg-slate-700"></span>
                            <span class="flex items-center gap-1"><flux:icon.clock class="h-3 w-3" /> {{ $entry['called_human'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-1 shrink-0">
                    <button
                        wire:click="$dispatch('command-center.recall', { entryId: {{ $entry['id'] }} })"
                        class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/5 text-slate-400 transition hover:bg-white/10 hover:text-white"
                        title="Recall"
                    >
                        <flux:icon.speaker-wave class="h-3.5 w-3.5" />
                    </button>

                    <button
                        wire:click="$dispatch('command-center.mark-done', { entryId: {{ $entry['id'] }} })"
                        class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/5 text-slate-400 transition hover:bg-teal-500 hover:text-teal-950"
                        title="Done"
                    >
                        <flux:icon.check class="h-3.5 w-3.5" />
                    </button>

                    <button
                        wire:click="$dispatch('command-center.skip', { entryId: {{ $entry['id'] }} })"
                        class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/5 text-slate-400 transition hover:bg-white/10 hover:text-white"
                        title="Skip"
                    >
                        <flux:icon.ellipsis-vertical class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="py-8 text-center text-slate-500">
                <flux:icon.bolt class="mx-auto h-5 w-5 opacity-50 mb-2" />
                <p class="text-[12px] font-semibold">No active tickets</p>
            </div>
        @endforelse
    </div>
</section>
