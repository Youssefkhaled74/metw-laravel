<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FcmChannel;

class OrderStatusUpdatedShipment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $title,
        protected string $body,
        protected array $data = [],
        protected string $type,
        protected string $navigationType
    ) {}

    public function via($notifiable): array
    {
        // Database + Custom FCM channel
        return ['database', FcmChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->data['key'],
            'body'  => $this->data['key'],
            'data'  => $this->data,
            'notification_type' => $this->type,
            'navigation_type' => $this->navigationType,

        ];
    }


    public function toFcm($notifiable): array
    {
        // لو عنده token يبعتله على طول
        if (!empty($notifiable->fcm_token_shipment)) {
            return [
                'token'            => $notifiable->fcm_token_shipment,
                'title'            => $this->title,
                'body'             => $this->body,
                'data'             => $this->data,
                'notification_type' => $this->type,
                'navigation_type'  => $this->navigationType,
                'is_topic'         => false,
            ];
        }

        // fallback: يشتغل بالـ topic
        return [
            'topic'            => 'user_' . $notifiable->id,
            'title'            => $this->title,
            'body'             => $this->body,
            'data'             => $this->data,
            'notification_type' => $this->type,
            'navigation_type'  => $this->navigationType,
            'is_topic'         => true,
        ];
    }
}
