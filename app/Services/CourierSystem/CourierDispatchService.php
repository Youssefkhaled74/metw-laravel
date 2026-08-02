<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierCategory;
use App\Enum\DispatchState;
use App\Enum\RequestLegType;
use App\Enum\ShippingType;
use App\Models\OrderItem;
use App\Models\ShipmentRequest;
use App\Models\ShipmentRequestPackage;
use Illuminate\Support\Collection;

/**
 * Distributes requests to couriers (Section 2): builds a matching profile from
 * the request, derives the required legs, matches eligible couriers and records
 * the resulting assignments.
 */
class CourierDispatchService
{
    public function __construct(
        protected CourierMatchService $matchService,
        protected CourierSystemConfigService $config,
        protected WorkingHoursService $workingHours
    ) {}

    public function dispatch(ShipmentRequest|OrderItem $assignable): DispatchResult
    {
        $profile = $this->buildProfile($assignable);

        $legs = $this->legsFor($assignable, $profile);

        $assignments = collect();

        foreach ($legs as $legType) {
            $excluded = $this->alreadyOfferedRepresentativeIds($assignable);

            $couriers = $this->matchService->eligibleCouriers($legType, $profile, $excluded);

            foreach ($couriers as $courier) {
                $assignments->push(
                    $assignable->assignments()->create([
                        'representative_id' => $courier->id,
                        'leg_type' => $legType->value,
                        'status' => 'pending',
                        'offered_at' => now(),
                        'response_deadline_at' => $this->workingHours->addWorkingHours(
                            now(),
                            $this->config->autoRejectWorkingHours(),
                        ),
                        'metadata' => array_merge($profile->toArray(), [
                            'offered_to' => trim(implode(' ', array_filter([
                                $courier->first_name,
                                $courier->father_name,
                                $courier->last_name,
                            ]))),
                        ]),
                    ])
                );
            }
        }

        if ($assignments->isNotEmpty()) {
            $assignable->update([
                'dispatch_state' => DispatchState::DISPATCHED->value,
                'dispatch_state_at' => now(),
            ]);
        }

        return new DispatchResult($profile, $legs, $assignments);
    }

    /**
     * @return array<int, RequestLegType>
     */
    public function legsFor(ShipmentRequest|OrderItem $assignable, CourierRequestProfile $profile): array
    {
        if (! $profile->isInterGovernorate()) {
            return [RequestLegType::DIRECT_DELIVERY];
        }

        $legs = [RequestLegType::DIRECT_SHIPPING];

        if ($profile->shippingType === ShippingType::DIRECT_AND_MULTI) {
            $legs = array_merge($legs, [
                RequestLegType::DELIVERY_TO_WAREHOUSE,
                RequestLegType::WAREHOUSE_TO_WAREHOUSE,
                RequestLegType::DELIVERY_FROM_WAREHOUSE,
                RequestLegType::DELIVERY_TO_BUS,
                RequestLegType::BUS_SEGMENT,
                RequestLegType::DELIVERY_FROM_BUS,
            ]);
        }

        return $legs;
    }

    public function buildProfile(ShipmentRequest|OrderItem $assignable): CourierRequestProfile
    {
        return $assignable instanceof ShipmentRequest
            ? $this->profileForShipmentRequest($assignable)
            : $this->profileForOrderItem($assignable);
    }

    protected function profileForShipmentRequest(ShipmentRequest $request): CourierRequestProfile
    {
        $packages = $request->packages()
            ->with('consignmentType')
            ->get();

        $shippingTypes = $packages
            ->map(fn (ShipmentRequestPackage $package) => $package->consignmentType?->shippingType?->value)
            ->filter()
            ->values()
            ->all();

        $categories = $packages
            ->map(fn (ShipmentRequestPackage $package) => $package->consignmentType?->courierCategory?->value)
            ->filter()
            ->values()
            ->all();

        return new CourierRequestProfile(
            originGovernorateId: $request->senderContact?->primaryAddress?->governorate_id,
            originCityId: $request->senderContact?->primaryAddress?->city_id,
            destinationGovernorateId: $request->receiverContact?->primaryAddress?->governorate_id,
            destinationCityId: $request->receiverContact?->primaryAddress?->city_id,
            weight: $packages->sum(fn ($package) => ($package->weight ?? 0) * ($package->quantity ?? 1)),
            volume: $packages->sum(fn ($package) => $this->packageVolume($package) * ($package->quantity ?? 1)),
            hasVillage: $this->addressIsVillage($request->senderContact?->primaryAddress)
                || $this->addressIsVillage($request->receiverContact?->primaryAddress),
            shippingType: ShippingType::mostRestrictive($shippingTypes),
            courierCategory: CourierCategory::mostRestrictive($categories),
        );
    }

    protected function profileForOrderItem(OrderItem $orderItem): CourierRequestProfile
    {
        $package = $orderItem->package()
            ->with('consignmentType')
            ->with(['pickupAddress', 'dropoffAddress'])
            ->first();

        $consignmentType = $package?->consignmentType;

        return new CourierRequestProfile(
            originGovernorateId: $package?->pickupAddress?->governorate_id,
            originCityId: $package?->pickupAddress?->city_id,
            destinationGovernorateId: $package?->dropoffAddress?->governorate_id,
            destinationCityId: $package?->dropoffAddress?->city_id,
            weight: (float) ($package?->weight ?? 0),
            volume: (float) data_get($package, 'metadata.volume', 0),
            hasVillage: $this->addressIsVillage($package?->pickupAddress)
                || $this->addressIsVillage($package?->dropoffAddress),
            shippingType: $consignmentType?->shippingType ?? ShippingType::DIRECT_AND_MULTI,
            courierCategory: $consignmentType?->courierCategory,
        );
    }

    protected function packageVolume(ShipmentRequestPackage $package): float
    {
        $metadata = data_get($package, 'metadata', []);

        if ($volume = (float) data_get($metadata, 'volume', 0)) {
            return $volume;
        }

        if ($package->length && $package->width && $package->height) {
            // Dimensions are stored in centimeters → convert to cubic meters.
            return ($package->length * $package->width * $package->height) / 1_000_000;
        }

        return 0;
    }

    protected function addressIsVillage($address): bool
    {
        if (! $address) {
            return false;
        }

        return data_get($address, 'district_or_village_type') === 'village';
    }

    /**
     * @return array<int, int>
     */
    protected function alreadyOfferedRepresentativeIds(ShipmentRequest|OrderItem $assignable): array
    {
        return $assignable->assignments()
            ->whereIn('status', ['pending', 'accepted'])
            ->pluck('representative_id')
            ->all();
    }
}

