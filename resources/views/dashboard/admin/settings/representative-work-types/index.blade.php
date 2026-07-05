@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'أنواع عمل المندوب' : 'Representative Work Types')
@section('page-title', app()->getLocale() === 'ar' ? 'إدارة أنواع عمل المندوب' : 'Representative Work Types Management')

@section('page-actions')
    <a href="{{ route('admin.settings.representative-work-types.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة نوع عمل' : 'Add Work Type' }}
    </a>
@endsection

@section('content')
    @php
        $sortBy = $sortBy ?? 'sort_order';
        $sortDir = $sortDir ?? 'asc';
        $nextSortDir = fn ($column) => $sortBy === $column && $sortDir === 'asc' ? 'desc' : 'asc';
        $sortIcon = fn ($column) => $sortBy === $column ? ($sortDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
    @endphp

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ app()->getLocale() === 'ar' ? 'جميع أنواع عمل المندوب' : 'All work types' }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                    <i class="fas fa-briefcase me-2"></i>
                    {{ $workTypes->count() }} / {{ $workTypes->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.representative-work-types.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالكود أو الاسم أو الوصف...' : 'Search by code, name, or description...' }}">
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
                    <a href="{{ route('admin.settings.representative-work-types.index') }}" class="btn btn-outline-secondary w-100">{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if ($workTypes->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.representative-work-types.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'id', 'sort_dir' => $nextSortDir('id')])) }}">
                                        # <i class="fas {{ $sortIcon('id') }} ms-1 text-muted"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.representative-work-types.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'code', 'sort_dir' => $nextSortDir('code')])) }}">
                                        {{ app()->getLocale() === 'ar' ? 'الكود' : 'Code' }}
                                        <i class="fas {{ $sortIcon('code') }} ms-1 text-muted"></i>
                                    </a>
                                </th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</th>
                                <th class="text-nowrap">
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.representative-work-types.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'sort_order', 'sort_dir' => $nextSortDir('sort_order')])) }}">
                                        {{ app()->getLocale() === 'ar' ? 'الترتيب' : 'Sort order' }}
                                        <i class="fas {{ $sortIcon('sort_order') }} ms-1 text-muted"></i>
                                    </a>
                                </th>
                                <th>{{ app()->getLocale() === 'ar' ? 'منفصل' : 'Exclusive' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'مستخدم' : 'Used' }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workTypes as $workType)
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $workType->id }}</td>
                                    <td class="fw-semibold text-primary">{{ $workType->code }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $workType->name_en }}</div>
                                        @if ($workType->name_ar)
                                            <small class="text-muted d-block">{{ $workType->name_ar }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $workType->description ? \Illuminate\Support\Str::limit($workType->description, 75) : '--' }}</td>
                                    <td>{{ $workType->sort_order }}</td>
                                    <td>
                                        <span class="badge rounded-pill {{ $workType->is_exclusive ? 'text-bg-warning' : 'text-bg-light border text-muted' }}">
                                            {{ $workType->is_exclusive ? (app()->getLocale() === 'ar' ? 'نعم' : 'Yes') : (app()->getLocale() === 'ar' ? 'لا' : 'No') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-info px-3 py-2">{{ $workType->work_types_count }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.settings.representative-work-types.toggle-status', $workType) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $workType->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $workType->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.settings.representative-work-types.edit', $workType) }}" class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.settings.representative-work-types.destroy', $workType) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل تريد حذف نوع العمل؟' : 'Delete this work type?' }}');">
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
                    {{ $workTypes->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="d-inline-block px-4 py-5 border rounded-4 bg-light">
                        <i class="fas fa-briefcase fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا توجد أنواع عمل بعد' : 'No work types found yet' }}</h5>
                        <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'ابدأ بإضافة أول نوع عمل.' : 'Start by adding your first work type.' }}</p>
                        <a href="{{ route('admin.settings.representative-work-types.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة نوع عمل' : 'Add Work Type' }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
