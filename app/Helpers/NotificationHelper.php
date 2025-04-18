<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;

class NotificationHelper
{
    public static function create(User $user, string $title, string $content, string $type)
    {
        return $user->notifications()->create([
            'title' => $title,
            'content' => $content,
            'is_read' => false,
            'notification_type' => $type
        ]);
    }
}