<div class="space-y-8">
    <div class="page-header">
        <div>
            <span class="page-kicker">Plans & Billing</span>
            <h1 class="page-title mt-4">Choose the pace that fits your queue.</h1>
            <p class="page-description mt-3">
                Review your current subscription and upgrade when you need more daily consistency or customer retention
                features.
            </p>
        </div>
    </div>

    @if (session()->has('error'))
        <div
            class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="glass-card">
        <div class="page-header">
            <div>
                <p class="metric-label">Current Plan</p>
                @if($subscription && $subscription->status === 'active')
                    {{-- FIX: was $subscription->tier which does not exist.
                    The Subscription model casts 'type' (not 'tier') to the SubTier enum. --}}
                    <h2 class="mt-3 text-3xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">
                        {{ ucfirst($subscription->type->value) }} Pass
                    </h2>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        Active until <span
                            class="font-semibold text-slate-800 dark:text-slate-100">{{ $subscription->expires_at->format('M d, Y h:i A') }}</span>
                    </p>
                @else
                    <h2 class="mt-3 text-3xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">No active plan
                    </h2>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        Pick one of the options below to activate the queue for your business.
                    </p>
                @endif
            </div>

            <span
                class="badge-pill {{ $subscription && $subscription->status === 'active' ? 'badge-pill--brand' : '' }}">
                {{ $subscription && $subscription->status === 'active' ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="pricing-card">
            <span class="badge-pill">Flexible</span>
            <h2 class="mt-6 text-3xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">Daily Pass</h2>
            <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
                Great when you want queue control on demand without committing to a full month.
            </p>

            <p class="mt-8 text-5xl font-bold tracking-[-0.07em] text-slate-950 dark:text-white">RM 15<span
                    class="ml-2 text-base font-semibold text-slate-400">/day</span></p>

            <ul class="mt-8 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                <li>Standard queue operations</li>
                <li>WhatsApp notifications</li>
                <li>QR standee access</li>
            </ul>

            <flux:button wire:click="subscribe('daily')" class="btn-link-secondary mt-8 w-full !rounded-full !py-3.5">
                Buy Daily Pass
            </flux:button>
        </div>

        <div class="pricing-card mesh-accent text-white">
            <div
                class="absolute right-6 top-6 rounded-full bg-white/15 px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.24em] backdrop-blur-sm">
                Best Value
            </div>

            <h2 class="text-3xl font-bold tracking-[-0.05em]">Monthly Ultimate</h2>
            <p class="mt-3 text-sm text-white/78">
                Ideal for teams that manage queues every day and want loyalty plus feedback tools included.
            </p>

            <p class="mt-8 text-5xl font-bold tracking-[-0.07em]">RM 400<span
                    class="ml-2 text-base font-semibold text-white/65">/month</span></p>

            <ul class="mt-8 space-y-3 text-sm text-white/82">
                <li>All Daily features included</li>
                <li>Customer feedback module</li>
                <li>Loyalty rewards engine</li>
                <li>Multi-counter ready operations</li>
            </ul>

            <flux:button wire:click="subscribe('monthly')"
                class="mt-8 inline-flex w-full items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-brand-700 shadow-[0_24px_60px_-30px_rgba(0,0,0,0.4)] transition duration-300 hover:-translate-y-0.5 hover:bg-slate-100">
                Upgrade to Monthly
            </flux:button>
        </div>
    </div>
</div>