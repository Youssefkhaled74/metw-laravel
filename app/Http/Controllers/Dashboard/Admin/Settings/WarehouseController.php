<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Enum\BusinessProfileStatus;
use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\WarehouseBusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.index')) {
            return view('dashboard.admin.no-permission');
        }
        $warehouses = Warehouse::with(['country', 'governorate', 'city', 'businessProfile'])
            ->latest()
            ->paginate(10);

        $warehouseMetrics = [
            'total' => Warehouse::count(),
            'main' => Warehouse::where('is_main', true)->count(),
            'pending_profiles' => WarehouseBusinessProfile::where('status', BusinessProfileStatus::PENDING_REVIEW)->count(),
            'approved_profiles' => WarehouseBusinessProfile::where('status', BusinessProfileStatus::APPROVED)->count(),
        ];

        $mainWarehouse = Warehouse::with(['country', 'governorate', 'city'])
            ->where('is_main', true)
            ->latest()
            ->first();

        return view('dashboard.admin.settings.warehouses.index', compact('warehouses', 'warehouseMetrics', 'mainWarehouse'));
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'required|exists:cities,id',
            'street_name' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_main' => 'boolean',
        ]);
        $validated['is_main'] = $request->boolean('is_main');

        DB::transaction(function () use ($validated) {
            $shouldBeMain = !empty($validated['is_main']);
            $warehouse = Warehouse::create($validated);

            if ($shouldBeMain) {
                Warehouse::where('id', '!=', $warehouse->id)->update(['is_main' => false]);
            }
        });

        return redirect()->route('admin.settings.warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    public function edit(Warehouse $warehouse)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.edit')) {
            return view('dashboard.admin.no-permission');
        }
        $countries = Country::active()->get();
        $governorates = Governorate::active()->orderBy('name_ar')->get();
        $cities = City::active()
            ->where('governorate_id', $warehouse->governorate_id)
            ->orderBy(app()->getLocale() === 'ar' ? 'name_ar' : 'name_en')
            ->get();

        return view('dashboard.admin.settings.warehouses.edit', compact('warehouse', 'countries', 'governorates', 'cities'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.warehouses.update')) {
            return view('dashboard.admin.no-permission');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'required|exists:cities,id',
            'street_name' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_main' => 'boolean',
        ]);
        $validated['is_main'] = $request->boolean('is_main');

        DB::transaction(function () use ($warehouse, $validated) {
            $shouldBeMain = !empty($validated['is_main']);
            $warehouse->update($validated);

            if ($shouldBeMain) {
                Warehouse::where('id', '!=', $warehouse->id)->update(['is_main' => false]);
            }
        });

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
        DB::transaction(function () use ($warehouse) {
            if ($warehouse->is_main) {
                $warehouse->update(['is_main' => false]);
                return;
            }

                Warehouse::where('id', '!=', $warehouse->id)->update(['is_main' => false]);
            $warehouse->update(['is_main' => true]);
        });

        return redirect()->route('admin.settings.warehouses.index')->with('success', 'Warehouse main status updated successfully.');
    }

    // AJAX dependent dropdowns
    public function getStates($countryId)
    {
        return response()->json(
            Governorate::active()
                ->orderBy('name_ar')
                ->get(['id', 'name_ar'])
        );
    }

    public function getCities($governorateId)
    {
        if ($governorateId instanceof \Illuminate\Database\Eloquent\Model) {
            $governorateId = $governorateId->getKey();
        }

        if ($governorateId instanceof \Illuminate\Support\Collection) {
            $governorateId = $governorateId->first();
        }

        if (is_array($governorateId)) {
            $governorateId = reset($governorateId);
        }

        $governorateId = (int) $governorateId;

        if ($governorateId <= 0) {
            return response()->json([]);
        }

        $governorate = Governorate::withoutGlobalScope('active')->whereKey($governorateId)->first();

        if (! $governorate) {
            return response()->json([]);
        }

        return response()->json(
            City::active()
                ->where('governorate_id', $governorate->id)
                ->orderBy(app()->getLocale() === 'ar' ? 'name_ar' : 'name_en')
                ->get(['id', 'name_en', 'name_ar', 'governorate_id'])
        );
    }

    public function getZones($cityId)
    {
        return response()->json([]);

    }
}

