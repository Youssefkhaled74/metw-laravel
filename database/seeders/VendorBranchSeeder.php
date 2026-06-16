<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use App\Models\Vendor;
use App\Models\VendorBranch;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class VendorBranchSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all()->keyBy('email');

        $zoneByCity = [];
        foreach (Zone::all() as $zone) {
            $zoneByCity[$zone->city_id] = $zone->id;
        }

        $branches = [
            'vendor1@lasco.test' => [
                [
                    'name'     => 'Main Branch - Cairo',
                    'state_id' => fn() => State::where('name_en', 'Cairo')->value('id'),
                    'city_id'  => fn() => City::where('name_en', 'Nasr City')->value('id'),
                    'street_main' => 'Abbas El Akkad',
                    'latitude' => 30.0584,
                    'longitude' => 31.3232,
                    'status'   => true,
                ],
                [
                    'name'     => 'Alex Branch',
                    'state_id' => fn() => State::where('name_en', 'Alexandria')->value('id'),
                    'city_id'  => fn() => City::where('name_en', 'Smouha')->value('id'),
                    'street_main' => 'Smouha Main St',
                    'latitude' => 31.2001,
                    'longitude' => 29.9187,
                    'status'   => true,
                ],
            ],
            'vendor2@lasco.test' => [
                [
                    'name'     => 'TechMart HQ - Nasr City',
                    'state_id' => fn() => State::where('name_en', 'Cairo')->value('id'),
                    'city_id'  => fn() => City::where('name_en', 'Nasr City')->value('id'),
                    'street_main' => 'El Teseen St',
                    'latitude' => 30.0625,
                    'longitude' => 31.3322,
                    'status'   => true,
                ],
            ],
            'vendor3@lasco.test' => [
                [
                    'name'     => 'Fashion Hub - Mohandessin',
                    'state_id' => fn() => State::where('name_en', 'Giza')->value('id'),
                    'city_id'  => fn() => City::where('name_en', 'Mohandessin')->value('id'),
                    'street_main' => 'Lebanon St',
                    'latitude' => 30.0473,
                    'longitude' => 31.2065,
                    'status'   => true,
                ],
                [
                    'name'     => 'Fashion Hub - Heliopolis',
                    'state_id' => fn() => State::where('name_en', 'Cairo')->value('id'),
                    'city_id'  => fn() => City::where('name_en', 'Heliopolis')->value('id'),
                    'street_main' => 'El Merghany St',
                    'latitude' => 30.0914,
                    'longitude' => 31.3232,
                    'status'   => true,
                ],
            ],
        ];

        foreach ($branches as $email => $vendorBranches) {
            $vendor = $vendors->get($email);
            if (!$vendor) continue;

            foreach ($vendorBranches as $data) {
                $stateId = is_callable($data['state_id']) ? $data['state_id']() : $data['state_id'];
                $cityId = is_callable($data['city_id']) ? $data['city_id']() : $data['city_id'];

                VendorBranch::updateOrCreate(
                    ['vendor_id' => $vendor->id, 'name' => $data['name']],
                    [
                        'state_id'   => $stateId,
                        'city_id'    => $cityId,
                        'zone_id'    => $zoneByCity[$cityId] ?? null,
                        'street_main' => $data['street_main'],
                        'latitude'   => $data['latitude'],
                        'longitude'  => $data['longitude'],
                        'status'     => $data['status'],
                    ]
                );
            }
        }
    }
}
