<div class="space-y-8">
    <div class="page-header">
        <div>
            <span class="page-kicker">Plans & Billing</span>
            <h1 class="page-title mt-4">Choose the plan that fits your queue.</h1>
            <p class="page-description mt-3">
                Start free with clear limits, then upgrade when you need more queue volume or service points.
            </p>
        </div>
    </div>

    @if (session()->has('error'))
        <div
            class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div
            class="rounded-[1.5rem] border border-brand-200 bg-brand-50 px-5 py-4 text-sm font-semibold text-brand-700 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card">
        <div class="page-header">
            <div>
                <p class="metric-label">Current Plan</p>
                @if($subscription && $subscription->status === 'active')
                    @php
                        $currentType = $subscription->type->value;
                        $currentLabel = config("qline.tiers.{$currentType}.label", ucfirst($currentType));
                        $currentCycle = $currentType === 'free'
                            ? 'free'
                            : ($subscription->billing_cycle ?? config("qline.tiers.{$currentType}.billing_cycle", 'monthly'));
                    @endphp
                    <h2 class="mt-3 text-3xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">
                        {{ $currentLabel }} Plan
                    </h2>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        {{ ucfirst($currentCycle) }} access
                        @if($subscription->expires_at)
                            until <span
                                class="font-semibold text-slate-800 dark:text-slate-100">{{ $subscription->expires_at->format('M d, Y h:i A') }}</span>
                        @else
                            with no expiry.
                        @endif
                    </p>
                @else
                    <h2 class="mt-3 text-3xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white">No active plan
                    </h2>
                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        Use the free plan to start queueing today, or choose a paid plan for higher limits.
                    </p>
                @endif
            </div>

            <span
                class="badge-pill {{ $subscription && $subscription->status === 'active' ? 'badge-pill--brand' : '' }}">
                {{ $subscription && $subscription->status === 'active' ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="metric-label">Billing Cycle</p>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Yearly Growth and Scale plans include 20% savings.</p>
        </div>

        <div class="inline-flex rounded-full border border-slate-200 bg-white p-1 shadow-sm dark:border-white/10 dark:bg-white/5">
            <button type="button" wire:click="$set('billingCycle', 'monthly')"
                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $billingCycle === 'monthly' ? 'bg-slate-950 text-white dark:bg-white dark:text-slate-950' : 'text-slate-500 hover:text-slate-950 dark:text-slate-400 dark:hover:text-white' }}">
                Monthly
            </button>
            <button type="button" wire:click="$set('billingCycle', 'yearly')"
                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $billingCycle === 'yearly' ? 'bg-slate-950 text-white dark:bg-white dark:text-slate-950' : 'text-slate-500 hover:text-slate-950 dark:text-slate-400 dark:hover:text-white' }}">
                Yearly
            </button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-4">
        @foreach($plans as $plan)
            @php
                $price = (float) $plan['price'];
                $priceLabel = $price === 0.0
                    ? '0'
                    : number_format($price, floor($price) == $price ? 0 : 2);
            @endphp

            <div class="pricing-card {{ $plan['featured'] ? 'mesh-accent text-white' : '' }}">
                @if($plan['featured'])
                    <div
                        class="absolute right-6 top-6 rounded-full bg-white/15 px-3 py-1 text-[0.68rem] font-semibold uppercase tracking-[0.24em] backdrop-blur-sm">
                        {{ $plan['badge'] }}
                    </div>
                @else
                    <span class="badge-pill">{{ $plan['badge'] }}</span>
                @endif

                <h2 class="{{ $plan['featured'] ? 'text-3xl font-bold tracking-[-0.05em]' : 'mt-6 text-3xl font-bold tracking-[-0.05em] text-slate-950 dark:text-white' }}">
                    {{ $plan['name'] }}
                </h2>
                <p class="mt-3 text-sm {{ $plan['featured'] ? 'text-white/78' : 'text-slate-600 dark:text-slate-300' }}">
                    {{ $plan['description'] }}
                </p>

                <p class="mt-8 text-5xl font-bold tracking-[-0.07em] {{ $plan['featured'] ? '' : 'text-slate-950 dark:text-white' }}">
                    RM {{ $priceLabel }}<span
                        class="ml-2 text-base font-semibold {{ $plan['featured'] ? 'text-white/65' : 'text-slate-400' }}">/{{ $plan['period'] }}</span>
                </p>

                <ul class="mt-8 space-y-3 text-sm {{ $plan['featured'] ? 'text-white/82' : 'text-slate-600 dark:text-slate-300' }}">
                    @foreach($plan['features'] as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>

                @if($plan['featured'])
                    <flux:button wire:click="subscribe('{{ $plan['tier'] }}')"
                        class="mt-8 inline-flex w-full items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-brand-700 shadow-[0_24px_60px_-30px_rgba(0,0,0,0.4)] transition duration-300 hover:-translate-y-0.5 hover:bg-slate-100">
                        {{ $plan['button'] }}
                    </flux:button>
                @else
                    <flux:button wire:click="subscribe('{{ $plan['tier'] }}')"
                        class="btn-link-secondary mt-8 w-full !rounded-full !py-3.5">
                        {{ $plan['button'] }}
                    </flux:button>
                @endif
            </div>
        @endforeach
    </div>
</div>
