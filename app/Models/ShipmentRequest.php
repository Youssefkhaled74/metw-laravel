<?php

namespace App\Models;

use App\Enum\ShipmentRequestStatus;
use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentRequest extends Model
{
    use HasFactory, SoftDeletes, GeneratesPrefixedNumber;

    protected $fillable = [
        'user_id',
        'request_number',
        'sender_contact_id',
        'receiver_contact_id',
        'status',
        'notes',
        'submitted_at',
        'metadata',
    ];

    protected $casts = [
        'status' => ShipmentRequestStatus::class,
        'submitted_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function senderContact()
    {
        return $this->belongsTo(ShipmentContact::class, 'sender_contact_id');
    }

    public function receiverContact()
    {
        return $this->belongsTo(ShipmentContact::class, 'receiver_contact_id');
    }

    public function packages()
    {
        return $this->hasMany(ShipmentRequestPackage::class);
    }

    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('request_number', 'SHR');
    }
}
