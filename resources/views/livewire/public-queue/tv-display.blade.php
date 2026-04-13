<div wire:poll.3s class="max-w-7xl mx-auto py-8">

    {{-- Header --}}
    <div class="text-center mb-12">
        <h1 class="text-5xl font-black text-white tracking-tighter">{{ $business->name }}</h1>
        <div class="flex items-center justify-center gap-4 mt-6">
            @if($business->queue_status === 'open')
                <div class="px-5 py-2 bg-teal-500 text-black rounded-full font-black text-sm uppercase tracking-widest shadow-lg shadow-teal-500/20">
                    Queue Open
                </div>
            @else
                <div class="px-5 py-2 bg-rose-500 text-white rounded-full font-black text-sm uppercase tracking-widest">
                    Queue Closed
                </div>
            @endif
            <div class="px-5 py-2 bg-slate-800 text-slate-400 rounded-full font-black text-sm uppercase tracking-widest border border-slate-700">
                {{ $this->waitingCount }} Waiting
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-12 gap-10 min-h-[500px]">

        {{-- NOW SERVING — Left panel (larger) --}}
        <div class="md:col-span-4 space-y-6">
            <div class="inline-block px-4 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs font-black uppercase tracking-[0.3em] text-teal-400">
                Now Serving
            </div>

            @if($this->nowServing->isEmpty())
                <div class="bg-slate-900 border-2 border-slate-800 rounded-[2.5rem] p-16 text-center h-full flex flex-col justify-center items-center">
                    <div class="text-7xl mb-6 grayscale opacity-20">📋</div>
                    <p class="text-xl font-bold text-slate-500">No active tickets</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($this->nowServing as $active)
                        <div class="bg-teal-500 rounded-[3rem] p-10 text-center shadow-2xl relative overflow-hidden group">
                            {{-- Counter badge --}}
                            @if($active->counter_id)
                                <div class="absolute top-0 right-0 bg-black text-white px-6 py-2 rounded-bl-3xl font-black text-sm uppercase tracking-widest">
                                    Counter {{ $active->counter_id }}
                                </div>
                            @endif

                            <div class="text-8xl font-black text-black tracking-tighter">
                                {{ $active->ticket_code }}
                            </div>
                            
                            <div class="mt-4 inline-block px-6 py-2 bg-black/10 rounded-full font-black text-xs uppercase tracking-[0.2em] text-black/60">
                                {{ $active->status === \App\Enums\QueueStatus::CALLED->value ? '🚨 Calling' : '✅ Serving' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- WAITING LIST — Right panel --}}
        <div class="md:col-span-8 space-y-6">
            <div class="inline-block px-4 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs font-black uppercase tracking-[0.3em] text-slate-500">
                Up Next
            </div>

            @if($this->waitingList->isEmpty())
                <div class="bg-slate-900 border-2 border-slate-800 rounded-[2.5rem] p-16 text-center h-full flex flex-col justify-center items-center">
                    <div class="text-7xl mb-6 grayscale opacity-20">👋</div>
                    <p class="text-xl font-bold text-slate-500">The line is empty. You're next!</p>
                </div>
            @else
                <div class="grid grid-cols-3 gap-6">
                    @foreach($this->waitingList as $index => $waiting)
                        <div class="bg-slate-900 border-2 {{ $index === 0 ? 'border-teal-500/50' : 'border-slate-800' }} rounded-[2rem] p-6 text-center relative overflow-hidden">
                            @if($index === 0)
                                <div class="absolute top-0 left-0 right-0 h-1 bg-teal-500"></div>
                            @endif
                            <div class="text-4xl font-black text-white tracking-tight">
                                {{ $waiting->ticket_code }}
                            </div>
                            <div class="mt-2 text-[10px] font-black uppercase tracking-widest text-slate-600">
                                Position #{{ $waiting->position }}
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($this->waitingCount > 12)
                    <div class="py-6 text-center">
                        <p class="text-2xl font-black text-slate-700">+{{ $this->waitingCount - 12 }} more in line</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="mt-20 border-t border-slate-800 pt-10 text-center">
        <p class="text-slate-500 text-lg">
            Scan QR or message <span class="bg-slate-800 text-slate-300 px-4 py-1 rounded-lg font-black mx-1">JOIN {{ $business->join_code }}</span> to WhatsApp to join.
        </p>
    </div>
</div>
