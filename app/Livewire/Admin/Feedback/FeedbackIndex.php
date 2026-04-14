<?php

namespace App\Livewire\Admin\Feedback;

use Livewire\Component;

class FeedbackIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.feedback.feedback-index')->layout('layouts.app');
    }
}
