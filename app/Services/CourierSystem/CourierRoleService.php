<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierRole;
use App\Enum\RequestLegType;
use App\Enum\RepresentativeWorkType;
use App\Models\Representative;
use App\Models\RepresentativeWorkTypeOption;
use Illuminate\Support\Collection;

/**
 * Resolves and manages courier classifications / roles (Section 1).
 */
class CourierRoleService
{
    /**
     * The four courier roles defined by the document.
     *
     * @return array<int, array{code: string, name: string, name_ar: string, work_types: array<int, string>}>
     */
    public function roleDefinitions(): array
    {
        return [
            [
                'code' => CourierRole::DELIVERY_COURIER->value,
                'name' => 'Delivery Courier',
                'name_ar' => 'مندوب التوصيل',
                'work_types' => [RepresentativeWorkType::LOCAL_DELIVERY->value],
            ],
            [
                'code' => CourierRole::SHIPPING_COURIER->value,
                'name' => 'Shipping Courier',
                'name_ar' => 'مندوب الشحن',
                'work_types' => [RepresentativeWorkType::INTER_GOVERNORATE_SHIPPING->value],
            ],
            [
                'code' => CourierRole::BUS_DRIVER->value,
                'name' => 'Bus Driver',
                'name_ar' => 'سائق الباص',
                'work_types' => [RepresentativeWorkType::BUS_DRIVER->value],
            ],
            [
                'code' => CourierRole::SHIPPING_AND_DELIVERY_COURIER->value,
                'name' => 'Shipping & Delivery Courier',
                'name_ar' => 'مندوب التوصيل والشحن',
                'work_types' => [
                    RepresentativeWorkType::LOCAL_DELIVERY->value,
                    RepresentativeWorkType::INTER_GOVERNORATE_SHIPPING->value,
                ],
            ],
        ];
    }

    /**
     * @return array<int, CourierRole>
     */
    public function rolesFor(Representative $representative): array
    {
        return $representative->courierRoles();
    }

    /**
     * Whether a courier can serve the given leg type (required work type).
     */
    public function canServeLeg(Representative $representative, RequestLegType $legType): bool
    {
        return $representative->hasWorkType($legType->requiredWorkType());
    }

    /**
     * Eligible work-type codes selectable by a courier account.
     *
     * @return array<int, string>
     */
    public function selectableWorkTypeCodes(): array
    {
        return RepresentativeWorkTypeOption::selectableCodes();
    }

    /**
     * Query couriers (representatives) by role filter.
     */
    public function couriersQuery(?string $role = null)
    {
        $query = Representative::query()
            ->with(['user', 'workTypes.option', 'governorates', 'cities', 'vehicle.transportType'])
            ->where('is_active', true);

        if ($role) {
            $workTypes = collect($this->roleDefinitions())
                ->firstWhere('code', $role)['work_types'] ?? null;

            if ($workTypes) {
                $query->whereHas('workTypes', function ($builder) use ($workTypes) {
                    $builder->whereIn('work_type', $workTypes);
                });
            }
        }

        return $query;
    }

    /**
     * @return Collection<int, Representative>
     */
    public function couriersByRole(?string $role): Collection
    {
        return $this->couriersQuery($role)->get();
    }
}
