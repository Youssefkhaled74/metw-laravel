<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', 'en');

        $data = $this->data;

        $type = $data['notification_type'] ?? null;

        if ($type === 'custom') {

            return [
                'id'         => $this->id,
                'type'       => $this->type,
                'data'       => $data,
                'read_at'    => $this->read_at,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }

        $key = $data['title'] ?? null;

        if ($key) {
            $data['title'] = trans("notifications.{$key}_title", [], $locale);
            $data['body']  = trans("notifications.{$key}_body", $data['data'] ?? [], $locale);
        }

        return [
            'id'         => $this->id,
            'type'       => $this->type,
            'data'       => $data,
            'read_at'    => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

