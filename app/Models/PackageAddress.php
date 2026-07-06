<?php

namespace App\Models;

use App\Enum\AddressType;
use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageAddress extends Model
{
    use HasFactory, GeneratesPrefixedNumber;

    protected $fillable = [
        'generated_address_number',
        'address_name',
        'location',
        'landmark',
        'phone',
        'address',
        'latitude',
        'longitude',
        'type',
        'district_or_village_name',
        'district_or_village_type',
        'street_name',
        'branch_from_street',
        'building_number',
        'floor_number',
        'building_name',
        'nearby_landmark',
        'address_description',
        'city_id',
        'state_id',
        'governorate_id',
        'country_id',
        'zone_id',
        'user_id',
        'is_saved', // Add this
    ];

    protected $casts = [
        'type' => AddressType::class,
        'is_saved' => 'boolean', // Add this
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function city()    { return $this->belongsTo(City::class); }
    public function state()   { return $this->belongsTo(State::class); }
    public function country() { return $this->belongsTo(Country::class); }
    public function zone()    { return $this->belongsTo(Zone::class); }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    // Scope for saved addresses
    public function scopeOnlySaved($query)
    {
        return $query->where('is_saved', true);
    }


    // Scope for specific user
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? optional(auth())->id();

        if (!$userId) {
            return $query->whereRaw('1 = 0'); // return no results when no user
        }

        return $query->where('user_id', $userId);
    }


    // Scope by address type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function pickupPackages()
    {
        return $this->hasMany(Package::class, 'pickup_address_id');
    }

    // Packages where this address is dropoff
    public function dropoffPackages()
    {
        return $this->hasMany(Package::class, 'dropoff_address_id');
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
        static::assignPrefixedNumberOnCreate('generated_address_number', 'PDA');
    }
}
