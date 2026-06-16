<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Bannar extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'link',
        'is_active',
    ];
    public function scopeActive($query){
        return $query->where('is_active',true);
    }

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }
}
