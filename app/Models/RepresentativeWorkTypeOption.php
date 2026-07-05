<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RepresentativeWorkTypeOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name_en',
        'name_ar',
        'description',
        'sort_order',
        'is_exclusive',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_exclusive' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    protected $appends = ['name'];

    public function workTypes()
    {
        return $this->hasMany(RepresentativeWorkType::class, 'work_type', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getNameAttribute(): string
    {
        return $this->name_ar ?: $this->name_en;
    }

    public static function activeCodes(): array
    {
        return static::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('code')
            ->all();
    }

    public static function exclusiveCodes(): array
    {
        return static::query()
            ->where('is_exclusive', true)
            ->pluck('code')
            ->all();
    }

    public static function selectableCodes(array $existingCodes = []): array
    {
        return array_values(array_unique(array_merge(
            static::activeCodes(),
            $existingCodes
        )));
    }

    public static function isExclusive(string $code): bool
    {
        return (bool) static::query()
            ->where('code', $code)
            ->value('is_exclusive');
    }

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }
}
