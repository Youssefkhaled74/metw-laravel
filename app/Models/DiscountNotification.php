<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountNotification extends Model
{
    protected $fillable = ['product_id', 'notified_at'];
    public $timestamps = false;
}
