<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SizeController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.sizes.index')) {
            return view('dashboard.admin.no-permission');
        }
        $sizes = Size::withoutGlobalScope('active')->latest()->paginate(10);
        return view('dashboard.admin.settings.sizes.index', compact('sizes'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.sizes.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.sizes.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.sizes.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $iconPath = uploadImage($request, 'icon', 'storage/sizes/icons');

        Size::create([
            'title' => $request->title,
            'icon' => $iconPath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.sizes.index')
            ->with('success', 'Size created successfully.');
    }

    public function update(Request $request, Size $size)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.sizes.update')) {
            return view('dashboard.admin.no-permission');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean', // nullable because unchecked won't send
        ]);

        $data = [
            'title' => $request->title,
            // if is_active is missing, set to false
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
        ];

        if ($request->hasFile('icon')) {
            if ($size->icon) {
                deleteImage($size->icon);
            }
            $data['icon'] = uploadImage($request, 'icon', 'storage/sizes/icons');
        }

        $size->update($data);

        return redirect()
            ->route('admin.settings.sizes.index')
            ->with('success', 'Size updated successfully.');
    }


    public function edit(Size $size)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.sizes.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.sizes.edit', compact('size'));
    }


    public function destroy(Size $size)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.sizes.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $size->delete();
        return redirect()
            ->route('admin.settings.sizes.index')
            ->with('success', 'Size deleted successfully.');
    }

    public function toggleStatus(Size $size)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.sizes.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $size->update(['is_active' => !$size->is_active]);
        return redirect()
            ->route('admin.settings.sizes.index')
            ->with('success', 'Size status updated successfully.');
    }
}
