@extends('layouts.admin')

@section('title', __('admin-dashboard.states_management'))
@section('page-title', __('admin-dashboard.states_management'))

@section('page-actions')
    <a href="{{ route('admin.settings.states.create') }}" class="btn btn-primary btn-modern-add">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_state') }}
    </a>
@endsection

@section('content')
    @php
        $sortBy = $sortBy ?? 'created_at';
        $sortDir = $sortDir ?? 'desc';

        $nextSortDir = function ($column) use ($sortBy, $sortDir) {
            if ($sortBy === $column) {
                return $sortDir === 'asc' ? 'desc' : 'asc';
            }

            return 'asc';
        };

        $sortIcon = function ($column) use ($sortBy, $sortDir) {
            if ($sortBy !== $column) {
                return 'fa-sort';
            }

            return $sortDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
        };

        $sortClass = function ($column) use ($sortBy) {
            return $sortBy === $column ? 'is-active' : '';
        };
    @endphp

    <div class="card shadow-sm border-0 states-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="mb-0">{{ __('admin-dashboard.state_list') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-map-marked-alt me-2"></i>
                    {{ $states->total() }}
                </span>
            </div>
        </div>
        <div class="card-body p-0 table-wrap">
            @if($states->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 states-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col {{ $sortClass('id') }}">
                                    <a class="sort-link" href="{{ route('admin.settings.states.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'id', 'sort_dir' => $nextSortDir('id')])) }}">
                                        <span>{{ __('admin-dashboard.id') }}</span>
                                        <i class="fas {{ $sortIcon('id') }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col {{ $sortClass('name_en') }}">
                                    <a class="sort-link" href="{{ route('admin.settings.states.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'name_en', 'sort_dir' => $nextSortDir('name_en')])) }}">
                                        <span>{{ __('admin-dashboard.name_en') }}</span>
                                        <i class="fas {{ $sortIcon('name_en') }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col {{ $sortClass('name_ar') }}">
                                    <a class="sort-link" href="{{ route('admin.settings.states.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'name_ar', 'sort_dir' => $nextSortDir('name_ar')])) }}">
                                        <span>{{ __('admin-dashboard.name_ar') }}</span>
                                        <i class="fas {{ $sortIcon('name_ar') }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>{{ __('admin-dashboard.country') }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap sortable-col {{ $sortClass('cities_count') }}">
                                    <a class="sort-link" href="{{ route('admin.settings.states.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'cities_count', 'sort_dir' => $nextSortDir('cities_count')])) }}">
                                        <span>{{ __('admin-dashboard.cities_count') }}</span>
                                        <i class="fas {{ $sortIcon('cities_count') }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col {{ $sortClass('created_at') }}">
                                    <a class="sort-link" href="{{ route('admin.settings.states.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), ['sort_by' => 'created_at', 'sort_dir' => $nextSortDir('created_at')])) }}">
                                        <span>{{ __('admin-dashboard.created_at') }}</span>
                                        <i class="fas {{ $sortIcon('created_at') }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($states as $state)
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $state->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="state-avatar"><i class="fas fa-map"></i></span>
                                            <span class="fw-semibold text-dark">{{ $state->name_en }}</span>
                                        </div>
                                    </td>
                                    <td class="fw-semibold">{{ $state->name_ar }}</td>
                                    <td>
                                        <span class="country-pill">
                                            {{ app()->getLocale() === 'ar'
                                                ? ($state->country->name_ar ?? '-')
                                                : ($state->country->name_en ?? '-') }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.settings.states.toggle-status', $state->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm status-pill btn-{{ $state->is_active ? 'success' : 'danger' }}">
                                                <span class="status-dot"></span>
                                                {{ $state->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill text-bg-info px-3 py-2">
                                            {{ $state->cities_count }}
                                        </span>
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $state->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.settings.states.edit', $state->id) }}"
                                               class="btn btn-sm btn-warning text-white action-icon-btn"
                                               title="{{ __('admin-dashboard.edit') }}"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.settings.states.destroy', $state->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this state?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger action-icon-btn"
                                                    title="{{ __('admin-dashboard.delete') }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
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
                    {{ $states->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-map-marked-alt empty-icon mb-3"></i>
                        <h5 class="text-muted">No States Found</h5>
                        <p class="text-muted mb-0">Start by adding your first state.</p>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.settings.states.create') }}" class="btn btn-primary btn-modern-add">
                            <i class="fas fa-plus"></i> Add New State
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .states-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }

        .btn-modern-add {
            border-radius: 12px;
            padding-inline: 1rem;
            box-shadow: 0 10px 22px rgba(59, 130, 246, 0.18);
        }

        .rows-counter-badge {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
        }

        .table-wrap {
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        }

        .states-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-color: #e5e7eb;
            padding: 0.95rem 1rem;
            box-shadow: inset 0 -1px 0 #e5e7eb;
            white-space: nowrap;
        }

        .states-table tbody td {
            padding: 1rem;
            border-color: #edf0f5;
        }

        .states-table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .states-table tbody tr:hover {
            background: #f8fafc;
            box-shadow: inset 0 0 0 9999px rgba(248, 250, 252, 0.35);
            transform: translateY(-1px);
        }

        .sortable-col {
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sort-link {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            width: 100%;
        }

        .sortable-col:hover {
            background: #eef2ff;
            color: #1e3a8a;
        }

        .sortable-col.is-active {
            background: #eef2ff;
            color: #1d4ed8;
        }

        .sort-indicator {
            font-size: 0.8rem;
            opacity: 0.85;
        }

        .state-avatar {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
            color: #4f46e5;
            flex: 0 0 auto;
        }

        .country-pill {
            display: inline-block;
            padding: 0.38rem 0.75rem;
            border-radius: 999px;
            background: #f8fafc;
            color: #334155;
            font-size: 0.88rem;
            border: 1px solid #e2e8f0;
        }

        .status-pill,
        .action-icon-btn {
            border-radius: 999px;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .status-pill {
            gap: 0.4rem;
            padding-inline: 0.85rem;
            font-weight: 600;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
        }

        .actions-group {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .action-icon-btn {
            width: 36px;
            height: 36px;
            padding: 0;
        }

        .empty-state {
            min-width: min(100%, 420px);
            border: 1px dashed #d0d7e2;
            border-radius: 18px;
            background: #fcfdff;
        }

        .empty-icon {
            font-size: 2.2rem;
            color: #94a3b8;
        }
    </style>
@endsection
