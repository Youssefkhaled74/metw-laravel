<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $addresses = [
            [
                'city_en'     => 'Nasr City',
                'street_name' => 'Abbas El Akkad St',
                'building'    => '12',
                'floor'       => '3',
                'landmark'    => 'Near City Stars Mall',
                'address_type' => 'HOME',
                'is_default'  => true,
                'latitude'    => 30.0584,
                'longitude'   => 31.3232,
            ],
            [
                'city_en'     => 'Mohandessin',
                'street_name' => 'Lebanon St',
                'building'    => '5',
                'floor'       => '8',
                'landmark'    => 'Near Mohandessin Hospital',
                'address_type' => 'OFFICE',
                'is_default'  => false,
                'latitude'    => 30.0473,
                'longitude'   => 31.2065,
            ],
        ];

        foreach ($users as $user) {
            foreach ($addresses as $i => $addr) {
                $city = City::where('name_en', $addr['city_en'])->first();
                if (!$city) continue;

                $state = State::find($city->state_id);

                UserAddress::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'street_name' => $addr['street_name'],
                        'building'    => $addr['building'],
                    ],
                    [
                        'city_id'      => $city->id,
                        'state_id'     => $state?->id,
                        'country_id'   => $state?->country_id ?? 1,
                        'zone_id'      => null,
                        'floor'        => $addr['floor'],
                        'landmark'     => $addr['landmark'],
                        'address_type' => $addr['address_type'],
                        'is_default'   => $i === 0,
                        'latitude'     => $addr['latitude'],
                        'longitude'    => $addr['longitude'],
                        'is_village'   => false,
                    ]
                );
            }
        }
    }
}
