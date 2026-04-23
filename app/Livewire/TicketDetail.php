<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Instruction;
use App\Models\Ticket;
use Livewire\Component;

class TicketDetail extends Component
{
    public Ticket $ticket;
    public $body;
    public $instruction;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->instruction = Instruction::where('category_id', $ticket->category_id)->first();
    }

    public function updateStatus($status)
    {
        $this->ticket->update(['status' => $status]);
        session()->flash('message', 'Статус обновлен!');
    }

    public function addComment()
    {
        $this->validate([
            'body' => 'required|string',
        ]);

        Comment::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => auth()->id() ?? 1,
            'body' => $this->body,
        ]);

        $this->body = '';
        $this->ticket->refresh();
    }

    public function render()
    {
        return view('livewire.ticket-detail')->layout('layouts.app');
    }
}
