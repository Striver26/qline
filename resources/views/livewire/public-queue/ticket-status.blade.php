<div wire:poll.4s class="max-w-md mx-auto space-y-6">

    {{-- Business name --}}
    <div class="text-center">
        <h1 class="text-2xl font-bold text-white">{{ $business->name }}</h1>
        <p class="text-xs text-slate-500 uppercase tracking-widest mt-1">Queue Tracker</p>
    </div>

    {{-- Ticket Card --}}
    <div class="glass-card rounded-3xl p-8 text-center glow-teal">

        {{-- Ticket Code --}}
        <div class="text-[10px] font-bold uppercase tracking-[0.25em] text-slate-500 mb-2">Your Ticket</div>
        <div class="text-5xl font-black tracking-wider text-white mb-6" style="font-family: 'Syne', sans-serif;">
            {{ $entry->ticket_code }}
        </div>

        {{-- Status Badge --}}
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 mb-6">
            @if($entry->status === \App\Enums\QueueStatus::WAITING->value)
                <span class="w-2 h-2 rounded-full bg-amber-400 pulse-dot"></span>
            @elseif($entry->status === \App\Enums\QueueStatus::CALLED->value || $entry->status === \App\Enums\QueueStatus::SERVING->value)
                <span class="w-2 h-2 rounded-full bg-teal-400 pulse-dot"></span>
            @else
                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
            @endif
            <span class="text-sm font-bold {{ $this->statusColor }}">{{ $this->statusLabel }}</span>
        </div>

        @if($entry->status === \App\Enums\QueueStatus::WAITING->value)
            {{-- Position Info --}}
            <div class="grid grid-cols-2 gap-4 mt-2">
                <div class="bg-white/5 rounded-2xl p-4">
                    <div class="text-3xl font-black text-white">{{ $this->positionInfo['position'] }}</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">Position</div>
                </div>
                <div class="bg-white/5 rounded-2xl p-4">
                    <div class="text-3xl font-black text-white">~{{ $this->positionInfo['estimated_wait_mins'] }}<span class="text-lg text-slate-500">m</span></div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">Est. Wait</div>
                </div>
            </div>

            <p class="text-xs text-slate-500 mt-4">
                {{ $this->positionInfo['ahead'] }} {{ $this->positionInfo['ahead'] === 1 ? 'person' : 'people' }} ahead of you
            </p>

        @elseif($entry->status === \App\Enums\QueueStatus::CALLED->value)
            {{-- CALLED — Big alert --}}
            <div class="mt-4 p-6 rounded-2xl border-2 border-teal-400/50 bg-teal-400/10">
                <div class="text-4xl mb-2">🔔</div>
                <h2 class="text-xl font-black text-teal-400">It's Your Turn!</h2>
                <p class="text-sm text-slate-400 mt-1">Please proceed to the counter now.</p>
                @if($entry->counter_id)
                    <div class="mt-3 text-lg font-bold text-white">Counter {{ $entry->counter_id }}</div>
                @endif
            </div>

        @elseif($entry->status === \App\Enums\QueueStatus::SERVING->value)
            <div class="mt-4 p-6 rounded-2xl border-2 border-blue-400/50 bg-blue-400/10">
                <div class="text-4xl mb-2">✨</div>
                <h2 class="text-xl font-black text-blue-400">Being Served</h2>
                <p class="text-sm text-slate-400 mt-1">You're currently being attended to.</p>
            </div>

        @elseif($entry->status === \App\Enums\QueueStatus::COMPLETED->value)
            <div class="mt-4 p-6 rounded-2xl border-2 border-emerald-400/50 bg-emerald-400/10">
                <div class="text-4xl mb-2">✅</div>
                <h2 class="text-xl font-black text-emerald-400">All Done!</h2>
                <p class="text-sm text-slate-400 mt-1">Thank you for visiting {{ $business->name }}.</p>

                {{-- Feedback link --}}
                @if(!$entry->customerFeedback)
                    <a href="{{ url("/q/{$business->slug}/feedback/{$entry->cancel_token}") }}"
                       class="inline-block mt-4 px-6 py-2 bg-teal-400 text-black text-sm font-bold rounded-full hover:opacity-90 transition">
                        Leave Feedback
                    </a>
                @else
                    <p class="text-xs text-emerald-400/60 mt-3">Thanks for your feedback! ⭐</p>
                @endif
            </div>

        @elseif($entry->status === \App\Enums\QueueStatus::SKIPPED->value)
            <div class="mt-4 p-6 rounded-2xl border-2 border-orange-400/50 bg-orange-400/10">
                <div class="text-4xl mb-2">⏭️</div>
                <h2 class="text-xl font-black text-orange-400">Skipped</h2>
                <p class="text-sm text-slate-400 mt-1">Your number was skipped. Please check with the counter.</p>
            </div>

        @elseif($entry->status === \App\Enums\QueueStatus::CANCELLED->value)
            <div class="mt-4 p-6 rounded-2xl border-2 border-red-400/50 bg-red-400/10">
                <div class="text-4xl mb-2">❌</div>
                <h2 class="text-xl font-black text-red-400">Cancelled</h2>
                <p class="text-sm text-slate-400 mt-1">This ticket has been cancelled.</p>
            </div>
        @endif
    </div>

    {{-- Cancel button (only if waiting) --}}
    @if($entry->status === \App\Enums\QueueStatus::WAITING->value)
        <div class="text-center">
            <button wire:click="cancelTicket" wire:confirm="Are you sure you want to leave the queue?"
                    class="text-xs text-red-400/60 hover:text-red-400 transition underline underline-offset-4">
                Cancel my ticket
            </button>
        </div>
    @endif

    {{-- Auto-refresh notice --}}
    <p class="text-center text-[10px] text-slate-600">
        This page updates automatically every 4 seconds
    </p>
</div>
