<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentCompanySubCategorySizePrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'shipment_company_category_price_id',
        'category_id',
        'price_small',
        'price_medium',
        'price_large',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function shipmentCompanyCategoryPrice()
    {
        return $this->belongsTo(ShipmentCompanyCategoryPrice::class, 'shipment_company_category_price_id');
    }


}
