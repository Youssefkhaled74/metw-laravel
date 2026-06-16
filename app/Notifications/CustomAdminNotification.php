<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FcmChannelCustom;

class CustomAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $title,
        protected string $body,
        protected array $data = [],
        protected string $type = 'custom',
        protected string $navigationType = 'none',
        protected string $appType = 'ecommerce' 
    ) {}

    public function via($notifiable): array
    {
        return ['database', FcmChannelCustom::class];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'data'  => $this->data,
            'notification_type' => $this->type,
            'navigation_type' => $this->navigationType,
        ];
    }

    public function toFcm($notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'data'  => $this->data,
            'notification_type' => $this->appType,
            'navigation_type' => $this->navigationType,
            'app_type' => $this->appType,
            'ecommerce_token' => $notifiable->fcm_token,
            'shipment_token'  => $notifiable->fcm_token_shipment,
        ];
    }
}
