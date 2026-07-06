<?php

namespace App\Models;

use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorBranch extends Model
{
    use HasFactory, SoftDeletes, GeneratesPrefixedNumber;

    protected $fillable = [
        'branch_number',
        'name',
        'vendor_id',
        'governorate_id',
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
        'address_description',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'status' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class)->withTrashed();
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
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

    public function foundationAddresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    protected static function booted(): void
    {
        static::assignPrefixedNumberOnCreate('branch_number', 'BR');
    }
}
