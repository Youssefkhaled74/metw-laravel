<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MetwContact;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MetwContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $metwContacts = MetwContact::orderBy('sort_order', 'asc')->get();

        return view('dashboard.admin.settings.metw-contacts.index', compact('metwContacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.admin.settings.metw-contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate the data
        $request->validate([
            'label_ar'       => 'required|string|max:255', // e.g. "إيميل إدارة ميتو"
            'label_en'       => 'nullable|string|max:255',
            'value'          => 'required|string|max:255', // e.g. "metwinfo@gmail.com"
            'type'           => 'required|string|in:phone,email,address,whatsapp',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        // 2. Create the record
        MetwContact::create([
            'label_ar'       => $request->label_ar,
            'label_en'       => $request->label_en,
            'value'          => $request->value,
            'type'           => $request->type,
            'sort_order'     => $request->sort_order ?? 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        // 3. Flash success message and redirect
        return redirect()->route('settings.metw-contacts.index')
            ->with('success', 'تم إضافة جهة الاتصال بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MetwContact $metwContact)
    {
        return redirect()->route('settings.metw-contacts.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetwContact $metwContact): View
    {
        return view('dashboard.admin.settings.metw-contacts.edit', compact('metwContact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetwContact $metwContact): RedirectResponse
    {
        // 1. Validate the data
        $request->validate([
            'label_ar'       => 'required|string|max:255',
            'label_en'       => 'nullable|string|max:255',
            'value'          => 'required|string|max:255',
            'type'           => 'required|string|in:phone,email,address,whatsapp',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        // 2. Update the record
        $metwContact->update([
            'label_ar'       => $request->label_ar,
            'label_en'       => $request->label_en,
            'value'          => $request->value,
            'type'           => $request->type,
            'sort_order'     => $request->sort_order ?? 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        // 3. Flash success message and redirect
        return redirect()->route('settings.metw-contacts.index')
            ->with('success', 'تم تحديث جهة الاتصال بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetwContact $metwContact): RedirectResponse
    {
        // Delete the record
        $metwContact->delete();

        return redirect()->route('settings.metw-contacts.index')
            ->with('success', 'تم حذف جهة الاتصال بنجاح.');
    }
}
