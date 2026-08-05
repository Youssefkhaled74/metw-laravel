<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MetwPolicyLink;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MetwPolicyLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $metwPolicyLinks = MetwPolicyLink::orderBy('sort_order', 'asc')->get();
        
        return view('dashboard.admin.settings.metw-policy-links.index', compact('metwPolicyLinks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.admin.settings.metw-policy-links.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate the data
        $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'route_name'     => 'nullable|string|max:255',
            'external_url'   => 'nullable|url|max:255',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        // 2. Create the record
        MetwPolicyLink::create([
            'title_ar'       => $request->title_ar,
            'title_en'       => $request->title_en,
            'route_name'     => $request->route_name,
            'external_url'   => $request->external_url,
            'sort_order'     => $request->sort_order ?? 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        // 3. Flash success message and redirect
        return redirect()->route('settings.metw-policy-links.index')
                         ->with('success', 'تم إضافة رابط السياسة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MetwPolicyLink $metwPolicyLink)
    {
        return redirect()->route('settings.metw-policy-links.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetwPolicyLink $metwPolicyLink): View
    {
        return view('dashboard.admin.settings.metw-policy-links.edit', compact('metwPolicyLink'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetwPolicyLink $metwPolicyLink): RedirectResponse
    {
        // 1. Validate the data
        $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'route_name'     => 'nullable|string|max:255',
            'external_url'   => 'nullable|url|max:255',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        // 2. Update the record
        $metwPolicyLink->update([
            'title_ar'       => $request->title_ar,
            'title_en'       => $request->title_en,
            'route_name'     => $request->route_name,
            'external_url'   => $request->external_url,
            'sort_order'     => $request->sort_order ?? 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        // 3. Flash success message and redirect
        return redirect()->route('settings.metw-policy-links.index')
                         ->with('success', 'تم تحديث رابط السياسة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetwPolicyLink $metwPolicyLink): RedirectResponse
    {
        // Delete the record
        $metwPolicyLink->delete();

        return redirect()->route('settings.metw-policy-links.index')
                         ->with('success', 'تم حذف رابط السياسة بنجاح.');
    }
}