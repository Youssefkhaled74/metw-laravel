<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'package_id',
        'shipment_company_id',
        'est_date',
        'est_price',
        'requires_split'
    ];

    protected $casts = [
        'est_date'  => 'date',
        'est_price' => 'decimal:2',
    ];


    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function shipmentCompany(): BelongsTo
    {
        return $this->belongsTo(ShipmentCompany::class);
    }
    public function route()
    {
        return $this->hasOne(CartItemRoute::class);
    }
}
