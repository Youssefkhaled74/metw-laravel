<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Bannar;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.brands.index')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort_by' => ['nullable', 'in:created_at,name_ar,name_en'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $brandsQuery = Brand::withoutGlobalScope('active');

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $brandsQuery->where(function ($query) use ($search) {
                $query->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        $brands = $brandsQuery
            ->orderBy($sortBy, $sortDir)
            ->paginate(10)
            ->appends(request()->query());

        return view('dashboard.admin.settings.brands.index', compact('brands'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.brands.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.brands.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.brands.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $imagePath = uploadImage($request, 'image', 'storage/brands');

        Brand::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.settings.brands.index')
            ->with('success', 'Brand created successfully.');
    }




    public function edit(Brand $brand)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.brands.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.brands.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            if ($brand->image && File::exists(public_path($brand->image))) {
                File::delete(public_path($brand->image));
            }

            $data['image'] = uploadImage($request, 'image', 'storage/brands');
        }

        $brand->update($data);

        return redirect()
            ->route('admin.settings.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.brands.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        if ($brand->image) {
            Storage::disk('public')->delete($brand->image);
        }

        $brand->delete();

        return redirect()
            ->route('admin.settings.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    public function toggleStatus(Brand $brand)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.brands.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $brand->update(['is_active' => !$brand->is_active]);

        return redirect()
            ->route('admin.settings.brands.index')
            ->with('success', 'Brand status updated successfully.');
    }
}
