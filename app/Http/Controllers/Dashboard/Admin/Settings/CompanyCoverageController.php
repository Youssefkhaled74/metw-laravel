<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\CompanyCoverage;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyCoverageController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.company-coverages.index')) {
            return view('dashboard.admin.no-permission');
        }
        $coverages = CompanyCoverage::with(['city'])->latest()->paginate(10);
        return view('dashboard.admin.settings.company-coverages.index', compact('coverages'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.company-coverages.create')) {
            return view('dashboard.admin.no-permission');
        }
        $cities = City::active()->get();
        return view('dashboard.admin.settings.company-coverages.create', compact('cities'));
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.company-coverages.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'price' => 'required|numeric|min:0',
            'delivery_time' => 'required|string',
            'is_active' => 'boolean',
        ]);

        CompanyCoverage::create([
            'city_id' => $request->city_id,
            'price' => $request->price,
            'delivery_time' => $request->delivery_time,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()
            ->route('admin.settings.company-coverages.index')
            ->with('success', 'Company coverage created successfully.');
    }

    public function edit(CompanyCoverage $companyCoverage)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.company-coverages.edit')) {
            return view('dashboard.admin.no-permission');
        }
        $cities = City::active()->get();
        return view('admin.settings.company-coverages.edit', compact('companyCoverage', 'cities'));
    }

    public function update(Request $request, CompanyCoverage $companyCoverage)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.company-coverages.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'price' => 'required|numeric|min:0',
            'delivery_time' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $companyCoverage->update([
            'city_id' => $request->city_id,
            'price' => $request->price,
            'delivery_time' => $request->delivery_time,
            'is_active' => $request->is_active ?? $companyCoverage->is_active,
        ]);

        return redirect()
            ->route('admin.settings.company-coverages.index')
            ->with('success', 'Company coverage updated successfully.');
    }

    public function destroy(CompanyCoverage $companyCoverage)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.company-coverages.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $companyCoverage->delete();
        return redirect()
            ->route('admin.settings.company-coverages.index')
            ->with('success', 'Company coverage deleted successfully.');
    }

    public function toggleStatus(CompanyCoverage $companyCoverage)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.company-coverages.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $companyCoverage->update(['is_active' => !$companyCoverage->is_active]);
        return redirect()
            ->route('admin.settings.company-coverages.index')
            ->with('success', 'Company coverage status updated successfully.');
    }
}
