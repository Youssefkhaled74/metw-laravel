<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MetwService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MetwServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Fetch services, ordered by sort_order (ascending)
        $metwServices = MetwService::orderBy('sort_order', 'asc')->get();
        
        return view('dashboard.admin.settings.metw-services.index', compact('metwServices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.admin.settings.metw-services.create');
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
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'icon'           => 'nullable|string|max:255',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        // 2. Create the record
        MetwService::create([
            'title_ar'       => $request->title_ar,
            'title_en'       => $request->title_en,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'icon'           => $request->icon,
            'sort_order'     => $request->sort_order ?? 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        // 3. Flash success message and redirect
        return redirect()->route('admin.settings.metw-services.index')
                         ->with('success', 'تم إضافة الخدمة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MetwService $metwService)
    {
        // Usually we redirect to the index or edit page for resource show
        return redirect()->route('admin.settings.metw-services.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetwService $metwService): View
    {
        return view('dashboard.admin.settings.metw-services.edit', compact('metwService'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetwService $metwService): RedirectResponse
    {
        // 1. Validate the data
        $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'icon'           => 'nullable|string|max:255',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        // 2. Update the record
        $metwService->update([
            'title_ar'       => $request->title_ar,
            'title_en'       => $request->title_en,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'icon'           => $request->icon,
            'sort_order'     => $request->sort_order ?? 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        // 3. Flash success message and redirect
        return redirect()->route('admin.settings.metw-services.index')
                         ->with('success', 'تم تحديث الخدمة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetwService $metwService): RedirectResponse
    {
        // Delete the record
        $metwService->delete();

        return redirect()->route('admin.settings.metw-services.index')
                         ->with('success', 'تم حذف الخدمة بنجاح.');
    }
}
