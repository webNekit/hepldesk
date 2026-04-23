<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;

class ItDashboard extends Component
{
    public function render()
    {
        $newTickets = Ticket::where('status', 'new')->latest()->get();
        $inProgressTickets = Ticket::where('status', 'in_progress')->latest()->get();
        $resolvedTickets = Ticket::where('status', 'resolved')->latest()->get();

        return view('livewire.it-dashboard', [
            'newTickets' => $newTickets,
            'inProgressTickets' => $inProgressTickets,
            'resolvedTickets' => $resolvedTickets,
        ])->layout('layouts.admin');
    }
}
