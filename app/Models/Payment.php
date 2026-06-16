<?php

namespace App\Models;

use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'payment_status',
        'promo_code_id',
        'discount_price',
        'final_price',
    ];

    protected $casts = [
        'total_amount'         => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'payment_status' => PaymentStatus::class,
        'payment_method' => PaymentMethod::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }
}
