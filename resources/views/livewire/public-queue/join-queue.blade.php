<div class="max-w-md mx-auto space-y-6">

    {{-- Business info --}}
    <div class="text-center">
        <h1 class="text-2xl font-bold text-white">{{ $business->name }}</h1>
        <p class="text-xs text-slate-500 uppercase tracking-widest mt-1">Join the Queue</p>
    </div>

    @if(!$joined)
        {{-- Queue Status --}}
        @if($business->queue_status !== 'open')
            <div class="glass-card rounded-2xl p-8 text-center">
                <div class="text-5xl mb-3">🚫</div>
                <h2 class="text-xl font-bold text-red-400">Queue is Closed</h2>
                <p class="text-sm text-slate-500 mt-2">Please check again later or ask the staff.</p>
            </div>
        @else
            {{-- Active queue info --}}
            <div class="glass-card rounded-2xl p-6 text-center">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-3xl font-black text-white">{{ $this->waitingCount }}</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">In Queue</div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-white">~{{ $this->waitingCount * 5 }}<span class="text-lg text-slate-500">m</span></div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">Est. Wait</div>
                    </div>
                </div>
            </div>

            {{-- Join form --}}
            <div class="glass-card rounded-2xl p-6 space-y-5">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 block mb-2">
                        WhatsApp Number <span class="text-slate-600">(optional)</span>
                    </label>
                    <input type="tel" wire:model="phone" placeholder="e.g. 60123456789"
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:border-teal-400/50 focus:outline-none focus:ring-1 focus:ring-teal-400/30 transition text-sm">
                    <p class="text-[10px] text-slate-600 mt-1.5">Enter your number to get notified on WhatsApp when it's your turn. Leave blank for walk-in.</p>
                </div>

                @if($errorMessage)
                    <div class="p-3 rounded-xl bg-red-400/10 border border-red-400/20 text-sm text-red-400">
                        {{ $errorMessage }}
                    </div>
                @endif

                <button wire:click="joinQueue"
                        wire:loading.attr="disabled"
                        class="w-full py-3.5 bg-teal-400 text-black font-bold rounded-xl hover:opacity-90 transition disabled:opacity-50">
                    <span wire:loading.remove>Join Queue</span>
                    <span wire:loading>Joining...</span>
                </button>
            </div>
        @endif

    @else
        {{-- SUCCESS — Ticket issued --}}
        <div class="glass-card rounded-3xl p-8 text-center glow-teal">
            <div class="text-5xl mb-4">🎉</div>
            <div class="text-[10px] font-bold uppercase tracking-[0.25em] text-slate-500 mb-2">Your Ticket</div>
            <div class="text-5xl font-black tracking-wider text-white mb-4" style="font-family: 'Syne', sans-serif;">
                {{ $ticket->ticket_code }}
            </div>

            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="bg-white/5 rounded-xl p-3">
                    <div class="text-2xl font-black text-white">{{ $ticket->position }}</div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-0.5">Position</div>
                </div>
                <div class="bg-white/5 rounded-xl p-3">
                    <div class="text-2xl font-black text-white">~{{ ($ticket->position - 1) * 5 }}<span class="text-base text-slate-500">m</span></div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-0.5">Est. Wait</div>
                </div>
            </div>

            <a href="{{ url("/q/{$business->slug}/status/{$ticket->id}") }}"
               class="inline-block px-6 py-2.5 bg-teal-400 text-black text-sm font-bold rounded-full hover:opacity-90 transition">
                Track Live Status →
            </a>
        </div>

        <p class="text-center text-xs text-slate-600">
            Bookmark this page or use the link above to check your status anytime.
        </p>
    @endif
</div>
