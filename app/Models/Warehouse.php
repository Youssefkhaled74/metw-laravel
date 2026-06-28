<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الأعمدة المسموح بملئها
     */
    protected $fillable = [
        'name',
        'phone',
        'country_id',
        'state_id',
        'city_id',
        'zone_id',
        'street_name',
        'building',
        'floor',
        'landmark',
        'address_type',
        'latitude',
        'longitude',
        'is_main',
    ];

    /**
     * العلاقات (Relationships)
     */

    // الدولة
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // المحافظة
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    // المدينة
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // المنطقة (Zone)
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    // لو عايز تربطه بالأوردرات فيما بعد
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

    /**
     * Accessors / Mutators (اختياري)
     * لو عايز تنسّق الاسم أو العنوان قبل العرض
     */
    public function getFullAddressAttribute(): string
    {
        $locale = app()->getLocale();

        $parts = array_filter([
            $this->street_name,
            $this->building,
            $this->floor ? 'Floor ' . $this->floor : null,
            $this->landmark,

            // هنا بقى نختار الاسم بناءً على اللغة الحالية
            $this->zone ? ($locale === 'ar' ? $this->zone->name_ar : $this->zone->name_en) : null,
            $this->city ? ($locale === 'ar' ? $this->city->name_ar : $this->city->name_en) : null,
            $this->state ? ($locale === 'ar' ? $this->state->name_ar : $this->state->name_en) : null,
            $this->country ? ($locale === 'ar' ? $this->country->name_ar : $this->country->name_en) : null,
        ]);

        return implode(', ', $parts);
    }
}
