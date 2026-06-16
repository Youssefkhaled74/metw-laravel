<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Country extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'is_active',
        'phone_code',
    ];

    public function states()
    {
        return $this->hasMany(State::class);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
    public function getNameAttribute()
    {
        $locale = app()->getLocale(); // ar or en
        return $this->{'name_' . $locale};
    }
    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }
}
