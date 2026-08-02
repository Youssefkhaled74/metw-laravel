<?php

namespace App\Models;

use App\Enum\CourierRole;
use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use App\Models\Concerns\GeneratesPrefixedNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Representative extends Model
{
    use GeneratesPrefixedNumber, HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'account_number',
        'warehouse_id',
        'shipment_company_id',
        'first_name',
        'father_name',
        'last_name',
        'account_opened_at',
        'account_type',
        'status',
        'phone',
        'second_phone',
        'birth_date',
        'gender',
        'address',
        'village_service',
        'notes',
        'rejection_reason',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'suspended_at',
        'is_active',
        'is_profile_complete',
        'metadata',
    ];

    protected $casts = [
        'account_type' => RepresentativeAccountType::class,
        'status' => RepresentativeStatus::class,
        'account_opened_at' => 'date',
        'birth_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
        'is_active' => 'boolean',
        'is_profile_complete' => 'boolean',
        'village_service' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::assignPrefixedNumberOnCreate('account_number', 'REP');
    }

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

    public function documents()
    {
        return $this->mediaFiles()->where('collection_name', 'representative_documents');
    }

    /**
     * Work-type codes held by this courier (e.g. local_delivery, inter_governorate_shipping, bus_driver).
     *
     * @return array<int, string>
     */
    public function workTypeCodes(): array
    {
        if ($this->relationLoaded('workTypes')) {
            return $this->workTypes->pluck('work_type')->values()->all();
        }

        return $this->workTypes()->pluck('work_type')->values()->all();
    }

    public function hasWorkType(string $code): bool
    {
        return in_array($code, $this->workTypeCodes(), true);
    }

    /**
     * Courier roles derived from the work-type combinations (Section 1).
     *
     * @return array<int, CourierRole>
     */
    public function courierRoles(): array
    {
        return CourierRole::fromWorkTypes($this->workTypeCodes());
    }

    /**
     * @return array<int, string>
     */
    public function courierRoleValues(): array
    {
        return array_map(
            static fn (CourierRole $role) => $role->value,
            $this->courierRoles()
        );
    }

    /**
     * Maximum payload weight this courier can carry (from their vehicle's transport type).
     */
    public function maxCarryWeight(): ?float
    {
        $transport = $this->vehicle?->transportType;

        if (! $transport) {
            return $this->vehicle ? (float) $this->vehicle->max_weight : null;
        }

        return (float) $transport->max_weight;
    }

    /**
     * Maximum payload volume this courier can carry.
     */
    public function maxCarryVolume(): ?float
    {
        $transport = $this->vehicle?->transportType;

        if (! $transport) {
            return $this->vehicle ? (float) $this->vehicle->max_volume : null;
        }

        return (float) $transport->max_volume;
    }

    /**
     * Courier categories the courier's transport type supports.
     *
     * @return array<int, string>
     */
    public function supportedCategories(): array
    {
        $transport = $this->vehicle?->transportType;

        if (! $transport) {
            return [];
        }

        $categories = [];

        if ($transport->category_1_available) {
            $categories[] = 'category_1';
        }
        if ($transport->category_2_available) {
            $categories[] = 'category_2';
        }
        if ($transport->category_3_available) {
            $categories[] = 'category_3';
        }

        return $categories;
    }

    /**
     * @return array<int, int>
     */
    public function serviceGovernorateIds(): array
    {
        if ($this->relationLoaded('governorates')) {
            return $this->governorates->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        }

        return $this->governorates()->pluck('governorate_id')->map(fn ($id) => (int) $id)->values()->all();
    }

    /**
     * @return array<int, int>
     */
    public function serviceCityIds(): array
    {
        if ($this->relationLoaded('cities')) {
            return $this->cities->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        }

        return $this->cities()->pluck('city_id')->map(fn ($id) => (int) $id)->values()->all();
    }

    public function isApproved(): bool
    {
        return in_array($this->status?->value ?? $this->status, [
            RepresentativeStatus::ACTIVE->value,
            RepresentativeStatus::APPROVED->value,
        ], true);
    }
}
