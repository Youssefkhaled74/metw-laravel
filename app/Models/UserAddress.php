<?php

namespace App\Models;

use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory, SoftDeletes, GeneratesPrefixedNumber;

    protected $fillable = [
        'user_id',
        'generated_address_number',
        'address_name',
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
        'country_id',
        'state_id',
        'governorate_id',
        'zone_id',
        'is_default',
        'is_village',
    ];

    protected $casts = [
        'is_default' => 'boolean',
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

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
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

    public function getFullAddressAttribute(): string
    {
        return implode('، ', array_filter([
            'محافظة: ' . ($this->governorate?->name_ar ?? $this->state?->name_ar ?? $this->governorate?->name ?? ''),
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

    protected static function booted(): void
    {
        static::assignPrefixedNumberOnCreate('generated_address_number', 'ADR');
    }
}
