<div
    wire:poll.4s
    x-data="{
        status: @entangle('entry.status'),
        init() {
            window.addEventListener('ticket-joined', (e) => {
                let tickets = JSON.parse(localStorage.getItem('qline_active_tickets') || '{}');
                tickets[e.detail.slug] = e.detail.id;
                localStorage.setItem('qline_active_tickets', JSON.stringify(tickets));
            });
            window.addEventListener('ticket-cleared', (e) => {
                let tickets = JSON.parse(localStorage.getItem('qline_active_tickets') || '{}');
                delete tickets[e.detail.slug];
                localStorage.setItem('qline_active_tickets', JSON.stringify(tickets));
            });
        }
    }"
    x-init="
        $watch('status', value => {
            if (value === 'called') {
                $refs.chime.play().catch(e => console.error('Audio play failed:', e));
            }
            if (['completed', 'cancelled', 'skipped'].includes(value)) {
                let tickets = JSON.parse(localStorage.getItem('qline_active_tickets') || '{}');
                delete tickets['{{ $business->slug }}'];
                localStorage.setItem('qline_active_tickets', JSON.stringify(tickets));
            }
        })
    "
    class="max-w-md mx-auto space-y-6"
>
    {{-- Notification Sound --}}
    <audio x-ref="chime" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    {{-- Header --}}
    <div class="text-center">
        <h1 class="text-xl font-black text-slate-900 tracking-tight">{{ $business->name }}</h1>
        @if($entry->status === \App\Enums\QueueStatus::WAITING->value)
            <div class="mt-1 flex items-center justify-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                <span class="text-[10px] font-black uppercase tracking-widest text-teal-600">Track your turn</span>
            </div>
        @endif
    </div>

    {{-- Main Ticket Card --}}
    <div class="brand-card rounded-[2.5rem] p-10 text-center relative overflow-hidden">
        {{-- Progress Bar (at top) --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-slate-100">
            <div class="h-full bg-teal-500 transition-all duration-1000" 
                 style="width: {{ 
                    match($entry->status) {
                        'waiting' => '33%',
                        'called', 'serving' => '66%',
                        'completed' => '100%',
                        default => '0%'
                    }
                 }}"></div>
        </div>

        <div class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2">Your ID</div>
        <div class="text-6xl font-black tracking-tighter text-slate-900 mb-8">
            {{ $entry->ticket_code }}
        </div>

        {{-- Status Pill --}}
        <div class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full border-2 border-slate-100 font-black text-xs uppercase tracking-widest {{ $this->statusColor }}">
            {{ $this->statusLabel }}
        </div>

        <div class="mt-10 pt-8 border-t border-slate-50">
            @if($entry->status === \App\Enums\QueueStatus::WAITING->value)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-2xl font-black text-slate-900">{{ $this->positionInfo['position'] }}</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Position</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-slate-900">~{{ $this->positionInfo['estimated_wait_mins'] }}m</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Est. Time</div>
                    </div>
                </div>
            @elseif($entry->status === \App\Enums\QueueStatus::CALLED->value)
                <div class="space-y-4">
                    <div class="w-16 h-16 bg-teal-500 text-white rounded-2xl flex items-center justify-center text-3xl mx-auto shadow-lg shadow-teal-200">
                        🔔
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900">It's Your Turn!</h2>
                        <p class="text-sm text-slate-500 mt-1">Please come to the counter now.</p>
                        @if($entry->counter_id)
                            <div class="mt-4 px-4 py-2 bg-slate-900 text-white inline-block rounded-xl font-black text-lg">
                                Counter {{ $entry->counter_id }}
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($entry->status === \App\Enums\QueueStatus::COMPLETED->value)
                 <div class="space-y-4">
                    <div class="w-16 h-16 bg-emerald-500 text-white rounded-2xl flex items-center justify-center text-3xl mx-auto">
                        ✅
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900">All Done</h2>
                        <p class="text-sm text-slate-500 mt-1">Thank you for visiting us!</p>
                        @if(!$entry->customerFeedback)
                            <a href="{{ url("/q/{$business->slug}/feedback/{$entry->cancel_token}") }}"
                               class="inline-block mt-4 btn-teal px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest">
                                Leave Feedback
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="py-4">
                    <p class="text-sm text-slate-400">Ticket status: {{ ucfirst($entry->status) }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Loyalty Card (Solid Style) --}}
    @if($this->loyaltyPoints)
        <div class="brand-card rounded-2xl p-5 flex items-center justify-between border-l-4 border-l-teal-500">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-2xl shadow-inner border border-slate-100">
                    {{ $this->loyaltyPoints['has_reward'] ? '🎁' : '⭐' }}
                </div>
                <div class="text-left">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">Royalty Perk</div>
                    <div class="text-sm font-bold text-slate-900">
                        @if($this->loyaltyPoints['has_reward'])
                            <span class="text-teal-600">Gift Ready: {{ $this->loyaltyPoints['reward_name'] }}</span>
                        @elseif($this->loyaltyPoints['next_reward_in'])
                            <span class="text-slate-600">{{ $this->loyaltyPoints['next_reward_in'] }} to go ({{ $this->loyaltyPoints['next_reward_name'] }})</span>
                        @else
                            <span class="text-slate-500">Visit #{{ $this->loyaltyPoints['visits'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($this->loyaltyPoints['has_reward'])
                <div class="px-2 py-1 bg-teal-500 text-white rounded font-black text-[9px] uppercase tracking-tighter">Claim Now</div>
            @endif
        </div>
    @endif

    {{-- Cancellation (Clean UI) --}}
    @if($entry->status === \App\Enums\QueueStatus::WAITING->value)
        <div class="text-center" x-data="{ confirming: false }">
            <button x-show="!confirming" @click="confirming = true" 
                    class="text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-rose-500 transition border-b border-transparent hover:border-rose-200 py-1">
                Cancel My Ticket
            </button>
            <div x-show="confirming" x-cloak class="mt-2 text-center p-4 bg-rose-50 border border-rose-100 rounded-2xl animate-in zoom-in-95">
                <p class="text-xs font-black text-rose-600 uppercase mb-3">Cancel this ticket?</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="cancelTicket" @click="confirming = false" class="px-4 py-2 bg-rose-500 text-white text-[10px] font-black uppercase rounded-lg shadow-sm">Confirm</button>
                    <button @click="confirming = false" class="px-4 py-2 text-slate-400 text-[10px] font-black uppercase">No, Nevermind</button>
                </div>
            </div>
        </div>
    @endif

    <p class="text-center text-[10px] font-bold text-slate-300 uppercase tracking-widest">
        Updating every 4s
    </p>
</div>
