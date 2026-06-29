<?php

namespace App\Models;

use App\Enum\BusinessProfileStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseBusinessProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warehouse_id',
        'legal_name',
        'commercial_name',
        'tax_number',
        'commercial_register_number',
        'manager_name',
        'manager_phone',
        'status',
        'rejection_reason',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'metadata',
    ];

    protected $casts = [
        'status' => BusinessProfileStatus::class,
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }
}
