<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;

class TicketTracking extends Component
{
    public $ticket;

    public $rating;

    public $feedback_comment;

    public function mount($uuid)
    {
        $this->ticket = Ticket::where('uuid', $uuid)
            ->with(['category', 'assignee', 'activities', 'activities.causer'])
            ->firstOrFail();
    }

    public function submitFeedback()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_comment' => 'nullable|string',
        ]);

        $this->ticket->update([
            'rating' => $this->rating,
            'feedback_comment' => $this->feedback_comment,
        ]);

        session()->flash('message', 'Спасибо за вашу оценку!');
    }

    public function render()
    {
        return view('livewire.ticket-tracking')
            ->layout('layouts.app');
    }
}
