<?php
namespace App\Livewire\Admin;
use Livewire\Component;
use App\Models\User;
use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Models\Tenant\Payment;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalUsers' => User::count(),
            'totalBusinesses' => Business::count(),
            'activeTickets' => QueueEntry::count(),
            'totalRevenue' => Payment::where('status', 'paid')->sum('amount') ?? 0,
        ])->layout('layouts.app');
    }
}