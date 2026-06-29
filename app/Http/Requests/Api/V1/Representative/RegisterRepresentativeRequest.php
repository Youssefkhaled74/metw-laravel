<?php

namespace App\Http\Requests\Api\V1\Representative;

use App\Enum\RepresentativeAccountType;
use App\Enum\RepresentativeWorkType;
use App\Models\City;
use App\Models\TransportType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRepresentativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_type' => ['required', Rule::enum(RepresentativeAccountType::class)],
            'warehouse_id' => [
                'nullable',
                'integer',
                'exists:warehouses,id',
                Rule::requiredIf(fn () => $this->input('account_type') === RepresentativeAccountType::WAREHOUSE->value),
            ],
            'phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'work_types' => ['required', 'array', 'min:1'],
            'work_types.*' => ['required', Rule::enum(RepresentativeWorkType::class)],
            'governorate_ids' => ['nullable', 'array'],
            'governorate_ids.*' => ['integer', 'exists:governorates,id'],
            'city_ids' => ['nullable', 'array'],
            'city_ids.*' => ['integer', 'exists:cities,id'],
            'vehicle' => ['nullable', 'array'],
            'vehicle.transport_type_id' => ['nullable', 'integer', 'exists:transport_types,id'],
            'vehicle.registration_number' => ['nullable', 'string', 'max:100', 'unique:representative_vehicles,registration_number'],
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
            $workTypes = collect((array) $this->input('work_types', []))
                ->filter()
                ->values();
            $transportTypeId = $this->input('vehicle.transport_type_id');

            if (empty($cityIds) && empty($governorateIds)) {
                $validator->errors()->add('governorate_ids', 'At least one governorate or city must be selected.');
            }

            if (
                $workTypes->contains(RepresentativeWorkType::LOCAL_DELIVERY->value)
                && $workTypes->count() > 1
            ) {
                $validator->errors()->add(
                    'work_types',
                    'Local delivery cannot be combined with other representative work types.'
                );
            }

            if (
                $workTypes->contains(RepresentativeWorkType::LOCAL_DELIVERY->value)
                && count($governorateIds) > 1
            ) {
                $validator->errors()->add(
                    'governorate_ids',
                    'Local delivery representatives can only select one governorate.'
                );
            }

            if (
                $workTypes->contains(RepresentativeWorkType::LOCAL_DELIVERY->value)
                && empty($cityIds)
            ) {
                $validator->errors()->add(
                    'city_ids',
                    'At least one city is required when local delivery is selected.'
                );
            }

            if (empty($cityIds) || empty($governorateIds)) {
                if ($transportTypeId) {
                    $transportType = TransportType::query()->find($transportTypeId);

                    if (! $transportType || ! $transportType->is_active) {
                        $validator->errors()->add(
                            'vehicle.transport_type_id',
                            'The selected transport type must exist and be active.'
                        );
                    }
                }

                return;
            }

            $invalidCityIds = City::withoutGlobalScopes()
                ->whereIn('id', $cityIds)
                ->whereNotIn('governorate_id', $governorateIds)
                ->pluck('id')
                ->all();

            if (! empty($invalidCityIds)) {
                $validator->errors()->add('city_ids', 'Selected cities must belong to the selected governorates.');
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
