<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'package_id'=>$this->package_id,
            'order_item_id'=>$this->order_item_id,
            'status'=>$this->status,
            'location'=>$this->location,
            'description'=>$this->description,
            'occurred_at_date'=>$this->occurred_at->format('Y-m-d'),
            'occurred_at_time'=>$this->occurred_at->format('H:i:s'),
            'metadata'=>$this->metadata,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}
