<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Config;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'country_code' => $this->country_code,
            'image' => $this->image ? asset($this->image) : null,
            'phone_verified_at' => $this->phone_verified_at,
            'email_verified_at' => $this->email_verified_at,
            'notifications_enabled' => $this->notifications_enabled,
            'notifications_shipment_enabled' => $this->enable_shipment_notifications,
            'default_lang' => $this->default_lang,
            'default_shipment_lang' => $this->default_shipment_lang,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'address' => $this->full_address,
            'unread_notifications_count' => $this->unreadNotifications()
                ->where('data->notification_type', 'ecommerce')
                ->count(),

            // ✅ Add phone from config
            'config_phone' => Config::getValue('phone'),
        ];
    }
}
