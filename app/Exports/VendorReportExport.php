<?php

namespace App\Exports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VendorReportExport implements WithMultipleSheets
{
    protected $vendor;

    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    public function sheets(): array
    {
        return [
            new Sheets\VendorOrdersSheet($this->vendor),
            new Sheets\VendorCommissionSheet($this->vendor),
        ];
    }
}
