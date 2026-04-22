<?php

namespace App\Livewire\Business;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Marketing\CustomerFeedback as FeedbackModel;

class CustomerFeedback extends Component
{
    use WithPagination;

    public $ratingFilter = '';

    public function updatingRatingFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $businessId = auth()->user()->getActiveBusiness()?->id;

        $query = FeedbackModel::where('business_id', $businessId);

        if ($this->ratingFilter) {
            $query->where('rating', $this->ratingFilter);
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.business.customer-feedback', compact('feedbacks'))
            ->layout('layouts.app');
    }
}
