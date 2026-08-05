<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\MetwGallery;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MetwGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $metwGalleries = MetwGallery::orderBy('sort_order', 'asc')->get();

        return view('dashboard.admin.settings.metw-galleries.index', compact('metwGalleries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('dashboard.admin.settings.metw-galleries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate the data (Image is required on create)
        $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'image'          => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        // 2. Handle Image Upload
        $path = $request->file('image')->store('metw-gallery', 'public');

        // 3. Create the record
        MetwGallery::create([
            'title_ar'       => $request->title_ar,
            'title_en'       => $request->title_en,
            'image_path'     => $path,
            'sort_order'     => $request->sort_order ?? 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        // 4. Flash success message and redirect
        return redirect()->route('settings.metw-galleries.index')
            ->with('success', 'تم إضافة الصورة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MetwGallery $metwGallery)
    {
        return redirect()->route('settings.metw-galleries.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetwGallery $metwGallery): View
    {
        return view('dashboard.admin.settings.metw-galleries.edit', compact('metwGallery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetwGallery $metwGallery): RedirectResponse
    {
        // 1. Validate the data (Image is optional on update)
        $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        // 2. Handle Image Upload (if a new image is uploaded)
        $data = [
            'title_ar'       => $request->title_ar,
            'title_en'       => $request->title_en,
            'sort_order'     => $request->sort_order ?? 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($metwGallery->image_path) {
                Storage::disk('public')->delete($metwGallery->image_path);
            }
            // Store the new image
            $data['image_path'] = $request->file('image')->store('metw-gallery', 'public');
        }

        // 3. Update the record
        $metwGallery->update($data);

        // 4. Flash success message and redirect
        return redirect()->route('settings.metw-galleries.index')
            ->with('success', 'تم تحديث الصورة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetwGallery $metwGallery): RedirectResponse
    {
        // Delete the image file from storage
        if ($metwGallery->image_path) {
            Storage::disk('public')->delete($metwGallery->image_path);
        }

        // Delete the database record
        $metwGallery->delete();

        return redirect()->route('settings.metw-galleries.index')
            ->with('success', 'تم حذف الصورة بنجاح.');
    }
}
