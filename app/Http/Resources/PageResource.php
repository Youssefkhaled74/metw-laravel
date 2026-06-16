<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
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
            'title' => $this->transelated_title,
            'content' => $this->translated_content,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'active_from' => $this->active_from?->toDateString(),
            'active_to' => $this->active_to?->toDateString(),
        ];
    }
}
