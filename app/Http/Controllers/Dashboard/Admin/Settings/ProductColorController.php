<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductColorController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.colors.index')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort_by' => ['nullable', 'in:name,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $colorsQuery = ProductColor::withoutGlobalScope('active');

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $colorsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('hex', 'like', "%{$search}%");
            });
        }

        $colors = $colorsQuery
            ->orderBy($sortBy, $sortDir)
            ->paginate(10)
            ->appends(request()->query());

        return view('dashboard.admin.settings.colors.index', compact('colors'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.colors.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.colors.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.colors.store')) {
            return view('dashboard.admin.no-permission');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_colors,name',
            'hex' => 'required|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        // Handle the is_active checkbox
        $validated['is_active'] = $request->has('is_active');

        ProductColor::create($validated);

        return redirect()
            ->route('admin.settings.colors.index')
            ->with('success', 'Color created successfully.');
    }

    public function edit(ProductColor $color)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.colors.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.colors.edit', compact('color'));
    }

    public function update(Request $request, ProductColor $color)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.colors.update')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_colors', 'name')->ignore($color->id),
            ],
            'hex' => [
                'required',
                'string',
                'max:7',
                'regex:/^#[a-fA-F0-9]{6}$/'
            ],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $color->update($validated);

        return redirect()
            ->route('admin.settings.colors.index')
            ->with('success', 'Color updated successfully.');
    }

    public function destroy(ProductColor $color)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.colors.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $color->delete();

        return redirect()
            ->route('admin.settings.colors.index')
            ->with('success', 'Color deleted successfully.');
    }

    public function toggleStatus(ProductColor $color)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.colors.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $color->update(['is_active' => !$color->is_active]);

        return redirect()
            ->route('admin.settings.colors.index')
            ->with('success', 'Color status updated successfully.');
    }
}
