<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceOrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'ecommerce_order_id',
        'return_request_id',
        'status',
        'refund_amount',
        'refunded_at',
    ];

    protected $casts = [
        'refunded_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'ecommerce_order_id');
    }

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }
}
