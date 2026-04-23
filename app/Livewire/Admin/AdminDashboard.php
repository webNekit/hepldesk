<?php

namespace App\Livewire\Admin;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Models\Category;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render()
    {
        $stats = [
            'tickets_new' => Ticket::where('status', 'new')->count(),
            'tickets_total' => Ticket::count(),
            'users_total' => User::count(),
            'departments_total' => Department::count(),
        ];

        $recentTickets = Ticket::with(['user', 'category'])->latest()->take(5)->get();

        return view('livewire.admin.admin-dashboard', [
            'stats' => $stats,
            'recentTickets' => $recentTickets
        ])->layout('layouts.admin');
    }
}
