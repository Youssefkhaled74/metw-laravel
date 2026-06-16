<?php

namespace App\Models;

use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class Vendor extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens, Notifiable, GeneratesPrefixedNumber;

    protected $fillable = [
        'vendor_number',
        'name',
        'email',
        'phone',
        'password',
        'address',
        'latitude',
        'longitude',
        'logo',
        'email_verified',
        'phone_verified',
        'is_active',
        'fcm_token',
        'country_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function commission()
    {
        return $this->hasOne(VendorCommission::class);
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function branches()
    {
        return $this->hasMany(VendorBranch::class);
    }

    public function ecommerceOrderItems()
    {
        return $this->hasManyThrough(
            EcommerceOrderItem::class, // final model
            Product::class,            // intermediate model
            'vendor_id',               // Foreign key on products table
            'product_id',              // Foreign key on ecommerce_order_items table
            'id',                      // Local key on vendors table
            'id'                       // Local key on products table
        );
    }
    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('vendor_number', 'VDR');

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }
    //active scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
