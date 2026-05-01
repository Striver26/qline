<div x-data="{ 
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
    }" x-on:queue-updated-notify.window="playChime(); $dispatch('flash-primary')"
    class="flex h-screen w-full flex-col overflow-hidden bg-[#050811] text-white relative z-0 font-sans">
    <!-- Audio Asset -->
    <audio wire:ignore x-ref="chime" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"
        preload="auto"></audio>

    <!-- Background Glows -->
    <div class="absolute inset-0 -z-10 bg-[#050811]">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-teal-500/5 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-coral-500/5 blur-[120px]"></div>
    </div>

    <!-- Launch Overlay -->
    <div wire:ignore x-show="!soundEnabled" class="fixed inset-0 z-[100] flex items-center justify-center bg-black">
        <button @click="
                soundEnabled = true; 
                playChime(); 
                document.documentElement.requestFullscreen().catch(e => console.log('Fullscreen denied'));
            " class="rounded-xl bg-teal-500 px-12 py-6 text-xl font-black text-slate-950 transition hover:bg-teal-400">
            LAUNCH DISPLAY
        </button>
    </div>

    <!-- HEADER: Logo + Name + Waiting + Status -->
    <header
        class="flex-none px-12 py-6 flex items-center justify-between border-b border-white/5 bg-white/[0.01] backdrop-blur-md relative z-10">
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-teal-500 flex items-center justify-center shadow-lg">
                    <flux:icon.bolt class="h-7 w-7 text-teal-950" />
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white uppercase">{{ $business->name }}</h1>
            </div>
        </div>

        <!-- CENTER: STATUS -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex justify-center">
            @if($business->queue_status === 'open')
                <div
                    class="flex items-center gap-3 rounded-full border border-teal-500/20 bg-teal-500/10 px-6 py-2 shadow-[0_0_30px_rgba(20,184,166,0.1)]">
                    <span class="h-2 w-2 rounded-full bg-teal-500 animate-pulse"></span>
                    <span class="text-[10px] font-black tracking-[0.2em] text-teal-400 uppercase">Live Queue Open</span>
                </div>
            @elseif($business->queue_status === 'paused')
                <div class="flex items-center gap-3 rounded-full border border-amber-500/20 bg-amber-500/10 px-6 py-2">
                    <flux:icon.pause-circle class="h-4 w-4 text-amber-400" />
                    <span class="text-[10px] font-black tracking-[0.2em] text-amber-400 uppercase">Queue Paused &mdash;
                        {{ $business->pause_reason ?? 'Break' }}</span>
                </div>
            @else
                <div class="flex items-center gap-3 rounded-full border border-rose-500/20 bg-rose-500/10 px-6 py-2">
                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                    <span class="text-[10px] font-black tracking-[0.2em] text-rose-400 uppercase">Closed</span>
                </div>
            @endif
        </div>

        <!-- RIGHT: TIME -->
        <div class="flex items-center gap-8">
            <span class="text-3xl font-black tracking-tighter text-white tabular-nums opacity-40"
                x-text="currentTime"></span>
        </div>
    </header>

    <!-- MAIN BODY -->
    <main class="flex-grow grid grid-cols-12 gap-10 p-12 relative z-10 overflow-hidden">

        <!-- LEFT: STANDALONE NOW SERVING -->
        <div class="col-span-7 flex flex-col items-center justify-center">
            @if($this->nowServing->isNotEmpty())
                @php $primary = $this->nowServing->first(); @endphp
                <div x-data="{ flash: false }"
                    x-on:flash-primary.window="flash = true; setTimeout(() => flash = false, 4000)"
                    class="w-full h-full rounded-[3.5rem] border border-white/10 bg-white/[0.02] backdrop-blur-3xl p-12 text-center relative overflow-hidden flex flex-col items-center justify-center transition-all duration-1000"
                    :class="flash ? 'shadow-[0_0_150px_rgba(20,184,166,0.2)] border-teal-500/40 scale-[1.01]' : 'shadow-xl scale-100'">
                    <div class="relative z-10">
                        <div
                            class="inline-flex items-center gap-3 rounded-full bg-teal-500/5 px-5 py-2 border border-teal-500/10 mb-8">
                            <flux:icon.sparkles class="h-5 w-5 text-teal-500/50" />
                            <h2 class="text-sm font-black uppercase tracking-[0.5em] text-teal-500/80">Now Serving</h2>
                        </div>

                        <div
                            class="text-[16rem] leading-none font-black tracking-tighter text-white drop-shadow-[0_10px_40px_rgba(0,0,0,0.5)]">
                            {{ $primary->ticket_code }}
                        </div>

                        @if($primary->servicePoint)
                            <div class="mt-12 flex flex-col items-center gap-4">
                                <p class="text-2xl font-bold text-slate-500 uppercase tracking-[0.3em]">Please proceed to</p>
                                <div class="rounded-2xl bg-teal-500 px-14 py-8 shadow-2xl">
                                    <strong
                                        class="text-6xl font-black text-teal-950 uppercase tracking-tight">{{ $primary->servicePoint->name }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="opacity-10 text-center">
                    <flux:icon.queue-list class="h-40 w-40 mb-6 mx-auto" />
                    <h2 class="text-3xl font-black uppercase tracking-widest text-slate-500">Standby</h2>
                </div>
            @endif
        </div>

        <!-- RIGHT: SIDEBAR -->
        <div class="col-span-5 flex flex-col gap-8 overflow-hidden h-full">

            <!-- SERVING -->
            <div class="flex-grow flex flex-col gap-4 overflow-hidden">
                <div class="flex items-center gap-4">
                    <h2 class="text-base font-black uppercase tracking-[0.4em] text-teal-500/60">Currently Serving</h2>
                    <div class="h-px flex-grow bg-white/5"></div>
                </div>

                <div class="flex-grow flex flex-col gap-4 overflow-y-auto pr-2">
                    @forelse($this->nowServing->slice(1) as $serving)
                        <div
                            class="rounded-3xl border border-white/5 bg-white/[0.02] p-6 flex items-center justify-between">
                            <div class="text-5xl font-black tracking-tighter text-white">
                                {{ $serving->ticket_code }}
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-black text-teal-400 uppercase tracking-tight">
                                    {{ $serving->servicePoint?->name ?? '--' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div
                            class="flex-grow flex items-center justify-center border border-dashed border-white/10 rounded-3xl opacity-20">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">No other calls</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- UP NEXT -->
            <div class="h-[35%] flex flex-col gap-4 overflow-hidden border-t border-white/5 pt-8">
                <div class="flex items-center gap-4">
                    <h2 class="text-base font-black uppercase tracking-[0.4em] text-slate-500">Up Next</h2>
                    <span
                        class="px-2 py-0.5 rounded-md bg-white/5 text-[10px] font-black text-slate-400 border border-white/5">{{ $this->waitingCount }}</span>
                    <div class="h-px flex-grow bg-white/5"></div>
                </div>

                <div class="grid grid-cols-2 gap-3 overflow-y-auto pr-2">
                    @forelse($this->waitingList->take(6) as $waiting)
                        <div
                            class="rounded-2xl border border-white/5 bg-white/[0.01] p-4 flex items-center justify-between opacity-50">
                            <div class="text-3xl font-black tracking-tighter text-slate-300">
                                {{ $waiting->ticket_code }}
                            </div>
                            <div class="text-[9px] font-black uppercase tracking-widest text-slate-600">Waiting</div>
                        </div>
                    @empty
                        <div
                            class="col-span-2 py-4 text-center text-slate-600 text-xs font-bold uppercase tracking-widest italic opacity-30">
                            Queue clear</div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <footer
        class="flex-none border-t border-white/5 bg-black/40 backdrop-blur-md relative z-10 flex items-center justify-center gap-3">
        <div class="flex items-center gap-4 opacity-50">
            <x-app-logo-icon class="h-6 w-6 fill-current text-white" />
            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-500">Powered by Qline</p>
        </div>
    </footer>

    @script
    <script>
        $wire.on('echo:business.' + {{ $businessId }} + ',QueueUpdated', (e) => {
            window.dispatchEvent(new CustomEvent('queue-updated-notify', { detail: { entryId: e.entry_id } }));
        });
    </script>
    @endscript
</div>