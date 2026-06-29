<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShippingProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'shipment_type',
        'shipment_description',
        'shipment_dimensions',
        'shipment_weight',
        'package_length',
        'package_width',
        'package_height',
        'package_weight',
        'storage_conditions',
        'delivery_zones',
        'delivery_options',
    ];

    protected $casts = [
        'package_length' => 'decimal:2',
        'package_width' => 'decimal:2',
        'package_height' => 'decimal:2',
        'package_weight' => 'decimal:2',
        'storage_conditions' => 'array',
        'delivery_zones' => 'array',
        'delivery_options' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
