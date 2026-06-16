<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FcmChannel;
use App\Models\Order;

class OrderAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Order Assigned',
            'body' => 'Order #' . $this->order->order_number . ' has been assigned to your shipment company ',
            'order_id' => $this->order->id,
            'navigation_type' => 'order_details',
            'url' => route('shipment.orders.show', $this->order->id),
        ];
    }
}
