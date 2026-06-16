<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZoneController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.zones.index')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort_by' => ['nullable', 'in:id,name_ar,name_en,city,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $zonesQuery = Zone::withoutGlobalScope('active')->with('city');

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $zonesQuery->where(function ($query) use ($search) {
                $query->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('id', is_numeric($search) ? (int) $search : 0)
                    ->orWhereHas('city', function ($cityQuery) use ($search) {
                        $cityQuery->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        if ($sortBy === 'city') {
            $cityNameColumn = app()->getLocale() === 'ar' ? 'cities.name_ar' : 'cities.name_en';
            $zonesQuery->leftJoin('cities', 'zones.city_id', '=', 'cities.id')
                ->select('zones.*')
                ->orderBy($cityNameColumn, $sortDir);
        } else {
            $zonesQuery->orderBy($sortBy, $sortDir);
        }

        $zones = $zonesQuery
            ->paginate(15)
            ->appends(request()->query());

        return view('dashboard.admin.settings.zones.index', compact('zones'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.zones.create')) {
            return view('dashboard.admin.no-permission');
        }
        $cities = City::all();
        return view('dashboard.admin.settings.zones.create', compact('cities'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.zones.store')) {
            return view('dashboard.admin.no-permission');
        }
        $data = $request->validate([
            'name_en'   => 'required|string|max:255',
            'name_ar'   => 'required|string|max:255',
            'city_id'   => 'required|exists:cities,id',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Zone::create($data);

        return redirect()->route('admin.settings.zones.index')
            ->with('success', 'Zone created successfully.');
    }

    public function edit(Zone $zone)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.zones.edit')) {
            return view('dashboard.admin.no-permission');
        }
        $cities = City::all();
        return view('dashboard.admin.settings.zones.edit', compact('zone', 'cities'));
    }

    public function update(Request $request, Zone $zone)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.zones.update')) {
            return view('dashboard.admin.no-permission');
        }
        $data = $request->validate([
            'name_en'   => 'required|string|max:255',
            'name_ar'   => 'required|string|max:255',
            'city_id'   => 'required|exists:cities,id',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $zone->update($data);

        return redirect()->route('admin.settings.zones.index')
            ->with('success', 'Zone updated successfully.');
    }

    public function destroy(Zone $zone)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.zones.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $zone->delete();
        return redirect()->route('admin.settings.zones.index')
            ->with('success', 'Zone deleted successfully.');
    }

    public function toggle(Zone $zone)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.zones.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $zone->update(['is_active' => !$zone->is_active]);

        return redirect()->route('admin.settings.zones.index')
            ->with('success', 'Zone status updated successfully.');
    }
}
