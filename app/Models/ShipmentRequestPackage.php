<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentRequestPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shipment_request_id',
        'package_name',
        'package_type',
        'quantity',
        'weight',
        'length',
        'width',
        'height',
        'declared_value',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function shipmentRequest()
    {
        return $this->belongsTo(ShipmentRequest::class);
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }
}
