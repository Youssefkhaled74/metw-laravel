<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\DeliveryType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DeliveryTypeController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.delivery-types.index')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort_by' => ['nullable', 'in:id,name'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $deliveryTypesQuery = DeliveryType::withoutGlobalScope('active');

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $deliveryTypesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('id', is_numeric($search) ? (int) $search : 0);
            });
        }

        $deliveryTypes = $deliveryTypesQuery
            ->orderBy($sortBy, $sortDir)
            ->paginate(10)
            ->appends(request()->query());

        return view('dashboard.admin.settings.delivery-types.index', compact('deliveryTypes'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.delivery-types.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.delivery-types.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.delivery-types.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:delivery_types',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'delivery_time' => 'required|string',
            'is_active' => 'boolean',
        ]);

        DeliveryType::create([
            'name' => $request->name,
            'code' => Str::upper(Str::random(3)),
            'description' => $request->description,
            // 'price' => $request->price,
            'delivery_time' => $request->delivery_time,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()
            ->route('admin.settings.delivery-types.index')
            ->with('success', 'Delivery type created successfully.');
    }

    public function edit(DeliveryType $deliveryType)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.delivery-types.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.delivery-types.edit', compact('deliveryType'));
    }

    public function update(Request $request, DeliveryType $deliveryType)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.delivery-types.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:delivery_types,name,' . $deliveryType->id,
            'description' => 'nullable|string',
            // 'price' => 'required|numeric|min:0',
            'delivery_time' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $deliveryType->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'delivery_time' => $request->delivery_time,
            'is_active' => $request->is_active ?? $deliveryType->is_active,
        ]);

        return redirect()
            ->route('admin.settings.delivery-types.index')
            ->with('success', 'Delivery type updated successfully.');
    }

    public function destroy(DeliveryType $deliveryType)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.delivery-types.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $deliveryType->delete();
        return redirect()
            ->route('admin.settings.delivery-types.index')
            ->with('success', 'Delivery type deleted successfully.');
    }

    public function toggleStatus(DeliveryType $deliveryType)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.delivery-types.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $deliveryType->update(['is_active' => !$deliveryType->is_active]);
        return redirect()
            ->route('admin.settings.delivery-types.index')
            ->with('success', 'Delivery type status updated successfully.');
    }
}
