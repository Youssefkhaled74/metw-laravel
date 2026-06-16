<?php

namespace Database\Seeders;

use App\Models\ShipmentCompany;
use App\Models\ShipmentLocation;
use App\Models\State;
use App\Models\City;
use Illuminate\Database\Seeder;

class ShipmentLocationSeeder extends Seeder
{
    public function run(): void
    {
        $companies = ShipmentCompany::all();

        $locations = [
            ['state_en' => 'Cairo', 'cities' => ['Heliopolis', 'Nasr City']],
            ['state_en' => 'Giza', 'cities' => ['Dokki', 'Mohandessin']],
            ['state_en' => 'Alexandria', 'cities' => ['Sidi Gaber', 'Smouha']],
        ];

        foreach ($companies as $company) {
            foreach ($locations as $loc) {
                $state = State::where('name_en', $loc['state_en'])->first();
                if (!$state) continue;

                ShipmentLocation::updateOrCreate(
                    [
                        'shipment_company_id' => $company->id,
                        'state' => json_encode([$state->id]),
                    ],
                    [
                        'country' => json_encode([1]),
                        'city'    => json_encode(
                            City::whereIn('name_en', $loc['cities'])->pluck('id')->toArray()
                        ),
                        'zone'    => json_encode([]),
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
