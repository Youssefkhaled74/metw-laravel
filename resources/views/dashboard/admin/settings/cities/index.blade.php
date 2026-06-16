@extends('layouts.admin')

@section('title', __('admin-dashboard.city_management'))
@section('page-title', __('admin-dashboard.city_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.cities.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_city') }}
    </a>
@endsection

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $cities */
@endphp

@section('content')
<div class="card shadow-sm border-0 cities-card">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h5 class="mb-0">{{ __('admin-dashboard.all_cities') }}</h5>
            <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                <i class="fas fa-city me-2"></i>
                {{ $cities->count() }} / {{ $cities->total() }}
            </span>
        </div>

        <form method="GET" action="{{ route('admin.settings.cities.index') }}" class="row g-2 align-items-center">
            <div class="col-lg-4">
                <div class="input-group input-group-sm search-shell">
                    <span class="input-group-text bg-white border-end-0 search-icon-shell">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input
                        id="citiesSearch"
                        type="text"
                        name="search"
                        class="form-control border-start-0 search-input-modern"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث باسم المدينة أو المحافظة...' : 'Search by city or state...' }}"
                        value="{{ request('search') }}"
                        autocomplete="off"
                    >
                </div>
            </div>

            <div class="col-lg-3">
                <select id="stateFilter" name="state_id" class="form-select form-select-sm filter-select-modern">
                    <option value="all">{{ app()->getLocale() === 'ar' ? 'كل المحافظات' : 'All states' }}</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ request('state_id', 'all') == (string)$state->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $state->name_ar : $state->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3">
                <select id="statusFilter" name="status" class="form-select form-select-sm filter-select-modern">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('admin-dashboard.active') }}</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('admin-dashboard.inactive') }}</option>
                </select>
            </div>

            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

            <div class="col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                </button>
                <a href="{{ route('admin.settings.cities.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="card-body p-0 table-wrap">
        @if($cities->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 cities-table">
                    <thead>
                        <tr>
                            <th class="text-nowrap sortable-col">
                                @php
                                    $idDir = request('sort_by') === 'id' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a class="text-decoration-none text-reset" href="{{ route('admin.settings.cities.index', array_merge(request()->except('page'), ['sort_by' => 'id', 'sort_dir' => $idDir])) }}">
                                    <span>#</span>
                                    <i class="fas {{ request('sort_by') === 'id' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                </a>
                            </th>
                            <th class="text-nowrap sortable-col">
                                @php
                                    $nameEnDir = request('sort_by') === 'name_en' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a class="text-decoration-none text-reset" href="{{ route('admin.settings.cities.index', array_merge(request()->except('page'), ['sort_by' => 'name_en', 'sort_dir' => $nameEnDir])) }}">
                                    <span>{{ __('admin-dashboard.city_name_en') }}</span>
                                    <i class="fas {{ request('sort_by') === 'name_en' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                </a>
                            </th>
                            <th class="text-nowrap sortable-col">
                                @php
                                    $nameArDir = request('sort_by') === 'name_ar' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a class="text-decoration-none text-reset" href="{{ route('admin.settings.cities.index', array_merge(request()->except('page'), ['sort_by' => 'name_ar', 'sort_dir' => $nameArDir])) }}">
                                    <span>{{ __('admin-dashboard.city_name_ar') }}</span>
                                    <i class="fas {{ request('sort_by') === 'name_ar' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                </a>
                            </th>
                            <th class="text-nowrap sortable-col">
                                @php
                                    $stateDir = request('sort_by') === 'state' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a class="text-decoration-none text-reset" href="{{ route('admin.settings.cities.index', array_merge(request()->except('page'), ['sort_by' => 'state', 'sort_dir' => $stateDir])) }}">
                                    <span>{{ __('admin-dashboard.state') }}</span>
                                    <i class="fas {{ request('sort_by') === 'state' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                </a>
                            </th>
                            <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                            <th class="text-nowrap sortable-col">
                                @php
                                    $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a class="text-decoration-none text-reset" href="{{ route('admin.settings.cities.index', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                    <span>{{ __('admin-dashboard.created_at') }}</span>
                                    <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                </a>
                            </th>
                            <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cities as $city)
                        <tr>
                            <td class="fw-semibold text-primary">{{ $city->id }}</td>
                            <td>{{ $city->name_en }}</td>
                            <td>{{ $city->name_ar }}</td>
                            <td>{{ app()->getLocale() === 'ar' ? ($city->state->name_ar ?? '-') : ($city->state->name_en ?? '-') }}</td>
                            <td>
                                <form action="{{ route('admin.settings.cities.toggle-status', $city->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="status-pill {{ $city->is_active ? 'status-active' : 'status-inactive' }} border-0" title="{{ __('admin-dashboard.status') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <span class="status-dot {{ $city->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                        {{ $city->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                    </button>
                                </form>
                            </td>
                            <td>@include('admin.partials.date', ['date' => $city->created_at])</td>
                            <td>
                                <div class="actions-group">
                                    <a href="{{ route('admin.settings.cities.edit', $city->id) }}" class="btn btn-sm btn-warning text-white action-icon-btn" title="{{ __('admin-dashboard.edit') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.settings.cities.destroy', $city->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_city') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger text-white action-icon-btn" title="{{ __('admin-dashboard.delete') }}" data-bs-toggle="tooltip" data-bs-placement="top">
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
                {{ $cities->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="empty-state d-inline-block px-4 py-5">
                    <i class="fas fa-city empty-icon mb-3"></i>
                    <h5 class="text-muted">{{ __('admin-dashboard.no_cities_found') }}</h5>
                    <a href="{{ route('admin.settings.cities.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_city') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .cities-card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
    }

    .table-wrap {
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
    }

    .search-shell .input-group-text,
    .search-shell .form-control,
    .filter-select-modern {
        border-color: #e5e7eb;
        min-height: 44px;
    }

    .search-shell .input-group-text {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    .search-input-modern {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .search-input-modern:focus,
    .filter-select-modern:focus {
        box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.12);
        border-color: #93c5fd;
    }

    .filter-select-modern {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .rows-counter-badge {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
    }

    .cities-table {
        width: 100%;
        table-layout: auto;
    }

    .cities-table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        border-color: #e5e7eb;
        padding: 0.95rem 1rem;
        box-shadow: inset 0 -1px 0 #e5e7eb;
        white-space: normal;
    }

    .cities-table tbody td {
        padding: 1rem;
        border-color: #edf0f5;
        white-space: normal;
        word-break: normal;
    }

    .cities-table tbody tr {
        transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .cities-table tbody tr:hover {
        background: #f8fafc;
        box-shadow: inset 0 0 0 9999px rgba(248, 250, 252, 0.35);
        transform: translateY(-1px);
    }

    .sortable-col {
        user-select: none;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .sortable-col:hover {
        background: #eef2ff;
        color: #1e3a8a;
    }

    .sortable-col .sort-indicator {
        margin-inline-start: 0.45rem;
        font-size: 0.8rem;
        opacity: 0.75;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding-inline: 0.85rem;
        border-radius: 999px;
        min-height: 36px;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        border: 1px solid transparent;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.42);
        flex: 0 0 auto;
    }

    .status-dot-active {
        color: #16a34a;
    }

    .status-dot-inactive {
        color: #dc2626;
    }

    .actions-group {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 0.45rem;
    }

    .actions-group form {
        display: inline-flex;
        margin: 0;
    }

    .action-icon-btn {
        width: 38px;
        min-width: 38px;
        height: 38px;
        padding: 0;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        border-radius: 999px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .empty-state {
        background: linear-gradient(180deg, #fafafa 0%, #f8fafc 100%);
        border: 1px dashed #d1d5db;
        border-radius: 18px;
    }

    .empty-icon {
        font-size: 2.25rem;
        color: #94a3b8;
    }

    @media (max-width: 767.98px) {
        .cities-table thead th,
        .cities-table tbody td {
            padding: 0.8rem 0.85rem;
            font-size: 0.9rem;
        }

        .cities-table th:nth-child(3),
        .cities-table td:nth-child(3),
        .cities-table th:nth-child(6),
        .cities-table td:nth-child(6) {
            display: none;
        }

        .status-pill,
        .action-icon-btn {
            width: 100%;
        }

        .actions-group {
            width: 100%;
            flex-wrap: wrap;
        }

        .actions-group .action-icon-btn {
            min-width: 0;
            width: auto;
            flex: 1 1 calc(50% - 0.25rem);
        }

        .search-shell .input-group-text,
        .search-shell .form-control,
        .filter-select-modern {
            min-height: 42px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'top',
                fallbackPlacements: [],
                offset: [0, 10],
                container: 'body'
            });
        });
    });
</script>
@endsection
