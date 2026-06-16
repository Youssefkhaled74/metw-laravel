<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'ecommerce_order_id',
        'ecommerce_order_item_id',
        'amount',
        'payment_method',
        'notes',
        'reference_number',
        'admin_id'
    ];

    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'ecommerce_order_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(EcommerceOrderItem::class, 'ecommerce_order_item_id');
    }

    public function admin()
    {
        return $this->belongsTo(Employee::class, 'admin_id');
    }
}
