<div 
    x-data="{ 
        soundEnabled: false,
        currentTime: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
        init() {
            setInterval(() => {
                this.currentTime = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }, 1000);
        },
        playChime() {
            if (this.soundEnabled) {
                this.$refs.chime.currentTime = 0;
                this.$refs.chime.play().catch(e => console.log('Audio blocked'));
            }
        }
    }"
    x-on:queue-updated.window="playChime()"
    class="flex h-screen w-full flex-col overflow-hidden bg-slate-950 text-white relative z-0"
>
    <!-- Audio Asset -->
    <audio wire:ignore x-ref="chime" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <!-- Background Elements -->
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-teal-900/20 via-slate-950 to-slate-950"></div>

    <!-- Sound Toggle Overlay -->
    <div wire:ignore x-show="!soundEnabled" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/95 backdrop-blur-md">
        <div class="text-center">
            <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-teal-500 text-white shadow-[0_0_80px_rgba(20,184,166,0.6)] animate-pulse">
                <flux:icon.speaker-wave class="h-12 w-12" />
            </div>
            <h2 class="text-4xl font-bold text-white tracking-tight">Enable Audio Notifications</h2>
            <p class="mt-4 text-xl text-slate-400 max-w-md mx-auto">Click below to allow sound alerts when new tickets are called.</p>
            <button 
                @click="soundEnabled = true; playChime()" 
                class="mt-10 rounded-full bg-teal-500 px-10 py-4 text-xl font-bold text-slate-950 transition-all hover:bg-teal-400 hover:scale-105 active:scale-95 shadow-[0_0_40px_rgba(20,184,166,0.4)]"
            >
                Launch Live Display
            </button>
        </div>
    </div>

    <!-- 1. TOP HEADER -->
    <header class="flex-none px-12 py-8 flex items-center justify-between border-b border-white/5 bg-slate-950/40 backdrop-blur-md relative z-10">
        <!-- Left: Business Name -->
        <div class="flex items-center gap-5 w-1/3">
            <div class="h-14 w-14 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center shadow-[0_0_30px_rgba(20,184,166,0.1)]">
                <flux:icon.building-storefront class="h-7 w-7 text-teal-400" />
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 uppercase">{{ $business->name }}</h1>
        </div>

        <!-- Center: Status (IMPORTANT) -->
        <div class="w-1/3 flex justify-center">
            @if($business->queue_status === 'open')
                <div class="flex items-center gap-4 rounded-full border border-teal-500/30 bg-teal-500/10 px-8 py-3 shadow-[0_0_40px_rgba(20,184,166,0.15)]">
                    <span class="relative flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-teal-500 shadow-[0_0_15px_rgba(20,184,166,1)]"></span>
                    </span>
                    <span class="text-base font-bold tracking-[0.2em] text-teal-400 uppercase">Live Queue Open</span>
                </div>
            @elseif($business->queue_status === 'paused')
                <div class="flex items-center gap-4 rounded-full border border-amber-500/30 bg-amber-500/10 px-8 py-3 shadow-[0_0_40px_rgba(245,158,11,0.15)]">
                    <flux:icon.pause-circle class="h-6 w-6 text-amber-400" />
                    <span class="text-base font-bold tracking-[0.2em] text-amber-400 uppercase">Queue Paused &mdash; {{ $business->pause_reason ?? 'Short Break' }}</span>
                </div>
            @else
                <div class="flex items-center gap-4 rounded-full border border-rose-500/30 bg-rose-500/10 px-8 py-3">
                    <div class="h-4 w-4 rounded-full bg-rose-500"></div>
                    <span class="text-base font-bold tracking-[0.2em] text-rose-400 uppercase">Queue Closed</span>
                </div>
            @endif
        </div>

        <!-- Right: Time & Waiting -->
        <div class="flex items-center justify-end gap-8 w-1/3 text-right">
            <div class="flex flex-col items-end">
                <span class="text-4xl font-light tracking-tight text-white" x-text="currentTime"></span>
            </div>
            <div class="h-12 w-[2px] bg-white/10 rounded-full"></div>
            <div class="flex flex-col items-start min-w-[5rem]">
                <span class="text-4xl font-bold text-teal-400">{{ $this->waitingCount }}</span>
                <span class="text-sm font-semibold uppercase tracking-widest text-slate-400">Waiting</span>
            </div>
        </div>
    </header>

    <!-- 2. MAIN HERO (CENTER) & 3. UP NEXT SECTION -->
    <main class="flex-grow flex flex-col items-center justify-center px-12 py-6 relative z-10">
        
        @if($this->nowServing->isEmpty() && $this->waitingList->isEmpty())
            <div class="flex flex-col items-center justify-center opacity-40 translate-y-[-2rem]">
                <flux:icon.queue-list class="h-40 w-40 text-slate-500 mb-8" />
                <h2 class="text-5xl font-bold text-slate-300">Queue is Clear</h2>
                <p class="mt-4 text-2xl text-slate-400">Ready to serve new arrivals.</p>
            </div>
        @else
            <!-- Main Serving Display -->
            <div class="flex w-full max-w-7xl items-stretch justify-center gap-12">
                @if($this->nowServing->isNotEmpty())
                    <!-- Current Number -->
                    @php $primary = $this->nowServing->first(); @endphp
                    <div 
                        x-data="{ flash: false }"
                        x-on:queue-updated.window="flash = true; setTimeout(() => flash = false, 4000)"
                        class="flex-1 rounded-[3rem] border border-white/10 bg-white/[0.03] backdrop-blur-2xl p-16 text-center relative overflow-hidden flex flex-col items-center justify-center transition-all duration-700"
                        :class="flash ? 'shadow-[0_0_120px_rgba(20,184,166,0.3)] border-teal-500/50 scale-[1.02]' : 'shadow-2xl scale-100'"
                    >
                        <!-- Glow effect behind text -->
                        <div class="absolute inset-0 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-3/4 bg-teal-500/20 blur-[120px] rounded-full pointer-events-none transition-opacity duration-1000" :class="flash ? 'opacity-100' : 'opacity-40'"></div>

                        <div class="relative z-10 w-full flex flex-col items-center">
                            <h2 class="text-2xl font-bold uppercase tracking-[0.4em] text-teal-400 mb-6 flex items-center gap-4">
                                <flux:icon.sparkles class="h-8 w-8" /> Now Serving
                            </h2>
                            <div class="text-[14rem] sm:text-[18rem] leading-none font-black tracking-tighter text-white drop-shadow-[0_0_40px_rgba(255,255,255,0.2)]">
                                {{ $primary->ticket_code }}
                            </div>
                            
                            @if($primary->servicePoint)
                                <div class="mt-12 rounded-full border border-teal-500/30 bg-teal-500/10 px-10 py-5 text-3xl font-medium text-teal-100 flex items-center gap-5 backdrop-blur-md shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                                    <span>Please proceed to</span>
                                    <strong class="font-bold text-white tracking-tight">{{ $primary->servicePoint->name }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- No one serving, but people waiting -->
                    <div class="flex-1 rounded-[3rem] border border-white/5 bg-white/[0.02] backdrop-blur-xl p-16 text-center flex flex-col items-center justify-center">
                        <flux:icon.clock class="h-24 w-24 text-slate-500 mb-6 opacity-50" />
                        <h2 class="text-4xl font-bold text-slate-300">Preparing Next Ticket</h2>
                    </div>
                @endif
            </div>

            <!-- Up Next Section -->
            @if($this->waitingList->isNotEmpty() || $this->nowServing->count() > 1)
                <div class="mt-12 w-full max-w-7xl">
                    <h3 class="text-lg font-bold uppercase tracking-[0.4em] text-slate-500 mb-6 flex items-center gap-4">
                        Up Next <div class="h-px flex-grow bg-gradient-to-r from-slate-800 to-transparent"></div>
                    </h3>
                    
                    <div class="flex items-center gap-6 overflow-hidden">
                        @php
                            $nextItems = collect();
                            // Include other serving items if any
                            if ($this->nowServing->count() > 1) {
                                $nextItems = $nextItems->concat($this->nowServing->slice(1));
                            }
                            // Add waiting items
                            $nextItems = $nextItems->concat($this->waitingList)->take(4);
                        @endphp

                        @foreach($nextItems as $index => $item)
                            @php 
                                $isNext = $index === 0 && $item->status === \App\Enums\QueueStatus::WAITING->value;
                                $isServing = in_array($item->status, [\App\Enums\QueueStatus::CALLED->value, \App\Enums\QueueStatus::SERVING->value]);
                            @endphp
                            
                            <div class="flex-1 rounded-3xl border {{ $isNext ? 'border-teal-500/40 bg-teal-500/10 shadow-[0_0_40px_rgba(20,184,166,0.15)] scale-[1.02]' : 'border-white/5 bg-white/[0.02] opacity-80' }} p-8 text-center backdrop-blur-xl transition-all">
                                <div class="text-sm font-semibold uppercase tracking-[0.2em] {{ $isNext ? 'text-teal-400' : 'text-slate-400' }} mb-3">
                                    {{ $isServing ? 'Serving' : ($isNext ? 'Next up' : 'Waiting') }}
                                </div>
                                <div class="text-6xl font-bold tracking-tight {{ $isNext ? 'text-white' : 'text-slate-300' }}">
                                    {{ $item->ticket_code }}
                                </div>
                                @if($isServing && $item->servicePoint)
                                    <div class="mt-4 text-lg font-medium text-teal-200/80 truncate">
                                        {{ $item->servicePoint->name }}
                                    </div>
                                @endif
                            </div>

                            @if(!$loop->last)
                                <flux:icon.chevron-right class="h-10 w-10 flex-shrink-0 text-slate-600" />
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </main>

    <!-- 5. BOTTOM BAR -->
    <footer class="flex-none border-t border-teal-500/20 bg-gradient-to-r from-slate-900 via-teal-950/30 to-slate-900 px-12 py-8 shadow-[0_-30px_60px_rgba(20,184,166,0.05)] backdrop-blur-2xl relative z-10">
        <div class="mx-auto flex w-full items-center justify-between">
            <div class="flex items-center gap-8">
                <div class="rounded-2xl bg-white p-3 shadow-[0_0_20px_rgba(255,255,255,0.2)]">
                    <!-- Fake QR Code Placeholder using SVG -->
                    <svg class="h-20 w-20 text-slate-950" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-3 0h2v2h-2v-2zm3 3h3v2h-3v-2zm-3 0h2v2h-2v-2zm3 3h3v2h-3v-2zm-3 0h2v2h-2v-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-4xl font-bold text-white tracking-tight">Scan QR or join online</p>
                    <p class="mt-2 text-2xl text-teal-100/70">Message <span class="rounded-lg bg-teal-500/20 px-3 py-1 font-bold text-teal-300 border border-teal-500/30 shadow-[0_0_15px_rgba(20,184,166,0.2)]">JOIN {{ $business->join_code }}</span> on WhatsApp</p>
                </div>
            </div>

            <!-- Optional Avg Wait Time -->
            @if($this->waitingCount > 0)
            <div class="flex items-center gap-6 text-right">
                <div class="flex flex-col">
                    <span class="text-sm font-bold uppercase tracking-widest text-slate-400">Est. Wait Time</span>
                    <span class="text-4xl font-bold text-white">~{{ max(5, $this->waitingCount * 3) }} min</span>
                </div>
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-800/50 border border-slate-700/50 shadow-inner">
                    <flux:icon.clock class="h-8 w-8 text-slate-400" />
                </div>
            </div>
            @endif
        </div>
    </footer>

    @script
    <script>
        if (window.Echo) {
            window.Echo.channel('business.' + {{ $businessId }})
                .listen('QueueUpdated', (e) => {
                    console.log('Realtime update received:', e);
                    $wire.syncRealtime();
                });
        }
    </script>
    @endscript
</div>
