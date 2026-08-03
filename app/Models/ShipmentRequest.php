<?php

namespace App\Models;

use App\Enum\CourierRequestType;
use App\Enum\DispatchState;
use App\Enum\ShipmentRequestStatus;
use App\Enum\ShippingType;
use App\Enum\SubmitterType;
use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentRequest extends Model
{
    use HasFactory, SoftDeletes, GeneratesPrefixedNumber;

    protected $fillable = [
        'user_id',
        'submitter_type',
        'submitter_id',
        'request_number',
        'sender_contact_id',
        'receiver_contact_id',
        'status',
        'shipping_type',
        'request_type',
        'is_fast_delivery',
        'courier_category',
        'total_weight',
        'total_volume',
        'has_village',
        'dispatch_state',
        'dispatch_note',
        'notes',
        'submitted_at',
        'metadata',
        'representative_id',
        'selected_request_path_id',
        'rejection_reason_id',
        'rejection_note',
        'failure_text',
        'paths_evaluated_at',
        'execution_started_at',
        'closed_at',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'status' => ShipmentRequestStatus::class,
        'shipping_type' => ShippingType::class,
        'request_type' => CourierRequestType::class,
        'submitter_type' => SubmitterType::class,
        'courier_category' => \App\Enum\CourierCategory::class,
        'dispatch_state' => DispatchState::class,
        'total_weight' => 'decimal:2',
        'total_volume' => 'decimal:2',
        'has_village' => 'boolean',
        'is_fast_delivery' => 'boolean',
        'submitted_at' => 'datetime',
        'metadata' => 'array',
        'paths_evaluated_at' => 'datetime',
        'execution_started_at' => 'datetime',
        'closed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The request submitter (User or Seller). Resolved dynamically from the enum.
     */
    public function submitter()
    {
        $type = $this->submitter_type?->value ?? $this->submitter_type;
        $class = $type === SubmitterType::SELLER->value ? Vendor::class : User::class;

        return $this->belongsTo($class, 'submitter_id');
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

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function rejectionReason()
    {
        return $this->belongsTo(RejectionReason::class);
    }

    public function assignments()
    {
        return $this->morphMany(CourierAssignment::class, 'assignable');
    }

    public function activeAssignments()
    {
        return $this->assignments()->active();
    }

    public function requestPaths()
    {
        return $this->morphMany(RequestPath::class, 'pathable');
    }

    public function selectedPath()
    {
        return $this->belongsTo(RequestPath::class, 'selected_request_path_id');
    }

    public function advancePayments()
    {
        return $this->morphMany(AdvancePayment::class, 'payable');
    }

    public function isFastDelivery(): bool
    {
        return ($this->request_type?->value ?? $this->request_type) === CourierRequestType::FAST_DELIVERY->value
            || $this->is_fast_delivery;
    }

    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('request_number', 'SHR');
    }
}
