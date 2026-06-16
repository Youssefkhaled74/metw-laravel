<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceCart extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'items_count',
        'total_price',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(EcommerceCartItem::class);
    }
    public function orders()
    {
        return $this->hasMany(EcommerceOrder::class);
    }
}
