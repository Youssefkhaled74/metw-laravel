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
        'category_1_available',
        'category_2_available',
        'category_3_available',
        'requires_driving_license',
        'requires_vehicle_license',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'max_weight' => 'decimal:2',
        'max_volume' => 'decimal:2',
        'category_1_available' => 'boolean',
        'category_2_available' => 'boolean',
        'category_3_available' => 'boolean',
        'requires_driving_license' => 'boolean',
        'requires_vehicle_license' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function representativeVehicles()
    {
        return $this->hasMany(RepresentativeVehicle::class);
    }
}
