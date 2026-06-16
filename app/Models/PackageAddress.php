<?php

namespace App\Models;

use App\Enum\AddressType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'landmark',
        'phone',
        'address',
        'latitude',
        'longitude',
        'type',
        'city_id',
        'state_id',
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
}
