<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GovernorateController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.governorates.index')) {
            return view('dashboard.admin.no-permission');
        }

        $allowedSortColumns = ['id', 'governorate_number', 'name_ar', 'capital_city', 'cities_count', 'created_at'];
        $sortBy = $request->string('sort_by', 'created_at')->toString();
        $sortDir = strtolower($request->string('sort_dir', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'created_at';
        }

        $governoratesQuery = Governorate::withoutGlobalScope('active')
            ->with(['capitalCity'])
            ->withCount('cities');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $governoratesQuery->where(function ($query) use ($search) {
                $query->where('governorate_number', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhereHas('capitalCity', function ($cityQuery) use ($search) {
                        $cityQuery->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $governoratesQuery->where('is_active', $request->input('status') === 'active');
        }

        if ($sortBy === 'capital_city') {
            $capitalCityColumn = app()->getLocale() === 'ar' ? 'cities.name_ar' : 'cities.name_en';
            $governoratesQuery->leftJoin('cities', 'governorates.capital_city_id', '=', 'cities.id')
                ->select('governorates.*')
                ->orderBy($capitalCityColumn, $sortDir);
        } elseif ($sortBy === 'cities_count') {
            $governoratesQuery->orderBy('cities_count', $sortDir);
        } else {
            $governoratesQuery->orderBy('governorates.' . $sortBy, $sortDir);
        }

        $governorates = $governoratesQuery->paginate(15)->withQueryString();

        return view('dashboard.admin.settings.governorates.index', compact('governorates', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.governorates.create')) {
            return view('dashboard.admin.no-permission');
        }

        $cities = City::withoutGlobalScope('active')->orderBy(app()->getLocale() === 'ar' ? 'name_ar' : 'name_en')->get();

        return view('dashboard.admin.settings.governorates.create', compact('cities'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.governorates.store')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'governorate_number' => ['required', 'integer', 'min:1', 'max:65535', 'unique:governorates,governorate_number'],
            'name_ar' => ['required', 'string', 'max:255', 'unique:governorates,name_ar'],
            'capital_city_id' => ['nullable', 'exists:cities,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        Governorate::create($data);

        return redirect()->route('admin.settings.governorates.index')->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء المحافظة بنجاح.' : 'Governorate created successfully.');
    }

    public function edit(Governorate $governorate)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.governorates.edit')) {
            return view('dashboard.admin.no-permission');
        }

        $cities = City::withoutGlobalScope('active')->orderBy(app()->getLocale() === 'ar' ? 'name_ar' : 'name_en')->get();

        return view('dashboard.admin.settings.governorates.edit', compact('governorate', 'cities'));
    }

    public function update(Request $request, Governorate $governorate)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.governorates.update')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'governorate_number' => ['required', 'integer', 'min:1', 'max:65535', 'unique:governorates,governorate_number,' . $governorate->id],
            'name_ar' => ['required', 'string', 'max:255', 'unique:governorates,name_ar,' . $governorate->id],
            'capital_city_id' => ['nullable', 'exists:cities,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $governorate->update($data);

        return redirect()->route('admin.settings.governorates.index')->with('success', app()->getLocale() === 'ar' ? 'تم تحديث المحافظة بنجاح.' : 'Governorate updated successfully.');
    }

    public function destroy(Governorate $governorate)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.governorates.destroy')) {
            return view('dashboard.admin.no-permission');
        }

        $governorate->delete();

        return redirect()->route('admin.settings.governorates.index')->with('success', app()->getLocale() === 'ar' ? 'تم حذف المحافظة بنجاح.' : 'Governorate deleted successfully.');
    }

    public function toggle(Governorate $governorate)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.governorates.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }

        $governorate->update(['is_active' => ! $governorate->is_active]);

        return redirect()->route('admin.settings.governorates.index')->with('success', app()->getLocale() === 'ar' ? 'تم تغيير حالة المحافظة بنجاح.' : 'Governorate status updated successfully.');
    }
}
