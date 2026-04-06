<?php

namespace App\Livewire\PublicQueue;

use Livewire\Component;
use App\Models\Tenant\Business;
use App\Models\Queue\QueueEntry;
use App\Models\Marketing\CustomerFeedback;

class FeedbackForm extends Component
{
    public Business $business;
    public QueueEntry $entry;
    public int $rating = 0;
    public string $comment = '';
    public bool $submitted = false;
    public bool $alreadySubmitted = false;

    public function mount($slug, $token)
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();
        $this->entry = QueueEntry::where('cancel_token', $token)
            ->where('business_id', $this->business->id)
            ->firstOrFail();

        // Check if feedback already exists
        if ($this->entry->customerFeedback) {
            $this->alreadySubmitted = true;
        }
    }

    public function setRating(int $value)
    {
        $this->rating = $value;
    }

    public function submitFeedback()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        CustomerFeedback::create([
            'business_id' => $this->business->id,
            'queue_entry_id' => $this->entry->id,
            'wa_id' => $this->entry->wa_id ?? 'walk-in',
            'rating' => $this->rating,
            'comment' => $this->comment ?: null,
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.public-queue.feedback-form')
            ->layout('layouts.public');
    }
}
