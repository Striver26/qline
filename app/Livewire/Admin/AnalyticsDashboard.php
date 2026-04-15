<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Tenant\Business;
use App\Models\Tenant\Payment;
use App\Models\Marketing\WhatsAppMessage;
use App\Models\Queue\QueueEntry;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboard extends Component
{
    #[\Livewire\Attributes\Computed]
    public function platformStats()
    {
        $activeBusinesses = Business::where('is_active', true)->count();
        $totalBusinesses = Business::count();

        // Count tickets the last 30 days
        $thirtyDaysAgo = now()->subDays(30);
        $queueVolume30d = QueueEntry::where('created_at', '>=', $thirtyDaysAgo)->count();

        // Messaging load
        $messagesSent = WhatsAppMessage::where('status', 'sent')->count();

        // Revenue (MRR) - Assuming monthly subscriptions.
        // Summing the active subscription plan prices. For simplicity, we just sum up total completed payments in last 30 days
        $thirtyDayRevenue = Payment::where('status', 'paid')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('amount');
        
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');

        return [
            'active_businesses' => $activeBusinesses,
            'total_businesses' => $totalBusinesses,
            'queue_volume_30d' => $queueVolume30d,
            'messages_sent' => $messagesSent,
            'mrr_estimate' => $thirtyDayRevenue,
            'gross_volume' => $totalRevenue,
        ];
    }

    public function render()
    {
        return view('livewire.admin.analytics-dashboard')
            ->layout('layouts.app');
    }
}
