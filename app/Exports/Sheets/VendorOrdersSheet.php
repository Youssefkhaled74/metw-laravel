<?php

namespace App\Exports\Sheets;

use App\Models\EcommerceOrder;
use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorOrdersSheet implements FromCollection, WithHeadings
{
    protected $vendor;

    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    public function collection()
    {
        return EcommerceOrder::whereHas('items.product', function ($query) {
                $query->where('vendor_id', $this->vendor->id);
            })
            ->with(['user', 'items.product'])
            ->get()
            ->map(function ($order) {
                return [
                    'Order ID'        => $order->id,
                    'Order Number'    => $order->order_number,
                    'Customer'        => $order->user?->name,
                    'Total Items'     => $order->items->count(),
                    'Total Price'     => $order->total_price,
                    'Paid Amount'     => $order->paid_amount,
                    'Remaining'       => $order->remaining_amount,
                    'Payment Status'  => $order->payment_status?->value, // <-- fix here
                    'Created At'      => $order->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Order Number',
            'Customer',
            'Total Items',
            'Total Price',
            'Paid Amount',
            'Remaining',
            'Payment Status',
            'Created At',
        ];
    }
}
