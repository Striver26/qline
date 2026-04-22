<?php
namespace App\Livewire\Admin;
use Livewire\Component;
use App\Models\User;
use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Payment;
use App\Enums\QueueStatus;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalUsers'       => User::count(),
            'totalBusinesses'  => Business::count(),
            'activeTickets'    => QueueEntry::whereIn('status', [
                QueueStatus::WAITING->value,
                QueueStatus::CALLED->value,
                QueueStatus::SERVING->value,
            ])->count(),
            'revenue30d'       => Payment::where('status', 'paid')
                ->where('paid_at', '>=', now()->subDays(30))
                ->sum('amount') ?? 0,
        ])->layout('layouts.app');
    }
}