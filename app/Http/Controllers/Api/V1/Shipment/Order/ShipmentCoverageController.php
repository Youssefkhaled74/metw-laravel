<?php

namespace App\Http\Controllers\Api\V1\Shipment\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\BestSuggestionRequest;
use App\Http\Requests\CheckCoverageRequest;
use App\Models\City;
use App\Models\ShipmentCompany;
use App\Models\ShipmentCompanyCategoryPrice;
use App\Models\ShipmentCompanySubCategorySizePrice;
use App\Models\ShipmentLocation;
use App\Services\CoverageService;
use App\Services\GoogleMapsService;
use App\Services\ShipmentSuggestionService;

class ShipmentCoverageController extends Controller
{
    protected $suggestionService;

    public function __construct(
        ShipmentSuggestionService $suggestionService
    ) {
        $this->suggestionService = $suggestionService;
    }
    /**
     * تحقق من تغطية الشركة وإرجاع الاقتراحات أو الحلول المقسّمة (split)
     */
    public function checkCoverage(CheckCoverageRequest $request)
    {
        $validated = $request->validated();

        // ✅ استدعاء الشركة المطلوبة
        $company = ShipmentCompany::findOrFail((int) $validated['shipment_company_id']);

        // ✅ تهيئة السيرفيس (مع Google Maps)
        $coverageService = new CoverageService(new GoogleMapsService());

        // ✅ استخراج بيانات pickup و dropoff
        $pickup = $validated['pickup_address'];
        $dropoff = $validated['dropoff_address'];

        // ✅ تنفيذ منطق التغطية
        $result = $coverageService->checkCompanyCoverage($company->id, $pickup, $dropoff);

        // ✅ إرسال الاستجابة النهائية
        return response()->json($result);
    }

    public function getSuggestions(BestSuggestionRequest $request)
    {
        $data = $request->validated();

        // Use the service to get suggestions
        $result = $this->suggestionService->getSuggestions($data);

        // Return appropriate response based on service result
        if ($result['success']) {
            return responseJson(
                true,
                $result['message'],
                [
                    'results' => $result['results'],
                    'packages' => $result['packages'] ?? []
                ]
            );
        }

        return responseJson(
            false,
            $result['message'],
            [],
            $result['code'] ?? 404
        );
    }

    // public function getSuggestions(BestSuggestionRequest $request)
    // {
    //     $data = $request->validated();

    //     $pickup    = $data['pickup_address'];
    //     $dropoff   = $data['dropoff_address'];
    //     $preferred = $data['preferred_company_id'] ?? null;

    //     // Attach city names
    //     $pickupCity = City::active()->find($pickup['city_id']);
    //     $dropoffCity = City::active()->find($dropoff['city_id']);
    //     $pickup['city_name'] = $pickupCity?->name_en;
    //     $dropoff['city_name'] = $dropoffCity?->name_en;

    //     // Handle packages or legacy single item format
    //     if (isset($data['packages'])) {
    //         // New format with packages array
    //         $packages = $data['packages'];
    //     } else {
    //         // Legacy format - create a single package from old fields
    //         $packages = [
    //             [
    //                 'category_id' => $data['category_ids'][0] ?? null,
    //                 'sub_category_id' => $data['sub_category_ids'][0] ?? null,
    //                 'weight' => $data['weight'] ?? 1,
    //                 'size_id' => $data['size_id'] ?? 1,
    //             ]
    //         ];
    //     }

    //     // Generate all packages (each package is its own combination)
    //     $packageCombinations = $this->generatePackageCombinations($packages);

    //     // Load active companies
    //     $companies = ShipmentCompany::active()->get();
    //     $results = [];

    //     /* ============================================================
    //     CASE 1: PREFERRED COMPANY
    //     ============================================================ */
    //     if ($preferred) {
    //         $pref = $companies->firstWhere('id', $preferred);

    //         if (!$pref) {
    //             return responseJson(false, "Preferred company not found", [], 404);
    //         }

    //         // 1) Split options involving preferred first
    //         foreach ($companies as $partner) {
    //             if ($partner->id == $pref->id) continue;

    //             // preferred → partner
    //             if ($this->companyCoversPickup($pref->id, $pickup) &&
    //                 $this->companyCoversDropoff($partner->id, $dropoff)) {
    //                 $common = $this->findCommonCoverageBetweenCompanies($pref->id, $partner->id);
    //                 if ($common) {
    //                     $handoff = $this->makeHandoffPoint($pickup, $dropoff, $common);

    //                     // Calculate prices for all packages
    //                     $legAPrices = [];
    //                     $legATotal = 0;
    //                     foreach ($packageCombinations as $package) {
    //                         $price = $this->calculatePriceForCompany(
    //                             $pref->id,
    //                             $package['category_id'],
    //                             $package['sub_category_id'],
    //                             $package['size_id'],
    //                             $package['weight'],
    //                             $pickup,
    //                             $handoff
    //                         );
    //                         $legAPrices[] = [
    //                             'package_id' => $package['id'] ?? null,
    //                             'category_id' => $package['category_id'],
    //                             'sub_category_id' => $package['sub_category_id'],
    //                             'size_id' => $package['size_id'],
    //                             'weight' => $package['weight'],
    //                             'price' => $price
    //                         ];
    //                         $legATotal += $price['total'];
    //                     }

    //                     $legBPrices = [];
    //                     $legBTotal = 0;
    //                     foreach ($packageCombinations as $package) {
    //                         $price = $this->calculatePriceForCompany(
    //                             $partner->id,
    //                             $package['category_id'],
    //                             $package['sub_category_id'],
    //                             $package['size_id'],
    //                             $package['weight'],
    //                             $handoff,
    //                             $dropoff
    //                         );
    //                         $legBPrices[] = [
    //                             'package_id' => $package['id'] ?? null,
    //                             'category_id' => $package['category_id'],
    //                             'sub_category_id' => $package['sub_category_id'],
    //                             'size_id' => $package['size_id'],
    //                             'weight' => $package['weight'],
    //                             'price' => $price
    //                         ];
    //                         $legBTotal += $price['total'];
    //                     }

    //                     // Calculate totals for each package
    //                     $packageTotals = [];
    //                     foreach ($packageCombinations as $index => $package) {
    //                         $total = ($legAPrices[$index]['price']['total'] ?? 0) +
    //                                 ($legBPrices[$index]['price']['total'] ?? 0);
    //                         $packageTotals[] = [
    //                             'package_id' => $package['id'] ?? $index,
    //                             'category_id' => $package['category_id'],
    //                             'sub_category_id' => $package['sub_category_id'],
    //                             'size_id' => $package['size_id'],
    //                             'weight' => $package['weight'],
    //                             'total' => $total
    //                         ];
    //                     }

    //                     $results[] = [
    //                         'type' => 'split',
    //                         'pickup_company' => $pref->toArray(),
    //                         'dropoff_company' => $partner->toArray(),
    //                         'handoff_point' => $handoff,
    //                         'legA_prices' => $legAPrices,
    //                         'legB_prices' => $legBPrices,
    //                         'legA_total' => $legATotal,
    //                         'legB_total' => $legBTotal,
    //                         'package_totals' => $packageTotals,
    //                         'total' => $legATotal + $legBTotal,
    //                         'is_preferred' => true,
    //                     ];
    //                 }
    //             }

    //             // partner → preferred
    //             if ($this->companyCoversPickup($partner->id, $pickup) &&
    //                 $this->companyCoversDropoff($pref->id, $dropoff)) {
    //                 $common = $this->findCommonCoverageBetweenCompanies($partner->id, $pref->id);
    //                 if ($common) {
    //                     $handoff = $this->makeHandoffPoint($pickup, $dropoff, $common);

    //                     $legAPrices = [];
    //                     $legATotal = 0;
    //                     foreach ($packageCombinations as $package) {
    //                         $price = $this->calculatePriceForCompany(
    //                             $partner->id,
    //                             $package['category_id'],
    //                             $package['sub_category_id'],
    //                             $package['size_id'],
    //                             $package['weight'],
    //                             $pickup,
    //                             $handoff
    //                         );
    //                         $legAPrices[] = [
    //                             'package_id' => $package['id'] ?? null,
    //                             'category_id' => $package['category_id'],
    //                             'sub_category_id' => $package['sub_category_id'],
    //                             'size_id' => $package['size_id'],
    //                             'weight' => $package['weight'],
    //                             'price' => $price
    //                         ];
    //                         $legATotal += $price['total'];
    //                     }

    //                     $legBPrices = [];
    //                     $legBTotal = 0;
    //                     foreach ($packageCombinations as $package) {
    //                         $price = $this->calculatePriceForCompany(
    //                             $pref->id,
    //                             $package['category_id'],
    //                             $package['sub_category_id'],
    //                             $package['size_id'],
    //                             $package['weight'],
    //                             $handoff,
    //                             $dropoff
    //                         );
    //                         $legBPrices[] = [
    //                             'package_id' => $package['id'] ?? null,
    //                             'category_id' => $package['category_id'],
    //                             'sub_category_id' => $package['sub_category_id'],
    //                             'size_id' => $package['size_id'],
    //                             'weight' => $package['weight'],
    //                             'price' => $price
    //                         ];
    //                         $legBTotal += $price['total'];
    //                     }

    //                     $packageTotals = [];
    //                     foreach ($packageCombinations as $index => $package) {
    //                         $total = ($legAPrices[$index]['price']['total'] ?? 0) +
    //                                 ($legBPrices[$index]['price']['total'] ?? 0);
    //                         $packageTotals[] = [
    //                             'package_id' => $package['id'] ?? $index,
    //                             'category_id' => $package['category_id'],
    //                             'sub_category_id' => $package['sub_category_id'],
    //                             'size_id' => $package['size_id'],
    //                             'weight' => $package['weight'],
    //                             'total' => $total
    //                         ];
    //                     }

    //                     $results[] = [
    //                         'type' => 'split',
    //                         'pickup_company' => $partner->toArray(),
    //                         'dropoff_company' => $pref->toArray(),
    //                         'handoff_point' => $handoff,
    //                         'legA_prices' => $legAPrices,
    //                         'legB_prices' => $legBPrices,
    //                         'legA_total' => $legATotal,
    //                         'legB_total' => $legBTotal,
    //                         'package_totals' => $packageTotals,
    //                         'total' => $legATotal + $legBTotal,
    //                         'is_preferred' => true,
    //                     ];
    //                 }
    //             }
    //         }

    //         // 2) Direct option for preferred
    //         if ($this->checkCompanyCoverage($pref->id, $pickup, $dropoff)['success']) {
    //             $prices = [];
    //             $totalPrice = 0;
    //             foreach ($packageCombinations as $package) {
    //                 $price = $this->calculatePriceForCompany(
    //                     $pref->id,
    //                     $package['category_id'],
    //                     $package['sub_category_id'],
    //                     $package['size_id'],
    //                     $package['weight'],
    //                     $pickup,
    //                     $dropoff
    //                 );
    //                 $prices[] = [
    //                     'package_id' => $package['id'] ?? null,
    //                     'category_id' => $package['category_id'],
    //                     'sub_category_id' => $package['sub_category_id'],
    //                     'size_id' => $package['size_id'],
    //                     'weight' => $package['weight'],
    //                     'price' => $price,
    //                     'total' => $price['total']
    //                 ];
    //                 $totalPrice += $price['total'];
    //             }

    //             $results[] = [
    //                 'type' => 'direct',
    //                 'company' => array_merge($pref->toArray(), [
    //                     'from_city' => $pickup['city_name'],
    //                     'to_city' => $dropoff['city_name'],
    //                 ]),
    //                 'prices' => $prices,
    //                 'total' => $totalPrice,
    //                 'is_preferred' => true,
    //             ];
    //         }

    //         // 3) Direct options for other companies
    //         foreach ($companies as $company) {
    //             if ($company->id === $pref->id) continue;

    //             if ($this->checkCompanyCoverage($company->id, $pickup, $dropoff)['success']) {
    //                 $prices = [];
    //                 $totalPrice = 0;
    //                 foreach ($packageCombinations as $package) {
    //                     $price = $this->calculatePriceForCompany(
    //                         $company->id,
    //                         $package['category_id'],
    //                         $package['sub_category_id'],
    //                         $package['size_id'],
    //                         $package['weight'],
    //                         $pickup,
    //                         $dropoff
    //                     );
    //                     $prices[] = [
    //                         'package_id' => $package['id'] ?? null,
    //                         'category_id' => $package['category_id'],
    //                         'sub_category_id' => $package['sub_category_id'],
    //                         'size_id' => $package['size_id'],
    //                         'weight' => $package['weight'],
    //                         'price' => $price,
    //                         'total' => $price['total']
    //                     ];
    //                     $totalPrice += $price['total'];
    //                 }

    //                 $results[] = [
    //                     'type' => 'direct',
    //                     'company' => array_merge($company->toArray(), [
    //                         'from_city' => $pickup['city_name'],
    //                         'to_city' => $dropoff['city_name'],
    //                     ]),
    //                     'prices' => $prices,
    //                     'total' => $totalPrice,
    //                     'is_preferred' => false,
    //                 ];
    //             }
    //         }

    //         if (empty($results)) {
    //             return responseJson(false, "No shipment options found", [], 404);
    //         }

    //         // Sort: preferred first, then by type (split first), then by total price
    //         $results = collect($results)
    //             ->sortByDesc('is_preferred')
    //             ->sortBy(function ($item) {
    //                 return $item['type'] === 'split' ? 0 : 1;
    //             })
    //             ->sortBy('total')
    //             ->values()
    //             ->all();

    //         return responseJson(true, "Shipment options with preferred company", [
    //             'results' => $results,
    //             'packages' => $packageCombinations
    //         ]);
    //     }

    //     /* ============================================================
    //     CASE 2: NO PREFERRED → SPLIT FIRST, THEN DIRECT
    //     ============================================================ */
    //     $results = [];

    //     // Split options
    //     foreach ($companies as $A) {
    //         foreach ($companies as $B) {
    //             if ($A->id === $B->id) continue;
    //             if (!$this->companyCoversPickup($A->id, $pickup)) continue;
    //             if (!$this->companyCoversDropoff($B->id, $dropoff)) continue;

    //             $common = $this->findCommonCoverageBetweenCompanies($A->id, $B->id);
    //             if (!$common) continue;

    //             $handoff = $this->makeHandoffPoint($pickup, $dropoff, $common);

    //             $legAPrices = [];
    //             $legATotal = 0;
    //             foreach ($packageCombinations as $package) {
    //                 $price = $this->calculatePriceForCompany(
    //                     $A->id,
    //                     $package['category_id'],
    //                     $package['sub_category_id'],
    //                     $package['size_id'],
    //                     $package['weight'],
    //                     $pickup,
    //                     $handoff
    //                 );
    //                 $legAPrices[] = [
    //                     'package_id' => $package['id'] ?? null,
    //                     'category_id' => $package['category_id'],
    //                     'sub_category_id' => $package['sub_category_id'],
    //                     'size_id' => $package['size_id'],
    //                     'weight' => $package['weight'],
    //                     'price' => $price
    //                 ];
    //                 $legATotal += $price['total'];
    //             }

    //             $legBPrices = [];
    //             $legBTotal = 0;
    //             foreach ($packageCombinations as $package) {
    //                 $price = $this->calculatePriceForCompany(
    //                     $B->id,
    //                     $package['category_id'],
    //                     $package['sub_category_id'],
    //                     $package['size_id'],
    //                     $package['weight'],
    //                     $handoff,
    //                     $dropoff
    //                 );
    //                 $legBPrices[] = [
    //                     'package_id' => $package['id'] ?? null,
    //                     'category_id' => $package['category_id'],
    //                     'sub_category_id' => $package['sub_category_id'],
    //                     'size_id' => $package['size_id'],
    //                     'weight' => $package['weight'],
    //                     'price' => $price
    //                 ];
    //                 $legBTotal += $price['total'];
    //             }

    //             $packageTotals = [];
    //             foreach ($packageCombinations as $index => $package) {
    //                 $total = ($legAPrices[$index]['price']['total'] ?? 0) +
    //                         ($legBPrices[$index]['price']['total'] ?? 0);
    //                 $packageTotals[] = [
    //                     'package_id' => $package['id'] ?? $index,
    //                     'category_id' => $package['category_id'],
    //                     'sub_category_id' => $package['sub_category_id'],
    //                     'size_id' => $package['size_id'],
    //                     'weight' => $package['weight'],
    //                     'total' => $total
    //                 ];
    //             }

    //             $results[] = [
    //                 'type' => 'split',
    //                 'pickup_company' => $A->toArray(),
    //                 'dropoff_company' => $B->toArray(),
    //                 'handoff_point' => $handoff,
    //                 'legA_prices' => $legAPrices,
    //                 'legB_prices' => $legBPrices,
    //                 'legA_total' => $legATotal,
    //                 'legB_total' => $legBTotal,
    //                 'package_totals' => $packageTotals,
    //                 'total' => $legATotal + $legBTotal,
    //                 'is_preferred' => false,
    //             ];
    //         }
    //     }

    //     // Direct options
    //     foreach ($companies as $company) {
    //         if ($this->checkCompanyCoverage($company->id, $pickup, $dropoff)['success']) {
    //             $prices = [];
    //             $totalPrice = 0;
    //             foreach ($packageCombinations as $package) {
    //                 $price = $this->calculatePriceForCompany(
    //                     $company->id,
    //                     $package['category_id'],
    //                     $package['sub_category_id'],
    //                     $package['size_id'],
    //                     $package['weight'],
    //                     $pickup,
    //                     $dropoff
    //                 );
    //                 $prices[] = [
    //                     'package_id' => $package['id'] ?? null,
    //                     'category_id' => $package['category_id'],
    //                     'sub_category_id' => $package['sub_category_id'],
    //                     'size_id' => $package['size_id'],
    //                     'weight' => $package['weight'],
    //                     'price' => $price,
    //                     'total' => $price['total']
    //                 ];
    //                 $totalPrice += $price['total'];
    //             }

    //             $results[] = [
    //                 'type' => 'direct',
    //                 'company' => array_merge($company->toArray(), [
    //                     'from_city' => $pickup['city_name'],
    //                     'to_city' => $dropoff['city_name'],
    //                 ]),
    //                 'prices' => $prices,
    //                 'total' => $totalPrice,
    //                 'is_preferred' => false,
    //             ];
    //         }
    //     }

    //     if (empty($results)) {
    //         return responseJson(false, "No shipment options found", [], 404);
    //     }

    //     // Sort: split first, then direct, then by total price
    //     $results = collect($results)
    //         ->sortBy(function ($item) {
    //             return $item['type'] === 'split' ? 0 : 1;
    //         })
    //         ->sortBy('total')
    //         ->values()
    //         ->all();

    //     return responseJson(true, "Shipment options", [
    //         'results' => $results,
    //         'packages' => $packageCombinations
    //     ]);
    // }

    private function generatePackageCombinations(array $packages): array
    {
        $combinations = [];

        foreach ($packages as $index => $package) {
            // Add unique ID for each package
            $combinations[] = [
                'id' => $package['id'] ?? $index, // Use provided ID or generate index
                'category_id' => $package['category_id'],
                'sub_category_id' => $package['sub_category_id'],
                'size_id' => $package['size_id'],
                'weight' => $package['weight'],
                'original_index' => $index // Keep original position
            ];
        }

        return $combinations;
    }

    /* ----------------------------------------------------
    Generate all category-subcategory combinations
    ---------------------------------------------------- */
    private function generateCategoryCombinations(array $categoryIds, array $subCategoryIds): array
    {
        $combinations = [];

        // If arrays have same length, pair them by index
        if (count($categoryIds) === count($subCategoryIds)) {
            foreach ($categoryIds as $index => $categoryId) {
                $combinations[] = [
                    'category_id' => $categoryId,
                    'sub_category_id' => $subCategoryIds[$index]
                ];
            }
        } else {
            // Otherwise create cartesian product
            foreach ($categoryIds as $categoryId) {
                foreach ($subCategoryIds as $subCategoryId) {
                    $combinations[] = [
                        'category_id' => $categoryId,
                        'sub_category_id' => $subCategoryId
                    ];
                }
            }
        }

        return $combinations;
    }

    /* ----------------------------------------------------
    Helper: use DB JSON checks for pickup/dropoff coverage
    ---------------------------------------------------- */
    private function companyCoversPickup(int $companyId, array $pickup): bool
    {
        return ShipmentLocation::where('shipment_company_id', $companyId)
            ->active()
            ->whereJsonContains('zone', (string)$pickup['zone_id'])
            ->whereJsonContains('city', (string)$pickup['city_id'])
            ->whereJsonContains('state', (string)$pickup['state_id'])
            ->exists();
    }

    private function companyCoversDropoff(int $companyId, array $dropoff): bool
    {
        return ShipmentLocation::where('shipment_company_id', $companyId)
            ->active()
            ->whereJsonContains('zone', (string)$dropoff['zone_id'])
            ->whereJsonContains('city', (string)$dropoff['city_id'])
            ->whereJsonContains('state', (string)$dropoff['state_id'])
            ->exists();
    }

    /* ----------------------------------------------------
    Helper: find common coverage between two companies
    ---------------------------------------------------- */
    private function findCommonCoverageBetweenCompanies(int $companyAId, int $companyBId): ?array
    {
        $locsA = ShipmentLocation::where('shipment_company_id', $companyAId)->active()->get();
        $locsB = ShipmentLocation::where('shipment_company_id', $companyBId)->active()->get();

        foreach ($locsA as $la) {
            foreach ($locsB as $lb) {
                $statesA = (array) $la->state;
                $statesB = (array) $lb->state;
                $citiesA = (array) $la->city;
                $citiesB = (array) $lb->city;
                $zonesA  = (array) $la->zone;
                $zonesB  = (array) $lb->zone;

                $commonCities = array_values(array_intersect($citiesA, $citiesB));
                $commonStates = array_values(array_intersect($statesA, $statesB));
                $commonZones  = array_values(array_intersect($zonesA, $zonesB));

                if (!empty($commonCities) && !empty($commonStates)) {
                    return [
                        'zone_id' => $commonZones[0] ?? null,
                        'city_id' => $commonCities[0],
                        'state_id' => $commonStates[0],
                    ];
                }

                if (!empty($commonCities)) {
                    return [
                        'zone_id' => $commonZones[0] ?? null,
                        'city_id' => $commonCities[0],
                        'state_id' => $commonStates[0] ?? null,
                    ];
                }

                if (!empty($commonZones)) {
                    return [
                        'zone_id' => $commonZones[0],
                        'city_id' => $commonCities[0] ?? null,
                        'state_id' => $commonStates[0] ?? null,
                    ];
                }
            }
        }

        return null;
    }

    /* ----------------------------------------------------
        Check coverage for direct shipments
    ---------------------------------------------------- */
    private function checkCompanyCoverage($companyId, $pickup, $dropoff)
    {
        $company = ShipmentCompany::find($companyId);
        if (!$company) return ['success' => false];

        $pickupCovered = $company->locations()
            ->active()
            ->whereJsonContains('zone', (string)$pickup['zone_id'])
            ->whereJsonContains('city', (string)$pickup['city_id'])
            ->whereJsonContains('state', (string)$pickup['state_id'])
            ->exists();

        $dropoffCovered = $company->locations()
            ->active()
            ->whereJsonContains('zone', (string)$dropoff['zone_id'])
            ->whereJsonContains('city', (string)$dropoff['city_id'])
            ->whereJsonContains('state', (string)$dropoff['state_id'])
            ->exists();

        return [
            'success' => $pickupCovered && $dropoffCovered
        ];
    }

    /* ----------------------------------------------------
        Calculate price for a company
    ---------------------------------------------------- */
    private function calculatePriceForCompany(
        $companyId,
        $categoryId,
        $subCategoryId,
        $sizeId,
        $weight,
        $pickup,
        $dropoff
    ) {
        // 1️⃣ Base shipment cost (can be dynamic based on category)
        $baseShipmentCost = 30; // You can make this dynamic based on category

        // 2️⃣ Calculate distance in kilometers
        $km = app(GoogleMapsService::class)->distanceInKm(
            $pickup['latitude'], $pickup['longitude'],
            $dropoff['latitude'], $dropoff['longitude']
        );

        // 3️⃣ Determine distance multiplier
        if ($km <= 100) {
            $distanceMultiplier = 1.0;
        } elseif ($km <= 200) {
            $distanceMultiplier = 1.2;
        } elseif ($km <= 300) {
            $distanceMultiplier = 1.3;
        } elseif ($km <= 400) {
            $distanceMultiplier = 1.4;
        } elseif ($km <= 500) {
            $distanceMultiplier = 1.5;
        } elseif ($km <= 600) {
            $distanceMultiplier = 1.6;
        } elseif ($km <= 700) {
            $distanceMultiplier = 1.7;
        } elseif ($km <= 800) {
            $distanceMultiplier = 1.8;
        } elseif ($km <= 900) {
            $distanceMultiplier = 1.9;
        } else {
            $distanceMultiplier = 2.0;
        }

        // 4️⃣ Calculate initial cost based on distance
        $initialCost = $baseShipmentCost * $distanceMultiplier;

        // 5️⃣ Village factor
        $villageFactor = 0;
        if (($pickup['is_village'] ?? false) && ($dropoff['is_village'] ?? false)) {
            $villageFactor = 0.20;
        } elseif (($pickup['is_village'] ?? false) || ($dropoff['is_village'] ?? false)) {
            $villageFactor = 0.10;
        }

        $finalCost = $initialCost + ($initialCost * $villageFactor);

        // 6️⃣ Add subcategory and size pricing
        $categoryPrice = ShipmentCompanyCategoryPrice::where('shipment_company_id', $companyId)
            ->where('main_category_id', $categoryId)
            ->first();

        $sizePricing = 0;
        if ($categoryPrice) {
            $sub = ShipmentCompanySubCategorySizePrice::where('shipment_company_category_price_id', $categoryPrice->id)
                ->where('category_id', $subCategoryId)
                ->first();

            if ($sub) {
                $sizePricing = match ($sizeId) {
                    1 => $sub->price_small,
                    2 => $sub->price_medium,
                    3 => $sub->price_large,
                    default => $sub->price_small,
                };
            }
        }

        $total = round($finalCost + $sizePricing, 2);

        return [
            'distance_km' => round($km, 2),
            'initial_cost' => round($initialCost, 2),
            'village_factor' => $villageFactor,
            'size_cost' => $sizePricing,
            'total' => $total,
        ];
    }

/* ----------------------------------------------------
   Make handoff point
---------------------------------------------------- */
private function makeHandoffPoint($pickup, $dropoff, $common)
{
    $mid = app(GoogleMapsService::class)->midpoint(
        $pickup['latitude'], $pickup['longitude'],
        $dropoff['latitude'], $dropoff['longitude']
    );

    return [
        'latitude' => $mid['lat'] ?? ($pickup['latitude'] + $dropoff['latitude'])/2,
        'longitude' => $mid['lng'] ?? ($pickup['longitude'] + $dropoff['longitude'])/2,
        'zone_id' => $common['zone_id'],
        'city_id' => $common['city_id'],
        'state_id' => $common['state_id'],
    ];
}


    private function findBestSplitRoute($categoryId, $subCategoryId, $sizeId, $weight, $pickup, $dropoff)
    {
        $companies = ShipmentCompany::active()->get();

        $pickupCompanies = [];
        $dropoffCompanies = [];

        foreach ($companies as $company) {
            if ($company->locations()->active()->whereJsonContains('zone', (string) $pickup['zone_id'])->exists()) {
                $pickupCompanies[] = $company;
            }

            if ($company->locations()->active()->whereJsonContains('zone->id', $dropoff['zone_id'])->exists()) {
                $dropoffCompanies[] = $company;
            }
        }

        $routes = [];

        foreach ($pickupCompanies as $A) {
            foreach ($dropoffCompanies as $B) {

                if ($A->id === $B->id) continue; // already checked direct earlier

                // mid-point handoff
                $handoff = app(GoogleMapsService::class)->midpoint(
                    $pickup['latitude'], $pickup['longitude'],
                    $dropoff['latitude'], $dropoff['longitude']
                );

                // segment A → handoff
                $legA = $this->calculatePriceForCompany(
                    $A->id, $categoryId, $subCategoryId, $sizeId, $weight,
                    $pickup,
                    $handoff
                );

                // segment B → dropoff
                $legB = $this->calculatePriceForCompany(
                    $B->id, $categoryId, $subCategoryId, $sizeId, $weight,
                    $handoff,
                    $dropoff
                );

                $routes[] = [
                    'pickup_company' => $A,
                    'dropoff_company' => $B,
                    'handoff_point' => $handoff,
                    'legA' => $legA,
                    'legB' => $legB,
                    'total' => $legA['total'] + $legB['total'],
                ];
            }
        }

        if (empty($routes)) {
            return null;
        }

        usort($routes, fn($a, $b) => $a['total'] <=> $b['total']);

        return $routes[0];
    }


    /**
     * الحصول على أفضل اقتراح (Best Split Suggestion)
     */
    public function getBestSuggestion(CheckCoverageRequest $request)
    {
        $validated = $request->validated();

        try {
            $company = ShipmentCompany::findOrFail((int) $validated['shipment_company_id']);

            $coverageService = new CoverageService(new GoogleMapsService());

            $pickup = $validated['pickup_address'];
            $dropoff = $validated['dropoff_address'];

            $analysis = $coverageService->analyzeCoverage($company, $pickup, $dropoff);

            if (
                empty($analysis['coverage_type']) ||
                $analysis['coverage_type'] !== 'split_via_shared_location' ||
                empty($analysis['best_suggestion'])
            ) {
                return responseJson(false, 'No split delivery suggestion found', [
                    'can_deliver' => false,
                ], 422);
            }

            return responseJson(true, 'Best split delivery suggestion found', [
                'can_deliver' => true,
                'best_suggestion' => $analysis['best_suggestion'],
            ]);
        } catch (\Throwable $e) {
            return responseJson(false, 'Error getting best suggestion: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * الحصول على handoff point فقط من أفضل اقتراح
     */
    public function getHandoffPoint(CheckCoverageRequest $request)
    {
        $validated = $request->validated();

        try {
            $company = ShipmentCompany::findOrFail((int) $validated['shipment_company_id']);
            $coverageService = new CoverageService(new GoogleMapsService());

            $pickup = $validated['pickup_address'];
            $dropoff = $validated['dropoff_address'];

            $analysis = $coverageService->analyzeCoverage($company, $pickup, $dropoff);

            if (
                empty($analysis['coverage_type']) ||
                $analysis['coverage_type'] !== 'split_via_shared_location' ||
                empty($analysis['best_suggestion']['handoff_point'])
            ) {
                return responseJson(false, 'No handoff point found', [
                    'can_deliver' => false,
                ], 422);
            }

            return responseJson(true, 'Handoff point retrieved successfully', [
                'handoff_point' => $analysis['best_suggestion']['handoff_point'],
            ]);
        } catch (\Throwable $e) {
            return responseJson(false, 'Error retrieving handoff point: ' . $e->getMessage(), null, 500);
        }
    }
}
