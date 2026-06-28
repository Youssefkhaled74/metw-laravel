<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name_en',
        'name_ar',
        'description',
        'max_weight',
        'max_volume',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'max_weight' => 'decimal:2',
        'max_volume' => 'decimal:2',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];
}
