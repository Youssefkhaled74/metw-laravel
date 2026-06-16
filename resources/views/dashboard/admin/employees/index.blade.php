@extends('layouts.admin')

@section('title', __('admin-dashboard.employees'))
@section('page-title', __('admin-dashboard.employees_management'))

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_dashboard') }}
    </a>
    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_employee') }}
    </a>
@endsection

@section('content')
    <div class="card shadow-sm border-0 employees-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_employees') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-users me-2"></i>
                    {{ $employees->count() }} / {{ $employees->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.employees.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-6">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم أو الإيميل أو الهاتف أو الوظيفة...' : 'Search by name, email, phone, or position...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1 flex-lg-grow-0">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 employees-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $employeeNumberDir = request('sort_by') === 'employee_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.employees.index', array_merge(request()->except('page'), ['sort_by' => 'employee_number', 'sort_dir' => $employeeNumberDir])) }}">
                                        <span>{{ __('admin-dashboard.employee_number') }}</span>
                                        <i class="fas {{ request('sort_by') === 'employee_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.full_name') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.email') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.phone') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.position') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $hireDateDir = request('sort_by') === 'hire_date' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.employees.index', array_merge(request()->except('page'), ['sort_by' => 'hire_date', 'sort_dir' => $hireDateDir])) }}">
                                        <span>{{ __('admin-dashboard.hire_date') }}</span>
                                        <i class="fas {{ request('sort_by') === 'hire_date' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.employees.index', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $employee->employee_number }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="employee-avatar">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark employee-name">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                                <small class="text-muted d-block employee-subname">{{ $employee->position ?? __('admin-dashboard.not_available') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->phone ?? '-' }}</td>
                                    <td>{{ $employee->position ?? '-' }}</td>
                                    <td>{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '-' }}</td>
                                    <td>{{ $employee->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.view') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-sm btn-warning text-white action-icon-btn" title="{{ __('admin-dashboard.edit') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('admin-dashboard.confirm_delete_employee') }}');">
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
                    {{ $employees->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-users empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_employees_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.no_employees_message') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .employees-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }

        .table-wrap {
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        }

        .search-shell .input-group-text,
        .search-shell .form-control {
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

        .search-input-modern:focus {
            box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.12);
            border-color: #93c5fd;
        }

        .rows-counter-badge {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
        }

        .employees-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-color: #e5e7eb;
            padding: 0.95rem 1rem;
            box-shadow: inset 0 -1px 0 #e5e7eb;
        }

        .employees-table tbody td {
            padding: 1rem;
            border-color: #edf0f5;
        }

        .employees-table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .employees-table tbody tr:hover {
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

        .employee-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1d4ed8;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
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

        .employee-name {
            line-height: 1.25;
        }

        .employee-subname {
            line-height: 1.2;
        }

        @media (max-width: 991.98px) {
            .rows-counter-badge {
                margin-inline-start: auto;
            }
        }

        @media (max-width: 767.98px) {
            .employees-table thead th,
            .employees-table tbody td {
                padding: 0.8rem 0.85rem;
                font-size: 0.9rem;
            }

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
                flex: 1 1 calc(33.333% - 0.3rem);
            }

            .search-shell .input-group-text,
            .search-shell .form-control {
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
                    fallbackPlacements: []
                });
            });
        });
    </script>
@endsection
