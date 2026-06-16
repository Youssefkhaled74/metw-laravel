<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'is_active',
        'shipment_company_id',
    ];

    /* Relation */
    public function shipmentCompany()
    {
        return $this->belongsTo(ShipmentCompany::class, 'shipment_company_id');
    }

    /* Helper to get config value */
    public static function getValue(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }
}

