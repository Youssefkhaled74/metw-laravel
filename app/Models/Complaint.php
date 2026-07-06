<?php

namespace App\Models;

use App\Enum\ComplaintStatus;
use App\Enum\ComplaintType;
use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory, GeneratesPrefixedNumber;

    protected $fillable = [
        'complaint_number',
        'complaint_type',
        'status',
        'user_id',
        'complaintable_type',
        'complaintable_id',
        'subject',
        'description',
        'reason',
        'admin_notes',
        'resolved_at',
    ];

    protected $casts = [
        'complaint_type' => ComplaintType::class,
        'status' => ComplaintStatus::class,
        'resolved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('complaint_number', 'CMP');
    }

    public function complaintable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value]);
    }
}
