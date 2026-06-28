<?php

namespace App\Services;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeStatus;
use App\Models\Representative;
use App\Models\TransportType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RepresentativeService
{
    public function register(User $user, array $data): Representative
    {
        if ($user->representative()->exists()) {
            throw ValidationException::withMessages([
                'representative' => ['Representative profile already exists for this user.'],
            ]);
        }

        return DB::transaction(function () use ($user, $data) {
            $representative = new Representative();
            $representative->user()->associate($user);

            return $this->persist($representative, $data);
        });
    }

    public function update(User $user, array $data): Representative
    {
        $representative = $this->getCurrentOrFail($user);

        return DB::transaction(function () use ($representative, $data) {
            return $this->persist($representative, $data);
        });
    }

    public function getCurrentOrFail(User $user): Representative
    {
        return $user->representative()
            ->with([
                'user',
                'warehouse',
                'workTypes',
                'governorates',
                'cities',
                'vehicle.transportType',
                'mediaFiles',
            ])
            ->firstOrFail();
    }

    public function getActiveTransportTypes()
    {
        return TransportType::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
    }

    protected function persist(Representative $representative, array $data): Representative
    {
        if (array_key_exists('account_type', $data)) {
            $representative->account_type = $data['account_type'];
        }

        if (array_key_exists('phone', $data)) {
            $representative->phone = $data['phone'];
        }

        if (array_key_exists('notes', $data)) {
            $representative->notes = $data['notes'];
        }

        if (array_key_exists('metadata', $data)) {
            $representative->metadata = $data['metadata'];
        }

        $accountType = $data['account_type']
            ?? ($representative->account_type?->value ?? $representative->account_type)
            ?? RepresentativeAccountType::FREE->value;

        $representative->warehouse_id = $accountType === RepresentativeAccountType::WAREHOUSE->value
            ? ($data['warehouse_id'] ?? $representative->warehouse_id)
            : null;

        $representative->save();

        if (array_key_exists('work_types', $data)) {
            $representative->workTypes()->delete();
            $representative->workTypes()->createMany(
                collect($data['work_types'])
                    ->unique()
                    ->values()
                    ->map(fn ($workType) => ['work_type' => $workType])
                    ->all()
            );
        }

        if (array_key_exists('governorate_ids', $data)) {
            $representative->governorates()->sync(array_values(array_unique($data['governorate_ids'])));
        }

        if (array_key_exists('city_ids', $data)) {
            $representative->cities()->sync(array_values(array_unique($data['city_ids'])));
        }

        if (array_key_exists('vehicle', $data)) {
            $this->syncVehicle($representative, $data['vehicle']);
        }

        $representative->loadMissing([
            'workTypes',
            'governorates',
            'cities',
            'vehicle.transportType',
        ]);

        $this->syncStatus($representative);

        return $representative->fresh([
            'user',
            'warehouse',
            'workTypes',
            'governorates',
            'cities',
            'vehicle.transportType',
            'mediaFiles',
        ]);
    }

    protected function syncVehicle(Representative $representative, ?array $vehicleData): void
    {
        if ($vehicleData === null) {
            $representative->vehicle()->delete();
            return;
        }

        $filteredVehicleData = array_filter(
            $vehicleData,
            fn ($value) => ! ($value === null || $value === '' || $value === [])
        );

        if (empty($filteredVehicleData)) {
            $representative->vehicle()->delete();
            return;
        }

        $representative->vehicle()->updateOrCreate(
            ['representative_id' => $representative->id],
            $vehicleData
        );
    }

    protected function syncStatus(Representative $representative): void
    {
        $currentStatus = $representative->status?->value ?? $representative->status;

        if (in_array($currentStatus, [
            RepresentativeStatus::APPROVED->value,
            RepresentativeStatus::SUSPENDED->value,
        ], true)) {
            return;
        }

        $isComplete = $this->isCompleteForReview($representative);

        if ($isComplete) {
            $representative->status = RepresentativeStatus::PENDING_REVIEW;
            $representative->submitted_at = $representative->submitted_at ?? now();
            $representative->rejection_reason = null;
            $representative->reviewed_at = null;
        } else {
            $representative->status = RepresentativeStatus::INCOMPLETE;
            $representative->submitted_at = null;
        }

        $representative->save();
    }

    protected function isCompleteForReview(Representative $representative): bool
    {
        $accountType = $representative->account_type?->value ?? $representative->account_type;

        if (blank($representative->phone)) {
            return false;
        }

        if (
            $accountType === RepresentativeAccountType::WAREHOUSE->value
            && blank($representative->warehouse_id)
        ) {
            return false;
        }

        if ($representative->workTypes->isEmpty()) {
            return false;
        }

        if ($representative->governorates->isEmpty() && $representative->cities->isEmpty()) {
            return false;
        }

        if (! $representative->vehicle || blank($representative->vehicle->transport_type_id)) {
            return false;
        }

        return true;
    }
}
