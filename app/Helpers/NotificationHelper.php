<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function send($userId, $title, $message, $type = 'info', $icon = null, $actionUrl = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'action_url' => $actionUrl,
        ]);
    }
}
