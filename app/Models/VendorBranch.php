<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorBranch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'vendor_id',
        'state_id',
        'city_id',
        'zone_id',
        'street_main',
        'street_sub',
        'building',
        'building_name',
        'floor',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'status' => 'boolean',
    ];

    /**
     * 🔹 Vendor: الفرع تابع لأي تاجر
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * 🔹 State: المحافظة
     */
    public function state()
    {
        return $this->belongsTo(State::class)->withTrashed();
    }

    /**
     * 🔹 City: المدينة
     */
    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    /**
     * 🔹 Zone: المنطقة
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class)->withTrashed();
    }

    /**
     * 🔹 Accessor لعرض العنوان الكامل (اختياري)
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->street_main,
            $this->street_sub,
            $this->building_name ?? 'Building ' . $this->building,
            $this->floor ? 'Floor ' . $this->floor : null,
            optional($this->zone)->name_en ?? optional($this->city)->name_en ?? optional($this->state)->name_en,
        ];

        return implode(', ', array_filter($parts));
    }

    public function foundationAddresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

}
