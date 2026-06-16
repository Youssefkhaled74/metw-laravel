<?php

namespace App\Models;

use App\Enum\DiscountType;
use App\Enum\PromoCodeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'valid_from',
        'valid_to',
        'max_uses',
        'user_max_uses',
        'uses',
        'is_active',
        'type',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'valid_from'     => 'date',
        'valid_to'       => 'date',
        'is_active'      => 'boolean',
        'discount_type'  => DiscountType::class,
        'type'           => PromoCodeType::class,
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function ecommer(): HasMany
    {
        return $this->hasMany(EcommerceOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


}
