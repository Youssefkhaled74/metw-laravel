<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCoverage extends Model
{
    use HasFactory;
    protected $fillable = [
        'shipment_company_id',
        'location_id',
        'pickup_available',
        'delivery_available',
        'eta_min_days',
        'eta_max_days',
        'eta_price',
        'notes',
    ];
    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
