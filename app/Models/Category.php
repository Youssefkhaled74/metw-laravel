<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'main_category_id',
        'is_active',
        'type',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const TYPES = [
        'piece',
        'weight',
        'weight_size',
    ];

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }

    public function getNameAttribute()
    {
        $translation = $this->translation(app()->getLocale());
        return $translation ? $translation->name : $this->attributes['name'] ?? '';
    }

    public function scopeActive($query){
        return $query->where('is_active',true);
    }
    protected static function booted()
    {
        // static::addGlobalScope('withCurrentLocaleTranslation', function (Builder $builder) {
        //     $locale = app()->getLocale();
        //     $builder->with(['translations' => function($query) use ($locale) {
        //         $query->where('locale', $locale);
        //     }]);
        // });

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });

        // static::addGlobalScope('active_main_category', function (Builder $builder) {
        //     $builder->whereHas('mainCategory', function ($q) {
        //         $q->where('is_active', true);
        //     });
        // });
    }

}
