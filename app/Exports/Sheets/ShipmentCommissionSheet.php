<?php

namespace App\Exports\Sheets;

use App\Models\ShipmentCompany;
use App\Models\ShipmentCommission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShipmentCommissionSheet implements FromCollection, WithHeadings
{
    protected $company;

    public function __construct(ShipmentCompany $company)
    {
        $this->company = $company;
    }

    public function collection()
    {
        $commission =
            ShipmentCommission::where('shipment_company_id', $this->company->id)->first()
            ?? ShipmentCommission::whereNull('shipment_company_id')->first();

        if (! $commission) {
            return collect();
        }

        return collect([
            [
                'Type'                       => $commission->shipment_company_id ? 'Custom' : 'Public',
                'Annual Subscription'        => $commission->annual_subscription,
                'Commission Percent'         => $commission->shipment_commission_percent,
                'Minimum Commission'         => $commission->shipment_commission_min,
                'Annual Target'              => $commission->annual_target,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'Type',
            'Annual Subscription',
            'Commission Percent',
            'Minimum Commission',
            'Annual Target',
        ];
    }
}
