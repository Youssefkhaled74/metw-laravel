<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\ProductSize;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductSizeController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.product-sizes.index')) {
            return view('dashboard.admin.no-permission');
        }
        $productSizes = ProductSize::withoutGlobalScope('active')->latest()->paginate(10);
        return view('dashboard.admin.settings.product-sizes.index', compact('productSizes'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.product-sizes.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.product-sizes.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.product-sizes.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        ProductSize::create([
            'title' => $request->title,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()
            ->route('admin.settings.product-sizes.index')
            ->with('success', 'Product size created successfully.');
    }

    public function edit(ProductSize $productSize)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.product-sizes.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.product-sizes.edit', compact('productSize'));
    }

    public function update(Request $request, ProductSize $productSize)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.product-sizes.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $productSize->update([
            'title' => $request->title,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
        ]);

        return redirect()
            ->route('admin.settings.product-sizes.index')
            ->with('success', 'Product size updated successfully.');
    }

    public function destroy(ProductSize $productSize)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.product-sizes.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $productSize->delete();
        return redirect()
            ->route('admin.settings.product-sizes.index')
            ->with('success', 'Product size deleted successfully.');
    }

    public function toggleStatus(ProductSize $productSize)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.product-sizes.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $productSize->update(['is_active' => !$productSize->is_active]);
        return redirect()
            ->route('admin.settings.product-sizes.index')
            ->with('success', 'Product size status updated successfully.');
    }
}
