<?php

namespace App\Services;

use App\Models\Category;
use App\Models\City;
use App\Models\ShipmentCompany;
use App\Models\ShipmentLocation;
use App\Models\ShipmentCompanyCategoryPrice;
use App\Models\ShipmentCompanySubCategorySizePrice;
use App\Services\GoogleMapsService;
use App\Models\Config;

class ShipmentSuggestionService
{
    protected GoogleMapsService $mapsService;

    public function __construct(GoogleMapsService $mapsService)
    {
        $this->mapsService = $mapsService;
    }

    public function directPriceForCompany($companyId, $package, $pickup, $dropoff): array
    {
        // Check route coverage first
        if (
            !$this->companyCoversPickup($companyId, $pickup) ||
            !$this->companyCoversDropoff($companyId, $dropoff)
        ) {
            return [
                'covered' => false,
                'price'   => $this->zeroPrice()
            ];
        }
        // Calculate price using the internal full logic
        $price = $this->calculatePackagePrice($companyId, $package, $pickup, $dropoff);

        return [
            'covered' => true,
            'price'   => $price
        ];
    }


    /**
     * Main function to get shipment suggestions
     */
    public function getSuggestions(array $data): array
    {
        $pickup  = $data['pickup_address'];
        $dropoff = $data['dropoff_address'];
        $preferredId = $data['preferred_company_id'] ?? null;

        // Attach city names
        $pickup['city_name']  = City::active()->find($pickup['city_id'])?->name_en;
        $dropoff['city_name'] = City::active()->find($dropoff['city_id'])?->name_en;

        // Prepare packages
        $packages = $this->normalizePackages($data);

        // Load companies
        $companies = ShipmentCompany::active()->get();

        $directResults = [];
        $splitResults = [];

        // If preferred company is set
        if ($preferredId) {
            $preferred = $companies->firstWhere('id', $preferredId);
            if (!$preferred) {
                return ['success' => false, 'message' => 'Preferred company not found', 'code' => 404];
            }

            // DIRECT option for preferred (check this FIRST)
            if ($this->checkCoverage($preferred->id, $pickup, $dropoff)) {
                $directResults[] = $this->calculateDirect($preferred, $pickup, $dropoff, $packages, true);
            }

            // Split options involving preferred
            foreach ($companies as $other) {
                if ($other->id === $preferred->id) continue;

                // Preferred → Other
                if ($this->companyCoversPickup($preferred->id, $pickup) &&
                    $this->companyCoversDropoff($other->id, $dropoff)) {

                    $handoff = $this->findHandoff($preferred->id, $other->id, $pickup, $dropoff);
                    if ($handoff) {
                        $splitResults[] = $this->calculateSplit($preferred, $other, $pickup, $dropoff, $handoff, $packages, true);
                    }
                }

                // Other → Preferred
                if ($this->companyCoversPickup($other->id, $pickup) &&
                    $this->companyCoversDropoff($preferred->id, $dropoff)) {

                    $handoff = $this->findHandoff($other->id, $preferred->id, $pickup, $dropoff);
                    if ($handoff) {
                        $splitResults[] = $this->calculateSplit($other, $preferred, $pickup, $dropoff, $handoff, $packages, true);
                    }
                }
            }
        }

        // General direct options (non-preferred)
        foreach ($companies as $company) {
            // Skip preferred company if we already processed it
            if ($preferredId && $company->id == $preferredId) continue;

            if ($this->checkCoverage($company->id, $pickup, $dropoff)) {
                $directResults[] = $this->calculateDirect($company, $pickup, $dropoff, $packages, false);
            }
        }

        // General split options (non-preferred)
        foreach ($companies as $A) {
            foreach ($companies as $B) {
                if ($A->id === $B->id) continue;
                if (!$this->companyCoversPickup($A->id, $pickup)) continue;
                if (!$this->companyCoversDropoff($B->id, $dropoff)) continue;

                // Skip if this involves preferred company (already processed)
                if ($preferredId && ($A->id == $preferredId || $B->id == $preferredId)) {
                    continue;
                }

                $handoff = $this->findHandoff($A->id, $B->id, $pickup, $dropoff);
                if ($handoff) {
                    $splitResults[] = $this->calculateSplit($A, $B, $pickup, $dropoff, $handoff, $packages, false);
                }
            }
        }

        // Combine results: direct first, then split
        $results = array_merge($directResults, $splitResults);

        if (empty($results)) {
            return ['success' => false, 'message' => 'No shipment options found', 'code' => 404];
        }

        // Sort results with better prioritization
        $results = $this->sortResults($results);

        return [
            'success' => true,
            'message' => 'Shipment options',
            'results' => $results,
            'packages' => $packages
        ];
    }

    /** Normalize packages array */
    protected function normalizePackages(array $data): array
    {
        if (!empty($data['packages'])) {
            return array_map(fn($p, $i) => [
                'id' => $p['id'] ?? $i,
                'category_id' => $p['category_id'],
                'sub_category_id' => $p['sub_category_id'],
                'weight' => $p['weight'] ?? 1,
                'size' => $p['size'] ?? 1,       // direct size value
                'piece' => $p['piece'] ?? 1,
                'piece_type' => $p['piece_type'] ?? 'small',
                'pieces_per_package' => $p['pieces_per_package'] ?? 1,
            ], $data['packages'], array_keys($data['packages']));
        }

        // Legacy single package
        return [[
            'id' => 0,
            'category_id' => $data['category_ids'][0] ?? null,
            'sub_category_id' => $data['sub_category_ids'][0] ?? null,
            'weight' => $data['weight'] ?? 1,
            'size' => $data['size'] ?? 1,
            'piece' => $data['piece'] ?? 1,
            'piece_type' => $data['piece_type'] ?? 'small',
            'pieces_per_package' => $data['pieces_per_package'] ?? 1,
        ]];
    }

    /** Calculate price for direct shipment */
    protected function calculateDirect($company, $pickup, $dropoff, $packages, bool $preferred): array
    {
        $total = 0;
        $details = [];
        foreach ($packages as $pkg) {
            $price = $this->calculatePackagePrice($company->id, $pkg, $pickup, $dropoff);
            $details[] = [
                'package_id' => $pkg['id'], // Add package_id
                'package' => $pkg,
                'price' => $price,
                'total' => $price['client_total']
            ];
            $total += $price['client_total'];
        }

        return [
            'type' => 'direct',
            'company' => $company->toArray(),
            'prices' => $details, // This contains package_id now
            'total' => $total,
            'is_preferred' => $preferred
        ];
    }

    /** Calculate price for split shipment */
    protected function calculateSplit($A, $B, $pickup, $dropoff, $handoff, $packages, bool $preferred): array
    {
        $legA = $legB = [];
        $legA_prices = $legB_prices = []; // Separate arrays for prices
        $totalA = $totalB = 0;

        foreach ($packages as $pkg) {
            $priceA = $this->calculatePackagePrice($A->id, $pkg, $pickup, $handoff);
            $priceB = $this->calculatePackagePrice($B->id, $pkg, $handoff, $dropoff);

            $legA[] = [
                'package_id' => $pkg['id'],
                'package' => $pkg,
                'price' => $priceA
            ];
            $legB[] = [
                'package_id' => $pkg['id'],
                'package' => $pkg,
                'price' => $priceB
            ];

            // Also store separate price arrays for easy access
            $legA_prices[] = [
                'package_id' => $pkg['id'],
                'price' => $priceA
            ];
            $legB_prices[] = [
                'package_id' => $pkg['id'],
                'price' => $priceB
            ];

            $totalA += $priceA['client_total'];
            $totalB += $priceB['client_total'];
        }

        return [
            'type' => 'split',
            'pickup_company' => $A->toArray(),
            'dropoff_company' => $B->toArray(),
            'handoff_point' => $handoff,
            'legA' => $legA,
            'legB' => $legB,
            'legA_prices' => $legA_prices, // Add this
            'legB_prices' => $legB_prices, // Add this
            'legA_total' => $totalA,
            'legB_total' => $totalB,
            'total' => $totalA + $totalB,
            'is_preferred' => $preferred
        ];
    }

    /** Calculate package price based on category type */
    protected function calculatePackagePrice($companyId, $package, $pickup, $dropoff): array
    {
        $category = Category::find($package['category_id']);
        if (!$category) return $this->zeroPrice();

        $type = $category->type;

        // Calculate ACTUAL distance between pickup and dropoff points
        $km = $this->calculateActualDistance($pickup, point2: $dropoff);

        // Check if company has coverage for this route
        if (!$this->companyCoversPickup($companyId, $pickup) || !$this->companyCoversDropoff($companyId, $dropoff)) {
            return $this->zeroPrice();
        }

        // Check if company has pricing for this category
        if (!$this->companyHasPricing($companyId, $package['category_id'])) {
            return $this->zeroPrice();
        }


        $distanceFactor = $this->getDistanceFactor($km , $companyId);
        if ($dropoff['is_village']){
            $villageFactor  = $this->getVillageFactor($pickup, $dropoff , $companyId);
        }else{
            $villageFactor = 1 ;
        }

        return match ($type) {
            'piece' => $this->pricePiece($companyId, $package, $km, $distanceFactor, $villageFactor),
            'weight' => $this->priceWeight($companyId, $package, $km, $distanceFactor, $villageFactor),
            'weight_size' => $this->priceWeightSize($companyId, $package, $km, $distanceFactor, $villageFactor),
            default => $this->priceWeight($companyId, $package, $km, $distanceFactor, $villageFactor),
        };
    }

    /** Calculate actual distance between two points */
    protected function calculateActualDistance($point1, $point2): float
    {
        // Try road distance first
        $km = $this->mapsService->roadDistanceInKm(
            $point1['latitude'], $point1['longitude'],
            $point2['latitude'], $point2['longitude']
        );

        // Fallback to straight-line distance if road distance fails
        if ($km <= 0) {
            $km = $this->mapsService->distanceInKm(
                $point1['latitude'], $point1['longitude'],
                $point2['latitude'], $point2['longitude']
            );
        }

        return max(0.1, $km); // Minimum 0.1 km
    }

    /** Check if company has pricing for a category */
    protected function companyHasPricing($companyId, $categoryId): bool
    {
        return ShipmentCompanyCategoryPrice::where('shipment_company_id', $companyId)
            ->where('category_id', $categoryId)
            ->exists();
    }

    /** Price calculation helpers */
    protected function pricePiece($companyId, $package, $km, $distanceFactor, $villageFactor): array
    {
        $pieceFactor = $this->getPieceFactor($package['piece_type'], $companyId);

        if (!$pieceFactor) {
            return $this->zeroPrice();
        }

        $quantity = $package['piece'];
        $perPackage = $package['pieces_per_package'];

        $packageCount = ($quantity <= $perPackage)
            ? 1
            : (int) ceil($quantity / $perPackage);

        $pieceCost = (float) $packageCount * $pieceFactor;

        $baseCost = $pieceCost;
        $companyTotal = round($baseCost * $villageFactor, 2);


        return $this->applyClientPercent(
            $companyTotal,
            $km,
            $pieceCost,
            $pieceCost,
            $companyId
        );
    }


    protected function priceWeight($companyId, $package, $km, $distanceFactor, $villageFactor): array
    {
        $pricePerKg = $this->getPricePerKg($companyId, $package['category_id']);
        if ($pricePerKg <= 0) {
            return $this->zeroPrice();
        }

        $pricekmKg= $package['weight'] * $pricePerKg ;
        $baseCost = $pricekmKg * $villageFactor;

        $companyTotal = $baseCost * $distanceFactor;

        return $this->applyClientPercent($companyTotal, $km, $baseCost, 0,$companyId);
    }

    protected function priceWeightSize($companyId, $package, $km, $distanceFactor, $villageFactor): array
    {
        $pricePerKg = $this->getPricePerKg($companyId, $package['category_id']);
        if ($pricePerKg <= 0) {
            return $this->zeroPrice();
        }

        // $weightCost = $package['weight'] * $pricePerKg;
        $pricekmKg = $package['weight'] * $pricePerKg;
        $weightCost = $pricekmKg * $villageFactor;

        $sizeCost = $this->getSizeCost($companyId, $package,$villageFactor);


        // Base cost should be the greater value between weight and size
        $companyTotal = max($weightCost, $sizeCost);

        return $this->applyClientPercent($companyTotal, $km, $weightCost, $sizeCost,$companyId);
    }

    /** Get price per kg */
    protected function getPricePerKg($companyId, $categoryId): float
    {
        $price = ShipmentCompanyCategoryPrice::where('shipment_company_id', $companyId)
            ->where('category_id', $categoryId)
            ->value('price_per_kg');

        return $price ? (float)$price : 0;
    }

    /** Get subcategory size cost */
    protected function getSizeCost($companyId, $package , $villageFactor): float
    {
        $basePrice = ShipmentCompanyCategoryPrice::where('shipment_company_id', $companyId)
            ->where('category_id', $package['category_id'])
            ->value('price_per_size') ?? 0;

        // Multiply by package size and number of pieces
        $totalSizeCost = $basePrice * $package['size'] * $villageFactor;

        return round($totalSizeCost, 2);
    }

    /** Apply client percentage */
    protected function applyClientPercent($total, $km, $c1, $c2, $companyId): array
    {
        $percent = (float)$this->config('shipment.client_percentage', $companyId, 100);
        $clientTotal = round($total * ($percent / 100), 2);

        return [
            'distance_km' => round($km, 2),
            'cost_component_1' => round($c1, 2),
            'cost_component_2' => round($c2, 2),
            'company_total' => round($total, 2),
            'client_total' => $clientTotal,
            'client_percent' => $percent
        ];
    }

    /** Distance factor from config */
    protected function getDistanceFactor($km , $companyId): float
    {
        $rows = json_decode($this->config('shipment.distance_factors',$companyId, '[]'), true);
        foreach ($rows as $r) {
            if ($km <= $r['max']) return (float)$r['factor'];
        }
        return 1.0;
    }

    protected function getPieceFactor($pieceType, $companyId): float
    {
        // Get the per-piece configuration as an associative array
        $pieces = json_decode($this->config('shipment.per_piece', $companyId, '[]'), true);

        if (!$pieces) {
            return 1.0;
        }

        switch (strtolower($pieceType)) {
            case 'small':
                return isset($pieces['small']) ? (float)$pieces['small'] : 1.0;
            case 'medium':
                return isset($pieces['medium']) ? (float)$pieces['medium'] : 1.0;
            case 'large':
                return isset($pieces['large']) ? (float)$pieces['large'] : 1.0;
            case 'xlarge':
                return isset($pieces['xlarge']) ? (float)$pieces['xlarge'] : 1.0;
            default:
                return 1.0;
        }
    }



    /** Village factor */
    protected function getVillageFactor($pickup, $dropoff ,$companyId): float
    {
        $both = (float)$this->config('shipment.village.both', $companyId, 1.2);
        $one  = (float)$this->config('shipment.village.one', $companyId, 1.1);


        if (($pickup['is_village'] ?? false) && ($dropoff['is_village'] ?? false)) return $both;
        if (($pickup['is_village'] ?? false) || ($dropoff['is_village'] ?? false)) return $one;
        return 1.0;
    }

    /** Check direct coverage */
    protected function checkCoverage($companyId, $pickup, $dropoff): bool
    {
        return $this->companyCoversPickup($companyId, $pickup) && $this->companyCoversDropoff($companyId, $dropoff);
    }

    /** Check if company covers pickup */
    protected function companyCoversPickup($companyId, $pickup): bool
    {
        return ShipmentLocation::where('shipment_company_id', $companyId)
            ->active()
            ->whereJsonContains('zone', (string)$pickup['zone_id'])
            ->whereJsonContains('city', (string)$pickup['city_id'])
            ->whereJsonContains('state', (string)$pickup['state_id'])
            ->exists();
    }

    /** Check if company covers dropoff */
    protected function companyCoversDropoff($companyId, $dropoff): bool
    {
        return ShipmentLocation::where('shipment_company_id', $companyId)
            ->active()
            ->whereJsonContains('zone', (string)$dropoff['zone_id'])
            ->whereJsonContains('city', (string)$dropoff['city_id'])
            ->whereJsonContains('state', (string)$dropoff['state_id'])
            ->exists();
    }

    /** Find handoff point */
    protected function findHandoff($AId, $BId, $pickup, $dropoff): ?array
    {
        $common = $this->findCommonCoverage($AId, $BId);
        if (!$common) return null;

        // Get midpoint coordinates
        $mid = $this->mapsService->midpoint($pickup['latitude'], $pickup['longitude'], $dropoff['latitude'], $dropoff['longitude']);

        return [
            'latitude' => $mid['latitude'] ?? ($pickup['latitude'] + $dropoff['latitude']) / 2,
            'longitude' => $mid['longitude'] ?? ($pickup['longitude'] + $dropoff['longitude']) / 2,
            'zone_id' => $common['zone_id'],
            'city_id' => $common['city_id'],
            'state_id' => $common['state_id'],
            'is_village' => false, // Default, can be enhanced based on actual location
        ];
    }

    /** Find common coverage between two companies */
    protected function findCommonCoverage($AId, $BId): ?array
    {
        $locsA = ShipmentLocation::where('shipment_company_id', $AId)->active()->get();
        $locsB = ShipmentLocation::where('shipment_company_id', $BId)->active()->get();

        foreach ($locsA as $a) {
            foreach ($locsB as $b) {
                $commonStates = array_intersect((array)$a->state, (array)$b->state);
                $commonCities = array_intersect((array)$a->city, (array)$b->city);
                $commonZones  = array_intersect((array)$a->zone, (array)$b->zone);

                if (!empty($commonCities) && !empty($commonStates)) {
                    return [
                        'zone_id' => $commonZones[0] ?? null,
                        'city_id' => $commonCities[0],
                        'state_id' => $commonStates[0],
                    ];
                }
            }
        }
        return null;
    }

    /** Sort results: preferred first, then split, then by total */
    protected function sortResults(array $results): array
    {
        return collect($results)
            ->sortBy(function($r) {
                // Primary sort: preferred first
                $preferredScore = $r['is_preferred'] ? 0 : 1;

                // Secondary sort: direct before split
                $typeScore = $r['type'] === 'direct' ? 0 : 1;

                // Tertiary sort: by total price (lowest first)
                $priceScore = $r['total'];

                // Combine scores: preferred > type > price
                return ($preferredScore * 1000000) + ($typeScore * 10000) + $priceScore;
            })
            ->values()
            ->all();
    }

    /** Config helper */
    protected function config(string $key,$companyId, $default = null)
    {
        return Config::where('key', $key)->where('shipment_company_id', $companyId)->where('is_active', true)->value('value') ?? $default;
    }

    /** Return zero price */
    protected function zeroPrice(): array
    {
        return [
            'distance_km' => 0,
            'cost_component_1' => 0,
            'cost_component_2' => 0,
            'company_total' => 0,
            'client_total' => 0,
            'client_percent' => 100,
        ];
    }
}
