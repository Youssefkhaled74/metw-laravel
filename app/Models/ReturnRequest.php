<?php

namespace App\Models;

use App\Enum\CancellationSellerStatus;
use App\Enum\ReturnStatus;
use App\Enum\ReturnReason;
use App\Enum\RequestType;
use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ReturnRequest extends Model
{
    use HasFactory, GeneratesPrefixedNumber;

    protected $fillable = [
        'user_id',
        'ecommerce_order_id',
        'return_number',
        'request_type',
        'status',
        'seller_status',
        'seller_rejection_reason',
        'return_rejection_reason',
        'admin_reactivation_reason',
        'reactivated_at',
        'inspected_at',
        'admin_refund_amount',
        'wallet_credited_at',
        'reason',
        'other_reason',
        'notes',
        'refund_amount',
        'pickup_address',
        'pickup_phone',
        'pickup_date',
        'refunded_at',
        'pickup_address_id',
        'cancel_reason_ids',
        'refund_type',

        // ===== إضافات مالية =====
        'vendor_refund_commission_total',
        'vendor_deduction_total',
        'return_shipping_total',
        'shipment_commission_total',
        'shipment_net_total',
        'shipping_paid_by',
    ];

    protected $casts = [
        'status' => ReturnStatus::class,
        'seller_status' => CancellationSellerStatus::class,
        'request_type' => RequestType::class,
        'pickup_date' => 'date',
        'refunded_at' => 'datetime',
        'reactivated_at' => 'datetime',
        'inspected_at' => 'datetime',
        'wallet_credited_at' => 'datetime',
        'cancel_reason_ids' => 'array',
        'admin_refund_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! empty($model->return_number)) {
                return;
            }

            $requestType = $model->request_type instanceof RequestType
                ? $model->request_type->value
                : (string) $model->request_type;

            $prefix = $requestType === RequestType::CANCELLATION->value
                ? 'M-CAN'
                : 'RET';

            $model->return_number = static::generateSequentialNumber('return_number', $prefix);
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'ecommerce_order_id');
    }

    public function items()
    {
        return $this->hasMany(ReturnRequestItem::class);
    }

    public function scopeReturns($query)
    {
        return $query->where('request_type', RequestType::RETURN->value);
    }

    public function scopeCancellations($query)
    {
        return $query->where('request_type', RequestType::CANCELLATION->value);
    }

    // public function generateReturnNumber(): string
    // {
    //     return DB::transaction(function () {

    //         $prefix = 'RET';

    //         $last = static::lockForUpdate()
    //             ->whereNotNull('return_number')
    //             ->orderByDesc('id')
    //             ->value('return_number');

    //         $next = 1;

    //         if ($last) {
    //             // RET-00000001 → ناخد الرقم بس
    //             $lastNumber = (int) substr($last, strlen($prefix) + 1);
    //             $next = $lastNumber + 1;
    //         }

    //         return $prefix . '-' . str_pad($next, 8, '0', STR_PAD_LEFT);
    //     });
    // }

    public function calculateRefundAmount()
    {
        return $this->items->sum('return_price');
    }

    public function pickupaddress(){
        return $this->belongsTo(UserAddress::class,'pickup_address_id');
    }

    public function canBeReturned()
    {
        return in_array($this->status, [ReturnStatus::REQUESTED, ReturnStatus::PICKUP]);
    }
    public function cancelReasons()
    {
        return CancelReason::whereIn('id', $this->cancel_reason_ids ?? [])->get();
    }
    public function cashBack()
    {
        return $this->hasOne(ReturnCashBack::class, 'return_id');
    }

    public function complaints()
    {
        return $this->morphMany(Complaint::class, 'complaintable');
    }

    public function scopeSellerApproved($query)
    {
        return $query->where('seller_status', CancellationSellerStatus::APPROVED->value);
    }

    public function scopePendingAdminCompletion($query)
    {
        return $query->cancellations()
            ->where('seller_status', CancellationSellerStatus::APPROVED->value)
            ->where('status', ReturnStatus::REQUESTED->value);
    }

    public function scopeWithComplaints($query)
    {
        return $query->cancellations()
            ->whereHas('complaints', function ($q) {
                $q->whereIn('status', ['pending', 'under_review']);
            });
    }

    public function sellerStatusLabel(): string
    {
        if (! $this->seller_status) {
            return 'بانتظار رد البائع';
        }

        return $this->seller_status instanceof CancellationSellerStatus
            ? $this->seller_status->label()
            : $this->seller_status;
    }

    public function sellerStatusCss(): string
    {
        if (! $this->seller_status) {
            return 'secondary';
        }

        return $this->seller_status instanceof CancellationSellerStatus
            ? $this->seller_status->cssClass()
            : 'secondary';
    }
}
