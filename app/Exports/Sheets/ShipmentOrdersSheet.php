<?php

namespace App\Exports\Sheets;

use App\Models\Order;
use App\Models\ShipmentCompany;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShipmentOrdersSheet implements FromCollection, WithHeadings
{
    protected $company;

    public function __construct(ShipmentCompany $company)
    {
        $this->company = $company;
    }

    public function collection()
    {
        return Order::where('shipment_company_id', $this->company->id)
            ->with('user')
            ->get()
            ->map(function ($order) {
                return [
                    'Order ID'        => $order->id,
                    'Order Number'   => $order->order_number,
                    'Customer'       => $order->user?->name,
                    'Status'         => $order->status->name,
                    'Total Price'    => $order->total_price,
                    'Paid Amount'    => $order->paid_amount,
                    'Remaining'      => $order->remaining_amount,
                    'Payment Status' => $order->payment_status,
                    'Created At'     => $order->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Order Number',
            'Customer',
            'Status',
            'Total Price',
            'Paid Amount',
            'Remaining',
            'Payment Status',
            'Created At',
        ];
    }
}
