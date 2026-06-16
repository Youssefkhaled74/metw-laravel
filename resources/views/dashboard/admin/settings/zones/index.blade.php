@extends('layouts.admin')

@section('title', __('admin-dashboard.zones_management'))
@section('page-title', __('admin-dashboard.zones_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.zones.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_zone') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.zone_list') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ $zones->count() }} / {{ $zones->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.zones.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث باسم المنطقة عربي/إنجليزي أو المدينة أو الرقم...' : 'Search by zone Arabic/English name, city, or ID...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                    </button>
                    <a href="{{ route('admin.settings.zones.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if($zones->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $idDir = request('sort_by') === 'id' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.zones.index', array_merge(request()->except('page'), ['sort_by' => 'id', 'sort_dir' => $idDir])) }}">
                                        <span>#</span>
                                        <i class="fas {{ request('sort_by') === 'id' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $nameArDir = request('sort_by') === 'name_ar' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.zones.index', array_merge(request()->except('page'), ['sort_by' => 'name_ar', 'sort_dir' => $nameArDir])) }}">
                                        <span>{{ __('admin-dashboard.name_ar') }}</span>
                                        <i class="fas {{ request('sort_by') === 'name_ar' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $nameEnDir = request('sort_by') === 'name_en' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.zones.index', array_merge(request()->except('page'), ['sort_by' => 'name_en', 'sort_dir' => $nameEnDir])) }}">
                                        <span>{{ __('admin-dashboard.name_en') }}</span>
                                        <i class="fas {{ request('sort_by') === 'name_en' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $cityDir = request('sort_by') === 'city' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.zones.index', array_merge(request()->except('page'), ['sort_by' => 'city', 'sort_dir' => $cityDir])) }}">
                                        <span>{{ __('admin-dashboard.city') }}</span>
                                        <i class="fas {{ request('sort_by') === 'city' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.settings.zones.index', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zones as $zone)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $zone->id }}</td>
                                    <td class="fw-semibold text-dark">{{ $zone->name_ar }}</td>
                                    <td class="fw-semibold text-dark">{{ $zone->name_en }}</td>
                                    <td>
                                        {{ app()->getLocale() === 'ar'
                                            ? ($zone->city->name_ar ?? '-')
                                            : ($zone->city->name_en ?? '-') }}
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $zone->is_active ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-dot {{ $zone->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                            {{ $zone->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                        </span>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $zone->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <form action="{{ route('admin.settings.zones.toggle-status', $zone->id) }}" method="POST" class="d-inline m-0"
                                                  onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? ($zone->is_active ? 'هل تريد إيقاف هذه المنطقة؟' : 'هل تريد تفعيل هذه المنطقة؟') : ($zone->is_active ? 'Are you sure you want to deactivate this zone?' : 'Are you sure you want to activate this zone?') }}');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-{{ $zone->is_active ? 'warning' : 'success' }} text-white action-icon-btn"
                                                        title="{{ app()->getLocale() === 'ar' ? ($zone->is_active ? 'إيقاف' : 'تفعيل') : ($zone->is_active ? 'Deactivate' : 'Activate') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-{{ $zone->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.settings.zones.edit', $zone->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn"
                                               title="{{ __('admin-dashboard.edit') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.settings.zones.destroy', $zone->id) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white action-icon-btn"
                                                        onclick="return confirm('{{ __('admin-dashboard.confirm_delete_zone') }}')"
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
                    {{ $zones->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-map-marker-alt empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_zones_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.start_by_adding_zone') }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.settings.zones.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_zone') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
