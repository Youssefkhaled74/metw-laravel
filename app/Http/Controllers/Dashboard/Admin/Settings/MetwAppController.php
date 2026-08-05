<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MetwApp;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MetwAppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $metwApps = MetwApp::orderBy('sort_order', 'asc')->get();

        return view('dashboard.admin.settings.metw-apps.index', compact('metwApps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.admin.settings.metw-apps.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate the data
        $request->validate([
            'name_ar'          => 'required|string|max:255',
            'name_en'          => 'nullable|string|max:255',
            'play_store_link'  => 'nullable|url|max:255',
            'app_store_link'   => 'nullable|url|max:255',
            'icon'             => 'nullable|string|max:255',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'nullable|boolean',
        ]);

        // 2. Create the record
        MetwApp::create([
            'name_ar'          => $request->name_ar,
            'name_en'          => $request->name_en,
            'play_store_link'  => $request->play_store_link,
            'app_store_link'   => $request->app_store_link,
            'icon'             => $request->icon,
            'sort_order'       => $request->sort_order ?? 0,
            'is_active'        => $request->has('is_active') ? 1 : 0,
        ]);

        // 3. Flash success message and redirect
        return redirect()->route('settings.metw-apps.index')
            ->with('success', 'تم إضافة التطبيق بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MetwApp $metwApp)
    {
        return redirect()->route('settings.metw-apps.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetwApp $metwApp): View
    {
        return view('dashboard.admin.settings.metw-apps.edit', compact('metwApp'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetwApp $metwApp): RedirectResponse
    {
        // 1. Validate the data
        $request->validate([
            'name_ar'          => 'required|string|max:255',
            'name_en'          => 'nullable|string|max:255',
            'play_store_link'  => 'nullable|url|max:255',
            'app_store_link'   => 'nullable|url|max:255',
            'icon'             => 'nullable|string|max:255',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'nullable|boolean',
        ]);

        // 2. Update the record
        $metwApp->update([
            'name_ar'          => $request->name_ar,
            'name_en'          => $request->name_en,
            'play_store_link'  => $request->play_store_link,
            'app_store_link'   => $request->app_store_link,
            'icon'             => $request->icon,
            'sort_order'       => $request->sort_order ?? 0,
            'is_active'        => $request->has('is_active') ? 1 : 0,
        ]);

        // 3. Flash success message and redirect
        return redirect()->route('settings.metw-apps.index')
            ->with('success', 'تم تحديث التطبيق بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetwApp $metwApp): RedirectResponse
    {
        // Delete the record
        $metwApp->delete();

        return redirect()->route('settings.metw-apps.index')
            ->with('success', 'تم حذف التطبيق بنجاح.');
    }
}
