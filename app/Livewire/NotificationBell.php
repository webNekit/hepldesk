<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead($id)
    {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => auth()->user()->unreadNotifications,
        ]);
    }
}
