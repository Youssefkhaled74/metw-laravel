<?php

namespace App\Models;

use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes, GeneratesPrefixedNumber;

    protected $fillable = [
        'warehouse_number',
        'name',
        'phone',
        'country_id',
        'governorate_id',
        'state_id',
        'city_id',
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
        'address_type',
        'latitude',
        'longitude',
        'is_main',
    ];

    protected static function booted(): void
    {
        static::assignPrefixedNumberOnCreate('warehouse_number', 'WAR');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function foundationAddresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    public function representatives()
    {
        return $this->hasMany(Representative::class);
    }

    public function businessProfile()
    {
        return $this->hasOne(WarehouseBusinessProfile::class);
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
