<?php

namespace App\Models;

use App\Enum\AdvancePaymentStatus;
use App\Enum\SubmitterType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class AdvancePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_type',
        'payable_id',
        'request_path_id',
        'submitter_type',
        'submitter_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'reference',
        'notes',
        'confirmed_by',
        'confirmed_at',
        'rejected_at',
        'refunded_at',
        'metadata',
    ];

    protected $casts = [
        'submitter_type' => SubmitterType::class,
        'status' => AdvancePaymentStatus::class,
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'refunded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function requestPath(): BelongsTo
    {
        return $this->belongsTo(RequestPath::class);
    }

    /**
     * The request submitter (User from Shipping App or Seller from Market App).
     * Resolved dynamically from the enum type without a global morph map.
     */
    public function submitter(): BelongsTo
    {
        $type = $this->submitter_type?->value ?? $this->submitter_type;
        $class = $type === SubmitterType::SELLER->value ? \App\Models\Vendor::class : \App\Models\User::class;

        return $this->belongsTo($class, 'submitter_id');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'confirmed_by');
    }

    public function isConfirmed(): bool
    {
        return ($this->status?->value ?? $this->status) === AdvancePaymentStatus::CONFIRMED->value;
    }
}
