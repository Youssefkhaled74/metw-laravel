<?php

namespace App\Http\Controllers\Dashboard\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\RepresentativeWorkTypeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RepresentativeWorkTypeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.representative-work-types.index')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,active,inactive'],
            'sort_by' => ['nullable', 'in:id,code,name_en,is_exclusive,is_active,sort_order,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'sort_order';
        $sortDir = $validated['sort_dir'] ?? 'asc';

        $query = RepresentativeWorkTypeOption::withoutGlobalScope('active')
            ->withCount('workTypes');

        if (! empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function ($builder) use ($search) {
                $builder->where('code', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('id', is_numeric($search) ? (int) $search : 0);
            });
        }

        if (! empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('is_active', $validated['status'] === 'active');
        }

        $query->orderBy($sortBy, $sortDir);

        $workTypes = $query->paginate(15)->withQueryString();

        return view('dashboard.admin.settings.representative-work-types.index', compact('workTypes', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.representative-work-types.create')) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.representative-work-types.create');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.representative-work-types.store')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:100', 'unique:representative_work_type_options,code'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_exclusive' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_exclusive'] = $request->boolean('is_exclusive');
        $data['is_active'] = $request->boolean('is_active');
        $data['metadata'] = null;

        RepresentativeWorkTypeOption::create($data);

        return redirect()->route('admin.settings.representative-work-types.index')->with('success', app()->getLocale() === 'ar' ? 'تم إنشاء نوع العمل بنجاح.' : 'Work type created successfully.');
    }

    public function edit(RepresentativeWorkTypeOption $representativeWorkType)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.representative-work-types.edit')) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.settings.representative-work-types.edit', compact('representativeWorkType'));
    }

    public function update(Request $request, RepresentativeWorkTypeOption $representativeWorkType)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.representative-work-types.update')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('representative_work_type_options', 'code')->ignore($representativeWorkType->id)],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_exclusive' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_exclusive'] = $request->boolean('is_exclusive');
        $data['is_active'] = $request->boolean('is_active');
        $data['metadata'] = $representativeWorkType->metadata;

        $representativeWorkType->update($data);

        return redirect()->route('admin.settings.representative-work-types.index')->with('success', app()->getLocale() === 'ar' ? 'تم تحديث نوع العمل بنجاح.' : 'Work type updated successfully.');
    }

    public function destroy(RepresentativeWorkTypeOption $representativeWorkType)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.representative-work-types.destroy')) {
            return view('dashboard.admin.no-permission');
        }

        $representativeWorkType->delete();

        return redirect()->route('admin.settings.representative-work-types.index')->with('success', app()->getLocale() === 'ar' ? 'تم حذف نوع العمل بنجاح.' : 'Work type deleted successfully.');
    }

    public function toggleStatus(RepresentativeWorkTypeOption $representativeWorkType)
    {
        if (Auth::guard('employee')->check() && ! Auth::guard('employee')->user()->can('admin.settings.representative-work-types.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }

        $representativeWorkType->update(['is_active' => ! $representativeWorkType->is_active]);

        return redirect()->route('admin.settings.representative-work-types.index')->with('success', app()->getLocale() === 'ar' ? 'تم تغيير حالة نوع العمل بنجاح.' : 'Work type status updated successfully.');
    }
}
