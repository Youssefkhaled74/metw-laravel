<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'addressable_type',
        'addressable_id',
        'label',
        'type',
        'contact_name',
        'contact_phone',
        'country_id',
        'state_id',
        'governorate_id',
        'city_id',
        'zone_id',
        'postal_code',
        'address_line_1',
        'address_line_2',
        'street_name',
        'building',
        'floor',
        'landmark',
        'latitude',
        'longitude',
        'is_primary',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
