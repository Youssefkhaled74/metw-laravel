<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentCommission extends Model
{
    protected $fillable = [
        'shipment_company_id',
        'annual_subscription',
        'shipment_commission_percent',
        'shipment_commission_min',
        'annual_target',
    ];

    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class);
    }
}
