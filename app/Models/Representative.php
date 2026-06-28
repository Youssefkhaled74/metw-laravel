<?php

namespace App\Models;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Representative extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'warehouse_id',
        'account_type',
        'status',
        'phone',
        'notes',
        'rejection_reason',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'suspended_at',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'account_type' => RepresentativeAccountType::class,
        'status' => RepresentativeStatus::class,
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function workTypes()
    {
        return $this->hasMany(RepresentativeWorkType::class);
    }

    public function serviceGovernorates()
    {
        return $this->hasMany(RepresentativeServiceGovernorate::class);
    }

    public function serviceCities()
    {
        return $this->hasMany(RepresentativeServiceCity::class);
    }

    public function governorates()
    {
        return $this->belongsToMany(
            Governorate::class,
            'representative_service_governorates'
        )->withTimestamps();
    }

    public function cities()
    {
        return $this->belongsToMany(
            City::class,
            'representative_service_cities'
        )->withTimestamps();
    }

    public function vehicle()
    {
        return $this->hasOne(RepresentativeVehicle::class);
    }

    public function mediaFiles()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }
}
