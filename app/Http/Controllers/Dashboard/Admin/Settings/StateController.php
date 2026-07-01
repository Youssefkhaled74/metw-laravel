<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Governorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StateController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.states.index')) {
            return view('dashboard.admin.no-permission');
        }

        $allowedSortColumns = ['id', 'governorate_number', 'name_ar', 'capital_city', 'cities_count', 'created_at'];
        $sortBy = $request->string('sort_by', 'created_at')->toString();
        $sortDir = strtolower($request->string('sort_dir', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sortBy, $allowedSortColumns, true)) {
            $sortBy = 'created_at';
        }

        $governoratesQuery = Governorate::withoutGlobalScope('active')
            ->with('capitalCity')
            ->withCount('cities');

        if ($search = trim((string) $request->input('search', ''))) {
            $governoratesQuery->where(function ($query) use ($search) {
                $query->where('governorate_number', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhereHas('capitalCity', function ($cityQuery) use ($search) {
                        $cityQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $governoratesQuery->where('is_active', $request->input('status') === 'active');
        }

        if ($sortBy === 'capital_city') {
            $governoratesQuery->leftJoin('cities', 'governorates.capital_city_id', '=', 'cities.id')
                ->select('governorates.*')
                ->orderBy('cities.name_ar', $sortDir);
        } elseif ($sortBy === 'cities_count') {
            $governoratesQuery->orderBy('cities_count', $sortDir);
        } else {
            $governoratesQuery->orderBy("governorates.{$sortBy}", $sortDir);
        }

        $governorates = $governoratesQuery->paginate(15)->withQueryString();

        return view('dashboard.admin.settings.states.index', compact('governorates', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.states.create')) {
            return view('dashboard.admin.no-permission');
        }

        $cities = City::withoutGlobalScope('active')->orderBy('name_ar')->get();

        return view('dashboard.admin.settings.states.create', compact('cities'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.states.store')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'governorate_number' => ['required', 'integer', 'min:1', 'max:65535', 'unique:governorates,governorate_number'],
            'name_ar' => ['required', 'string', 'max:255', 'unique:governorates,name_ar'],
            'capital_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        Governorate::create($data);

        return redirect()->route('admin.settings.states.index')->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء المحافظة بنجاح.' : 'Governorate created successfully.');
    }

    public function edit(Governorate $state)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.states.edit')) {
            return view('dashboard.admin.no-permission');
        }

        $cities = City::withoutGlobalScope('active')->orderBy('name_ar')->get();

        return view('dashboard.admin.settings.states.edit', [
            'governorate' => $state,
            'cities' => $cities,
        ]);
    }

    public function update(Request $request, Governorate $state)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.states.update')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'governorate_number' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
                Rule::unique('governorates', 'governorate_number')->ignore($state->id),
            ],
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('governorates', 'name_ar')->ignore($state->id),
            ],
            'capital_city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $state->update($data);

        return redirect()->route('admin.settings.states.index')->with('success', app()->getLocale() === 'ar' ? 'تم تحديث المحافظة بنجاح.' : 'Governorate updated successfully.');
    }

    public function destroy(Governorate $state)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.states.destroy')) {
            return view('dashboard.admin.no-permission');
        }

        $state->delete();

        return redirect()->route('admin.settings.states.index')->with('success', app()->getLocale() === 'ar' ? 'تم حذف المحافظة بنجاح.' : 'Governorate deleted successfully.');
    }

    public function toggle(Governorate $state)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.states.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }

        $state->update(['is_active' => ! $state->is_active]);

        return redirect()->route('admin.settings.states.index')->with('success', app()->getLocale() === 'ar' ? 'تم تغيير حالة المحافظة بنجاح.' : 'Governorate status updated successfully.');
    }
}
