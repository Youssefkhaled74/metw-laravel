<?php

namespace Database\Seeders;

use App\Models\DeliveryType;
use Illuminate\Database\Seeder;

class DeliveryTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name'        => 'Door to Door',
                'code'        => 'DOOR_DOOR',
                'description' => 'Pick up from sender address and deliver to receiver address.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Pickup Point',
                'code'        => 'PICKUP_POINT',
                'description' => 'Receiver collects the shipment from a pickup point.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Express Same Day',
                'code'        => 'SAME_DAY',
                'description' => 'Same-day delivery within the city.',
                'is_active'   => true,
            ],
        ];

        foreach ($types as $data) {
            DeliveryType::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}


