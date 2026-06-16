<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'slug',
        'type',
        'content',
        'content_ar',
        'is_active',
        'active_from',
        'active_to',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'active_from' => 'date',
        'active_to' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    public function scopeValid(Builder $query){
        $today = now()->toDateString();
        return $query->where(function ($q) use ($today) {
            $q->whereNull('active_from')->orWhere('active_from', '<=', $today);
        })->where(function ($q) use ($today) {
            $q->whereNull('active_to')->orWhere('active_to', '>=', $today);
        });
    }

    public function getTranselatedTitleAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->title_ar ?? $this->title) : $this->title;
    }
    public function getTranslatedContentAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->content_ar ?? $this->content) : $this->content;
    }
}
