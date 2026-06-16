<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.index')) {
            return view('dashboard.admin.no-permission');
        }
        $warehouses = Warehouse::with(['country', 'state', 'city', 'zone'])->latest()->paginate(10);
        return view('dashboard.admin.settings.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.create')) {
            return view('dashboard.admin.no-permission');
        }
        $countries = Country::active()->get();
        return view('dashboard.admin.settings.warehouses.create', compact('countries'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'zone_id' => 'nullable|exists:zones,id',
            'street_name' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_main' => 'boolean',
        ]);

        Warehouse::create($request->all());

        return redirect()->route('admin.settings.warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    public function edit(Warehouse $warehouse)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.edit')) {
            return view('dashboard.admin.no-permission');
        }
        $countries = Country::active()->get();
        $states = State::active()->where('country_id', $warehouse->country_id)->get();
        $cities = City::active()->where('state_id', $warehouse->state_id)->get();
        $zones = Zone::active()->where('city_id', $warehouse->city_id)->get();

        return view('dashboard.admin.settings.warehouses.edit', compact('warehouse', 'countries', 'states', 'cities', 'zones'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'zone_id' => 'nullable|exists:zones,id',
            'street_name' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_main' => 'boolean',
        ]);

        $warehouse->update($request->all());

        return redirect()->route('admin.settings.warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $warehouse->delete();
        return redirect()->route('admin.settings.warehouses.index')->with('success', 'Warehouse deleted successfully.');
    }

    public function toggleStatus(Warehouse $warehouse)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $warehouse->update(['is_main' => !$warehouse->is_main]);
        return redirect()->route('admin.settings.warehouses.index')->with('success', 'Warehouse status updated successfully.');
    }

    // AJAX dependent dropdowns
    public function getStates($countryId)
    {
        return response()->json(State::active()->where('country_id', $countryId)->get(['id', 'name_en', 'name_ar']));
    }

    public function getCities($stateId)
    {
        return response()->json(City::active()->where('state_id', $stateId)->get(['id', 'name_en', 'name_ar']));
    }

    public function getZones($cityId)
    {
        return response()->json(Zone::active()->where('city_id', $cityId)->get(['id', 'name_en', 'name_ar']));
    }
}
