<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'shipment_company_id',
        'items_count',
        'item_total_price'
    ];
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
