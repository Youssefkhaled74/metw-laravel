<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.cities.index')) {
                return view('dashboard.admin.no-permission');
            }

            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'state_id' => ['nullable', 'string'],
                'status' => ['nullable', 'in:all,active,inactive'],
                'sort_by' => ['nullable', 'in:id,name_ar,name_en,state,created_at'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDir = $validated['sort_dir'] ?? 'desc';

            $citiesQuery = City::withoutGlobalScope('active')
                ->with('state')
                ->leftJoin('states', 'cities.state_id', '=', 'states.id')
                ->select('cities.*');

            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $citiesQuery->where(function ($query) use ($search) {
                    $query->where('cities.name_en', 'like', "%{$search}%")
                        ->orWhere('cities.name_ar', 'like', "%{$search}%")
                        ->orWhere('states.name_en', 'like', "%{$search}%")
                        ->orWhere('states.name_ar', 'like', "%{$search}%");
                });
            }

            if (!empty($validated['state_id']) && $validated['state_id'] !== 'all') {
                $citiesQuery->where('cities.state_id', (int) $validated['state_id']);
            }

            if (!empty($validated['status']) && $validated['status'] !== 'all') {
                $citiesQuery->where('cities.is_active', $validated['status'] === 'active');
            }

            if ($sortBy === 'state') {
                $stateColumn = app()->getLocale() === 'ar' ? 'states.name_ar' : 'states.name_en';
                $citiesQuery->orderBy($stateColumn, $sortDir);
            } else {
                $citiesQuery->orderBy('cities.' . $sortBy, $sortDir);
            }

            $cities = $citiesQuery
                ->paginate(15)
                ->appends($request->query());

            $states = State::orderBy(app()->getLocale() === 'ar' ? 'name_ar' : 'name_en')->get(['id', 'name_en', 'name_ar']);

            return view('dashboard.admin.settings.cities.index', compact('cities', 'states', 'sortBy', 'sortDir'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.cities.create')) {
            return view('dashboard.admin.no-permission');
        }
        $states = State::all();
        return view('dashboard.admin.settings.cities.create', compact('states'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.cities.store')) {
            return view('dashboard.admin.no-permission');
        }
        $data = $request->validate([
            'name_en'   => 'required|string|max:255',
            'name_ar'   => 'required|string|max:255',
            'state_id'  => 'required|exists:states,id',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        City::create($data);

        return redirect()->route('admin.settings.cities.index')->with('success', 'City created successfully.');
    }

    public function edit(City $city)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.cities.edit')) {
            return view('dashboard.admin.no-permission');
        }
        $states = State::all();
        return view('dashboard.admin.settings.cities.edit', compact('city', 'states'));
    }

    public function update(Request $request, City $city)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.cities.update')) {
            return view('dashboard.admin.no-permission');
        }
        $data = $request->validate([
            'name_en'   => 'required|string|max:255',
            'name_ar'   => 'required|string|max:255',
            'state_id'  => 'required|exists:states,id',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        $city->update($data);

        return redirect()->route('admin.settings.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.cities.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $city->delete();
        return redirect()->route('admin.settings.cities.index')->with('success', 'City deleted successfully.');
    }

    public function toggle(City $city)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.cities.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $city->update(['is_active' => !$city->is_active]);
        return redirect()->route('admin.settings.cities.index')->with('success', 'City status updated successfully.');
    }
}
