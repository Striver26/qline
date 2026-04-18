<div class="mx-auto max-w-4xl space-y-8">
    <div class="text-center">
        <span class="page-kicker mx-auto">{{ $business->name }}</span>
        <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em] text-slate-950 sm:text-5xl">Join the queue in seconds.</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-600 sm:text-base">
            Add your WhatsApp number for live notifications, or leave it blank and still take a ticket instantly.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="public-stat">
            <p class="metric-label">Waiting Now</p>
            <p class="metric-value">{{ $this->waitingCount }}</p>
        </div>
        <div class="public-stat">
            <p class="metric-label">Estimated Wait</p>
            <p class="metric-value">~{{ max(5, $this->waitingCount * 5) }}m</p>
        </div>
    </div>

    <div class="glass-card">
        <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
            <div class="soft-card mesh-accent text-white">
                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.28em] text-white/75">What happens next</p>
                <h3 class="mt-4 text-3xl font-bold tracking-[-0.05em]">Get your ticket, then track your turn live.</h3>
                <div class="mt-6 grid gap-3 text-sm text-white/82">
                    <div class="rounded-[1.2rem] border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-sm">
                        Scan once, take a ticket, and avoid waiting near the counter.
                    </div>
                    <div class="rounded-[1.2rem] border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-sm">
                        Add your WhatsApp number to receive a nudge when you are close.
                    </div>
                    <div class="rounded-[1.2rem] border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-sm">
                        Keep this page open to watch your position update automatically.
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                @if($business->queue_status === 'paused')
                    <div class="rounded-[1.4rem] border border-amber-200 bg-amber-50 p-5 text-center">
                        <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-amber-700">Temporarily Paused</p>
                        <p class="mt-2 text-sm font-semibold text-amber-900">
                            {{ $business->pause_reason ?? 'The business is currently on a short break.' }}
                        </p>
                    </div>
                @endif
                <form wire:submit.prevent="joinQueue" class="space-y-6">
                    <fieldset @disabled($business->queue_status !== 'open') class="space-y-6 disabled:opacity-60">
                        <div>
                            <label class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            WhatsApp Number <span class="text-brand-700">(Recommended)</span>
                        </label>
                        <div class="mt-3">
                            <input
                                type="tel"
                                wire:model="phone"
                                placeholder="e.g. 60123456789"
                                class="w-full rounded-[1.15rem] border border-white/75 bg-white/88 px-4 py-4 text-base font-semibold text-slate-900 shadow-[0_24px_60px_-38px_rgba(15,23,42,0.38)] outline-none transition placeholder:text-slate-400 focus:border-brand-300 focus:ring-4 focus:ring-brand-200/50"
                            >
                        </div>
                        <p class="mt-3 text-sm text-slate-500">
                            @if($activeReward)
                                Enter your number to receive turn updates and earn loyalty progress automatically.
                            @else
                                Enter a number to get WhatsApp updates when it is almost your turn. You can still join without one.
                            @endif
                        </p>
                    </div>

                    @if($activeReward)
                        <div class="rounded-[1.4rem] border border-brand-200 bg-brand-50 p-5">
                            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.24em] text-brand-700">Loyalty Perk</p>
                            <p class="mt-3 text-lg font-bold tracking-[-0.03em] text-slate-900">
                                {{ $activeReward->reward_value }}
                            </p>
                            <p class="mt-2 text-sm text-slate-600">
                                Unlock it on your
                                {{ $activeReward->required_visits }}{{ $activeReward->required_visits == 1 ? 'st' : ($activeReward->required_visits == 2 ? 'nd' : ($activeReward->required_visits == 3 ? 'rd' : 'th')) }}
                                visit.
                            </p>
                        </div>
                    @endif

                    @if($errorMessage)
                        <div class="rounded-[1.2rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                            {{ $errorMessage }}
                        </div>
                    @endif

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="mesh-accent inline-flex w-full items-center justify-center rounded-[1.25rem] px-6 py-4 text-sm font-semibold uppercase tracking-[0.24em] text-white shadow-[0_24px_60px_-30px_rgba(15,159,124,0.65)] transition duration-300 hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span wire:loading.remove>Take My Ticket</span>
                        <span wire:loading>Joining Queue...</span>
                    </button>
                    </fieldset>
                </form>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="soft-card">
                        <p class="metric-label">Flexible Join</p>
                        <p class="mt-3 text-sm font-semibold text-slate-800">Anonymous tickets work too, even without a phone number.</p>
                    </div>
                    <div class="soft-card">
                        <p class="metric-label">Live Tracking</p>
                        <p class="mt-3 text-sm font-semibold text-slate-800">You can leave the counter area and still follow the queue.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-data x-init="
        let tickets = JSON.parse(localStorage.getItem('qline_active_tickets') || '{}');
        let today = new Date().toISOString().split('T')[0];
        let ticket = tickets['{{ $business->slug }}'];
        
        if (ticket) {
            if (ticket.date === today) {
                window.location.href = '/q/{{ $business->slug }}/status/' + ticket.token;
            } else {
                // Clear stale ticket from previous day
                delete tickets['{{ $business->slug }}'];
                localStorage.setItem('qline_active_tickets', JSON.stringify(tickets));
            }
        }
    "></div>
</div>
