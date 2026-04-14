<div class="space-y-8">
    <div class="page-header">
        <div>
            <span class="page-kicker">Platform Overview</span>
            <h1 class="page-title mt-4">Growth Analytics</h1>
            <p class="page-description mt-3">
                Monitor adoption across the platform, aggregate traffic loads, and verify financial targets.
            </p>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card">
            <p class="metric-label">Active Tenants</p>
            <p class="metric-value mt-4 text-brand-600">{{ $this->platformStats['active_businesses'] }}</p>
            <p class="mt-2 text-sm text-slate-500">Out of {{ $this->platformStats['total_businesses'] }} total</p>
        </div>

        <div class="metric-card">
            <p class="metric-label">Queue Volume (30d)</p>
            <p class="metric-value mt-4">{{ number_format($this->platformStats['queue_volume_30d']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Tickets generated</p>
        </div>

        <div class="metric-card">
            <p class="metric-label">WA Messages Sent</p>
            <p class="metric-value mt-4 text-ui-600">{{ number_format($this->platformStats['messages_sent']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Successfully delivered API calls</p>
        </div>

        <div class="metric-card">
            <p class="metric-label">30d Revenue / MRR</p>
            <p class="metric-value mt-4 text-emerald-600">${{ number_format($this->platformStats['mrr_estimate'] / 100, 2) }}</p>
            <p class="mt-2 text-sm text-slate-500">Gross Vol: ${{ number_format($this->platformStats['gross_volume'] / 100, 2) }}</p>
        </div>
    </div>
</div>
