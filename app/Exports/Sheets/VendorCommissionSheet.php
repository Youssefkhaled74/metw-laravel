<?php

namespace App\Exports\Sheets;

use App\Models\Vendor;
use App\Models\VendorCommission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorCommissionSheet implements FromCollection, WithHeadings
{
    protected $vendor;

    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    public function collection()
    {
        $commission = VendorCommission::where('vendor_id', $this->vendor->id)->first()
                     ?? VendorCommission::whereNull('vendor_id')->first();

        if (! $commission) {
            return collect();
        }

        return collect([
            [
                'Type'                       => $commission->vendor_id ? 'Custom' : 'Public',
                'Annual Subscription'        => $commission->annual_subscription,
                'Order Commission Percent'   => $commission->order_commission_percent,
                'Minimum Order Commission'   => $commission->order_commission_min,
                'Refund Fee Percent'         => $commission->refund_fee_percent,
                'Minimum Refund Fee'         => $commission->refund_fee_min,
                'Annual Target Commission'   => $commission->annual_target_commission,
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Type',
            'Annual Subscription',
            'Order Commission Percent',
            'Minimum Order Commission',
            'Refund Fee Percent',
            'Minimum Refund Fee',
            'Annual Target Commission',
        ];
    }
}
