<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class City extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'name_en',
        'name_ar',
        'is_active',
        'state_id',
        'governorate_id',
        'excel_sort',
        'is_capital',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_capital' => 'boolean',
    ];

    protected $appends = ['name'];

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function zones()
    {
        return $this->hasMany(Zone::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
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
