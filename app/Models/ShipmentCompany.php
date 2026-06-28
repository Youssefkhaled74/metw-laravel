<?php

namespace App\Models;

use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Notifications\Notifiable;

class ShipmentCompany extends Authenticatable
{
    use HasFactory, SoftDeletes , Notifiable, GeneratesPrefixedNumber;

    protected $fillable = [
        'company_number',
        'name',
        'address',
        'phone',
        'email',
        'password',
        'description',
        'logo',
        'is_active',
        'facebook_url',
        'whatsapp_url',
        'price_per_km',
        'est_days',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'est_date' => 'datetime',
        'price_per_km' => 'decimal:2',
    ];

    protected $hidden = [
        'password',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function cartitmes()
    {
        return $this->hasMany(Cart::class);
    }
    public function ecommerceOrders()
    {
        return $this->hasMany(EcommerceOrder::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    // public function favourites()
    // {
    //     return $this->hasMany(Favourite::class, 'shipment_company_id');
    // }
    public function favourites()
    {
        return $this->morphMany(Favourite::class, 'favouriteable');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function coverages()
    {
        return $this->belongsToMany(Location::class, 'company_coverages')
            ->withPivot(['pickup_available', 'delivery_available', 'eta_min_days', 'eta_max_days', 'eta_price', 'notes'])
            ->withTimestamps();
    }

    public function shipmentLocations()
    {
        return $this->hasMany(ShipmentLocation::class, 'shipment_company_id');
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) =>
            blank($value) ? ($this->attributes['password'] ?? null)
                : (Hash::needsRehash($value) ? Hash::make($value) : $value)
        );
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function isFavourite(): bool
    {
        $userId = null;

        if (request()->bearerToken()) {
            try {
                $accessToken = PersonalAccessToken::findToken(request()->bearerToken());
                $userId = $accessToken?->tokenable_id;
            } catch (\Exception $e) {
                $userId = null;
            }
        }

        return $userId
            ? $this->favourites()->where('user_id', $userId)->exists()
            : false;
    }

    public function locations()
    {
        return $this->hasMany(ShipmentLocation::class, 'shipment_company_id');
    }

    public function accountProfile()
    {
        return $this->morphOne(AccountProfile::class, 'profileable');
    }

    public function foundationAddresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }

    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('company_number', 'SHC');

        static::addGlobalScope('active', function ($q) {
            $q->where('is_active', true);
        });
    }

    public function commission()
    {
        return $this->hasOne(ShipmentCommission::class);
    }

}
