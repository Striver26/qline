<div class="space-y-6">
    <div class="page-header">
        <span class="page-kicker">Multi-Tenant Operations</span>
        <h1 class="page-title mt-4">Platform Tenants</h1>
    </div>

    <div class="toolbar-card flex flex-col sm:flex-row gap-4 mb-4 lg:mb-0">
        @if(session('status'))
            <div class="p-4 mb-4 text-sm text-brand-800 rounded-xl bg-brand-50 border border-brand-200 w-full sm:w-auto order-last sm:order-first">
                {{ session('status') }}
            </div>
        @endif
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search businesses by name or code..." class="flex-1 max-w-sm" />
        <flux:select wire:model.live="filterStatus" class="w-full sm:w-48">
            <option value="">All Statuses</option>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
            <option value="paused">Paused</option>
        </flux:select>
    </div>

    <div class="glass-card !p-0 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Business Name</flux:table.column>
                <flux:table.column>Join Code</flux:table.column>
                <flux:table.column>Location</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Created</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($businesses as $biz)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold text-slate-800 dark:text-slate-100">{{$biz->name}}</flux:table.cell>
                        <flux:table.cell>
                            <span class="font-mono bg-slate-100 text-slate-700 px-2 py-1 rounded dark:bg-slate-800 dark:text-slate-300">{{$biz->join_code}}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="text-sm truncate max-w-[200px] inline-block">{{$biz->address ?? 'No Address'}}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="badge-pill badge-pill--brand">{{$biz->queue_status}}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{$biz->created_at->format('M d, Y')}}</flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown align="end" position="bottom">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="bottom top" />
                                <flux:menu>
                                    <flux:menu.item wire:click="editStatus({{$biz->id}})" icon="pause-circle">Forced Status</flux:menu.item>
                                    <flux:menu.item wire:click="manageSubscription({{$biz->id}})" icon="credit-card">Manage Subscription</flux:menu.item>
                                    <flux:menu.item wire:click="confirmDelete({{$biz->id}})" variant="danger" icon="trash">Melt Down Tenant</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="5" class="text-center py-8 text-slate-500">No businesses found.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="border-t border-slate-200/70 dark:border-white/10 px-6 py-4">{{$businesses->links()}}</div>
    </div>

    <flux:modal name="edit-business" class="min-w-[22rem]">
        <form wire:submit="updateStatus">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Command Queue Status</h2>
            <p class="text-sm text-slate-500 mb-6">Manually override this tenant's queue operational state (e.g., force close to halt ticket joining temporarily).</p>
            
            <div class="mb-4">
                <flux:select label="System State" wire:model="editStatus" required>
                    <option value="open">Open (Active Tracking)</option>
                    <option value="paused">Paused (Suspended Join)</option>
                    <option value="closed">Closed (Hard Halt)</option>
                </flux:select>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Enforce Command</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-business" class="min-w-[22rem]">
        <form wire:submit="deleteBusiness">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Melt Down Tenant?</h2>
            <p class="text-sm text-slate-500 mb-6">Are you absolutely sure? This violently rips out the tenant, their billing subs, financial history, and global queues. It cannot be salvaged.</p>
            
            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">OBLITERATE ALL</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="manage-subscription" class="min-w-[24rem]">
        <form wire:submit="updateSubscription">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Manage Subscription</h2>
            <p class="text-sm text-slate-500 mb-6">Manually override billing details for this business.</p>
            
            <div class="space-y-4">
                <flux:select label="Subscription Tier" wire:model="editSubType" required>
                    @foreach(\App\Enums\SubTier::cases() as $tier)
                        <option value="{{ $tier->value }}">{{ ucfirst($tier->value) }}</option>
                    @endforeach
                </flux:select>

                <flux:select label="Status" wire:model="editSubStatus" required>
                    <option value="pending">Pending</option>
                    <option value="active">Active</option>
                    <option value="past_due">Past Due</option>
                    <option value="canceled">Canceled</option>
                </flux:select>

                <flux:input type="datetime-local" label="Expires At" wire:model="editSubExpiresAt" required />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Save Subscription</flux:button>
            </div>
        </form>
    </flux:modal>
</div>