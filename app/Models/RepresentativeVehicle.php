<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RepresentativeVehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'representative_id',
        'transport_type_id',
        'registration_number',
        'license_number',
        'brand',
        'model',
        'color',
        'manufacture_year',
        'max_weight',
        'max_volume',
        'is_active',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'manufacture_year' => 'integer',
        'max_weight' => 'decimal:2',
        'max_volume' => 'decimal:2',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function transportType()
    {
        return $this->belongsTo(TransportType::class);
    }
}
