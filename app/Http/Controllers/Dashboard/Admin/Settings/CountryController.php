<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.countries.index')) {
            return view('dashboard.admin.no-permission');
        }
        $countries = Country::withoutGlobalScope('active')->latest()->paginate(10);
        return view('dashboard.admin.settings.countries.index', compact('countries'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.countries.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.countries.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.countries.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name_en' => 'required|string|max:255|unique:countries,name_en',
            'name_ar' => 'required|string|max:255|unique:countries,name_ar',
            'is_active' => 'boolean'
        ]);

        Country::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()
            ->route('admin.settings.countries.index')
            ->with('success', 'Country created successfully.');
    }

    public function edit(Country $country)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.countries.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.countries.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name_en' => 'required|string|max:255|unique:countries,name_en,' . $country->id,
            'name_ar' => 'required|string|max:255|unique:countries,name_ar,' . $country->id,
            'is_active' => 'boolean'
        ]);

        $country->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()
            ->route('admin.settings.countries.index')
            ->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.countries.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        if ($country->states()->count() > 0) {
            return redirect()
                ->route('admin.settings.countries.index')
                ->with('error', 'Cannot delete country as it has associated states.');
        }

        $country->delete();

        return redirect()
            ->route('admin.settings.countries.index')
            ->with('success', 'Country deleted successfully.');
    }

    public function toggleStatus(Country $country)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.countries.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $country->update(['is_active' => !$country->is_active]);

        return redirect()
            ->route('admin.settings.countries.index')
            ->with('success', 'Country status updated successfully.');
    }
}
