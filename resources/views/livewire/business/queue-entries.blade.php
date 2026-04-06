<div class="space-y-8">

    {{-- ═══════════ Header ═══════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <flux:subheading class="text-sm font-bold uppercase tracking-widest mb-1" style="color: #14B8A6;">History</flux:subheading>
            <flux:heading size="xl" class="text-4xl font-black tracking-tight text-gray-900 dark:text-white leading-none">Ticket History</flux:heading>
        </div>
        <div class="flex items-center gap-3">
            {{-- Search --}}
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search ticket or phone..." class="w-64" />

            {{-- Status Filter --}}
            <flux:select wire:model.live="status" class="w-44">
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

    {{-- ═══════════ Table ═══════════ --}}
    <flux:card class="p-0 overflow-hidden border-gray-100 dark:border-zinc-800">
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
                            <flux:badge size="sm" class="font-black tracking-wider" style="background-color: #f0fdfa; color: #0d9488; border: none;">
                                {{ $entry->ticket_code }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell class="px-6 py-4">
                            <flux:text class="font-semibold">{{ $entry->wa_id ?: 'Walk-in' }}</flux:text>
                        </flux:table.cell>

                        <flux:table.cell class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'waiting'   => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
                                    'called'    => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
                                    'serving'   => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                                    'completed' => 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400',
                                    'skipped'   => 'bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                                    'cancelled' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400',
                                ];
                                $colorClass = $statusColors[$entry->status] ?? 'bg-gray-50 text-gray-700 dark:bg-zinc-800 dark:text-zinc-400';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $colorClass }}">
                                {{ $entry->status }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell class="px-6 py-4">
                            <flux:text class="text-sm font-medium">{{ $entry->source === 'whatsapp' ? '🟢 WhatsApp' : '⚪ Walk-in' }}</flux:text>
                        </flux:table.cell>

                        <flux:table.cell class="px-6 py-4 text-right">
                            <flux:text class="text-sm font-medium">{{ $entry->created_at->format('M d, Y h:i A') }}</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="px-6 py-16 text-center">
                            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                                <flux:icon.clock class="w-7 h-7 text-gray-300 dark:text-zinc-700" />
                            </div>
                            <flux:heading class="text-sm font-bold text-gray-600 dark:text-gray-400">No tickets found</flux:heading>
                            <flux:subheading class="text-sm text-gray-400 dark:text-gray-500 mt-1">Try adjusting your search or filter.</flux:subheading>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if($entries->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800 bg-gray-50/50 dark:bg-zinc-900/50">
                {{ $entries->links() }}
            </div>
        @endif
    </flux:card>
</div>