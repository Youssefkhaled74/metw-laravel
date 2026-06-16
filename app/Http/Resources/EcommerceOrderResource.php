<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enum\OrderStatus; // تأكد إنك مستورد الـ Enum

class EcommerceOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Load items relationship
        $items = $this->whenLoaded('items');

        // 1) has_deposit → TRUE if ANY product has deposit
        $hasDeposit = $items->contains(function ($item) {
            return $item->product?->has_deposit == 1;
        });

        // 2) is_accepted_to_pay → TRUE only if ALL items accepted
        $isAcceptedToPay = $items->every(function ($item) {
            return $item->is_shipment_accepted == 1;
        }) ? 1 : 0;

        // 3) deposit_percentage → total of all product percentages
        $depositPercentage = $items->sum(function ($item) {
            return $item->product?->deposit_percentage ?? 0;
        });

        // 4) deposit_value → sum (unit_price * qty * percentage)
        $depositValue = $items->sum(function ($item) {
            $percentage = $item->product?->deposit_percentage ?? 0;
            return ($item->unit_price * $item->quantity) * ($percentage / 100);
        });

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'tracking_number' => $this->tracking_number,
            'subtotal' => $this->subtotal,
            'shipping_price' => $this->shipping_price,
            'discount' => $this->discount,
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'remaining_amount' => $this->remaining_amount,
            'final_price' => $this->final_price,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'phone' => $this->phone,

            'main_status' => in_array($this->status, [
                OrderStatus::PENDING->value,
                OrderStatus::ACCEPTED->value,
                OrderStatus::PICKUP->value,
                OrderStatus::ON_WAY->value,
            ]) ? OrderStatus::PENDING->value : $this->status,

            'status' => $this->status,

            'has_deposit'        => $hasDeposit,
            'is_accepted_to_pay' => $isAcceptedToPay,
            // 'deposit_percentage' => $depositPercentage,
            'deposit_value'      => round($depositValue, 2),

            'user' => new UserResource($this->whenLoaded('user')),
            'user_address' => new UserAddressResource($this->whenLoaded('userAddress')),
            'items' => EcommerceOrderItemResource::collection($this->whenLoaded('items')),

            'actual_delivery_date' => $this->actual_delivery_date,
            'estimated_delivery_to' => $this->estimated_delivery_to,
            'estimated_delivery_from' => $this->estimated_delivery_from,

            'created_at' => $this->created_at?->format('Y-m-d h:i A'),
        ];
    }

}
