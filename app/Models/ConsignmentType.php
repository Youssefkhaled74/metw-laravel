<?php

namespace App\Models;

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
        'description',
        'description_ar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
