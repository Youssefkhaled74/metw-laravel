<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceCartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'ecommerce_cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'unit_price',
        'total_price',
        'product_discount',
        'final_price',
    ];
    public function cart()
    {
        return $this->belongsTo(EcommerceCart::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
