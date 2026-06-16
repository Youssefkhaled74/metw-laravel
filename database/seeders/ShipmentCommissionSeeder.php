<?php

namespace Database\Seeders;

use App\Models\ShipmentCompany;
use App\Models\ShipmentCommission;
use Illuminate\Database\Seeder;

class ShipmentCommissionSeeder extends Seeder
{
    public function run(): void
    {
        $companies = ShipmentCompany::all();

        $commissions = [
            ['annual_subscription' => 0, 'shipment_commission_percent' => 8.00, 'shipment_commission_min' => 5, 'annual_target' => 0],
            ['annual_subscription' => 10000, 'shipment_commission_percent' => 6.00, 'shipment_commission_min' => 3, 'annual_target' => 500000],
        ];

        foreach ($companies as $i => $company) {
            $idx = $i < count($commissions) ? $i : 0;
            ShipmentCommission::updateOrCreate(
                ['shipment_company_id' => $company->id],
                $commissions[$idx]
            );
        }
    }
}
