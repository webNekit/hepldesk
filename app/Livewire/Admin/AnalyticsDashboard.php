<?php

namespace App\Livewire\Admin;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AnalyticsDashboard extends Component
{
    public function render()
    {
        $stats = [
            'total' => Ticket::count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'pending' => Ticket::whereIn('status', ['new', 'in_progress'])->count(),
            'avg_rating' => round(Ticket::whereNotNull('rating')->avg('rating'), 1),
        ];

        $statusDistribution = Ticket::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $techPerformance = User::role('it_support')
            ->withCount(['assignedTickets as resolved_count' => function ($query) {
                $query->where('status', 'resolved');
            }])
            ->orderBy('resolved_count', 'desc')
            ->take(5)
            ->get();

        return view('livewire.admin.analytics-dashboard', [
            'stats' => $stats,
            'statusDistribution' => $statusDistribution,
            'techPerformance' => $techPerformance,
        ])->layout('layouts.admin');
    }
}
