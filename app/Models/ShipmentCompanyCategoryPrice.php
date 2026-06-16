<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentCompanyCategoryPrice extends Model
{
    protected $fillable = [
        'shipment_company_id',
        'category_id',
        'price_per_size',
        'price_per_kg',
        'per_piece',
    ];

    public function company()
    {
        return $this->belongsTo(ShipmentCompany::class, 'shipment_company_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

