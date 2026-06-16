<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name_ar",
        "name_en",
        "image",
        "is_active",
    ];

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
