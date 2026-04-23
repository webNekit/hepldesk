<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;

class ItDashboard extends Component
{
    public function render()
    {
        $query = Ticket::query();

        // Если не администратор, показываем только свои заявки
        if (!auth()->user()->hasRole('admin')) {
            $query->where('assigned_to', auth()->id());
        }

        return view('livewire.it-dashboard', [
            'newTickets' => $query->clone()->where('status', 'new')->latest()->get(),
            'inProgressTickets' => $query->clone()->where('status', 'in_progress')->latest()->get(),
            'resolvedTickets' => $query->clone()->where('status', 'resolved')->latest()->get(),
        ])->layout('layouts.admin');
    }
}