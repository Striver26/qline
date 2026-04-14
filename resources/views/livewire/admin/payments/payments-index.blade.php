<div class="space-y-6">
    <div class="page-header">
        <span class="page-kicker">Revenue</span>
        <h1 class="page-title mt-4">SaaS Payments</h1>
    </div>

    @if(session('status'))
        <div class="p-4 mb-4 text-sm text-brand-800 rounded-xl bg-brand-50 border border-brand-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="toolbar-card flex flex-col sm:flex-row gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search businesses or REF codes..." class="flex-1 max-w-sm" />
        <flux:select wire:model.live="filterStatus" class="w-full sm:w-48">
            <option value="">All Statuses</option>
            <option value="paid">Paid (Ledger)</option>
            <option value="pending">Pending</option>
            <option value="failed">Failed Tx</option>
        </flux:select>
    </div>

    <div class="glass-card !p-0 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Business</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Method</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($payments as $payment)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold text-slate-800 dark:text-slate-100">{{$payment->business->name ?? 'Unknown'}}</flux:table.cell>
                        <flux:table.cell>{{$payment->currency}} {{$payment->amount}}</flux:table.cell>
                        <flux:table.cell class="uppercase">{{$payment->method}}</flux:table.cell>
                        <flux:table.cell>
                            <span class="badge-pill badge-pill--brand">{{$payment->status}}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{$payment->created_at->format('M d, Y h:i A')}}</flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown align="end" position="bottom">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="bottom top" />
                                <flux:menu>
                                    <flux:menu.item wire:click="editPayment({{$payment->id}})" icon="document-check">Reconcile Status</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="text-center py-8 text-slate-500">No payments found.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="border-t border-slate-200/70 dark:border-white/10 px-6 py-4">{{$payments->links()}}</div>
    </div>

    <flux:modal name="edit-payment" class="min-w-[22rem]">
        <form wire:submit="updatePayment">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Reconcile Transaction</h2>
            <p class="text-sm text-slate-500 mb-6">Manually force this payment's status state (e.g. if the FPX gateway callback failed).</p>
            
            <div class="mb-4">
                <flux:select label="Payment Status" wire:model="editStatus" required>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid (Confirmed)</option>
                    <option value="failed">Failed / Rejected</option>
                </flux:select>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Enforce Ledger Status</flux:button>
            </div>
        </form>
    </flux:modal>
</div>