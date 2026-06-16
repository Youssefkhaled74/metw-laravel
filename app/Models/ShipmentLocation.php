<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentLocation extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'shipment_company_id',
        'country',
        'state',
        'city',
        'zone',
        'is_active',
    ];

    protected $casts = [
        'country' => 'array',
        'state' => 'array',
        'city' => 'array',
        'zone' => 'array',
    ];

    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class, 'shipment_company_id');
    }

    public function getCountryIdsAttribute()
    {
        return $this->country ?? [];
    }

    public function getStateIdsAttribute()
    {
        return $this->state ?? [];
    }


    public function stateModel()
    {
        return $this->belongsTo(State::class, 'state->id');
    }

    public function cityModel()
    {
        return $this->belongsTo(City::class, 'city->id');
    }

    public function zoneModel()
    {
        return $this->belongsTo(Zone::class, 'zone->id');
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
