<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enum\OrderStatus;

class ReturnRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // حساب الـ subtotal من الـ items المرتجعة
        $subtotal = $this->items->sum('return_price');

        return [
            'id' => $this->id,
            'order_number' => $this->order->order_number ?? null,
            'total_amount' => $this->refund_amount ?? $subtotal,
            'phone' => $this->pickup_phone ?? $this->order->phone,
            'reason' => $this->reason,
            'cancel_reasons' => CancelReasonResource::collection(
                $this->cancelReasons()
            ),
            'main_status' => OrderStatus::RETURNED->value,
            'status' => $this->status,


            // 'user' => new UserResource($this->whenLoaded('user')),
            // 'actual_delivery_date' => null,
            // 'estimated_delivery_to' => null,
            // 'estimated_delivery_from' => null,
            'user_address' => $this->whenLoaded('pickupaddress', function () {
                return $this->pickupaddress?->full_address;
            }),            'items' => ReturnRequestItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at
                ? $this->created_at->format('Y-m-d h:i A')
                : null,
        ];
    }
}
