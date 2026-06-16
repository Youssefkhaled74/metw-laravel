<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'sku',
        'price',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(ProductColor::class);
    }

    public function size()
    {
        return $this->belongsTo(ProductSize::class);
    }
    public function cartitmes()
    {
        return $this->hasMany(CartItem::class);
    }
    public function ecommerceOrderItems()
    {
        return $this->hasMany(EcommerceOrderItem::class);
    }
    public function media()
    {
        return $this->hasMany(ProductMedia::class, 'variant_id');
    }
}

