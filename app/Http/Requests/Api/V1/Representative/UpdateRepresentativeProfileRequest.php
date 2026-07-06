<?php

namespace App\Http\Requests\Api\V1\Representative;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeWorkType as RepresentativeWorkTypeEnum;
use App\Models\City;
use App\Models\TransportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepresentativeProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->input('phone', $this->input('main_mobile')),
            'main_mobile' => $this->input('main_mobile', $this->input('phone')),
            'second_phone' => $this->input('second_phone', $this->input('second_mobile')),
            'second_mobile' => $this->input('second_mobile', $this->input('second_phone')),
        ]);
    }

    public function rules(): array
    {
        $representative = $this->user()?->representative;
        $vehicleId = $representative?->vehicle?->id;

        return [
            'account_type' => ['sometimes', Rule::enum(RepresentativeAccountType::class)],
            'warehouse_id' => [
                'nullable',
                'integer',
                'exists:warehouses,id',
                Rule::requiredIf(fn () => $this->input('account_type', $representative?->account_type?->value) === RepresentativeAccountType::WAREHOUSE->value),
            ],
            'first_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'father_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()?->id)],
            'phone' => ['sometimes', 'required', 'string', 'max:30'],
            'second_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'main_mobile' => ['sometimes', 'nullable', 'string', 'max:30'],
            'second_mobile' => ['sometimes', 'nullable', 'string', 'max:30'],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'gender' => ['sometimes', 'nullable', Rule::in(['male', 'female'])],
            'address' => ['sometimes', 'nullable', 'string'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['sometimes', 'nullable', 'string', 'min:8'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'metadata' => ['sometimes', 'nullable', 'array'],
            'village_service' => ['sometimes', 'nullable', 'boolean'],
            'work_types' => ['sometimes', 'array', 'min:1'],
            'work_types.*' => ['required', Rule::in([
                RepresentativeWorkTypeEnum::LOCAL_DELIVERY->value,
                RepresentativeWorkTypeEnum::INTER_GOVERNORATE_SHIPPING->value,
                RepresentativeWorkTypeEnum::BUS_DRIVER->value,
            ])],
            'governorate_ids' => ['sometimes', 'array'],
            'governorate_ids.*' => ['integer', 'exists:governorates,id'],
            'city_ids' => ['sometimes', 'array'],
            'city_ids.*' => ['integer', 'exists:cities,id'],
            'vehicle' => ['sometimes', 'nullable', 'array'],
            'vehicle.transport_type_id' => ['nullable', 'integer', 'exists:transport_types,id'],
            'vehicle.registration_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('representative_vehicles', 'registration_number')->ignore($vehicleId),
            ],
            'vehicle.registration_plate_letters' => ['nullable', 'string', 'max:50'],
            'vehicle.registration_plate_numbers' => ['nullable', 'string', 'max:50'],
            'vehicle.license_number' => ['nullable', 'string', 'max:100'],
            'vehicle.brand' => ['nullable', 'string', 'max:100'],
            'vehicle.model' => ['nullable', 'string', 'max:100'],
            'vehicle.color' => ['nullable', 'string', 'max:100'],
            'vehicle.manufacture_year' => ['nullable', 'integer', 'min:1950', 'max:' . (date('Y') + 1)],
            'vehicle.max_weight' => ['nullable', 'numeric', 'min:0'],
            'vehicle.max_volume' => ['nullable', 'numeric', 'min:0'],
            'vehicle.is_active' => ['nullable', 'boolean'],
            'vehicle.notes' => ['nullable', 'string'],
            'vehicle.metadata' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $cityIds = array_values(array_filter((array) $this->input('city_ids', [])));
            $governorateIds = array_values(array_filter((array) $this->input('governorate_ids', [])));
            $representative = $this->user()?->representative;
            $existingWorkTypes = $representative
                ? $representative->workTypes->map(fn ($workType) => $workType->work_type)
                : collect();
            $workTypes = collect((array) ($this->input('work_types', $existingWorkTypes->all())))
                ->filter()
                ->map(fn ($workType) => trim((string) $workType))
                ->values();
            $transportTypeId = data_get(
                $this->all(),
                'vehicle.transport_type_id',
                $representative?->vehicle?->transport_type_id
            );

            if ($workTypes->contains(RepresentativeWorkTypeEnum::BUS_DRIVER->value) && $workTypes->count() > 1) {
                $validator->errors()->add('work_types', 'Bus driver cannot be combined with other representative work types.');
            }

            if ($workTypes->contains(RepresentativeWorkTypeEnum::LOCAL_DELIVERY->value)) {
                if (count($governorateIds) > 1) {
                    $validator->errors()->add('governorate_ids', 'Local delivery representatives can only select one governorate.');
                }

                if ($this->has('work_types') && empty($cityIds)) {
                    $validator->errors()->add('city_ids', 'At least one city is required when local delivery is selected.');
                }
            } elseif (! empty($cityIds)) {
                $validator->errors()->add('city_ids', 'Cities can only be selected for local delivery representatives.');
            }

            if (! empty($cityIds) && ! empty($governorateIds)) {
                $invalidCityIds = City::withoutGlobalScopes()
                    ->whereIn('id', $cityIds)
                    ->whereNotIn('governorate_id', $governorateIds)
                    ->pluck('id')
                    ->all();

                if (! empty($invalidCityIds)) {
                    $validator->errors()->add('city_ids', 'Selected cities must belong to the selected governorates.');
                }
            }

            if ($transportTypeId) {
                $transportType = TransportType::query()->find($transportTypeId);

                if (! $transportType || ! $transportType->is_active) {
                    $validator->errors()->add(
                        'vehicle.transport_type_id',
                        'The selected transport type must exist and be active.'
                    );
                }
            }
        });
    }
}
