<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Vendor;

class NewVendorRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Vendor $vendor
    ) {}

    public function via($notifiable)
    {
        // Admin dashboard notification
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Vendor Registration',
            'body'  => 'Vendor "' . $this->vendor->name . '" has registered and is waiting for approval.',
            'vendor_id' => $this->vendor->id,
            'notification_type' => 'vendor',
            'navigation_type' => 'vendor_approval',
            'url' => route('admin.vendors.show', $this->vendor->id), // صفحة القبول
        ];
    }
}
