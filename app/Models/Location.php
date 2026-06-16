<?php

namespace App\Models;

use App\Enum\LocationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'path',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'type'=> LocationType::class
    ];
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function companies()
    {
        return $this->belongsToMany(ShipmentCompany::class, 'company_coverages')
        ->withPivot(['pickup_available','delivery_available','eta_min_days','eta_max_days', 'eta_price','notes'])
        ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
