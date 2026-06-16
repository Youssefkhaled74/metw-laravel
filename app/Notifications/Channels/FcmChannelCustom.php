<?php

namespace App\Notifications\Channels;

use App\Services\FirebaseService;
use Illuminate\Notifications\Notification;

class FcmChannelCustom
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

        $appType = $message['app_type'] ?? 'ecommerce';

        // ==========================
        // Ecommerce Notification
        // ==========================
        if ($appType === 'ecommerce' || $appType === 'both') {

            if (!empty($message['ecommerce_token'])) {

                $service = new FirebaseService('ecommerce');

                $service->sendNotification(
                    $message['ecommerce_token'],
                    $message['title'],
                    $message['body'],
                    $message['data'] ?? []
                );
            }
        }

        // ==========================
        // Shipment Notification
        // ==========================
        if ($appType === 'shipment' || $appType === 'both') {

            if (!empty($message['shipment_token'])) {

                $service = new FirebaseService('shipment');

                $service->sendNotification(
                    $message['shipment_token'],
                    $message['title'],
                    $message['body'],
                    $message['data'] ?? []
                );
            }
        }
    }
}
