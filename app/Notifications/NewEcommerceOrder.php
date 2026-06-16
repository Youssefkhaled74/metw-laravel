<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FcmChannel;
use App\Models\EcommerceOrder;

class NewEcommerceOrder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EcommerceOrder $order
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Order Received',
            'body' => 'Order #' . $this->order->order_number . ' has been placed by ' . $this->order->user->name,
            'order_id' => $this->order->id,
            'navigation_type' => 'order_details',
            'url' => route('admin.ecommerce-orders.show', $this->order->id),
        ];
    }

    public function toFcm($notifiable)
    {
        return [
            'token' => $notifiable->fcm_token,
            'title' => 'New Order Received',
            'body' => 'Order #' . $this->order->order_number,
            'data' => [
                'order_id' => $this->order->id,
                'navigation_type' => 'order_details',
            ],
            'notification_type' => 'new_order',
            'is_topic' => false,
        ];
    }
}
