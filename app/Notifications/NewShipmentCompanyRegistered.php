<?php

namespace App\Notifications;

use App\Models\ShipmentCompany;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewShipmentCompanyRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ShipmentCompany $company
    ) {}

    public function via($notifiable)
    {
        return ['database']; // Admin dashboard notification
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Shipment Company Registration',
            'body'  => 'Shipment company "' . $this->company->name . '" has registered and is waiting for approval.',
            'shipment_company_id' => $this->company->id,
            'notification_type' => 'shipment',
            'navigation_type' => 'shipment_approval',
            'url' => route('admin.shipment-companies.show', $this->company->id),
        ];
    }
}
