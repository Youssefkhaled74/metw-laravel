@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'إدارة المحافظات' : 'Governorates Management')
@section('page-title', app()->getLocale() === 'ar' ? 'إدارة المحافظات' : 'Governorates Management')

@section('page-actions')
    <a href="{{ route('admin.settings.states.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة محافظة' : 'Add Governorate' }}
    </a>
@endsection

@section('content')
    @php
        $sortBy = $sortBy ?? 'created_at';
        $sortDir = $sortDir ?? 'desc';
        $nextSortDir = function ($column) use ($sortBy, $sortDir) {
            return $sortBy === $column && $sortDir === 'asc' ? 'desc' : 'asc';
        };
        $sortIcon = function ($column) use ($sortBy, $sortDir) {
            if ($sortBy !== $column) {
                return 'fa-sort';
            }

            return $sortDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
        };
    @endphp

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ app()->getLocale() === 'ar' ? 'جميع المحافظات' : 'All governorates' }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                    <i class="fas fa-map-location-dot me-2"></i>
                    {{ $governorates->count() }} / {{ $governorates->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.states.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم المحافظة أو اسم العاصمة...' : 'Search by governorate number or capital city...' }}">
                </div>
                <div class="col-lg-3">
                    <select name="status" class="form-select">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="active" @selected(request('status') === 'active')>{{ __('admin-dashboard.active') }}</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>{{ __('admin-dashboard.inactive') }}</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">{{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}</button>
                </div>
                <div class="col-lg-2">
                    <a href="{{ route('admin.settings.states.index') }}" class="btn btn-outline-secondary w-100">{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if ($governorates->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.states.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'id', 'sort_dir' => $nextSortDir('id')])) }}">
                                        # <i class="fas {{ $sortIcon('id') }} ms-1 text-muted"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.states.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'governorate_number', 'sort_dir' => $nextSortDir('governorate_number')])) }}">
                                        {{ app()->getLocale() === 'ar' ? 'رقم المحافظة' : 'Governorate no.' }}
                                        <i class="fas {{ $sortIcon('governorate_number') }} ms-1 text-muted"></i>
                                    </a>
                                </th>
                                <th>{{ app()->getLocale() === 'ar' ? 'اسم المحافظة' : 'Governorate' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'العاصمة' : 'Capital city' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'المدن' : 'Cities' }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap">
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.states.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'created_at', 'sort_dir' => $nextSortDir('created_at')])) }}">
                                        {{ __('admin-dashboard.created_at') }}
                                        <i class="fas {{ $sortIcon('created_at') }} ms-1 text-muted"></i>
                                    </a>
                                </th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($governorates as $governorate)
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $governorate->id }}</td>
                                    <td class="fw-semibold text-primary">{{ $governorate->governorate_number ?? '--' }}</td>
                                    <td>{{ $governorate->name_ar ?? $governorate->name_en ?? '--' }}</td>
                                    <td>{{ $governorate->capitalCity?->name ?? '--' }}</td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-info px-3 py-2">{{ $governorate->cities_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.settings.states.toggle-status', $governorate->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $governorate->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $governorate->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $governorate->created_at])</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.settings.states.edit', $governorate->id) }}" class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.settings.states.destroy', $governorate->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل تريد حذف المحافظة؟' : 'Delete this governorate?' }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $governorates->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="d-inline-block px-4 py-5 border rounded-4 bg-light">
                        <i class="fas fa-map-location-dot fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا توجد محافظات بعد' : 'No governorates found yet' }}</h5>
                        <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'ابدأ بإضافة أول محافظة.' : 'Start by adding your first governorate.' }}</p>
                        <a href="{{ route('admin.settings.states.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة محافظة' : 'Add Governorate' }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
