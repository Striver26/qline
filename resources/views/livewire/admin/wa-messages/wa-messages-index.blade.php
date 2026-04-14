<div class="space-y-6">
    <div class="page-header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center w-full gap-4">
            <div>
                <span class="page-kicker">Communications</span>
                <h1 class="page-title mt-4">WhatsApp Log Trace</h1>
            </div>
            
            <flux:modal.trigger name="prune-wa">
                <flux:button variant="danger" icon="trash">Prune Dumps</flux:button>
            </flux:modal.trigger>
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
                <flux:table.column>Direction</flux:table.column>
                <flux:table.column>Recipient</flux:table.column>
                <flux:table.column>Content</flux:table.column>
                <flux:table.column>Date</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($messages as $msg)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold text-slate-800 dark:text-slate-100">{{$msg->business->name ?? 'Unknown'}}</flux:table.cell>
                        <flux:table.cell>
                            <span class="badge-pill badge-pill--brand">{{$msg->direction}}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{$msg->wa_id}}</flux:table.cell>
                        <flux:table.cell><span class="truncate max-w-[250px] inline-block">{{$msg->body}}</span></flux:table.cell>
                        <flux:table.cell>{{$msg->created_at->format('M d h:i A')}}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="text-center py-8 text-slate-500">No messages found.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="border-t border-slate-200/70 dark:border-white/10 px-6 py-4">{{$messages->links()}}</div>
    </div>

    <flux:modal name="prune-wa" class="min-w-[22rem]">
        <form wire:submit="pruneLogs">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Prune WhatsApp Logs?</h2>
            <p class="text-sm text-slate-500 mb-6">This will permanently and irreversibly purge all WhatsApp transmission logs older than 30 days to free storage.</p>
            
            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger" wire:loading.attr="disabled">Yes, Prune Logs</flux:button>
            </div>
        </form>
    </flux:modal>
</div>