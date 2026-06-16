<?php

namespace App\Models;

use App\Enum\ProductMediaType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    use HasFactory;

    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'type',
        'url',
        'position',
        'variant_id',
    ];

    protected $casts = [
        'position' => 'integer',
        'type' =>   ProductMediaType::class,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}

