<div wire:poll.3s class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">{{ $business->name }}</h1>
        <div class="flex items-center justify-center gap-3 mt-2">
            @if($business->queue_status === 'open')
                <span class="w-2 h-2 rounded-full bg-teal-400 pulse-dot"></span>
                <span class="text-sm font-bold text-teal-400 uppercase tracking-widest">Queue Open</span>
            @else
                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                <span class="text-sm font-bold text-red-400 uppercase tracking-widest">Queue Closed</span>
            @endif
            <span class="text-slate-600">·</span>
            <span class="text-sm text-slate-500 font-semibold">{{ $this->waitingCount }} waiting</span>
        </div>
    </div>

    <div class="grid md:grid-cols-5 gap-6">

        {{-- NOW SERVING — Left panel (larger) --}}
        <div class="md:col-span-2">
            <div class="text-[10px] font-black uppercase tracking-[0.25em] text-teal-400 mb-3 text-center">Now Serving</div>

            @if($this->nowServing->isEmpty())
                <div class="glass-card rounded-2xl p-10 text-center">
                    <div class="text-5xl mb-3 opacity-30">📋</div>
                    <p class="text-sm text-slate-500">No one being served</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($this->nowServing as $active)
                        <div class="glass-card rounded-2xl p-6 text-center glow-teal border-teal-400/20 border">
                            <div class="text-4xl md:text-5xl font-black text-white tracking-wider" style="font-family: 'Syne', sans-serif;">
                                {{ $active->ticket_code }}
                            </div>
                            @if($active->counter_id)
                                <div class="text-xs font-bold text-teal-400 mt-2 uppercase tracking-wider">Counter {{ $active->counter_id }}</div>
                            @endif
                            <div class="text-[10px] font-bold uppercase tracking-widest mt-1 {{ $active->status === \App\Enums\QueueStatus::CALLED->value ? 'text-amber-400' : 'text-blue-400' }}">
                                {{ $active->status === \App\Enums\QueueStatus::CALLED->value ? 'Called' : 'Serving' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- WAITING LIST — Right panel --}}
        <div class="md:col-span-3">
            <div class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 mb-3 text-center">Up Next</div>

            @if($this->waitingList->isEmpty())
                <div class="glass-card rounded-2xl p-10 text-center">
                    <div class="text-5xl mb-3 opacity-30">🎉</div>
                    <p class="text-sm text-slate-500">No one waiting — queue is empty!</p>
                </div>
            @else
                <div class="grid grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach($this->waitingList as $index => $waiting)
                        <div class="glass-card rounded-xl p-4 text-center transition-all {{ $index === 0 ? 'border-amber-400/30 border bg-amber-400/5' : '' }}">
                            <div class="text-lg md:text-xl font-black text-white tracking-wider">
                                {{ $waiting->ticket_code }}
                            </div>
                            <div class="text-[10px] font-bold text-slate-500 mt-1">
                                #{{ $waiting->position }}
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($this->waitingCount > 12)
                    <p class="text-center text-xs text-slate-600 mt-3">
                        +{{ $this->waitingCount - 12 }} more waiting
                    </p>
                @endif
            @endif
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="mt-10 text-center">
        <p class="text-xs text-slate-600">
            Scan the QR code or text <strong class="text-slate-400">JOIN {{ $business->join_code }}</strong> on WhatsApp to join the queue
        </p>
    </div>
</div>
