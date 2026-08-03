<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierAssignmentStatus;
use App\Enum\DispatchState;
use App\Enum\RequestLegType;
use App\Enum\RequestPathStatus;
use App\Enum\RequestPathType;
use App\Enum\ShipmentRequestStatus;
use App\Models\OrderItem;
use App\Models\RequestPath;
use App\Models\ShipmentRequest;
use App\Models\ShipmentRequestPackage;
use Illuminate\Support\Collection;

/**
 * Distributes requests to couriers (Sections 2–5): detects the request type,
 * creates the parallel paths, and records the per-leg courier offers.
 */
class CourierDispatchService
{
    public function __construct(
        protected CourierMatchService $matchService,
        protected CourierSystemConfigService $config,
        protected WorkingHoursService $workingHours,
        protected CourierRequestTypeDetector $typeDetector
    ) {}

    /**
     * Backward-compatible entry point used by the auto-assign-next timeout action.
     */
    public function dispatch(ShipmentRequest|OrderItem $assignable): DispatchResult
    {
        return $this->dispatchPathsFor($assignable);
    }

    public function dispatchPathsFor(ShipmentRequest|OrderItem $assignable): DispatchResult
    {
        $profile = $this->buildProfile($assignable);
        $type = $this->typeDetector->detect($profile);

        $paths = collect();
        $assignments = collect();

        foreach ($type->paths() as $pathType) {
            $path = $this->createPath($assignable, $pathType, $type);
            $paths->push($path);

            $excluded = $this->alreadyOfferedRepresentativeIds($assignable);

            foreach ($pathType->legs() as $legType) {
                $couriers = $this->matchService->eligibleCouriers($legType, $profile, $excluded);

                foreach ($couriers as $courier) {
                    $assignments->push(
                        $assignable->assignments()->create([
                            'request_path_id' => $path->id,
                            'leg_type' => $legType->value,
                            'representative_id' => $courier->id,
                            'status' => CourierAssignmentStatus::PENDING->value,
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
        }

        $this->markDispatched($assignable, $type, $profile);

        return new DispatchResult($profile, $type, $paths, $assignments);
    }

    /**
     * Dispatch a single path (used when re-matching one failed path).
     */
    public function dispatchPath(ShipmentRequest|OrderItem $assignable, RequestPathType $pathType): DispatchResult
    {
        $profile = $this->buildProfile($assignable);
        $type = $this->typeDetector->detect($profile);

        $path = $this->createPath($assignable, $pathType, $type);

        $excluded = $this->alreadyOfferedRepresentativeIds($assignable);
        $assignments = collect();

        foreach ($pathType->legs() as $legType) {
            $couriers = $this->matchService->eligibleCouriers($legType, $profile, $excluded);

            foreach ($couriers as $courier) {
                $assignments->push(
                    $assignable->assignments()->create([
                        'request_path_id' => $path->id,
                        'leg_type' => $legType->value,
                        'representative_id' => $courier->id,
                        'status' => CourierAssignmentStatus::PENDING->value,
                        'offered_at' => now(),
                        'response_deadline_at' => $this->workingHours->addWorkingHours(
                            now(),
                            $this->config->autoRejectWorkingHours(),
                        ),
                        'metadata' => $profile->toArray(),
                    ])
                );
            }
        }

        return new DispatchResult($profile, $type, collect([$path]), $assignments);
    }

    protected function createPath($assignable, RequestPathType $pathType, $type): RequestPath
    {
        return $assignable->requestPaths()->create([
            'type' => $pathType->value,
            'request_type' => $type->value,
            'status' => RequestPathStatus::MATCHING->value,
            'legs' => array_map(
                static fn (RequestLegType $leg) => $leg->value,
                $pathType->legs()
            ),
            'total_cost' => 0,
        ]);
    }

    protected function markDispatched($assignable, $type, CourierRequestProfile $profile): void
    {
        $payload = [
            'dispatch_state' => DispatchState::DISPATCHED->value,
            'dispatch_state_at' => now(),
            'request_type' => $type->value,
            'is_fast_delivery' => $type === \App\Enum\CourierRequestType::FAST_DELIVERY,
        ];

        if ($assignable instanceof ShipmentRequest) {
            $payload['status'] = ShipmentRequestStatus::MATCHING->value;
        }

        $assignable->update($payload);
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
            shippingType: $consignmentType?->shippingType ?? \App\Enum\ShippingType::DIRECT_AND_MULTI,
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
            ->whereIn('status', [
                CourierAssignmentStatus::PENDING->value,
                CourierAssignmentStatus::ACCEPTED->value,
                CourierAssignmentStatus::CONFIRMED->value,
            ])
            ->pluck('representative_id')
            ->all();
    }
}
