<?php

namespace App\Livewire\Admin\Feedback;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Marketing\CustomerFeedback;

class FeedbackIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRating = '';

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedFilterRating(): void { $this->resetPage(); }

    public function render()
    {
        $feedback = CustomerFeedback::with(['business', 'queueEntry'])
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->whereHas('business', fn($b) => $b->where('name', 'like', '%' . $this->search . '%'))
                          ->orWhere('wa_id', 'like', '%' . $this->search . '%')
                          ->orWhere('comment', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterRating, fn($q) => $q->where('rating', $this->filterRating))
            ->latest()
            ->paginate(20);

        $avgRating = CustomerFeedback::whereNotNull('rating')->avg('rating');

        return view('livewire.admin.feedback.feedback-index', [
            'feedback' => $feedback,
            'avgRating' => $avgRating ? round($avgRating, 1) : null,
        ])->layout('layouts.app');
    }
}
