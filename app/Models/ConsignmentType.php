<?php

namespace App\Models;

use App\Enum\CourierCategory;
use App\Enum\ShippingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ConsignmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'shipping_type',
        'courier_category',
        'max_weight',
        'max_volume',
        'description',
        'description_ar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'shipping_type' => ShippingType::class,
        'courier_category' => CourierCategory::class,
        'max_weight' => 'decimal:2',
        'max_volume' => 'decimal:2',
    ];

    /* OPTIONAL but VERY useful */
    public function getTranslatedNameAttribute()
    {
        return app()->getLocale() === 'ar'
            ? ($this->name_ar ?? $this->name)
            : $this->name;
    }

    public function getTranslatedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar'
            ? ($this->description_ar ?? $this->description)
            : $this->description;
    }
    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }
}
