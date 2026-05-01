<section class="rounded-2xl border border-white/[0.06] bg-white/[0.02] overflow-hidden backdrop-blur-sm">
    <div class="p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Next in line</p>
            @if($nextEntry)
                <span class="flex h-6 w-6 items-center justify-center rounded-md bg-white/5 text-[10px] font-bold text-slate-400 ring-1 ring-white/[0.06]">
                    #{{ $nextEntry['queue_position'] }}
                </span>
            @endif
        </div>
        
        @if($nextEntry)
            <div class="flex flex-col items-center text-center">
                <h2 class="text-[5rem] lg:text-[6rem] font-black text-teal-400 drop-shadow-[0_0_60px_rgba(45,212,191,0.25)] leading-none tracking-tighter mb-6">
                    {{ $nextEntry['ticket_code'] }}
                </h2>

                <div class="flex items-center justify-center gap-6 divide-x divide-white/10 mb-8 w-full max-w-md mx-auto bg-white/[0.02] rounded-xl py-3 ring-1 ring-white/[0.04]">
                    <div class="px-3 text-center flex-1">
                        <div class="flex items-center justify-center gap-1.5 mb-1 text-slate-500">
                            <flux:icon.user class="h-3 w-3" />
                            <p class="text-[10px] uppercase tracking-widest">Customer</p>
                        </div>
                        <p class="text-sm font-bold text-white truncate">{{ $nextEntry['customer_label'] }}</p>
                    </div>
                    
                    <div class="px-3 text-center flex-1">
                        <div class="flex items-center justify-center gap-1.5 mb-1 text-slate-500">
                            <flux:icon.clock class="h-3 w-3" />
                            <p class="text-[10px] uppercase tracking-widest">Wait</p>
                        </div>
                        <p class="text-sm font-bold text-white">~{{ $nextEntry['estimated_wait_mins'] }}m</p>
                    </div>

                    <div class="px-3 text-center flex-1">
                        <div class="flex items-center justify-center gap-1.5 mb-1 text-slate-500">
                            <flux:icon.hashtag class="h-3 w-3" />
                            <p class="text-[10px] uppercase tracking-widest">Joined</p>
                        </div>
                        <p class="text-sm font-bold text-white">{{ $nextEntry['created_human'] }}</p>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="$dispatch('command-center.call-next')"
                    @disabled($businessState['queue_status'] !== 'open' || !$nextEntry)
                    class="group relative w-full max-w-md overflow-hidden rounded-2xl bg-teal-500 py-5 text-center text-teal-950 shadow-[0_0_40px_rgba(45,212,191,0.2)] transition-all hover:bg-teal-400 hover:scale-[1.02] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
                >
                    <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent opacity-50"></div>
                    <div class="relative flex items-center justify-center gap-3">
                        <flux:icon.speaker-wave class="h-6 w-6" />
                        <span class="text-lg font-black tracking-tight uppercase">Call Next</span>
                    </div>
                </button>
                <div class="mt-4 flex items-center gap-2 text-[11px] font-semibold text-slate-500">
                    Press <kbd class="rounded-md border border-slate-700 bg-slate-800 px-2 py-0.5 font-mono text-[10px] text-slate-300 shadow-sm">SPACE</kbd> to call next
                </div>
            </div>
        @else
            <div class="flex min-h-[18rem] flex-col items-center justify-center text-center text-slate-500">
                <div class="h-14 w-14 rounded-full bg-white/5 flex items-center justify-center mb-5 ring-1 ring-white/[0.06]">
                    <flux:icon.check class="h-7 w-7 text-slate-400" />
                </div>
                <p class="text-lg font-bold text-white mb-1">Queue is clear</p>
                <p class="text-sm">New arrivals can be served immediately.</p>
            </div>
        @endif
    </div>

    @if($this->availableServicePoints)
        <div class="border-t border-white/[0.06] bg-white/[0.02] px-6 py-4 flex flex-wrap items-center gap-3">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500 shrink-0">Call to</p>
            
            @foreach($this->availableServicePoints as $servicePoint)
                <button
                    type="button"
                    wire:click="$dispatch('command-center.call-entry', { entryId: {{ $nextEntry['id'] ?? 0 }}, servicePointId: {{ $servicePoint['id'] }} })"
                    @disabled($businessState['queue_status'] !== 'open' || !$nextEntry)
                    class="shrink-0 rounded-lg border px-3 py-1.5 text-[12px] font-bold transition-all disabled:cursor-not-allowed disabled:opacity-50 
                        {{ $selectedServicePointId === $servicePoint['id'] 
                            ? 'border-teal-500/50 bg-teal-500/10 text-teal-400' 
                            : 'border-white/[0.06] bg-white/[0.04] text-slate-300 hover:bg-white/[0.08] hover:text-white' }}"
                >
                    {{ $servicePoint['name'] }}
                </button>
            @endforeach
        </div>
    @endif
</section>
