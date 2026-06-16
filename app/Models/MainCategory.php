<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class MainCategory extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'image',
        'slug',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function translations()
    {
        return $this->hasMany(MainCategoryTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    protected static function booted()
    {
        // translation scope (existing)
        static::addGlobalScope('withCurrentLocaleTranslation', function (Builder $builder) {
            $locale = app()->getLocale();
            $builder->with(['translations' => function($query) use ($locale) {
                $query->where('locale', $locale);
            }]);
        });

        // 🔥 new active scope
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }


    public function getNameAttribute()
    {
        $translation = $this->translation(app()->getLocale());
        return $translation ? $translation->name : $this->attributes['name'] ?? '';
    }
}
