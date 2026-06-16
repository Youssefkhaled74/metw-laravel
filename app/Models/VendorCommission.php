<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorCommission extends Model
{
    protected $fillable = [
        'vendor_id',
        'annual_subscription',
        'order_commission_percent',
        'order_commission_min',
        'annual_target_commission',
        'refund_fee_percent',
        'refund_fee_min',
        'is_default'
    ];

    protected $casts = [
        'annual_subscription' => 'decimal:2',
        'order_commission_percent' => 'decimal:2',
        'order_commission_min' => 'decimal:2',
        'annual_target_commission' => 'decimal:2',
        'refund_fee_percent' => 'decimal:2',
        'refund_fee_min' => 'decimal:2',
        'is_default' => 'boolean'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // Get the commission for a specific vendor or default
    public static function getForVendor($vendorId)
    {
        $vendorCommission = self::where('vendor_id', $vendorId)->first();

        if (!$vendorCommission) {
            $vendorCommission = self::where('is_default', true)->first();
        }

        return $vendorCommission;
    }
}
