@extends('layouts.admin')

@section('title', __('admin-dashboard.vendors_management'))
@section('page-title', __('admin-dashboard.vendors_management'))

@section('page-actions')
    <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_new_vendor') }}
    </a>
@endsection

@section('content')
    <div class="card shadow-sm border-0 vendors-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_vendors') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-store me-2"></i>
                    {{ $vendors->count() }} / {{ $vendors->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.vendors') }}" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            id="vendorsSearch"
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم أو الإيميل أو الهاتف أو العنوان...' : 'Search by name, email, phone, or address...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
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

                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('admin.vendors') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($vendors->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 vendors-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $idDir = request('sort_by') === 'id' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.vendors', array_merge(request()->except('page'), ['sort_by' => 'id', 'sort_dir' => $idDir])) }}">
                                        <span>{{ __('admin-dashboard.vendor_number') ?? 'Vendor Number' }}</span>
                                        <i class="fas {{ request('sort_by') === 'id' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.vendor_name') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.vendor_email') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.vendor_phone') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.vendor_status') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $productsDir = request('sort_by') === 'products' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.vendors', array_merge(request()->except('page'), ['sort_by' => 'products', 'sort_dir' => $productsDir])) }}">
                                        <span>{{ __('admin-dashboard.vendor_products') }}</span>
                                        <i class="fas {{ request('sort_by') === 'products' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $ordersDir = request('sort_by') === 'orders' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.vendors', array_merge(request()->except('page'), ['sort_by' => 'orders', 'sort_dir' => $ordersDir])) }}">
                                        <span>{{ __('admin-dashboard.vendor_orders') }}</span>
                                        <i class="fas {{ request('sort_by') === 'orders' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.vendors', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.vendor_created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.vendor_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vendors as $vendor)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $vendor->vendor_number }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if ($vendor->logo)
                                                <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->name }}" class="rounded-circle vendor-logo" width="42" height="42">
                                            @else
                                                <div class="vendor-avatar">
                                                    <i class="fas fa-store"></i>
                                                </div>
                                            @endif
                                            <div class="fw-semibold text-dark vendor-name">{{ $vendor->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $vendor->email }}" class="text-decoration-none">{{ $vendor->email }}</a>
                                    </td>
                                    <td>
                                        <a href="tel:{{ $vendor->phone }}" class="text-decoration-none">{{ $vendor->phone }}</a>
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $vendor->is_active ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-dot {{ $vendor->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                            {{ $vendor->is_active ? __('admin-dashboard.vendor_active') : __('admin-dashboard.vendor_inactive') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="count-pill products-pill">{{ $vendor->products_count }}</span>
                                    </td>
                                    <td>
                                        <span class="count-pill orders-pill">{{ $vendor->ecommerce_order_items_count }}</span>
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $vendor->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.vendor_view') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.vendors.toggle-status', $vendor->id) }}" class="d-inline m-0" onsubmit="return confirm('{{ $vendor->is_active ? __('admin-dashboard.vendor_confirm_deactivate') : __('admin-dashboard.vendor_confirm_activate') }}')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-{{ $vendor->is_active ? 'warning' : 'success' }} text-white action-icon-btn" title="{{ $vendor->is_active ? __('admin-dashboard.deactivate_vendor') : __('admin-dashboard.activate_vendor') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-{{ $vendor->is_active ? 'pause' : 'play' }}"></i>
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
                    {{ $vendors->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-store empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_vendors_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.no_vendors_message') }}</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_first_vendor') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .vendors-card {
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

        .vendors-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-color: #e5e7eb;
            padding: 0.95rem 1rem;
            box-shadow: inset 0 -1px 0 #e5e7eb;
        }

        .vendors-table {
            width: 100%;
            table-layout: auto;
        }

        .vendors-table tbody td {
            padding: 1rem;
            border-color: #edf0f5;
            white-space: normal;
            word-break: normal;
        }

        .vendors-table thead th {
            white-space: normal;
        }

        .vendors-table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .vendors-table tbody tr:hover {
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

        .vendor-logo {
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }

        .vendor-avatar {
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

        .vendor-name {
            line-height: 1.25;
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

        .count-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            min-height: 32px;
            border-radius: 999px;
            font-weight: 700;
            padding: 0.2rem 0.7rem;
        }

        .products-pill {
            background: #e0f2fe;
            color: #075985;
        }

        .orders-pill {
            background: #fef3c7;
            color: #92400e;
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

        @media (max-width: 991.98px) {
            .rows-counter-badge {
                margin-inline-start: auto;
            }
        }

        @media (max-width: 767.98px) {
            .vendors-table thead th,
            .vendors-table tbody td {
                padding: 0.8rem 0.85rem;
                font-size: 0.9rem;
            }

            .vendors-table th:nth-child(3),
            .vendors-table td:nth-child(3),
            .vendors-table th:nth-child(4),
            .vendors-table td:nth-child(4),
            .vendors-table th:nth-child(8),
            .vendors-table td:nth-child(8) {
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
                    fallbackPlacements: []
                });
            });
        });
    </script>
@endsection
