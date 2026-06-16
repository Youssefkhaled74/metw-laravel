@extends('layouts.admin')

@section('title', __('admin-dashboard.consignment_types_management'))
@section('page-title', __('admin-dashboard.consignment_types_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.consignment-types.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_consignment_type') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.consignment_types_management') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-box me-2"></i>
                    {{ $consignmentTypes->count() }} / {{ $consignmentTypes->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.consignment-types.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم أو الوصف أو رقم النوع...' : 'Search by name, description, or ID...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'id') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                    </button>
                    <a href="{{ route('admin.settings.consignment-types.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($consignmentTypes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $idDir = request('sort_by', 'id') === 'id' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.consignment-types.index', array_merge(request()->except('page'), ['sort_by' => 'id', 'sort_dir' => $idDir])) }}">
                                        <span>ID</span>
                                        <i class="fas {{ request('sort_by', 'id') === 'id' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $nameDir = request('sort_by') === 'name' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.consignment-types.index', array_merge(request()->except('page'), ['sort_by' => 'name', 'sort_dir' => $nameDir])) }}">
                                        <span>{{ __('admin-dashboard.consignment_type_name') }}</span>
                                        <i class="fas {{ request('sort_by') === 'name' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consignmentTypes as $type)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $type->id }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark entity-name">{{ $type->translated_name }}</div>
                                        @if($type->translated_description)
                                            <small class="text-muted d-block entity-subname">{{ \Illuminate\Support\Str::limit($type->translated_description, 80) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $type->is_active ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-dot {{ $type->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                            {{ $type->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions-group">
                                            <form action="{{ route('admin.settings.consignment-types.toggle-status', $type) }}" method="POST" class="d-inline m-0"
                                                  onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? ($type->is_active ? 'هل تريد إيقاف هذا النوع؟' : 'هل تريد تفعيل هذا النوع؟') : ($type->is_active ? 'Are you sure you want to deactivate this type?' : 'Are you sure you want to activate this type?') }}');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-{{ $type->is_active ? 'warning' : 'success' }} text-white action-icon-btn"
                                                        title="{{ app()->getLocale() === 'ar' ? ($type->is_active ? 'إيقاف' : 'تفعيل') : ($type->is_active ? 'Deactivate' : 'Activate') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-{{ $type->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.settings.consignment-types.edit', $type) }}" class="btn btn-sm btn-primary text-white action-icon-btn"
                                               title="{{ __('admin-dashboard.edit') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.settings.consignment-types.destroy', $type) }}" method="POST" class="d-inline m-0"
                                                  onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_consignment_type') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white action-icon-btn"
                                                        title="{{ __('admin-dashboard.delete') }}" data-bs-toggle="tooltip" data-bs-placement="top">
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
                    {{ $consignmentTypes->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-box empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_consignment_types_found') }}</h5>
                        <p class="text-muted mb-0">{{ app()->getLocale() === 'ar' ? 'لا يوجد أنواع شحنات مضافة حالياً.' : 'No consignment types are available at the moment.' }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.settings.consignment-types.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_consignment_type') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
