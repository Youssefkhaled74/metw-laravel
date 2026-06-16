<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItemRoute extends Model
{
    protected $fillable = [
        'cart_item_id',
        'pickup_company_id',
        'dropoff_company_id',
        'pickup_address',
        'dropoff_address',
        'handoff_point',
        'legs',
        'total_cost',
        'is_split',
    ];

    protected $casts = [
        'pickup_address' => 'array',
        'dropoff_address' => 'array',
        'legs' => 'array',
        'handoff_point' => 'array',
        'is_split' => 'boolean',
    ];

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function pickupCompany()
    {
        return $this->belongsTo(ShipmentCompany::class, 'pickup_company_id');
    }

    public function dropoffCompany()
    {
        return $this->belongsTo(ShipmentCompany::class, 'dropoff_company_id');
    }
}
