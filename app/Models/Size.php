<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Size extends Model
{
    use HasFactory;

    protected $fillable=[
        'title',
        'icon',
        'is_active'
    ];

    public function packages()
    {
        return $this->hasMany(Package::class, 'size_id');
    }
    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }
}
