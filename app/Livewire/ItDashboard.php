<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Component;

class ItDashboard extends Component
{
    public $filterAssignee = '';

    public $filterCategory = '';

    public $technicians = [];

    public $categories = [];

    public function mount()
    {
        if (auth()->user()->hasRole('admin')) {
            $this->technicians = User::role(['it_support', 'admin'])->get();
            $this->categories = Category::all();
        }
    }

    public function render()
    {
        $query = Ticket::query();

        // Если не администратор, показываем только свои заявки
        if (! auth()->user()->hasRole('admin')) {
            $query->where('assigned_to', auth()->id());
        } else {
            if ($this->filterAssignee) {
                $query->where('assigned_to', $this->filterAssignee);
            }
            if ($this->filterCategory) {
                $query->where('category_id', $this->filterCategory);
            }
        }

        return view('livewire.it-dashboard', [
            'newTickets' => $query->clone()->where('status', 'new')->latest()->get(),
            'inProgressTickets' => $query->clone()->where('status', 'in_progress')->latest()->get(),
            'resolvedTickets' => $query->clone()->where('status', 'resolved')->latest()->get(),
        ])->layout('layouts.admin');
    }
}
