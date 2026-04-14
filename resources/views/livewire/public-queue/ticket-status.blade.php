<div
    wire:poll.4s
    x-data="{
        status: @entangle('currentStatus'),
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
    class="mx-auto max-w-4xl space-y-8"
>
    <audio x-ref="chime" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    @if($business->queue_status === 'paused')
        <div class="rounded-[1.4rem] border border-amber-200 bg-amber-50 px-5 py-4 text-center shadow-[0_12px_40px_-24px_rgba(245,158,11,0.5)]">
            <p class="text-sm font-semibold text-amber-800">
                <span class="mr-2 inline-block h-2 w-2 rounded-full bg-amber-500"></span>
                Queue Paused: {{ $business->pause_reason ?? 'Short break' }}
            </p>
        </div>
    @endif

    <div class="text-center">
        <span class="page-kicker mx-auto">{{ $business->name }}</span>
        <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em] text-slate-950 sm:text-5xl">{{ $entry->ticket_code }}</h2>
        <p class="mt-3 text-sm text-slate-600 sm:text-base">
            Keep this page open. Your position and status will update automatically every few seconds.
        </p>
    </div>

    <div class="glass-card overflow-hidden !p-0">
        <div class="h-2 w-full bg-slate-100">
            <div
                class="h-full bg-linear-to-r from-brand-500 via-brand-400 to-coral-400 transition-all duration-700"
                style="width: {{
                    match($entry->status) {
                        'waiting' => '36%',
                        'called', 'serving' => '72%',
                        'completed' => '100%',
                        default => '18%'
                    }
                }}"
            ></div>
        </div>

        <div class="grid gap-6 p-6 lg:grid-cols-[0.9fr_1.1fr] lg:p-8">
            <div class="soft-card mesh-accent text-white">
                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-white/75">Current status</p>
                <div class="mt-5 inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-white backdrop-blur-sm {{ $this->statusColor }}">
                    {{ $this->statusLabel }}
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[1.3rem] border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-white/70">Ticket</p>
                        <p class="mt-3 text-3xl font-bold tracking-[-0.05em]">{{ $entry->ticket_code }}</p>
                    </div>
                    <div class="rounded-[1.3rem] border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-white/70">Refresh</p>
                        <p class="mt-3 text-3xl font-bold tracking-[-0.05em]">4s</p>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                @if($entry->status === \App\Enums\QueueStatus::WAITING->value)
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="public-stat">
                            <p class="metric-label">Position</p>
                            <p class="metric-value">{{ $this->positionInfo['position'] }}</p>
                        </div>
                        <div class="public-stat">
                            <p class="metric-label">Estimated Wait</p>
                            <p class="metric-value">~{{ $this->positionInfo['estimated_wait_mins'] }}m</p>
                        </div>
                    </div>

                    <div class="soft-card">
                        <h3 class="text-2xl font-bold tracking-[-0.04em] text-slate-950">You are in line.</h3>
                        <p class="mt-3 text-sm text-slate-600">
                            You do not need to stay right at the counter. Keep this page open and we will update you when it is your turn.
                        </p>
                    </div>
                @elseif($entry->status === \App\Enums\QueueStatus::CALLED->value)
                    <div class="rounded-[1.8rem] border border-brand-200 bg-brand-50 p-6 text-center shadow-[0_24px_60px_-36px_rgba(15,159,124,0.35)]">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.25rem] bg-brand-500 text-3xl text-white shadow-lg shadow-brand-300/40">
                            !
                        </div>
                        <h3 class="mt-5 text-3xl font-bold tracking-[-0.05em] text-slate-950">It is your turn.</h3>
                        <p class="mt-3 text-sm text-slate-600">Please head to the counter now.</p>
                        @if($entry->counter_id)
                            <div class="mt-5 inline-flex rounded-full bg-slate-950 px-5 py-2 text-sm font-semibold text-white">
                                Counter {{ $entry->counter_id }}
                            </div>
                        @endif
                    </div>
                @elseif($entry->status === \App\Enums\QueueStatus::COMPLETED->value)
                    <div class="rounded-[1.8rem] border border-emerald-200 bg-emerald-50 p-6 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.25rem] bg-emerald-500 text-3xl text-white shadow-lg shadow-emerald-200">
                            ✓
                        </div>
                        <h3 class="mt-5 text-3xl font-bold tracking-[-0.05em] text-slate-950">All done.</h3>
                        <p class="mt-3 text-sm text-slate-600">Thanks for visiting. We hope the queue felt smooth.</p>
                        @if(!$entry->customerFeedback)
                            <a
                                href="{{ url("/q/{$business->slug}/feedback/{$entry->cancel_token}") }}"
                                class="btn-link-primary mt-6"
                            >
                                Leave Feedback
                            </a>
                        @endif
                    </div>
                @else
                    <div class="soft-card">
                        <h3 class="text-2xl font-bold tracking-[-0.04em] text-slate-950">Status updated</h3>
                        <p class="mt-3 text-sm text-slate-600">
                            Ticket status: {{ ucfirst($entry->status) }}.
                        </p>
                    </div>
                @endif

                @if($this->loyaltyPoints)
                    <div class="soft-card">
                        <p class="metric-label">Loyalty Progress</p>
                        <p class="mt-4 text-lg font-bold tracking-[-0.03em] text-slate-950">
                            @if($this->loyaltyPoints['has_reward'])
                                Reward ready: {{ $this->loyaltyPoints['reward_name'] }}
                            @elseif($this->loyaltyPoints['next_reward_in'])
                                {{ $this->loyaltyPoints['next_reward_in'] }} more visit(s) for {{ $this->loyaltyPoints['next_reward_name'] }}
                            @else
                                Visit #{{ $this->loyaltyPoints['visits'] }}
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($entry->status === \App\Enums\QueueStatus::WAITING->value)
        <div class="text-center" x-data="{ confirming: false }">
            <button
                x-show="!confirming"
                @click="confirming = true"
                class="text-sm font-semibold text-slate-400 transition hover:text-rose-500"
            >
                Cancel my ticket
            </button>

            <div x-show="confirming" x-cloak class="mx-auto mt-4 max-w-md rounded-[1.6rem] border border-rose-200 bg-rose-50 p-5 text-center">
                <p class="text-sm font-semibold text-rose-700">Are you sure you want to leave the queue?</p>
                <div class="mt-4 flex justify-center gap-3">
                    <button wire:click="cancelTicket" @click="confirming = false" class="rounded-full bg-rose-600 px-5 py-2 text-sm font-semibold text-white">
                        Confirm Cancel
                    </button>
                    <button @click="confirming = false" class="rounded-full border border-slate-200 px-5 py-2 text-sm font-semibold text-slate-500">
                        Keep My Ticket
                    </button>
                </div>
            </div>
        </div>
    @endif

    <p class="text-center text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-slate-400">
        Updating automatically every 4 seconds
    </p>
</div>
