<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name'           => 'Demo Vendor 1',
                'email'          => 'vendor1@lasco.test',
                'phone'          => '201000000100',
                'password'       => Hash::make('Vendor1234'),
                'address'        => 'Cairo, Egypt',
                'latitude'       => 30.0444,
                'longitude'      => 31.2357,
                'email_verified' => true,
                'phone_verified' => true,
                'is_active'      => true,
                'country_code'   => '+20',
            ],
            [
                'name'           => 'TechMart Egypt',
                'email'          => 'vendor2@lasco.test',
                'phone'          => '201000000200',
                'password'       => Hash::make('Vendor1234'),
                'address'        => 'Nasr City, Cairo',
                'latitude'       => 30.0584,
                'longitude'      => 31.3232,
                'email_verified' => true,
                'phone_verified' => true,
                'is_active'      => true,
                'country_code'   => '+20',
            ],
            [
                'name'           => 'Fashion Hub',
                'email'          => 'vendor3@lasco.test',
                'phone'          => '201000000300',
                'password'       => Hash::make('Vendor1234'),
                'address'        => 'Mohandessin, Giza',
                'latitude'       => 30.0496,
                'longitude'      => 31.2058,
                'email_verified' => true,
                'phone_verified' => true,
                'is_active'      => true,
                'country_code'   => '+20',
            ],
        ];

        foreach ($vendors as $data) {
            Vendor::updateOrCreate(
                ['email' => $data['email']],
                $data + ['remember_token' => Str::random(60)]
            );
        }
    }
}
