<?php

namespace Database\Seeders;

use App\Models\ShipmentCompany;
use App\Models\Location;
use App\Models\CompanyCoverage;
use Illuminate\Database\Seeder;

class CompanyCoverageSeeder extends Seeder
{
    public function run(): void
    {
        $companies = ShipmentCompany::all();
        $locations = Location::where('type', 'city')->get();

        foreach ($companies as $company) {
            foreach ($locations as $location) {
                CompanyCoverage::updateOrCreate(
                    [
                        'shipment_company_id' => $company->id,
                        'location_id'         => $location->id,
                    ],
                    [
                        'pickup_available'   => true,
                        'delivery_available' => true,
                        'eta_min_days'       => 1,
                        'eta_max_days'       => 3,
                        'eta_price'          => 0,
                        'notes'              => null,
                    ]
                );
            }
        }
    }
}
