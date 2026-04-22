<div class="space-y-8">
    <div class="page-header">
        <div>
            <span class="page-kicker">Performance Insights</span>
            <h1 class="page-title mt-4">Queue Analytics</h1>
            <p class="page-description mt-3">
                Understand your queue volume, track average wait times, and locate your peak hours to schedule staff efficiently.
            </p>
        </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card">
            <p class="metric-label">Total Entries</p>
            <p class="metric-value mt-4">{{ $this->queueStats['total'] }}</p>
            <p class="mt-2 text-sm text-slate-500">Historical tickets generated</p>
        </div>

        <div class="metric-card">
            <p class="metric-label">Completion Rate</p>
            <p class="metric-value mt-4 text-brand-600">{{ $this->queueStats['completion_rate'] }}%</p>
            <p class="mt-2 text-sm text-slate-500">{{ $this->queueStats['completed'] }} successfully served</p>
        </div>

        <div class="metric-card">
            <p class="metric-label">Avg. Wait Time</p>
            <p class="metric-value mt-4 text-amber-600">{{ $this->queueStats['avg_wait_time'] }} min</p>
            <p class="mt-2 text-sm text-slate-500">From join to call</p>
        </div>

        <div class="metric-card">
            <p class="metric-label">Avg. Service Time</p>
            <p class="metric-value mt-4 text-emerald-600">{{ $this->queueStats['avg_service_time'] }} min</p>
            <p class="mt-2 text-sm text-slate-500">From call to completion</p>
        </div>

        <div class="metric-card">
            <p class="metric-label">Peak Traffic Time</p>
            <p class="metric-value mt-4 text-ui-600">{{ $this->queueStats['busiest_hour'] }}</p>
            <p class="mt-2 text-sm text-slate-500">Busiest hour of operations</p>
        </div>
    </div>
    
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="glass-card">
            <h3 class="text-xl font-bold tracking-[-0.04em] text-slate-900 dark:text-white">Cancellation Overview</h3>
            <p class="mt-2 text-sm text-slate-500">Tracks how many people left before being served.</p>
            
            <div class="mt-6 flex items-center justify-between rounded-[1.2rem] border border-rose-200/50 bg-rose-50/50 p-5 dark:border-rose-500/20 dark:bg-rose-500/10">
                <span class="font-semibold text-rose-800 dark:text-rose-400">Total Dropped Tickets</span>
                <span class="text-2xl font-bold text-rose-600">{{ $this->queueStats['cancelled'] }}</span>
            </div>
            <p class="mt-4 text-xs text-slate-400">If abandonment is high, consider exploring the loyalty rewards plugin to encourage customers to remain in queue.</p>
        </div>
    </div>
</div>
