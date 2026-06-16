<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.price-per-km.index')) {
            return view('dashboard.admin.no-permission');
        }
        $pricePerKmMin = Setting::where('key', 'price_per_km_min')->first();
        $pricePerKmMax = Setting::where('key', 'price_per_km_max')->first();

        return view('dashboard.admin.settings.price-per-km.index', compact('pricePerKmMin', 'pricePerKmMax'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.price-per-km.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'price_per_km_min' => 'required|numeric|min:0',
            'price_per_km_max' => 'required|numeric|min:0|gte:price_per_km_min',
        ]);

        Setting::updateOrCreate(
            ['key' => 'price_per_km_min'],
            ['value' => $request->price_per_km_min]
        );

        Setting::updateOrCreate(
            ['key' => 'price_per_km_max'],
            ['value' => $request->price_per_km_max]
        );

        return redirect()->route('admin.settings.price-per-km.index')
            ->with('success', 'Price per km settings updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.price-per-km.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'value' => 'required|numeric|min:0',
        ]);

        $setting->update(['value' => $request->value]);

        return redirect()->route('admin.settings.price-per-km.index')
            ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
