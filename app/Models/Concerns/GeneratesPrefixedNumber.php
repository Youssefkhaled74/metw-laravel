<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\DB;

trait GeneratesPrefixedNumber
{
    protected static function assignPrefixedNumberOnCreate(string $column, string $prefix): void
    {
        static::creating(function ($model) use ($column, $prefix) {
            if (! empty($model->{$column})) {
                return;
            }

            $model->{$column} = static::generateSequentialNumber($column, $prefix);
        });
    }

    protected static function generateSequentialNumber(string $column, string $prefix): string
    {
        return DB::transaction(function () use ($column, $prefix) {

            // آخر رقم موجود (حتى مع soft deletes + بدون scopes)
            $last = static::withoutGlobalScopes()
                ->withTrashed()
                ->whereNotNull($column)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value($column);

            $nextNumber = 1;

            if ($last) {
                // استخراج الرقم من الشكل: VDR-00000001
                $numberPart = (int) substr($last, strlen($prefix) + 1);
                $nextNumber = $numberPart + 1;
            }

            return $prefix . '-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
        });
    }
}
