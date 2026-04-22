<div class="space-y-6">
    <div class="page-header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center w-full gap-4">
            <div>
                <span class="page-kicker">Customer Experience</span>
                <h1 class="page-title mt-4">Feedback Log</h1>
                <p class="page-description mt-3">Platform-wide customer satisfaction responses across all tenants.</p>
            </div>
            @if($avgRating)
                <div class="metric-card !py-3 !px-5 shrink-0">
                    <p class="metric-label text-sm">Avg. Platform Rating</p>
                    <p class="text-2xl font-bold text-amber-500 mt-1">{{ $avgRating }} / 5</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card flex flex-col sm:flex-row gap-4">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by business, customer, or comment..." icon="magnifying-glass" class="flex-1" />
        <select wire:model.live="filterRating" class="input-base w-full sm:w-44">
            <option value="">All Ratings</option>
            @foreach(range(5, 1) as $star)
                <option value="{{ $star }}">{{ $star }} ⭐</option>
            @endforeach
        </select>
    </div>

    <div class="glass-card !p-0 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Business</flux:table.column>
                <flux:table.column>Customer (WA)</flux:table.column>
                <flux:table.column>Rating</flux:table.column>
                <flux:table.column>Comment</flux:table.column>
                <flux:table.column>Date</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($feedback as $fb)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold text-slate-800 dark:text-slate-100">
                            {{ $fb->business->name ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-slate-500 text-sm">{{ $fb->wa_id ?? 'Anonymous' }}</flux:table.cell>
                        <flux:table.cell>
                            @if($fb->rating)
                                <span class="font-bold text-amber-500">{{ $fb->rating }}</span>
                                <span class="text-slate-400 text-xs">/ 5</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <span class="truncate max-w-[260px] inline-block text-sm text-slate-600 dark:text-slate-300">
                                {{ $fb->comment ?: '—' }}
                            </span>
                        </flux:table.cell>
                        <flux:table.cell class="text-sm text-slate-500">{{ $fb->created_at->format('M d, Y h:i A') }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-10 text-slate-400">No feedback submitted yet.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="border-t border-slate-200/70 dark:border-white/10 px-6 py-4">{{ $feedback->links() }}</div>
    </div>
</div>
