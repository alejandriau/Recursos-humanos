<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    use Queueable;

    public function __construct(public array $data) {}

    public function via($notifiable): array
    {
        return ['database']; // Se guarda en tabla notifications
    }

    public function toDatabase($notifiable): array
    {
        return $this->data;
    }

    public function toArray($notifiable): array
    {
        return $this->data;
    }
}
