<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DeliveryType extends Model
{
    use HasFactory;

    protected $fillable = ['name','code','description','is_active'];

    protected $casts = [

        'is_active'    => 'boolean',
    ];

    public function packageDetails()
    {
        return $this->hasMany(PackageDetails::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }
}
