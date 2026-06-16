<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorCommission;
use Illuminate\Database\Seeder;

class VendorCommissionSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();

        $commissions = [
            ['annual_subscription' => 0, 'order_commission_percent' => 5.00, 'order_commission_min' => 10, 'annual_target_commission' => 0, 'refund_fee_percent' => 2.50, 'refund_fee_min' => 5, 'is_default' => true],
            ['annual_subscription' => 5000, 'order_commission_percent' => 3.00, 'order_commission_min' => 5, 'annual_target_commission' => 2.00, 'refund_fee_percent' => 1.50, 'refund_fee_min' => 3, 'is_default' => false],
        ];

        foreach ($vendors as $i => $vendor) {
            $idx = $i < count($commissions) ? $i : 0;
            VendorCommission::updateOrCreate(
                ['vendor_id' => $vendor->id],
                $commissions[$idx]
            );
        }
    }
}
