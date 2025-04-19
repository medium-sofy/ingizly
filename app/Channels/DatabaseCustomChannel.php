<?php

namespace App\Channels;

use App\Models\Notification;
use Illuminate\Notifications\Notification as LaravelNotification;

class DatabaseCustomChannel
{
    public function send($notifiable, LaravelNotification $notification)
    {
        $data = $notification->toArray($notifiable);

        return Notification::create([
            'user_id' => $notifiable->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'is_read' => false,
            'notification_type' => $data['notification_type'],
            'data' => json_encode($data['data'] ?? []),
        ]);
    }
}