<?php

namespace Database\Seeders;

use App\Models\ShipmentCompany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ShipmentCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name'           => 'Express Delivery Co.',
                'email'          => 'info@expressdelivery.test',
                'password'       => Hash::make('Ship1234'),
                'phone'          => '201000001001',
                'address'        => 'Cairo, Egypt',
                'description'    => 'Fast and reliable express delivery service covering all Egyptian cities.',
                'is_active'      => true,
                'price_per_km'   => 2.50,
                'est_days'       => 3,
                'facebook_url'   => 'https://facebook.com/expressdelivery',
                'whatsapp_url'   => 'https://wa.me/201000001001',
            ],
            [
                'name'           => 'CargoLink Logistics',
                'email'          => 'info@cargolink.test',
                'password'       => Hash::make('Ship1234'),
                'phone'          => '201000002002',
                'address'        => 'Alexandria, Egypt',
                'description'    => 'Bulk cargo and logistics solutions across Egypt and the Middle East.',
                'is_active'      => true,
                'price_per_km'   => 1.80,
                'est_days'       => 5,
                'facebook_url'   => 'https://facebook.com/cargolink',
                'whatsapp_url'   => 'https://wa.me/201000002002',
            ],
        ];

        foreach ($companies as $data) {
            ShipmentCompany::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
