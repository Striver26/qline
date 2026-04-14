<div class="space-y-6">
    <div class="page-header">
        <div>
            <span class="page-kicker">Platform Command</span>
            <h1 class="page-title mt-4">SuperAdmin Dashboard</h1>
            <p class="page-description mt-3">High-level insights into your software performance across all tenants.</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card">
            <p class="metric-label">Total Users</p>
            <h3 class="metric-value">{{$totalUsers}}</h3>
        </div>
        <div class="metric-card">
            <p class="metric-label">Registered Tenants</p>
            <h3 class="metric-value">{{$totalBusinesses}}</h3>
        </div>
        <div class="metric-card">
            <p class="metric-label">All-time Tickets</p>
            <h3 class="metric-value">{{$activeTickets}}</h3>
        </div>
        <div class="metric-card">
            <p class="metric-label">Platform Revenue</p>
            <h3 class="metric-value">MYR {{number_format($totalRevenue, 2)}}</h3>
        </div>
    </div>
</div>