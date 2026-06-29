<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShippingFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'free_shipping',
        'free_shipping_min_order',
        'free_shipping_price',
    ];

    protected $casts = [
        'free_shipping_min_order' => 'decimal:2',
        'free_shipping_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
