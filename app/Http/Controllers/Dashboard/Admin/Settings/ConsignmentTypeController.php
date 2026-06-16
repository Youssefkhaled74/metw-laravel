<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\ConsignmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ConsignmentTypeController extends Controller
{
    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.consignment-types.index')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = request()->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort_by' => ['nullable', 'in:id,name'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $consignmentTypesQuery = ConsignmentType::withoutGlobalScope('active');

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $consignmentTypesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('description_ar', 'like', "%{$search}%")
                    ->orWhere('id', is_numeric($search) ? (int) $search : 0);
            });
        }

        $consignmentTypes = $consignmentTypesQuery
            ->orderBy($sortBy, $sortDir)
            ->paginate(10)
            ->appends(request()->query());

        return view('dashboard.admin.settings.consignment-types.index', compact('consignmentTypes'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.consignment-types.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.consignment-types.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.consignment-types.store')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:consignment_types,name',
            'name_ar' => 'required|string|max:255|unique:consignment_types,name_ar',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'is_active' => 'boolean',
        ]);


        ConsignmentType::create([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'is_active' => $request->is_active ?? true,
        ]);


        return redirect()
            ->route('admin.settings.consignment-types.index')
            ->with('success', 'Consignment type created successfully.');
    }

    public function edit(ConsignmentType $consignmentType)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.consignment-types.edit')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.settings.consignment-types.edit', compact('consignmentType'));
    }

    public function update(Request $request, ConsignmentType $consignmentType)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.consignment-types.update')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:consignment_types,name,' . $consignmentType->id,
            'name_ar' => 'required|string|max:255|unique:consignment_types,name_ar,' . $consignmentType->id,
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $consignmentType->update([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'is_active' => $request->is_active ?? $consignmentType->is_active,
        ]);


        return redirect()
            ->route('admin.settings.consignment-types.index')
            ->with('success', 'Consignment type updated successfully.');
    }

    public function destroy(ConsignmentType $consignmentType)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.consignment-types.destroy')) {
            return view('dashboard.admin.no-permission');
        }
        $consignmentType->delete();
        return redirect()
            ->route('admin.settings.consignment-types.index')
            ->with('success', 'Consignment type deleted successfully.');
    }

    public function toggleStatus(ConsignmentType $consignmentType)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.settings.consignment-types.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }
        $consignmentType->update(['is_active' => !$consignmentType->is_active]);
        return redirect()
            ->route('admin.settings.consignment-types.index')
            ->with('success', 'Consignment type status updated successfully.');
    }
}
