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
    #[\Livewire\Attributes\Validate('required|integer|min:1|max:5')]
    public int $rating = 0;

    #[\Livewire\Attributes\Validate('nullable|string|max:500')]
    public string $comment = '';

    public bool $submitted = false;
    public bool $alreadySubmitted = false;

    public function mount($slug, $token)
    {
        $this->business = Business::where('slug', $slug)->firstOrFail();
        $this->entry = QueueEntry::where('cancel_token', $token)
            ->where('business_id', $this->business->id)
            ->firstOrFail();

        $this->checkExistingFeedback();
    }

    private function checkExistingFeedback(): void
    {
        if ($this->entry->customerFeedback()->exists()) {
            $this->alreadySubmitted = true;
        }
    }

    public function setRating(int $value)
    {
        $this->rating = $value;
    }

    public function submitFeedback()
    {
        $this->validate();
        $this->storeFeedbackRecord();
        $this->submitted = true;
    }

    private function storeFeedbackRecord(): void
    {
        CustomerFeedback::create([
            'business_id' => $this->business->id,
            'queue_entry_id' => $this->entry->id,
            'wa_id' => $this->entry->wa_id ?? 'Anonymous',
            'rating' => $this->rating,
            'comment' => $this->comment ?: null,
        ]);
    }

    public function render()
    {
        return view('livewire.public-queue.feedback-form')
            ->layout('layouts.public');
    }
}
