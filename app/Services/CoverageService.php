<?php

namespace App\Services;

use App\Models\ShipmentCompany;
use App\Models\Zone;
use App\Models\City;
use App\Models\State;

class CoverageService
{
    protected GoogleMapsService $googleMapsService;

    public function __construct(GoogleMapsService $googleMapsService)
    {
        $this->googleMapsService = $googleMapsService;
    }

    /**
     * Check coverage and return either full delivery or split suggestions
     *
     * @param int $companyId  - selected pickup company id
     * @param array $pickup   - pickup data from request (contains city_id/state_id/zone_id/latitude/longitude/location/address...)
     * @param array $dropoff  - dropoff data from request
     * @return array
     */
    public function checkCompanyCoverage(int $companyId, array $pickup, array $dropoff): array
    {
        $company = ShipmentCompany::with('locations')->find($companyId);

        if (!$company) {
            return [
                'success' => false,
                'message' => 'Company not found',
                'data' => [
                    'coverage_status' => 'not_found',
                    'can_deliver' => false,
                ]
            ];
        }

        // determine if this company covers pickup and dropoff zone (zone is the most granular)
        $pickupCovered = false;
        $dropoffCovered = false;

        foreach ($company->locations as $location) {
            $zones = (array) $location->zone;

            if (!empty($pickup['zone_id']) && in_array((string)$pickup['zone_id'], $zones)) {
                $pickupCovered = true;
            }

            if (!empty($dropoff['zone_id']) && in_array((string)$dropoff['zone_id'], $zones)) {
                $dropoffCovered = true;
            }

            if ($pickupCovered && $dropoffCovered) break;
        }

        // Full coverage - same company can handle pickup -> dropoff
        if ($pickupCovered && $dropoffCovered) {
            $distance = $this->googleMapsService->distanceInKm(
                (float)$pickup['latitude'],
                (float)$pickup['longitude'],
                (float)$dropoff['latitude'],
                (float)$dropoff['longitude']
            );

            $cost = round($distance * (float)$company->price_per_km, 2);

            return [
                'success' => true,
                'message' => 'Selected company covers both pickup and dropoff locations',
                'data' => [
                    'coverage_status' => 'full_coverage',
                    'can_deliver' => true,
                    'requires_split' => false,
                    'direct_delivery_available' => true,
                    'suggestions' => [
                        [
                            'pickup_leg' => [
                                'company_id' => $company->id,
                                'company_name' => $company->name,
                                'company_logo' => $company->logo ? asset($company->logo) : null,
                                'price_per_km' => (float)$company->price_per_km,
                                'distance_km' => round($distance, 2),
                                'cost' => $cost,
                                'coverage_level' => 'zone',
                                'from' => $this->formatFullLocation($pickup),
                                'to' => $this->formatFullLocation($dropoff),
                            ],
                            'total_cost' => $cost,
                            'total_distance_km' => round($distance, 2),
                            'route_summary' => "{$company->name} handles full delivery from {$pickup['location']} to {$dropoff['location']}",
                        ]
                    ]
                ]
            ];
        }

        // Partial coverage: pickup or dropoff covered by selected company -> try split suggestions
        if ($pickupCovered || $dropoffCovered) {
            $suggestions = $this->suggestSplitDelivery($company, $pickup, $dropoff);

            return [
                'success' => !empty($suggestions),
                'message' => !empty($suggestions)
                    ? 'Split delivery possible via shared locations'
                    : 'Partial coverage but no shared points found',
                'data' => [
                    'coverage_status' => !empty($suggestions) ? 'split_via_shared_location' : 'partial_coverage',
                    'can_deliver' => !empty($suggestions),
                    'company' => $this->formatCompany($company),
                    'pickup' => $this->formatFullLocation($pickup),
                    'dropoff' => $this->formatFullLocation($dropoff),
                    'requires_split' => true,
                    'direct_delivery_available' => false,
                    'suggestions' => $suggestions,
                ]
            ];
        }

        // No coverage
        return [
            'success' => false,
            'message' => 'Selected company does not cover pickup or dropoff locations',
            'data' => [
                'coverage_status' => 'no_coverage',
                'can_deliver' => false,
            ]
        ];
    }

    /**
     * Build split delivery suggestions.
     * For each company that can reach dropoff, find shared coverage with pickup company at zone->city->state levels.
     *
     * @param \App\Models\ShipmentCompany $pickupCompany
     * @param array $pickup
     * @param array $dropoff
     * @return array
     */
    public function suggestSplitDelivery(ShipmentCompany $pickupCompany, array $pickup, array $dropoff): array
    {
        $shareCompanies = ShipmentCompany::whereHas('locations', function ($q) use ($dropoff) {
            $q->whereJsonContains('zone', (string)($dropoff['zone_id'] ?? ''));
        })->with('locations')->get();

        $suggestions = [];

        // prepluck pickup company's covered ids for faster lookups
        $pickupZones = $pickupCompany->locations->pluck('zone')->flatten()->map(fn($v) => (string)$v)->unique()->toArray();
        $pickupCities = $pickupCompany->locations->pluck('city')->flatten()->map(fn($v) => (string)$v)->unique()->toArray();
        $pickupStates = $pickupCompany->locations->pluck('state')->flatten()->map(fn($v) => (string)$v)->unique()->toArray();

        foreach ($shareCompanies as $shareCompany) {
            // skip same company
            if ($shareCompany->id === $pickupCompany->id) continue;

            $otherZones = $shareCompany->locations->pluck('zone')->flatten()->map(fn($v) => (string)$v)->unique()->toArray();
            $otherCities = $shareCompany->locations->pluck('city')->flatten()->map(fn($v) => (string)$v)->unique()->toArray();
            $otherStates = $shareCompany->locations->pluck('state')->flatten()->map(fn($v) => (string)$v)->unique()->toArray();

            // 1) zone-level matches (most precise)
            $commonZones = array_values(array_intersect($pickupZones, $otherZones));
            foreach ($commonZones as $zoneId) {
                $zone = Zone::find($zoneId);
                if (!$zone) continue;

                // build handoff location full details (zone + city + state)
                $handoffLocation = $this->buildHandoffLocationFromZone($zone);

                // get coordinates (try zone name + city + state for best geocode)
                $geoName = $this->buildGeocodeNameForHandoff($handoffLocation);
                $coords = $this->googleMapsService->getHandoffPoint($geoName);
                if (!$coords) continue;

                $suggestions[] = $this->buildSuggestionEntry(
                    $pickupCompany,
                    $shareCompany,
                    $pickup,
                    $dropoff,
                    'zone',
                    $handoffLocation,
                    $coords
                );
            }

            // 2) city-level matches
            $commonCities = array_values(array_intersect($pickupCities, $otherCities));
            foreach ($commonCities as $cityId) {
                $city = City::find($cityId);
                if (!$city) continue;

                $handoffLocation = [
                    'state' => $city->state_id ? $this->modelToArray(State::find($city->state_id)) : null,
                    'city' => $this->modelToArray($city),
                    'zone' => null,
                ];

                $geoName = $this->buildGeocodeNameForHandoff($handoffLocation);
                $coords = $this->googleMapsService->getHandoffPoint($geoName);
                if (!$coords) continue;

                $suggestions[] = $this->buildSuggestionEntry(
                    $pickupCompany,
                    $shareCompany,
                    $pickup,
                    $dropoff,
                    'city',
                    $handoffLocation,
                    $coords
                );
            }

            // 3) state-level matches
            $commonStates = array_values(array_intersect($pickupStates, $otherStates));
            foreach ($commonStates as $stateId) {
                $state = State::find($stateId);
                if (!$state) continue;

                $handoffLocation = [
                    'state' => $this->modelToArray($state),
                    'city' => null,
                    'zone' => null,
                ];

                $geoName = $this->buildGeocodeNameForHandoff($handoffLocation);
                $coords = $this->googleMapsService->getHandoffPoint($geoName);
                if (!$coords) continue;

                $suggestions[] = $this->buildSuggestionEntry(
                    $pickupCompany,
                    $shareCompany,
                    $pickup,
                    $dropoff,
                    'state',
                    $handoffLocation,
                    $coords
                );
            }
        }

        return $suggestions;
    }

    /**
     * Build a single suggestion entry with all required fields.
     *
     * @param ShipmentCompany $pickupCompany
     * @param ShipmentCompany $handoffCompany
     * @param array $pickup
     * @param array $dropoff
     * @param string $matchType  // 'zone'|'city'|'state'
     * @param array $handoffLocation // ['state'=>['id','name'], 'city'=>..., 'zone'=>...]
     * @param array $coords // ['latitude'=>.., 'longitude'=>..]
     * @return array
     */
    protected function buildSuggestionEntry(ShipmentCompany $pickupCompany, ShipmentCompany $handoffCompany, array $pickup, array $dropoff, string $matchType, array $handoffLocation, array $coords): array
    {
        $handoffLat = $coords['latitude'] ?? null;
        $handoffLng = $coords['longitude'] ?? null;

        if (!$handoffLat || !$handoffLng) {
            return [];
        }

        // distances
        $distancePickupToHandoff = $this->googleMapsService->distanceInKm(
            (float)$pickup['latitude'],
            (float)$pickup['longitude'],
            (float)$handoffLat,
            (float)$handoffLng
        );

        $distanceHandoffToDropoff = $this->googleMapsService->distanceInKm(
            (float)$handoffLat,
            (float)$handoffLng,
            (float)$dropoff['latitude'],
            (float)$dropoff['longitude']
        );

        // costs
        $pickupCost = round($distancePickupToHandoff * (float)$pickupCompany->price_per_km, 2);
        $dropoffCost = round($distanceHandoffToDropoff * (float)$handoffCompany->price_per_km, 2);

        $totalCost = round($pickupCost + $dropoffCost, 2);
        $totalDistance = round($distancePickupToHandoff + $distanceHandoffToDropoff, 2);

        // from/to objects for legs (include full location details)
        // For "to" or "from" that are handoff locations we pass the handoffLocation structure
        $pickupLeg = [
            'company_id' => $pickupCompany->id,
            'company_name' => $pickupCompany->name,
            'company_logo' => $pickupCompany->logo ? asset($pickupCompany->logo) : null,
            'price_per_km' => (float)$pickupCompany->price_per_km,
            'distance_km' => round($distancePickupToHandoff, 2),
            'cost' => $pickupCost,
            'coverage_level' => $matchType,
            'from' => $this->formatFullLocation($pickup),
            'to' => $handoffLocation,
        ];

        $dropoffLeg = [
            'company_id' => $handoffCompany->id,
            'company_name' => $handoffCompany->name,
            'company_logo' => $handoffCompany->logo ? asset($handoffCompany->logo) : null,
            'price_per_km' => (float)$handoffCompany->price_per_km,
            'distance_km' => round($distanceHandoffToDropoff, 2),
            'cost' => $dropoffCost,
            'coverage_level' => $matchType,
            'from' => $handoffLocation,
            'to' => $this->formatFullLocation($dropoff),
        ];

        $routeSummary = "{$pickupCompany->name} handles pickup from " .
            ($pickup['location'] ?? $this->shortLocationString($pickup)) .
            " to " . ($handoffLocation['zone']['name'] ?? $handoffLocation['city']['name'] ?? $handoffLocation['state']['name'] ?? 'handoff') .
            ", then {$handoffCompany->name} handles delivery to " .
            ($dropoff['location'] ?? $this->shortLocationString($dropoff));

        return [
            'pickup_leg' => $pickupLeg,
            'dropoff_leg' => $dropoffLeg,
            'match_type' => $matchType,
            'handoff' => array_merge(
                $handoffLocation,
                ['lat' => $handoffLat, 'lng' => $handoffLng]
            ),
            'handoff_company' => $this->formatCompany($handoffCompany),
            'total_cost' => $totalCost,
            'total_distance_km' => $totalDistance,
            'route_summary' => $routeSummary,
        ];
    }

    /**
     * Convert an Eloquent model instance to ['id'=>..., 'name'=>...] or null if model null.
     */
    protected function modelToArray($model): ?array
    {
        if (!$model) return null;
        return ['id' => $model->id, 'name' => $model->name_en];
    }

    /**
     * Build handoff location array given a Zone model (include its city and state)
     */
    protected function buildHandoffLocationFromZone(Zone $zone): array
    {
        $city = $zone->city_id ? City::find($zone->city_id) : null;
        $state = $city && $city->state_id ? State::find($city->state_id) : ($zone->state_id ? State::find($zone->state_id) : null);

        return [
            'state' => $this->modelToArray($state),
            'city' => $this->modelToArray($city),
            'zone' => $this->modelToArray($zone),
        ];
    }

    /**
     * Given handoffLocation array, build a good geocode string (zone -> city,state -> state)
     */
    protected function buildGeocodeNameForHandoff(array $handoffLocation): string
    {
        // Prefer zone if available (zone name + city + state)
        if (!empty($handoffLocation['zone'])) {
            $zoneName = $handoffLocation['zone']['name'] ?? null;
            $cityName = $handoffLocation['city']['name'] ?? null;
            $stateName = $handoffLocation['state']['name'] ?? null;
            $parts = array_filter([$zoneName, $cityName, $stateName, 'Egypt']);
            return implode(', ', $parts);
        }

        // then city
        if (!empty($handoffLocation['city'])) {
            $cityName = $handoffLocation['city']['name'] ?? null;
            $stateName = $handoffLocation['state']['name'] ?? null;
            $parts = array_filter([$cityName, $stateName, 'Egypt']);
            return implode(', ', $parts);
        }

        // fallback to state
        if (!empty($handoffLocation['state'])) {
            $stateName = $handoffLocation['state']['name'] ?? null;
            $parts = array_filter([$stateName, 'Egypt']);
            return implode(', ', $parts);
        }

        return 'Egypt';
    }

    /**
     * Short human readable fallback from address array
     */
    protected function shortLocationString(array $address): string
    {
        $parts = [];
        if (!empty($address['location'])) $parts[] = $address['location'];
        if (!empty($address['city_id'])) {
            $city = City::find($address['city_id']);
            if ($city) $parts[] = $city->name_en;
        }
        if (!empty($address['state_id'])) {
            $state = State::find($address['state_id']);
            if ($state) $parts[] = $state->name_en;
        }
        return implode(', ', $parts);
    }

    /**
     * Format company basic info
     */
    protected function formatCompany(ShipmentCompany $company): array
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'price_per_km' => (float)$company->price_per_km,
        ];
    }

    /**
     * Format a full location object (state/city/zone) from an input address array.
     * The input may contain keys: state_id, city_id, zone_id.
     *
     * @param array $address
     * @return array{state: ?array, city: ?array, zone: ?array}
     */
    protected function formatFullLocation(array $address): array
    {
        $state = isset($address['state_id']) && $address['state_id'] ? State::find($address['state_id']) : null;
        $city  = isset($address['city_id']) && $address['city_id'] ? City::find($address['city_id']) : null;
        $zone  = isset($address['zone_id']) && $address['zone_id'] ? Zone::find($address['zone_id']) : null;

        return [
            'state' => $this->modelToArray($state),
            'city' => $this->modelToArray($city),
            'zone' => $this->modelToArray($zone),
        ];
    }
}
