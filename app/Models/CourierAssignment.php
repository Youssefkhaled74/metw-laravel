<?php

namespace App\Models;

use App\Enum\CourierAssignmentStatus;
use App\Enum\RequestLegType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CourierAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignable_type',
        'assignable_id',
        'leg_type',
        'representative_id',
        'status',
        'sort_order',
        'offered_at',
        'responded_at',
        'response_deadline_at',
        'window_opens_at',
        'window_closes_at',
        'auto_reject_working_hours',
        'response_window_working_hours',
        'auto_action',
        'auto_action_executed_at',
        'rejection_reason_id',
        'rejection_note',
        'metadata',
    ];

    protected $casts = [
        'leg_type' => RequestLegType::class,
        'status' => CourierAssignmentStatus::class,
        'offered_at' => 'datetime',
        'responded_at' => 'datetime',
        'response_deadline_at' => 'datetime',
        'window_opens_at' => 'datetime',
        'window_closes_at' => 'datetime',
        'auto_action_executed_at' => 'datetime',
        'auto_reject_working_hours' => 'integer',
        'response_window_working_hours' => 'integer',
        'metadata' => 'array',
    ];

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(Representative::class);
    }

    public function rejectionReason(): BelongsTo
    {
        return $this->belongsTo(RejectionReason::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', CourierAssignmentStatus::PENDING->value);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            CourierAssignmentStatus::PENDING->value,
            CourierAssignmentStatus::ACCEPTED->value,
        ]);
    }
}
