<?php

namespace App\Models;

use App\Enum\CourierRequestType;
use App\Enum\RequestPathStatus;
use App\Enum\RequestPathType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RequestPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'pathable_type',
        'pathable_id',
        'type',
        'request_type',
        'status',
        'legs',
        'total_cost',
        'currency',
        'failure_reason',
        'courier_confirmed_at',
        'submitted_to_client_at',
        'client_selected_at',
        'execution_started_at',
        'executed_at',
        'failed_at',
        'metadata',
    ];

    protected $casts = [
        'type' => RequestPathType::class,
        'request_type' => CourierRequestType::class,
        'status' => RequestPathStatus::class,
        'legs' => 'array',
        'total_cost' => 'decimal:2',
        'courier_confirmed_at' => 'datetime',
        'submitted_to_client_at' => 'datetime',
        'client_selected_at' => 'datetime',
        'execution_started_at' => 'datetime',
        'executed_at' => 'datetime',
        'failed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function pathable(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CourierAssignment::class, 'request_path_id');
    }

    public function advancePayments(): HasMany
    {
        return $this->hasMany(AdvancePayment::class);
    }

    public function selectedPathRequest(): BelongsTo
    {
        return $this->belongsTo(ShipmentRequest::class, 'selected_request_path_id');
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status?->value ?? $this->status, [
            RequestPathStatus::COURIER_CONFIRMED->value,
            RequestPathStatus::SUBMITTED_TO_CLIENT->value,
            RequestPathStatus::CLIENT_SELECTED->value,
            RequestPathStatus::EXECUTING->value,
            RequestPathStatus::EXECUTED->value,
        ], true);
    }

    public function isSettled(): bool
    {
        return ! in_array($this->status?->value ?? $this->status, [
            RequestPathStatus::MATCHING->value,
        ], true);
    }

    /**
     * Whether every leg of this path currently has an accepted courier.
     */
    public function hasConfirmedCourierForEveryLeg(): bool
    {
        $legs = $this->legs ?? [];

        if (empty($legs)) {
            return false;
        }

        foreach ($legs as $leg) {
            $hasAccepted = $this->assignments()
                ->where('leg_type', $leg)
                ->where('status', \App\Enum\CourierAssignmentStatus::ACCEPTED->value)
                ->exists();

            if (! $hasAccepted) {
                return false;
            }
        }

        return true;
    }
}
