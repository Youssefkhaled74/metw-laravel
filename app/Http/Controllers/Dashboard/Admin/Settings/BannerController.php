<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bannar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.banners.index')) {
            return view('dashboard.admin.no-permission');
        }
        $banners = Bannar::withoutGlobalScope('active')->latest()->paginate(10);
        return view('dashboard.admin.settings.banners.index', compact('banners'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.banners.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.banners.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.banners.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $imagePath = uploadImage($request, 'image', 'storage/banners');

        Bannar::create([
            'image' => $imagePath,
            'link' => $request->link,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.banners.index')
            ->with('success', 'Banner created successfully.');
    }


    public function edit(Bannar $banner)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.banners.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.banners.edit', compact('banner'));
    }


    public function update(Request $request, Bannar $banner)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.admin.settings.banners.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $data = [
            'link' => $request->link,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة من public
            if ($banner->image && File::exists(public_path($banner->image))) {
                File::delete(public_path($banner->image));
            }

            // رفع الصورة الجديدة
            $data['image'] = uploadImage($request, 'image', 'storage/banners');
        }

        $banner->update($data);

        return redirect()
            ->route('admin.settings.banners.index')
            ->with('success', 'Banner updated successfully.');
    }


    public function destroy(Bannar $banner)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.banners.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        // Delete the image file
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()
            ->route('admin.settings.banners.index')
            ->with('success', 'Banner deleted successfully.');
    }

    public function toggleStatus(Bannar $banner)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.banners.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $banner->update(['is_active' => !$banner->is_active]);

        return redirect()
            ->route('admin.settings.banners.index')
            ->with('success', 'Banner status updated successfully.');
    }
}
