<?php

namespace App\Http\Controllers\Dashboard\ShipmentCompany;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Location;
use App\Models\ShipmentLocation;
use App\Models\State;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ShipmentLocationController extends Controller
{
    public function index(Request $request)
    {
        $company = Auth::guard('shipment')->user();
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'sort_by' => ['nullable', 'in:created_at,country,state,city'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $locationsQuery = $company->locations();

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);

            $countryIds = Country::query()
                ->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_ar', 'like', "%{$search}%")
                ->pluck('id')
                ->all();

            $stateIds = State::query()
                ->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_ar', 'like', "%{$search}%")
                ->pluck('id')
                ->all();

            $cityIds = City::query()
                ->where('name_en', 'like', "%{$search}%")
                ->orWhere('name_ar', 'like', "%{$search}%")
                ->pluck('id')
                ->all();

            $locationsQuery->where(function ($query) use ($countryIds, $stateIds, $cityIds) {
                foreach ($countryIds as $id) {
                    $query->orWhereJsonContains('country', (int) $id);
                }
                foreach ($stateIds as $id) {
                    $query->orWhereJsonContains('state', (int) $id);
                }
                foreach ($cityIds as $id) {
                    $query->orWhereJsonContains('city', (int) $id);
                }
            });
        }

        if ($sortBy === 'country') {
            $locationsQuery->orderByRaw("(
                SELECT c.name_en
                FROM countries c
                WHERE c.id = CAST(JSON_UNQUOTE(JSON_EXTRACT(shipment_locations.country, '$[0]')) AS UNSIGNED)
                LIMIT 1
            ) {$sortDir}");
        } elseif ($sortBy === 'state') {
            $locationsQuery->orderByRaw("(
                SELECT s.name_en
                FROM states s
                WHERE s.id = CAST(JSON_UNQUOTE(JSON_EXTRACT(shipment_locations.state, '$[0]')) AS UNSIGNED)
                LIMIT 1
            ) {$sortDir}");
        } elseif ($sortBy === 'city') {
            $locationsQuery->orderByRaw("(
                SELECT ci.name_en
                FROM cities ci
                WHERE ci.id = CAST(JSON_UNQUOTE(JSON_EXTRACT(shipment_locations.city, '$[0]')) AS UNSIGNED)
                LIMIT 1
            ) {$sortDir}");
        } else {
            $locationsQuery->orderBy('shipment_locations.created_at', $sortDir);
        }

        $locations = $locationsQuery
            ->paginate(10)
            ->appends($request->query());

        $countryIds = $locations->getCollection()
            ->pluck('country')
            ->flatten()
            ->filter()
            ->unique()
            ->values();

        $stateIds = $locations->getCollection()
            ->pluck('state')
            ->flatten()
            ->filter()
            ->unique()
            ->values();

        $cityIds = $locations->getCollection()
            ->pluck('city')
            ->flatten()
            ->filter()
            ->unique()
            ->values();

        $zoneIds = $locations->getCollection()
            ->pluck('zone')
            ->flatten()
            ->filter()
            ->unique()
            ->values();

        $countriesMap = Country::whereIn('id', $countryIds)->get()->keyBy('id');
        $statesMap = State::whereIn('id', $stateIds)->get()->keyBy('id');
        $citiesMap = City::whereIn('id', $cityIds)->get()->keyBy('id');
        $zonesMap = Zone::whereIn('id', $zoneIds)->get()->keyBy('id');

        return view('dashboard.shipment.shipment-locations.index', compact('locations', 'countriesMap', 'statesMap', 'citiesMap', 'zonesMap'));
    }

    public function create()
    {
        $countries = Country::active()->get();
        $states    = State::active()->get();
        $cities    = City::active()->get();
        $zones     = Zone::active()->get();

        return view('dashboard.shipment.shipment-locations.create', compact('countries', 'states', 'cities', 'zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
            'city_id'    => 'required|exists:cities,id',
            'zone_id'    => 'nullable|array',
            'zone_id.*' => 'exists:zones,id',
        ]);

        $company = Auth::guard('shipment')->user();

        // Get existing locations for this company
        $existingLocations = $company->locations()->get();

        // Check if this exact combination already exists
        $exists = $existingLocations->first(function($location) use ($validated) {
            // Check if this single combination exists in any of the arrays
            $countryExists = in_array($validated['country_id'], $location->country ?? []);
            $stateExists = in_array($validated['state_id'], $location->state ?? []);
            $cityExists = in_array($validated['city_id'], $location->city ?? []);

            // Check zones if provided
            if (!empty($validated['zone_id'])) {
                foreach ($validated['zone_id'] as $zoneId) {
                    if (in_array($zoneId, $location->zone ?? [])) {
                        return true;
                    }
                }
            }

            return $countryExists && $stateExists && $cityExists;
        });

        if ($exists) {
            return redirect()
                ->route('shipment.shipment-locations.index')
                ->with('error', 'This location combination already exists.');
        }

        // Find or create a location to merge into
        // Try to find a location that shares some common elements
        $locationToUpdate = null;
        foreach ($existingLocations as $location) {
            // Check if there's any overlap that we can merge into
            if (in_array($validated['country_id'], $location->country ?? []) ||
                in_array($validated['state_id'], $location->state ?? []) ||
                in_array($validated['city_id'], $location->city ?? [])) {
                $locationToUpdate = $location;
                break;
            }
        }

        if ($locationToUpdate) {
            // Merge into existing location
            $country = array_unique(array_merge($locationToUpdate->country ?? [], [$validated['country_id']]));
            $state = array_unique(array_merge($locationToUpdate->state ?? [], [$validated['state_id']]));
            $city = array_unique(array_merge($locationToUpdate->city ?? [], [$validated['city_id']]));

            $zones = $locationToUpdate->zone ?? [];
            if (!empty($validated['zone_id'])) {
                $zones = array_unique(array_merge($zones, $validated['zone_id']));
            }

            $locationToUpdate->update([
                'country' => $country,
                'state' => $state,
                'city' => $city,
                'zone' => $zones,
            ]);
        } else {
            // Create new location
            ShipmentLocation::create([
                'shipment_company_id' => $company->id,
                'country' => [$validated['country_id']],
                'state' => [$validated['state_id']],
                'city' => [$validated['city_id']],
                'zone' => $validated['zone_id'] ?? []
            ]);
        }

        return redirect()
            ->route('shipment.shipment-locations.index')
            ->with('success', 'Shipment location saved successfully.');
    }


    public function edit(ShipmentLocation $shipmentLocation)
    {
        $countries = Country::active()->get();

        // For states, you might want to filter by selected countries
        $selectedCountryIds = $shipmentLocation->country ?? [];
        $states = State::active()->when(count($selectedCountryIds) > 0, function($query) use ($selectedCountryIds) {
            return $query->whereIn('country_id', $selectedCountryIds);
        })->get();

        // For cities, filter by selected states
        $selectedStateIds = $shipmentLocation->state ?? [];
        $cities = City::active()->when(count($selectedStateIds) > 0, function($query) use ($selectedStateIds) {
            return $query->whereIn('state_id', $selectedStateIds);
        })->get();

        $zones = Zone::active()->get();

        return view('dashboard.shipment.shipment-locations.edit', compact(
            'shipmentLocation', 'countries', 'states', 'cities', 'zones'
        ));
    }

    public function update(Request $request, ShipmentLocation $shipmentLocation)
    {
        $validated = $request->validate([
            'country' => 'required|array',
            'country.*' => 'exists:countries,id',
            'state' => 'required|array',
            'state.*' => 'exists:states,id',
            'city' => 'required|array',
            'city.*' => 'exists:cities,id',
            'zone' => 'nullable|array',
            'zone.*' => 'exists:zones,id',
        ]);

        $company = Auth::guard('shipment')->user();

        // Check if this combination already exists in another location
        if ($shipmentLocation->exists) {
            $otherLocations = $company->locations()
                ->where('id', '!=', $shipmentLocation->id)
                ->get();

            foreach ($otherLocations as $otherLocation) {
                // Check for any overlap
                $countryOverlap = array_intersect($validated['country'], $otherLocation->country ?? []);
                $stateOverlap = array_intersect($validated['state'], $otherLocation->state ?? []);
                $cityOverlap = array_intersect($validated['city'], $otherLocation->city ?? []);

                // If all three have overlap, they're duplicates
                if (!empty($countryOverlap) && !empty($stateOverlap) && !empty($cityOverlap)) {
                    // Check if zones also overlap (if zones are provided)
                    if (!empty($validated['zone'])) {
                        $zoneOverlap = array_intersect($validated['zone'], $otherLocation->zone ?? []);
                        if (!empty($zoneOverlap)) {
                            return redirect()
                                ->back()
                                ->withInput()
                                ->with('error', 'This location combination already exists in another record.');
                        }
                    } else {
                        return redirect()
                            ->back()
                            ->withInput()
                            ->with('error', 'This location combination already exists in another record.');
                    }
                }
            }
        }

        // Ensure arrays are unique
        $shipmentLocation->update([
            'country' => array_unique($validated['country']),
            'state' => array_unique($validated['state']),
            'city' => array_unique($validated['city']),
            'zone' => isset($validated['zone']) ? array_unique($validated['zone']) : [],
        ]);

        return redirect()
            ->route('shipment.shipment-locations.index')
            ->with('success', 'Shipment location updated successfully.');
    }


    public function destroy(ShipmentLocation $shipmentLocation)
    {
        $shipmentLocation->delete();

        return redirect()
            ->route('shipment.shipment-locations.index')
            ->with('success', 'Shipment location deleted successfully.');
    }

    public function toggleStatus(ShipmentLocation $shipmentLocation)
    {
        $shipmentLocation->update([
            'is_active' => !$shipmentLocation->is_active
        ]);

        return redirect()
            ->route('shipment.shipment-locations.index')
            ->with('success', 'Shipment location status updated successfully.');
    }
    public function getStatesByCountries(Request $request)
    {
        $countryIds = $request->input('country_ids', []);

        $states = State::active()
            ->whereIn('country_id', $countryIds)
            ->get()
            ->map(function ($state) {
                return [
                    'id'   => $state->id,
                    'name' => $state->name,
                ];
            });

        return response()->json($states);
    }

    public function getCitiesByStates(Request $request)
    {
        $stateIds = $request->input('state_ids', []);

        $cities = City::active()
            ->whereIn('state_id', $stateIds)
            ->get(['id', 'name_en', 'name_ar']);

        return response()->json(['cities' => $cities]);
    }
}
