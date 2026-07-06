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
        'generated_address_number',
        'address_name',
        'district_or_village_name',
        'district_or_village_type',
        'street_name',
        'branch_from_street',
        'building_number',
        'building',
        'floor_number',
        'floor',
        'building_name',
        'nearby_landmark',
        'landmark',
        'address_description',
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

    public function getFullAddressAttribute(): string
    {
        return implode('، ', array_filter([
            'محافظة: ' . ($this->governorate?->name_ar ?? $this->governorate?->name ?? ''),
            'مدينة: ' . ($this->city?->name_ar ?? $this->city?->name_en ?? $this->city?->name ?? ''),
            $this->district_or_village_name ? 'حي/قرية: ' . $this->district_or_village_name : null,
            $this->street_name ? 'شارع: ' . $this->street_name : null,
            $this->branch_from_street ? 'من شارع: ' . $this->branch_from_street : null,
            $this->building_number ? 'عمارة: ' . $this->building_number : null,
            $this->floor_number ? 'دور: ' . $this->floor_number : null,
            $this->building_name ? 'اسم العمارة: ' . $this->building_name : null,
            $this->nearby_landmark ? 'بالقرب من: ' . $this->nearby_landmark : null,
            $this->address_description ? 'وصف العنوان: ' . $this->address_description : null,
        ]));
    }
}
