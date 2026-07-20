<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandLogoController extends Controller
{
    public function index()
    {
        $vendor = auth('vendor')->user();

        return view('dashboard.vendor.brand-logo.index', compact('vendor'));
    }

    public function update(Request $request)
    {
        $vendor = auth('vendor')->user();

        $validated = $request->validate([
            'brand_name' => 'nullable|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($vendor->logo && Storage::disk('public')->exists($vendor->logo)) {
                Storage::disk('public')->delete($vendor->logo);
            }

            $path = $request->file('logo')->store('vendor-logos', 'public');
            $validated['logo'] = $path;
        }

        if ($request->filled('brand_name')) {
            $validated['brand_name'] = $request->brand_name;
        }

        $vendor->update($validated);

        return redirect()
            ->route('vendor.brand-logo.index')
            ->with('success', __('vendor-dashboard.brand_logo_updated'));
    }

    public function destroy()
    {
        $vendor = auth('vendor')->user();

        if ($vendor->logo && Storage::disk('public')->exists($vendor->logo)) {
            Storage::disk('public')->delete($vendor->logo);
        }

        $vendor->update(['logo' => null]);

        return redirect()
            ->route('vendor.brand-logo.index')
            ->with('success', __('vendor-dashboard.brand_logo_removed'));
    }
}
