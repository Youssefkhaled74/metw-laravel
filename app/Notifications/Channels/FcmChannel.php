<?php

namespace App\Notifications\Channels;

use App\Services\FirebaseService;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        if (!$message) {
            return;
        }

        $type            = $message['notification_type'] ?? 'ecommerce';
        $navigation_type = $message['navigation_type'] ?? 'order_tracking';
        $service         = new FirebaseService($type);

        if (($message['is_topic'] ?? true) && !empty($message['topic'])) {
            // إرسال لتوبيك
            $service->sendToTopic(
                $message['topic'],
                $message['title'],
                $message['body'],
                $message['data'] ?? [],
                $type,
                $navigation_type
            );
        } elseif (!empty($message['token'])) {
            // إرسال لتوكن
            $service->sendNotification(
                $message['token'],
                $message['title'],
                $message['body'],
                $message['data'] ?? [],
                $type,
                $navigation_type
            );
        }
    }
}
