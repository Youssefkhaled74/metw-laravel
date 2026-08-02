<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierCategory;
use App\Enum\RequestLegType;
use App\Models\Representative;
use Illuminate\Support\Collection;

/**
 * Applies the request distribution criteria (Section 2) and returns the set of
 * couriers eligible to receive a given request leg.
 */
class CourierMatchService
{
    public function __construct(
        protected CourierSystemConfigService $config
    ) {}

    /**
     * @param  array<int, int>  $excludeRepresentativeIds
     * @return Collection<int, Representative>
     */
    public function eligibleCouriers(
        RequestLegType $legType,
        CourierRequestProfile $profile,
        array $excludeRepresentativeIds = []
    ): Collection {
        $maxOffers = $this->config->maxOffersPerLeg();

        return Representative::query()
            ->with(['user', 'workTypes', 'governorates', 'cities', 'vehicle.transportType'])
            ->where('is_active', true)
            ->whereHas('workTypes', fn ($q) => $q->where('work_type', $legType->requiredWorkType()))
            ->when($excludeRepresentativeIds, fn ($q) => $q->whereNotIn('id', $excludeRepresentativeIds))
            ->get()
            ->filter(fn (Representative $representative) => $this->matchesCriteria($representative, $legType, $profile))
            ->take($maxOffers)
            ->values();
    }

    public function matchesCriteria(
        Representative $representative,
        RequestLegType $legType,
        CourierRequestProfile $profile
    ): bool {
        if (! $representative->isApproved()) {
            return false;
        }

        if (! $representative->hasWorkType($legType->requiredWorkType())) {
            return false;
        }

        if ($profile->hasVillage && ! $representative->village_service) {
            return false;
        }

        if (! $this->governorateMatches($representative, $legType, $profile)) {
            return false;
        }

        if (! $this->carryCapacityMatches($representative, $profile)) {
            return false;
        }

        if (! $this->categoryMatches($representative, $profile)) {
            return false;
        }

        return true;
    }

    protected function governorateMatches(
        Representative $representative,
        RequestLegType $legType,
        CourierRequestProfile $profile
    ): bool {
        $governorates = $representative->serviceGovernorateIds();

        if (empty($governorates)) {
            return false;
        }

        $requiredGovernorate = $this->requiredGovernorateId($legType, $profile);

        if ($requiredGovernorate === null) {
            return true;
        }

        if (! in_array($requiredGovernorate, $governorates, true)) {
            return false;
        }

        $requiredCityId = $this->requiredCityId($legType, $profile);

        if ($requiredCityId === null) {
            return true;
        }

        $cities = $representative->serviceCityIds();

        // Courier did not restrict to specific cities → any city inside the governorate works.
        if (empty($cities)) {
            return true;
        }

        return in_array($requiredCityId, $cities, true);
    }

    protected function requiredGovernorateId(RequestLegType $legType, CourierRequestProfile $profile): ?int
    {
        return match ($legType) {
            RequestLegType::DELIVERY_FROM_WAREHOUSE,
            RequestLegType::DELIVERY_FROM_BUS => $profile->destinationGovernorateId,

            default => $profile->originGovernorateId,
        };
    }

    protected function requiredCityId(RequestLegType $legType, CourierRequestProfile $profile): ?int
    {
        return match ($legType) {
            RequestLegType::DELIVERY_FROM_WAREHOUSE,
            RequestLegType::DELIVERY_FROM_BUS => $profile->destinationCityId,

            default => $profile->originCityId,
        };
    }

    protected function carryCapacityMatches(Representative $representative, CourierRequestProfile $profile): bool
    {
        if ($profile->weight > 0) {
            $maxWeight = $representative->maxCarryWeight();

            if ($maxWeight !== null && $profile->weight > $maxWeight) {
                return false;
            }
        }

        if ($profile->volume > 0) {
            $maxVolume = $representative->maxCarryVolume();

            if ($maxVolume !== null && $profile->volume > $maxVolume) {
                return false;
            }
        }

        return true;
    }

    protected function categoryMatches(Representative $representative, CourierRequestProfile $profile): bool
    {
        $supported = $representative->supportedCategories();

        if (empty($supported)) {
            return false;
        }

        if ($profile->courierCategory === null) {
            return true;
        }

        return in_array($profile->courierCategory->value, $supported, true);
    }
}
