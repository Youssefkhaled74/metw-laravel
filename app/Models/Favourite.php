<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    // protected $fillable = ['user_id', 'shipment_company_id'];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function company()
    // {
    //     return $this->belongsTo(ShipmentCompany::class, 'shipment_company_id');
    // }
    protected $fillable = ['user_id', 'favouriteable_id', 'favouriteable_type'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function favouriteable()
    {
        return $this->morphTo();
    }
}
