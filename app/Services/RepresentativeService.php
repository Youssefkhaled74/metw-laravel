<?php

namespace App\Services;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeDocumentType;
use App\Enum\RepresentativeStatus;
use App\Enum\RepresentativeWorkType as RepresentativeWorkTypeEnum;
use App\Models\Governorate;
use App\Models\Representative;
use App\Models\RepresentativeWorkTypeOption;
use App\Models\TransportType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
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

            return $this->persist($representative, $data, true);
        });
    }

    public function update(User $user, array $data): Representative
    {
        $representative = $this->getCurrentOrFail($user);

        return DB::transaction(function () use ($representative, $data) {
            return $this->persist($representative, $data, false);
        });
    }

    public function complete(User $user, array $data): Representative
    {
        return $this->update($user, $data);
    }

    public function getCurrentOrFail(User $user): Representative
    {
        return $user->representative()
            ->with([
                'user',
                'warehouse',
                'workTypes',
                'workTypes.option',
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

    public function getActiveGovernorates()
    {
        return Governorate::query()
            ->orderBy('name_ar')
            ->get();
    }

    public function recalculateStatus(Representative $representative): Representative
    {
        $representative->loadMissing([
            'user',
            'workTypes',
            'governorates',
            'cities',
            'vehicle.transportType',
            'mediaFiles',
        ]);

        $this->syncStatus($representative);

        return $representative->fresh([
            'user',
            'warehouse',
            'workTypes',
            'workTypes.option',
            'governorates',
            'cities',
            'vehicle.transportType',
            'mediaFiles',
        ]);
    }

    protected function persist(Representative $representative, array $data, bool $isNew = false): Representative
    {
        $user = $representative->user;

        if (array_key_exists('email', $data) && $user) {
            $user->email = $data['email'];
        }

        if (array_key_exists('password', $data) && filled($data['password']) && $user) {
            $user->password = $data['password'];
        }

        if (array_key_exists('phone', $data) && $user) {
            $user->phone = $data['phone'];
        }

        if (array_key_exists('main_mobile', $data)) {
            $representative->phone = $data['main_mobile'];
        } elseif (array_key_exists('phone', $data)) {
            $representative->phone = $data['phone'];
        }

        if (array_key_exists('second_mobile', $data)) {
            $representative->second_phone = $data['second_mobile'];
        } elseif (array_key_exists('second_phone', $data)) {
            $representative->second_phone = $data['second_phone'];
        }

        foreach ([
            'first_name',
            'father_name',
            'last_name',
            'birth_date',
            'gender',
            'address',
            'notes',
            'metadata',
        ] as $field) {
            if (array_key_exists($field, $data)) {
                $representative->{$field} = $data[$field];
            }
        }

        if (array_key_exists('village_service', $data)) {
            $representative->village_service = (bool) $data['village_service'];
        }

        if (array_key_exists('account_type', $data)) {
            $representative->account_type = $data['account_type'];
        }

        if (array_key_exists('warehouse_id', $data)) {
            $representative->warehouse_id = $data['warehouse_id'];
        }

        $accountType = $data['account_type']
            ?? ($representative->account_type?->value ?? $representative->account_type)
            ?? RepresentativeAccountType::FREE->value;

        if ($accountType !== RepresentativeAccountType::WAREHOUSE->value) {
            $representative->warehouse_id = null;
        }

        if ($isNew || blank($representative->account_opened_at)) {
            $representative->account_opened_at = $representative->account_opened_at ?: now()->toDateString();
        }

        $representative->save();

        if ($user) {
            $user->save();
        }

        if (array_key_exists('work_types', $data)) {
            $this->syncWorkTypes($representative, (array) $data['work_types']);

            $selectedWorkTypes = collect((array) $data['work_types'])->filter();

            if (! $selectedWorkTypes->contains(RepresentativeWorkTypeEnum::LOCAL_DELIVERY->value)) {
                $this->syncCities($representative, []);
            }
        }

        if (array_key_exists('governorate_ids', $data)) {
            $this->syncGovernorates($representative, (array) $data['governorate_ids']);
        }

        if (array_key_exists('city_ids', $data)) {
            $this->syncCities($representative, (array) $data['city_ids']);
        }

        if (array_key_exists('vehicle', $data)) {
            $this->syncVehicle($representative, $data['vehicle']);
        }

        $representative->loadMissing([
            'user',
            'warehouse',
            'workTypes',
            'workTypes.option',
            'governorates',
            'cities',
            'vehicle.transportType',
            'mediaFiles',
        ]);

        $this->syncStatus($representative);

        return $representative->fresh([
            'user',
            'warehouse',
            'workTypes',
            'workTypes.option',
            'governorates',
            'cities',
            'vehicle.transportType',
            'mediaFiles',
        ]);
    }

    protected function syncWorkTypes(Representative $representative, array $workTypes): void
    {
        $normalizedWorkTypes = collect($workTypes)
            ->filter()
            ->map(fn ($workType) => trim((string) $workType))
            ->unique()
            ->values();

        $representative->workTypes()->delete();
        $representative->workTypes()->createMany(
            $normalizedWorkTypes->map(fn ($workType) => ['work_type' => $workType])->all()
        );
    }

    protected function syncGovernorates(Representative $representative, array $governorateIds): void
    {
        $representative->governorates()->sync(array_values(array_unique(array_filter($governorateIds))));
    }

    protected function syncCities(Representative $representative, array $cityIds): void
    {
        $representative->cities()->sync(array_values(array_unique(array_filter($cityIds))));
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

        if (empty($filteredVehicleData['registration_number'])) {
            $letters = trim((string) ($filteredVehicleData['registration_plate_letters'] ?? ''));
            $numbers = trim((string) ($filteredVehicleData['registration_plate_numbers'] ?? ''));
            $filteredVehicleData['registration_number'] = trim($letters . ' ' . $numbers);
        }

        $representative->vehicle()->updateOrCreate(
            ['representative_id' => $representative->id],
            $filteredVehicleData
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
        $representative->loadMissing([
            'user',
            'workTypes',
            'governorates',
            'cities',
            'vehicle.transportType',
            'mediaFiles',
        ]);

        if (blank($representative->account_number)) {
            return false;
        }

        if (blank($representative->first_name) || blank($representative->father_name) || blank($representative->last_name)) {
            return false;
        }

        if (blank($representative->phone)) {
            return false;
        }

        if (blank($representative->user?->email)) {
            return false;
        }

        if (blank($representative->birth_date) || blank($representative->gender) || blank($representative->address)) {
            return false;
        }

        if (
            ($representative->account_type?->value ?? $representative->account_type) === RepresentativeAccountType::WAREHOUSE->value
            && blank($representative->warehouse_id)
        ) {
            return false;
        }

        if ($representative->workTypes->isEmpty()) {
            return false;
        }

        $workTypeCodes = $representative->workTypes->pluck('work_type');

        if ($workTypeCodes->contains(RepresentativeWorkTypeEnum::BUS_DRIVER->value) && $workTypeCodes->count() > 1) {
            return false;
        }

        if ($workTypeCodes->contains(RepresentativeWorkTypeEnum::LOCAL_DELIVERY->value)) {
            if ($representative->governorates->count() !== 1 || $representative->cities->isEmpty()) {
                return false;
            }
        } elseif ($representative->governorates->isEmpty()) {
            return false;
        }

        if (! $representative->vehicle || blank($representative->vehicle->transport_type_id)) {
            return false;
        }

        if (blank($representative->vehicle->brand) || blank($representative->vehicle->model)) {
            return false;
        }

        if (
            blank($representative->vehicle->registration_plate_letters)
            || blank($representative->vehicle->registration_plate_numbers)
        ) {
            return false;
        }

        $requiredDocumentTypes = [
            RepresentativeDocumentType::PERSONAL_PHOTO->value,
            RepresentativeDocumentType::NATIONAL_ID_FRONT->value,
            RepresentativeDocumentType::NATIONAL_ID_BACK->value,
        ];

        $transportType = $representative->vehicle->transportType;

        if (! $transportType) {
            return false;
        }

        $requiresVehiclePhoto = true;
        $requiresDrivingLicense = (bool) $transportType->requires_driving_license;
        $requiresVehicleLicense = (bool) $transportType->requires_vehicle_license;

        if ($requiresVehiclePhoto) {
            $requiredDocumentTypes[] = RepresentativeDocumentType::VEHICLE_PHOTO->value;
        }

        if ($requiresDrivingLicense) {
            $requiredDocumentTypes[] = RepresentativeDocumentType::DRIVING_LICENSE_FRONT->value;
            $requiredDocumentTypes[] = RepresentativeDocumentType::DRIVING_LICENSE_BACK->value;
        }

        if ($requiresVehicleLicense) {
            $requiredDocumentTypes[] = RepresentativeDocumentType::VEHICLE_LICENSE_FRONT->value;
            $requiredDocumentTypes[] = RepresentativeDocumentType::VEHICLE_LICENSE_BACK->value;
        }

        $storedDocumentTypes = $representative->mediaFiles
            ->where('collection_name', 'representative_documents')
            ->pluck('document_type')
            ->filter()
            ->unique()
            ->values();

        return collect($requiredDocumentTypes)->every(
            fn (string $documentType) => $storedDocumentTypes->contains($documentType)
        );
    }
}
