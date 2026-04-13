<div wire:poll.3s class="mx-auto max-w-7xl">
    <div class="tv-shell">
        <div class="flex flex-col gap-6 border-b border-white/10 pb-8 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="inline-flex rounded-full border border-white/15 bg-white/8 px-4 py-1.5 text-[0.72rem] font-semibold uppercase tracking-[0.26em] text-white/70">
                    {{ $business->name }}
                </span>
                <h2 class="mt-5 text-5xl font-bold tracking-[-0.08em] text-white sm:text-6xl">Live Queue Board</h2>
                <p class="mt-3 max-w-2xl text-sm text-white/65 sm:text-base">
                    Customers can relax away from the counter while this screen shows exactly who is being served next.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/8 px-4 py-2 text-sm font-semibold text-white/85">
                    <span class="h-2.5 w-2.5 rounded-full {{ $business->queue_status === 'open' ? 'bg-brand-300' : 'bg-rose-400' }}"></span>
                    {{ $business->queue_status === 'open' ? 'Queue Open' : 'Queue Closed' }}
                </span>
                <span class="inline-flex items-center rounded-full border border-white/10 bg-white/8 px-4 py-2 text-sm font-semibold text-white/75">
                    {{ $this->waitingCount }} waiting
                </span>
            </div>
        </div>

        <div class="mt-8 grid gap-8 xl:grid-cols-[0.92fr_1.08fr]">
            <section class="space-y-5">
                <div class="flex items-center justify-between">
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.28em] text-white/55">Now Serving</p>
                    <span class="rounded-full border border-white/10 bg-white/8 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-white/60">
                        Live
                    </span>
                </div>

                @if($this->nowServing->isEmpty())
                    <div class="tv-card flex min-h-[20rem] items-center justify-center text-center">
                        <div>
                            <p class="text-6xl font-bold tracking-[-0.08em] text-white/25">--</p>
                            <p class="mt-4 text-lg font-semibold text-white/55">No active tickets at the moment.</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-5">
                        @foreach($this->nowServing as $active)
                            <div class="mesh-accent relative overflow-hidden rounded-[2.2rem] p-8 text-white shadow-[0_35px_100px_-42px_rgba(15,159,124,0.75)]">
                                @if($active->counter_id)
                                    <div class="absolute right-6 top-6 rounded-full border border-white/20 bg-white/12 px-4 py-1.5 text-sm font-semibold uppercase tracking-[0.2em] backdrop-blur-sm">
                                        Counter {{ $active->counter_id }}
                                    </div>
                                @endif

                                <p class="text-[0.72rem] font-semibold uppercase tracking-[0.26em] text-white/70">
                                    {{ $active->status === \App\Enums\QueueStatus::CALLED->value ? 'Calling Now' : 'Currently Serving' }}
                                </p>
                                <p class="mt-5 text-8xl font-bold tracking-[-0.1em] sm:text-[7rem]">{{ $active->ticket_code }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="space-y-5">
                <div class="flex items-center justify-between">
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.28em] text-white/55">Up Next</p>
                    <span class="text-sm text-white/55">Positions update automatically</span>
                </div>

                @if($this->waitingList->isEmpty())
                    <div class="tv-card flex min-h-[20rem] items-center justify-center text-center">
                        <div>
                            <p class="text-5xl font-bold tracking-[-0.08em] text-white/30">Queue Clear</p>
                            <p class="mt-4 text-lg font-semibold text-white/55">The line is empty. New arrivals can join immediately.</p>
                        </div>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($this->waitingList as $index => $waiting)
                            <div class="tv-card {{ $index === 0 ? 'border-brand-300/45 bg-brand-500/10' : '' }}">
                                <p class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-white/55">
                                    Position #{{ $waiting->position }}
                                </p>
                                <p class="mt-4 text-5xl font-bold tracking-[-0.08em] text-white">{{ $waiting->ticket_code }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if($this->waitingCount > 12)
                        <div class="tv-card text-center">
                            <p class="text-3xl font-bold tracking-[-0.05em] text-white/75">+{{ $this->waitingCount - 12 }} more waiting</p>
                        </div>
                    @endif
                @endif
            </section>
        </div>

        <div class="mt-8 border-t border-white/10 pt-6 text-center text-lg text-white/70">
            Scan the QR code or message <span class="rounded-full border border-white/10 bg-white/8 px-4 py-1.5 font-semibold text-white">JOIN {{ $business->join_code }}</span> to join the queue.
        </div>
    </div>
</div>
