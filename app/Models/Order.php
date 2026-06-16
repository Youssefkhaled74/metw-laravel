<?php

namespace App\Models;

use App\Enum\OrderStatus;
use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes , GeneratesPrefixedNumber;
    protected $fillable = [
        'user_id',
        'parent_id',
        'cart_id',
        'order_number',
        'total_price',
        'discount_price',
        'paid_amount',
        'remaining_amount',
        'shipment_company_id',
        'status',
        'final_price',
        'payment_status',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];
    protected static function booted()
    {
        static::assignPrefixedNumberOnCreate('order_number', 'ORD');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function parent()
    {
        return $this->belongsTo(Order::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Order::class, 'parent_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class);
    }
}
