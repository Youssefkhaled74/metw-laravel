<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, GeneratesPrefixedNumber;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_number',
        'username',
        'email',
        'password',
        'phone',
        'country_code',
        'notifications_enabled',
        'image',
        'phone_verified_at',
        'email_verified_at',
        'fcm_token',
        'default_lang',
        'enable_shipment_notifications',
        'fcm_token_shipment',
        'default_shipment_lang',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function OtpCode()
    {
        return $this->hasMany(OtpCode::class);
    }
    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
    public function ecommerceCarts()
    {
        return $this->hasMany(EcommerceCart::class);
    }
    public function ecommerceOrders()
    {
        return $this->hasMany(EcommerceOrder::class);
    }
    public function recentViews()
    {
        return $this->hasMany(RecentView::class);
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) =>
            blank($value) ? ($this->attributes['password'] ?? null)
                : (Hash::needsRehash($value) ? Hash::make($value) : $value)
        );
    }
    // في موديل User
    public function getFullAddressAttribute(): ?string
    {
        // هات العنوان الافتراضي أو أول عنوان لو مفيش افتراضي
        $address = $this->addresses()
            ->where('is_default', true)
            ->first() ?? $this->addresses()->first();

        if (! $address) {
            return null;
        }

        // تحديد اللغة الحالية
        $lang = app()->getLocale();

        $parts = [
            optional($address->country)->{"name_{$lang}"} ?? '',
            optional($address->state)->{"name_{$lang}"} ?? '',
            optional($address->city)->{"name_{$lang}"} ?? '',
            optional($address->zone)->{"name_{$lang}"} ?? '',
            $address->street_name,
            __('messages.building') . ' ' . $address->building,
            __('messages.floor') . ' ' . $address->floor,
            $address->landmark ? __('messages.landmark') . ': ' . $address->landmark : null,
        ];

        return implode(' - ', array_filter($parts));
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
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

    public function representative()
    {
        return $this->hasOne(Representative::class);
    }

    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('user_number', 'USR');
    }

}
