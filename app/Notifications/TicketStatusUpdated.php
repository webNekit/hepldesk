<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Ticket $ticket, public string $status) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'status' => $this->status,
            'message' => "Статус заявки #{$this->ticket->id} изменен на: ".$this->getStatusLabel(),
        ];
    }

    protected function getStatusLabel(): string
    {
        return match ($this->status) {
            'new' => 'Новая',
            'in_progress' => 'В работе',
            'resolved' => 'Готово',
            default => $this->status
        };
    }
}
