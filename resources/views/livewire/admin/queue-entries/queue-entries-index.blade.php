<div class="space-y-6">
    <div class="page-header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center w-full gap-4">
            <div>
                <span class="page-kicker">Core System</span>
                <h1 class="page-title mt-4">Global Queue Trace</h1>
            </div>

        <div class="flex flex-wrap items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search business, ticket, or 'Anonymous'..." class="w-full md:w-80" />
            
            <flux:modal.trigger name="prune-queues">
                <flux:button variant="danger" icon="trash">Prune Dumps</flux:button>
            </flux:modal.trigger>
        </div>
        </div>
    </div>

    @if(session('status'))
        <div class="p-4 mb-4 text-sm text-brand-800 rounded-xl bg-brand-50 border border-brand-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="glass-card !p-0 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Business</flux:table.column>
                <flux:table.column>Ticket</flux:table.column>
                <flux:table.column>Phone</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Logged</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($entries as $entry)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold text-slate-800 dark:text-slate-100">
                            {{$entry->business->name ?? 'Unknown'}}</flux:table.cell>
                        <flux:table.cell><span
                                class="font-mono bg-slate-100 text-slate-700 px-2 py-1 rounded dark:bg-slate-800 dark:text-slate-300">{{$entry->ticket_code}}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{$entry->wa_id ?? 'Anonymous'}}</flux:table.cell>
                        <flux:table.cell>
                            <span class="badge-pill badge-pill--brand">{{$entry->status}}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{$entry->created_at->format('M d h:i A')}}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-8 text-slate-500">No queue entries found.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="border-t border-slate-200/70 dark:border-white/10 px-6 py-4">{{$entries->links()}}</div>
    </div>

    <flux:modal name="prune-queues" class="min-w-[22rem]">
        <form wire:submit="pruneLogs">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Prune Legacy Queues?</h2>
            <p class="text-sm text-slate-500 mb-6">This rigidly wipes all queue entries older than 30 days. It
                aggressively helps free up database storage parameters.</p>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger" wire:loading.attr="disabled">Yes, Prune Records
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>