<?php

namespace App\Models;

use App\Enum\ShipmentContactType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'contact_number',
        'full_name',
        'primary_mobile',
        'secondary_mobile',
    ];

    protected $casts = [
        'type' => ShipmentContactType::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foundationAddresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function primaryAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_primary', true);
    }

    public function sentShipmentRequests()
    {
        return $this->hasMany(ShipmentRequest::class, 'sender_contact_id');
    }

    public function receivedShipmentRequests()
    {
        return $this->hasMany(ShipmentRequest::class, 'receiver_contact_id');
    }
}
