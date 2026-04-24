<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    public function __construct(public Comment $comment) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->comment->ticket_id,
            'user_name' => $this->comment->user->name,
            'message' => "Новый комментарий в заявке #{$this->comment->ticket_id} от {$this->comment->user->name}",
        ];
    }
}
