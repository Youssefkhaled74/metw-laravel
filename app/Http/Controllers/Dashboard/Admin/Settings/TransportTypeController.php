<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\TransportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransportTypeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.transport-types.index')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,active,inactive'],
            'sort_by' => ['nullable', 'in:id,code,name_en,max_weight,max_volume,is_active,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $transportTypesQuery = TransportType::withoutGlobalScope('active');

        if (! empty($validated['search'])) {
            $search = trim($validated['search']);
            $transportTypesQuery->where(function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('id', is_numeric($search) ? (int) $search : 0);
            });
        }

        if (! empty($validated['status']) && $validated['status'] !== 'all') {
            $transportTypesQuery->where('is_active', $validated['status'] === 'active');
        }

        $transportTypesQuery->orderBy($sortBy === 'is_active' ? 'is_active' : $sortBy, $sortDir);

        $transportTypes = $transportTypesQuery->paginate(15)->withQueryString();

        return view('dashboard.admin.settings.transport-types.index', compact('transportTypes', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.transport-types.create')) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.transport-types.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.transport-types.store')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:transport_types,code'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_weight' => ['nullable', 'numeric', 'min:0'],
            'max_volume' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'unlimited_capacity' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['metadata'] = [
            'unlimited_capacity' => $request->boolean('unlimited_capacity'),
        ];

        if ($request->boolean('unlimited_capacity')) {
            $data['max_weight'] = null;
            $data['max_volume'] = null;
        }

        unset($data['unlimited_capacity']);

        TransportType::create($data);

        return redirect()->route('admin.settings.transport-types.index')->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء نوع النقل بنجاح.' : 'Transport type created successfully.');
    }

    public function edit(TransportType $transportType)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.transport-types.edit')) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.transport-types.edit', compact('transportType'));
    }

    public function update(Request $request, TransportType $transportType)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.transport-types.update')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('transport_types', 'code')->ignore($transportType->id)],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_weight' => ['nullable', 'numeric', 'min:0'],
            'max_volume' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'unlimited_capacity' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['metadata'] = array_merge($transportType->metadata ?? [], [
            'unlimited_capacity' => $request->boolean('unlimited_capacity'),
        ]);

        if ($request->boolean('unlimited_capacity')) {
            $data['max_weight'] = null;
            $data['max_volume'] = null;
        }

        unset($data['unlimited_capacity']);

        $transportType->update($data);

        return redirect()->route('admin.settings.transport-types.index')->with('success', app()->getLocale() === 'ar' ? 'تم تحديث نوع النقل بنجاح.' : 'Transport type updated successfully.');
    }

    public function destroy(TransportType $transportType)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.transport-types.destroy')) {
            return view('dashboard.admin.no-permission');
        }

        $transportType->delete();

        return redirect()->route('admin.settings.transport-types.index')->with('success', app()->getLocale() === 'ar' ? 'تم حذف نوع النقل بنجاح.' : 'Transport type deleted successfully.');
    }

    public function toggleStatus(TransportType $transportType)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.transport-types.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }

        $transportType->update(['is_active' => ! $transportType->is_active]);

        return redirect()->route('admin.settings.transport-types.index')->with('success', app()->getLocale() === 'ar' ? 'تم تغيير حالة نوع النقل بنجاح.' : 'Transport type status updated successfully.');
    }
}
