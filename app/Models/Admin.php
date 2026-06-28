<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'is_active',
        'password',
        'last_login_at',
        'photo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
    ];


    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) =>
                blank($value) ? ($this->attributes['password'] ?? null)
                              : (Hash::needsRehash($value) ? Hash::make($value) : $value)
        );
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
}
