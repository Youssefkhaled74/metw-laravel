<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.cities.index')) {
            return view('dashboard.admin.no-permission');
        }

        $allowedSortColumns = ['id', 'name_en', 'name_ar', 'governorate', 'created_at'];
        $sortBy = $request->string('sort_by', 'created_at')->toString();
        $sortDir = strtolower($request->string('sort_dir', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'created_at';
        }

        $citiesQuery = City::withoutGlobalScope('active')
            ->with('governorate')
            ->leftJoin('governorates', 'cities.governorate_id', '=', 'governorates.id')
            ->select('cities.*');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $citiesQuery->where(function ($query) use ($search) {
                $query->where('cities.name_en', 'like', "%{$search}%")
                    ->orWhere('cities.name_ar', 'like', "%{$search}%")
                    ->orWhereHas('governorate', function ($governorateQuery) use ($search) {
                        $governorateQuery->where('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('governorate_id') && $request->input('governorate_id') !== 'all') {
            $citiesQuery->where('cities.governorate_id', (int) $request->input('governorate_id'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $citiesQuery->where('cities.is_active', $request->input('status') === 'active');
        }

        if ($sortBy === 'governorate') {
            $governorateColumn = 'governorates.name_ar';
            $citiesQuery->orderBy($governorateColumn, $sortDir);
        } else {
            $citiesQuery->orderBy('cities.' . $sortBy, $sortDir);
        }

        $cities = $citiesQuery->paginate(15)->withQueryString();
        $governorates = Governorate::withoutGlobalScope('active')->orderBy('name_ar')->get(['id', 'name_ar']);

        return view('dashboard.admin.settings.cities.index', compact('cities', 'governorates', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.cities.create')) {
            return view('dashboard.admin.no-permission');
        }

        $governorates = Governorate::withoutGlobalScope('active')->orderBy('name_ar')->get();

        return view('dashboard.admin.settings.cities.create', compact('governorates'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.cities.store')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'governorate_id' => ['required', 'exists:governorates,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        City::create($data);

        return redirect()->route('admin.settings.cities.index')->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء المدينة بنجاح.' : 'City created successfully.');
    }

    public function edit(City $city)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.cities.edit')) {
            return view('dashboard.admin.no-permission');
        }

        $governorates = Governorate::withoutGlobalScope('active')->orderBy('name_ar')->get();

        return view('dashboard.admin.settings.cities.edit', compact('city', 'governorates'));
    }

    public function update(Request $request, City $city)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.cities.update')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'governorate_id' => ['required', 'exists:governorates,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $city->update($data);

        return redirect()->route('admin.settings.cities.index')->with('success', app()->getLocale() === 'ar' ? 'تم تحديث المدينة بنجاح.' : 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.cities.destroy')) {
            return view('dashboard.admin.no-permission');
        }

        $city->delete();

        return redirect()->route('admin.settings.cities.index')->with('success', app()->getLocale() === 'ar' ? 'تم حذف المدينة بنجاح.' : 'City deleted successfully.');
    }

    public function toggle(City $city)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.cities.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }

        $city->update(['is_active' => ! $city->is_active]);

        return redirect()->route('admin.settings.cities.index')->with('success', app()->getLocale() === 'ar' ? 'تم تغيير حالة المدينة بنجاح.' : 'City status updated successfully.');
    }
}
