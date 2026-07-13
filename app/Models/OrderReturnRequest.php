<?php

namespace App\Models;

use App\Enum\ReturnRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnRequest extends Model
{
    use HasFactory;

    protected $table = 'order_return_requests';

    protected $fillable = [
        'order_id',
        'representative_id',
        'user_id',
        'reason_id',
        'custom_reason_text',
        'status',
        'admin_notes',
        'rejection_reason',
        'whatsapp_deep_link',
        'refund_amount',
        'shipping_fees',
        'return_fees',
        'net_refund',
        'completed_at',
    ];

    protected $casts = [
        'status' => ReturnRequestStatus::class,
        'completed_at' => 'datetime',
        'refund_amount' => 'decimal:2',
        'shipping_fees' => 'decimal:2',
        'return_fees' => 'decimal:2',
        'net_refund' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reason()
    {
        return $this->belongsTo(ReturnReason::class, 'reason_id');
    }

    public function complaints()
    {
        return $this->hasMany(ReturnComplaint::class, 'return_request_id');
    }

    public function logs()
    {
        return $this->hasMany(ReturnLog::class, 'return_request_id');
    }
}
