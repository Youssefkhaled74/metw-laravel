<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class State extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name_en',
        'name_ar',
        'is_active',
        'country_id',
    ];
    protected $appends = ['name'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }
}
