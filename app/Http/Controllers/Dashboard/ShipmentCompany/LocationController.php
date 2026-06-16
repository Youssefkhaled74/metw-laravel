<?php

namespace App\Http\Controllers\Dashboard\ShipmentCompany;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Location;
use App\Models\State;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->paginate(10);
        return view('dashboard.shipment.locations.index', compact('locations'));
    }

    public function create()
    {
        $locations = Location::latest()->paginate(10);
        return view('dashboard.shipment.locations.create' , compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'type'      => ['required', 'string'],
            'parent_id' => 'nullable|exists:locations,id',
            'path'      => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Location::create([
            'name'      => $validated['name'],
            'type'      => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'path'      => $validated['path'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('shipment.locations.index')
            ->with('success', 'Location created successfully.');
    }
    public function edit(Location $location)
    {
        $locations = Location::latest()->paginate(10);
        return view('dashboard.shipment.locations.edit', compact('location','locations'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'type'      => ['required', 'string'],
            'parent_id' => 'nullable|exists:locations,id',
            'path'      => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name'      => $validated['name'],
            'type'      => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'path'      => $validated['path'] ?? null,
            'is_active' => $request->boolean('is_active', $location->is_active),
        ];

        $location->update($data);

        return redirect()
            ->route('shipment.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()
            ->route('shipment.locations.index')
            ->with('success', 'Location deleted successfully.');
    }

    public function toggleStatus(Location $location)
    {
        $location->update([
            'is_active' => !$location->is_active
        ]);

        return redirect()
            ->route('shipment.locations.index')
            ->with('success', 'Location status updated successfully.');
    }


    public function getStates($countryId)
    {
        $states = State::where('country_id', $countryId)
            ->active()
            ->get()
            ->map(function ($state) {
                return [
                    'id' => $state->id,
                    'name' => $state->{'name_' . app()->getLocale()},
                ];
            });

        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $cities = City::where('state_id', $stateId)
            ->active()
            ->get()
            ->map(function ($city) {
                return [
                    'id' => $city->id,
                    'name' => $city->{'name_' . app()->getLocale()},
                ];
            });

        return response()->json($cities);
    }

    public function getZones($cityId)
    {
        $zones = Zone::where('city_id', $cityId)
            ->active()
            ->get()
            ->map(function ($zone) {
                return [
                    'id' => $zone->id,
                    'name' => $zone->{'name_' . app()->getLocale()},
                ];
            });

        return response()->json($zones);
    }

}
