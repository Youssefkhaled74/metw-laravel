<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\ShipmentCompany;
use App\Models\ShipmentCommission;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ShipmentCompanyReportExport implements WithMultipleSheets
{
    protected $company;

    public function __construct(ShipmentCompany $company)
    {
        $this->company = $company;
    }

    public function sheets(): array
    {
        return [
            new Sheets\ShipmentOrdersSheet($this->company),
            new Sheets\ShipmentCommissionSheet($this->company),
        ];
    }
}
