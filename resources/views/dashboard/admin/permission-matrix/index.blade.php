@extends('layouts.admin')

@section('title', __('admin-dashboard.permission_matrix'))
@section('page-title', __('admin-dashboard.permission_matrix_management'))

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_dashboard') }}
    </a>
@endsection

@section('content')
<div class="card shadow-sm border-0 matrix-card">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-1 fw-bold">{{ __('admin-dashboard.permission_matrix') }}</h5>
                <p class="mb-0 text-muted small">{{ __('admin-dashboard.permission_matrix_subtitle') }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                    <i class="fas fa-users me-1"></i>
                    {{ $employees->count() }} {{ __('admin-dashboard.employees') }}
                </span>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                    <i class="fas fa-key me-1"></i>
                    {{ $permissions->count() }} {{ __('admin-dashboard.permissions') }}
                </span>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Search & Toolbar --}}
        <form method="GET" action="{{ route('admin.permission-matrix.index') }}" class="mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="input-group input-group-sm" style="max-width: 320px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text"
                               name="search"
                               class="form-control border-start-0"
                               placeholder="{{ __('admin-dashboard.search_employees') }}"
                               value="{{ $search }}"
                               autocomplete="off">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ __('admin-dashboard.apply') }}
                    </button>
                    @if($search)
                        <a href="{{ route('admin.permission-matrix.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo me-1"></i> {{ __('admin-dashboard.reset') }}
                        </a>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="matrixSelectAll()">
                        <i class="fas fa-check-double me-1"></i> {{ __('admin-dashboard.select_all') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="matrixDeselectAll()">
                        <i class="fas fa-times me-1"></i> {{ __('admin-dashboard.deselect_all') }}
                    </button>
                    <span class="text-muted small" id="matrixSelectedCount">
                        {{ __('admin-dashboard.matrix_selected_count', ['count' => 0]) }}
                    </span>
                </div>
            </div>
        </form>

        @if($employees->count() > 0)
            <form method="POST" action="{{ route('admin.permission-matrix.update') }}" id="matrixForm">
                @csrf
                @method('PUT')

                <div class="matrix-wrapper">
                    <table class="table table-bordered align-middle matrix-table" id="permissionMatrix">
                        <thead>
                            <tr>
                                <th class="permission-col sticky-col">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ __('admin-dashboard.permission_name') }}</span>
                                    </div>
                                </th>
                                @foreach($employees as $employee)
                                    <th class="employee-col" data-employee-id="{{ $employee->id }}">
                                        <div class="employee-header">
                                            <span class="employee-name">{{ $employee->first_name }} {{ $employee->last_name }}</span>
                                            @if($employee->position)
                                                <small class="text-muted d-block">{{ $employee->position }}</small>
                                            @endif
                                            <div class="mt-1">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary matrix-toggle-col"
                                                        data-employee-id="{{ $employee->id }}"
                                                        onclick="matrixToggleColumn({{ $employee->id }})"
                                                        title="{{ __('admin-dashboard.toggle_all') }}">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissionGroups as $groupKey => $groupPermissions)
                                @php
                                    $translatedGroupLabel = __('permissions.' . $groupKey);
                                    $groupLabel = $translatedGroupLabel !== 'permissions.' . $groupKey
                                        ? $translatedGroupLabel
                                        : ucwords(str_replace(['admin.', '.', '-', '_'], ' ', $groupKey));
                                @endphp
                                <tr class="group-header-row" data-group="{{ $groupKey }}">
                                    <td class="sticky-col group-header-cell" colspan="{{ $employees->count() + 1 }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button"
                                                        class="btn btn-sm btn-link text-decoration-none p-0 matrix-group-toggle"
                                                        onclick="matrixToggleGroup('{{ $groupKey }}')">
                                                    <i class="fas fa-chevron-down group-arrow"></i>
                                                </button>
                                                <strong class="group-label">{{ $groupLabel }}</strong>
                                                <span class="badge bg-primary-subtle text-primary">{{ $groupPermissions->count() }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        onclick="matrixToggleGroup('{{ $groupKey }}')">
                                                    {{ __('admin-dashboard.toggle_all') }}
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($groupPermissions as $permission)
                                    <tr class="permission-row" data-group="{{ $groupKey }}" data-permission="{{ $permission->name }}">
                                        <td class="sticky-col permission-label-cell">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="permission-text">{{ __('permissions.' . $permission->name) }}</span>
                                                <small class="text-muted permission-key">{{ $permission->name }}</small>
                                            </div>
                                        </td>
                                        @foreach($employees as $employee)
                                            @php
                                                $isChecked = in_array($permission->name, $employeePermissionMap[$employee->id] ?? [], true);
                                            @endphp
                                            <td class="checkbox-cell" data-employee-id="{{ $employee->id }}" data-group="{{ $groupKey }}">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input matrix-checkbox"
                                                           type="checkbox"
                                                           name="matrix[{{ $employee->id }}][]"
                                                           value="{{ $permission->name }}"
                                                           data-employee-id="{{ $employee->id }}"
                                                           data-group="{{ $groupKey }}"
                                                           data-permission="{{ $permission->name }}"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           onchange="matrixCheckboxChanged()">
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="{{ $employees->count() + 1 }}" class="text-center py-5">
                                        <i class="fas fa-shield-halved text-muted mb-3" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted">{{ __('admin-dashboard.no_permissions_found') }}</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.permission-matrix.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-undo me-1"></i> {{ __('admin-dashboard.reset') }}
                    </a>
                    <button type="submit" class="btn btn-primary px-4" id="saveMatrixBtn">
                        <i class="fas fa-save me-1"></i> {{ __('admin-dashboard.save_changes') }}
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">{{ __('admin-dashboard.no_employees_found') }}</h5>
            </div>
        @endif
    </div>
</div>

<style>
    .matrix-card {
        border-radius: 18px;
        overflow: hidden;
    }

    .matrix-wrapper {
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .matrix-table {
        min-width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.85rem;
    }

    .matrix-table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .matrix-table thead th {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #fff;
        font-weight: 600;
        padding: 0.75rem 0.5rem;
        border: none;
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
    }

    .matrix-table thead th.permission-col {
        text-align: start;
        min-width: 250px;
    }

    .matrix-table thead th.employee-col {
        min-width: 130px;
        max-width: 160px;
    }

    .employee-header {
        line-height: 1.3;
    }

    .employee-name {
        font-weight: 600;
        font-size: 0.85rem;
    }

    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 5;
        background: #fff;
        min-width: 250px;
        max-width: 300px;
    }

    .matrix-table thead th.permission-col {
        z-index: 15;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
    }

    .permission-label-cell {
        padding: 0.5rem 0.75rem;
        border-inline-end: 2px solid #e5e7eb;
    }

    .permission-text {
        font-weight: 600;
        color: #111827;
        font-size: 0.82rem;
    }

    .permission-key {
        font-size: 0.7rem;
        word-break: break-all;
    }

    .group-header-row td {
        background: #f8fafc;
        padding: 0.6rem 0.75rem;
        border-block: 2px solid #e5e7eb;
    }

    .group-header-row .sticky-col {
        background: #f8fafc;
    }

    .group-label {
        font-size: 0.9rem;
        color: #374151;
    }

    .group-arrow {
        transition: transform 0.2s ease;
        font-size: 0.7rem;
        color: #4f46e5;
    }

    .group-header-row.collapsed .group-arrow {
        transform: rotate(-90deg);
    }

    .permission-row td {
        padding: 0.4rem 0.5rem;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .permission-row:hover td {
        background: #f0f4ff;
    }

    .permission-row:hover .sticky-col {
        background: #f0f4ff;
    }

    .checkbox-cell {
        width: 60px;
        min-width: 60px;
    }

    .matrix-checkbox {
        width: 1.15em;
        height: 1.15em;
        cursor: pointer;
        accent-color: #4f46e5;
    }

    .group-header-row.collapsed + .permission-row {
        display: none;
    }

    .permission-row.hidden-row {
        display: none;
    }

    .matrix-toggle-col {
        padding: 0.15rem 0.4rem;
        font-size: 0.65rem;
        border-radius: 6px;
    }

    .matrix-wrapper::-webkit-scrollbar {
        height: 8px;
        width: 8px;
    }

    .matrix-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .matrix-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .matrix-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    @media (max-width: 768px) {
        .sticky-col {
            min-width: 180px;
            max-width: 200px;
        }

        .matrix-table thead th.permission-col {
            min-width: 180px;
        }
    }
</style>
@endsection

@push('scripts')
<script>
    function getAllCheckboxes() {
        return Array.from(document.querySelectorAll('.matrix-checkbox'));
    }

    function matrixUpdateCount() {
        const checked = getAllCheckboxes().filter(cb => cb.checked).length;
        const total = getAllCheckboxes().length;
        const counter = document.getElementById('matrixSelectedCount');
        if (counter) {
            counter.textContent = `{{ __('admin-dashboard.matrix_selected_count', ['count' => '__COUNT__']) }}`.replace('__COUNT__', String(checked));
        }
    }

    function matrixSelectAll() {
        getAllCheckboxes().forEach(cb => { cb.checked = true; });
        matrixUpdateCount();
    }

    function matrixDeselectAll() {
        getAllCheckboxes().forEach(cb => { cb.checked = false; });
        matrixUpdateCount();
    }

    function matrixToggleColumn(employeeId) {
        const colCheckboxes = getAllCheckboxes().filter(cb => parseInt(cb.dataset.employeeId) === employeeId);
        const shouldCheck = colCheckboxes.some(cb => !cb.checked);
        colCheckboxes.forEach(cb => { cb.checked = shouldCheck; });
        matrixUpdateCount();
    }

    function matrixToggleGroup(groupKey) {
        const rows = document.querySelectorAll(`.permission-row[data-group="${groupKey}"]`);
        const headerRow = document.querySelector(`.group-header-row[data-group="${groupKey}"]`);
        const isCollapsed = headerRow.classList.contains('collapsed');

        if (isCollapsed) {
            rows.forEach(row => row.classList.remove('hidden-row'));
            headerRow.classList.remove('collapsed');
        } else {
            rows.forEach(row => row.classList.add('hidden-row'));
            headerRow.classList.add('collapsed');
        }
    }

    function matrixCheckboxChanged() {
        matrixUpdateCount();
    }

    document.addEventListener('DOMContentLoaded', function () {
        matrixUpdateCount();

        document.getElementById('matrixForm')?.addEventListener('submit', function (e) {
            const btn = document.getElementById('saveMatrixBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> {{ __("admin-dashboard.saving") }}';
            }
        });
    });
</script>
@endpush
