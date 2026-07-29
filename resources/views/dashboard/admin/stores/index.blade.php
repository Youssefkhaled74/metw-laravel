@extends('layouts.admin')

@section('title', __('admin-dashboard.stores_management'))
@section('page-title', __('admin-dashboard.stores_management'))

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';
    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $visibleCount = $stores->count();
    $totalCount = method_exists($stores, 'total') ? $stores->total() : $stores->count();

    $sortBy = request('sort_by', 'created_at');
    $sortDir = request('sort_dir', 'desc');
    $statusFilter = request('status', 'all');

    $sortUrl = static function (string $column) use ($sortBy, $sortDir) {
        $nextDir = $sortBy === $column && $sortDir === 'asc' ? 'desc' : 'asc';
        return route('admin.stores', array_merge(
            request()->except('page'),
            ['sort_by' => $column, 'sort_dir' => $nextDir]
        ));
    };

    $sortIcon = static function (string $column) use ($sortBy, $sortDir) {
        if ($sortBy !== $column) return 'fa-sort';
        return $sortDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    };
@endphp

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $text('Stores', 'المتاجر') }}</h4>
            <p class="text-muted small mb-0">{{ $text('Manage vendor stores and business profiles.', 'إدارة متاجر الموردون والملفات التجارية.') }}</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-right me-1"></i> {{ $text('Back to Dashboard', 'العودة للوحة التحكم') }}
        </a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="fas fa-store text-primary"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $totalCount }}</h5>
                        <small class="text-muted">{{ $text('Total Stores', 'إجمالي المتاجر') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ \App\Models\VendorBusinessProfile::where('status', 'approved')->count() }}</h5>
                        <small class="text-muted">{{ $text('Approved', 'معتمد') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ \App\Models\VendorBusinessProfile::where('status', 'pending')->count() }}</h5>
                        <small class="text-muted">{{ $text('Pending', 'قيد المراجعة') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ \App\Models\VendorBusinessProfile::where('status', 'rejected')->count() }}</h5>
                        <small class="text-muted">{{ $text('Rejected', 'مرفوض') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.stores') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label class="form-label fw-semibold small">{{ $text('Search', 'بحث') }}</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input
                                name="search"
                                type="text"
                                class="form-control"
                                placeholder="{{ $text('Search by store name, vendor name, or phone...', 'ابحث باسم المتجر أو المورد أو الهاتف...') }}"
                                value="{{ request('search') }}"
                                autocomplete="off"
                            >
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label fw-semibold small">{{ $text('Status', 'الحالة') }}</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="all">{{ $text('All statuses', 'كل الحالات') }}</option>
                            <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>{{ $text('Approved', 'معتمد') }}</option>
                            <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>{{ $text('Pending', 'قيد المراجعة') }}</option>
                            <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>{{ $text('Rejected', 'مرفوض') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter me-1"></i> {{ $text('Apply', 'تطبيق') }}
                        </button>
                        <a href="{{ route('admin.stores') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo me-1"></i> {{ $text('Reset', 'إعادة ضبط') }}
                        </a>
                    </div>
                </div>
                <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($stores->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-3 py-3">
                                    <a href="{{ $sortUrl('commercial_name') }}" class="text-decoration-none text-dark fw-semibold d-flex align-items-center gap-1">
                                        {{ $text('Store Name', 'اسم المتجر') }}
                                        <i class="fas {{ $sortIcon('commercial_name') }} sort-indicator small"></i>
                                    </a>
                                </th>
                                <th class="px-3 py-3 fw-semibold text-dark">{{ $text('Vendor', 'المورد') }}</th>
                                <th class="px-3 py-3 fw-semibold text-dark">{{ $text('Contact Phone', 'هاتف التواصل') }}</th>
                                <th class="px-3 py-3">
                                    <a href="{{ $sortUrl('status') }}" class="text-decoration-none text-dark fw-semibold d-flex align-items-center gap-1">
                                        {{ $text('Status', 'الحالة') }}
                                        <i class="fas {{ $sortIcon('status') }} sort-indicator small"></i>
                                    </a>
                                </th>
                                <th class="px-3 py-3 fw-semibold text-dark">{{ $text('Products', 'المنتجات') }}</th>
                                <th class="px-3 py-3">
                                    <a href="{{ $sortUrl('created_at') }}" class="text-decoration-none text-dark fw-semibold d-flex align-items-center gap-1">
                                        {{ $text('Created', 'تاريخ الإنشاء') }}
                                        <i class="fas {{ $sortIcon('created_at') }} sort-indicator small"></i>
                                    </a>
                                </th>
                                <th class="px-3 py-3 fw-semibold text-dark text-end">{{ $text('Actions', 'الإجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stores as $store)
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($store->vendor)
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-bold" style="width:40px;height:40px;font-size:14px;">
                                                    {{ mb_substr($store->commercial_name, 0, 1, 'UTF-8') }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $store->commercial_name }}</div>
                                                @if($store->legal_name)
                                                    <small class="text-muted">{{ $store->legal_name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        @if($store->vendor)
                                            <a href="{{ route('admin.vendors.show', $store->vendor->id) }}" class="text-decoration-none">
                                                {{ $store->vendor->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">{{ $store->contact_phone ?? '—' }}</td>
                                    <td class="px-3 py-3">
                                        @php
                                            $statusColors = [
                                                'approved' => 'success',
                                                'pending' => 'warning',
                                                'rejected' => 'danger',
                                            ];
                                            $statusLabels = [
                                                'approved' => $text('Approved', 'معتمد'),
                                                'pending' => $text('Pending', 'قيد المراجعة'),
                                                'rejected' => $text('Rejected', 'مرفوض'),
                                            ];
                                            $currentStatus = $store->status->value ?? $store->status;
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$currentStatus] ?? 'secondary' }} bg-opacity-10 text-{{ $statusColors[$currentStatus] ?? 'secondary' }} px-2 py-1">
                                            {{ $statusLabels[$currentStatus] ?? $currentStatus }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3">
                                        {{ $store->vendor ? $store->vendor->products()->count() : 0 }}
                                    </td>
                                    <td class="px-3 py-3 text-muted small">
                                        {{ $store->created_at?->format('Y-m-d') ?? '—' }}
                                    </td>
                                    <td class="px-3 py-3 text-end">
                                        @if($store->vendor)
                                            <a href="{{ route('admin.vendors.show', $store->vendor->id) }}" class="btn btn-sm btn-outline-primary" title="{{ $text('View Vendor', 'عرض المورد') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.vendors.products', $store->vendor->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ $text('View Products', 'عرض المنتجات') }}">
                                                <i class="fas fa-box"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($stores, 'links'))
                    <div class="d-flex justify-content-center py-3">
                        {{ $stores->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ $text('No stores found', 'لم يتم العثور على متاجر') }}</h5>
                    <p class="text-muted small">{{ $text('No stores match your search criteria.', 'لا توجد متاجر تطابق معايير البحث.') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
