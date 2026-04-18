<div class="space-y-6">
    <div class="page-header">
        <div>
            <span class="page-kicker">Queue History</span>
            <h1 class="page-title mt-4">Ticket Timeline</h1>
            <p class="page-description mt-3">
                Search by ticket or contact, filter by status, and quickly review how the queue moved through the day.
            </p>
        </div>
    </div>

    <div class="toolbar-card">
        <div>
            <p class="metric-label">Filters</p>
            <h2 class="mt-2 text-2xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Find the exact ticket
                fast</h2>
        </div>

        <div class="flex w-full flex-col gap-3 md:w-auto md:flex-row">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                placeholder="Search ticket or phone..." class="w-full md:w-72" />
            <flux:select wire:model.live="status" class="w-full md:w-48">
                <option value="">All Statuses</option>
                <option value="waiting">Waiting</option>
                <option value="called">Called</option>
                <option value="serving">Serving</option>
                <option value="completed">Completed</option>
                <option value="skipped">Skipped</option>
                <option value="cancelled">Cancelled</option>
            </flux:select>
        </div>
    </div>

    <div class="glass-card !p-0 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="px-6 py-4">Ticket</flux:table.column>
                <flux:table.column class="px-6 py-4">Phone Number</flux:table.column>
                <flux:table.column class="px-6 py-4">Status</flux:table.column>
                <flux:table.column class="px-6 py-4">Source</flux:table.column>
                <flux:table.column class="px-6 py-4 text-right">Date & Time</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($entries as $entry)
                    <flux:table.row>
                        <flux:table.cell class="px-6 py-4">
                            <span class="badge-pill badge-pill--brand">{{ $entry->ticket_code }}</span>
                        </flux:table.cell>

                        <flux:table.cell class="px-6 py-4">
                            <span
                                class="font-semibold text-slate-800 dark:text-slate-100">{{ $entry->wa_id ?: 'Anonymous' }}</span>
                        </flux:table.cell>

                        <flux:table.cell class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'waiting' => 'border-brand-200 bg-brand-50 text-brand-700',
                                    'called' => 'border-amber-200 bg-amber-50 text-amber-700',
                                    'serving' => 'border-blue-200 bg-blue-50 text-blue-700',
                                    'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                    'skipped' => 'border-orange-200 bg-orange-50 text-orange-700',
                                    'cancelled' => 'border-rose-200 bg-rose-50 text-rose-700',
                                ];
                                $colorClass = $statusColors[$entry->status] ?? 'border-slate-200 bg-slate-100 text-slate-700';
                            @endphp
                            <span
                                class="inline-flex items-center rounded-full border px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.24em] {{ $colorClass }}">
                                {{ $entry->status }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell class="px-6 py-4">
                            <span
                                class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ $entry->source === 'whatsapp' ? 'WhatsApp' : 'Anonymous' }}</span>
                        </flux:table.cell>

                        <flux:table.cell class="px-6 py-4 text-right">
                            <span
                                class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ $entry->created_at->format('M d, Y h:i A') }}</span>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="px-6 py-16 text-center">
                            <div
                                class="mx-auto flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-slate-100 dark:bg-slate-800">
                                <flux:icon.clock class="h-7 w-7 text-slate-300 dark:text-slate-600" />
                            </div>
                            <h3 class="mt-4 text-lg font-bold tracking-[-0.04em] text-slate-700 dark:text-slate-200">No
                                tickets found</h3>
                            <p class="mt-2 text-sm text-slate-400">Try adjusting your search or status filter.</p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if($entries->hasPages())
            <div class="border-t border-slate-200/70 px-6 py-4 dark:border-white/10">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>