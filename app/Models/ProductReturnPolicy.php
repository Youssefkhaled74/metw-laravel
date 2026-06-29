<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturnPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'is_returnable',
        'return_fee',
        'return_validity',
    ];

    protected $casts = [
        'is_returnable' => 'boolean',
        'return_fee' => 'decimal:2',
        'return_validity' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
