<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\RepresentativeStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectRepresentativeRequest;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Representative;
use App\Models\RepresentativeWorkTypeOption;
use Illuminate\Http\Request;

class RepresentativeController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'account_type' => ['nullable', 'in:all,free,warehouse'],
            'work_type' => ['nullable', 'in:all,local_delivery,inter_governorate_shipping,bus_driver'],
            'status' => ['nullable', 'in:all,incomplete,pending_review,approved,rejected,suspended'],
            'governorate_id' => ['nullable', 'integer', 'exists:governorates,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'sort_by' => ['nullable', 'in:account_number,first_name,phone,account_type,status,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'account_number';
        $sortDir = $validated['sort_dir'] ?? 'asc';

        $representatives = Representative::query()
            ->withoutGlobalScopes()
            ->with(['user', 'warehouse', 'workTypes.option', 'governorates', 'cities', 'vehicle.transportType'])
            ->when($validated['account_number'] ?? null, fn ($query, $accountNumber) => $query->where('account_number', 'like', "%{$accountNumber}%"))
            ->when($validated['name'] ?? null, function ($query, $name) {
                $query->where(function ($builder) use ($name) {
                    $builder->where('first_name', 'like', "%{$name}%")
                        ->orWhere('father_name', 'like', "%{$name}%")
                        ->orWhere('last_name', 'like', "%{$name}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('email', 'like', "%{$name}%"));
                });
            })
            ->when($validated['mobile'] ?? null, function ($query, $mobile) {
                $query->where(function ($builder) use ($mobile) {
                    $builder->where('phone', 'like', "%{$mobile}%")
                        ->orWhere('second_phone', 'like', "%{$mobile}%");
                });
            })
            ->when(($validated['account_type'] ?? 'all') !== 'all', fn ($query) => $query->where('account_type', $validated['account_type']))
            ->when(($validated['status'] ?? 'all') !== 'all', fn ($query) => $query->where('status', $validated['status']))
            ->when(($validated['governorate_id'] ?? null), fn ($query, $governorateId) => $query->whereHas('governorates', fn ($governorateQuery) => $governorateQuery->where('governorates.id', $governorateId)))
            ->when(($validated['city_id'] ?? null), fn ($query, $cityId) => $query->whereHas('cities', fn ($cityQuery) => $cityQuery->where('cities.id', $cityId)))
            ->when(($validated['work_type'] ?? 'all') !== 'all', fn ($query) => $query->whereHas('workTypes', fn ($workQuery) => $workQuery->where('work_type', $validated['work_type'])))
            ->orderBy($sortBy, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $governorates = Governorate::query()->orderBy('name_ar')->get();
        $cities = City::query()->orderBy('name_ar')->get();
        $workTypes = RepresentativeWorkTypeOption::query()->orderBy('sort_order')->get();

        return view('dashboard.admin.representatives.index', compact('representatives', 'sortBy', 'sortDir', 'governorates', 'cities', 'workTypes'));
    }

    public function show(Representative $representative)
    {
        $representative->load([
            'user',
            'warehouse',
            'workTypes.option',
            'governorates',
            'cities',
            'vehicle.transportType',
            'mediaFiles',
        ]);

        return view('dashboard.admin.representatives.show', compact('representative'));
    }

    public function approve(Representative $representative)
    {
        $representative->update([
            'status' => RepresentativeStatus::APPROVED,
            'reviewed_at' => now(),
            'approved_at' => now(),
            'suspended_at' => null,
            'rejection_reason' => null,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'تم اعتماد المندوب بنجاح.');
    }

    public function reject(RejectRepresentativeRequest $request, Representative $representative)
    {
        $representative->update([
            'status' => RepresentativeStatus::REJECTED,
            'reviewed_at' => now(),
            'approved_at' => null,
            'suspended_at' => null,
            'rejection_reason' => $request->validated()['rejection_reason'],
            'is_active' => false,
        ]);

        return redirect()->back()->with('success', 'تم رفض المندوب بنجاح.');
    }

    public function suspend(Representative $representative)
    {
        $representative->update([
            'status' => RepresentativeStatus::SUSPENDED,
            'suspended_at' => now(),
            'is_active' => false,
        ]);

        return redirect()->back()->with('success', 'تم إيقاف المندوب بنجاح.');
    }

    public function reactivate(Representative $representative)
    {
        $representative->update([
            'status' => RepresentativeStatus::APPROVED,
            'approved_at' => $representative->approved_at ?? now(),
            'suspended_at' => null,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'تمت إعادة تفعيل المندوب بنجاح.');
    }
}
