<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory ,SoftDeletes;
    protected $fillable = [
        'package_number',
        'type_id',
        'size',
        'piece',
        'pickup_address_id',
        'dropoff_address_id',
        'package_details_id',
        'shipment_company_id',
        'delivery_type_id',
        'consignment_type_id',
        'est_days',

        // NEW:
        'category_id',
        'sub_category_id',
        'weight',
    ];

    public function pickupAddress()
    {
        return $this->belongsTo(PackageAddress::class, 'pickup_address_id');
    }

    public function dropoffAddress()
    {
        return $this->belongsTo(PackageAddress::class, 'dropoff_address_id');
    }

    public function packageDetails()
    {
        return $this->belongsTo(PackageDetails::class, 'package_details_id');
    }
    public function images()
    {
        return $this->hasMany(PackageImage::class);
    }
    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class);
    }
    // public function carts()
    // {
    //     return $this->hasMany(Cart::class);
    // }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function trackings()
    {
        return $this->hasMany(PackageTracking::class);
    }
    public function type()
    {
        return $this->belongsTo(PackageType::class, 'type_id');
    }
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
    public function deliveryType()
    {
        return $this->belongsTo(DeliveryType::class, 'delivery_type_id');
    }

    public function consignmentType()
    {
        return $this->belongsTo(ConsignmentType::class, 'consignment_type_id');
    }
    public function category()
    {
        return $this->belongsTo(MainCategory::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

}
