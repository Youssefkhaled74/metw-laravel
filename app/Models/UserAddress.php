<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'user_id',
        'city_id',
        'street_name',
        'building',
        'floor',
        'landmark',
        'address_type',
        'latitude',
        'longitude',
        'country_id',
        'state_id',
        'zone_id',
        'is_default',
        'is_village'
    ];
    protected $casts = [
        'is_default'=>'boolean'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function orders()
    {
        return $this->hasMany(EcommerceOrder::class);
    }

    public function getFullAddressAttribute()
    {
        // تحديد اللغة الحالية (من الـ app locale)
        $lang = app()->getLocale(); // هتكون 'ar' أو 'en'

        $parts = [];

        if ($this->state) {
            $parts[] = $this->state->{'name_'.$lang} ?? '';
        }

        if ($this->city) {
            $parts[] = $this->city->{'name_'.$lang} ?? '';
        }

        if ($this->zone) {
            $parts[] = $this->zone->{'name_'.$lang} ?? '';
        }

        if ($this->street_name) {
            $parts[] = $this->street_name;
        }

        if ($this->building) {
            $parts[] = 'Building: ' . $this->building;
        }

        if ($this->floor) {
            $parts[] = 'Floor: ' . $this->floor;
        }

        if ($this->landmark) {
            $parts[] = 'Near: ' . $this->landmark;
        }

        // دمج كل الأجزاء بعلامة فاصلة
        return implode(', ', array_filter($parts));
    }
}
