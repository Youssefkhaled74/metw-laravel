<?php

namespace App\Models;

use App\Enum\ReturnStatus;
use App\Enum\ReturnReason;
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
        'status',
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
        'vendor_refund_commission_total', // مجموع عمولة المرتجع على الفيندور
        'vendor_deduction_total',         // الخصم الفعلي من الفيندور
        'return_shipping_total',          // مجموع تكلفة المرتجع للشحن
        'shipment_commission_total',      // مجموع عمولة شركة الشحن
        'shipment_net_total',             // مجموع صافي شركة الشحن
        'shipping_paid_by',               // مين دفع الشحن: customer/vendor/platform
    ];

    protected $casts = [
        'status' => ReturnStatus::class,
        // 'reason' => ReturnReason::class,
        'pickup_date' => 'date',
        'refunded_at' => 'datetime',
        'cancel_reason_ids' => 'array',
    ];

    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('return_number', 'RET');
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

}
