<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Review;
use App\Models\ShipmentCompany;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $companies = ShipmentCompany::all();
        $users = User::all();
        $orders = Order::all();

        if ($companies->isEmpty() || $users->isEmpty()) return;

        foreach ($companies as $company) {
            Review::updateOrCreate(
                [
                    'user_id'            => $users->random()->id,
                    'shipment_company_id' => $company->id,
                ],
                [
                    'rate'    => rand(3, 5),
                    'comment' => 'Good service, delivery was on time.',
                    'order_id' => $orders->isNotEmpty() ? $orders->random()->id : null,
                ]
            );
        }
    }
}
