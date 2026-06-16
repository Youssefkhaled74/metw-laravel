@extends('layouts.admin')

@section('title', __('admin-dashboard.ecommerce_orders'))
@section('page-title', __('admin-dashboard.ecommerce_orders_management'))

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_dashboard') }}
    </a>
@endsection

@section('content')
    <div class="card shadow-sm border-0 orders-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_ecommerce_orders') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-list me-2"></i>
                    {{ $orders->count() }} / {{ $orders->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.ecommerce-orders') }}" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            id="ordersSearch"
                            name="search"
                            type="text"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم الطلب أو اسم العميل...' : 'Search by order number or customer name...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-3">
                    <select id="statusFilter" name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status', 'all') === $status ? 'selected' : '' }}>
                                {{ __('admin-dashboard.' . $status) !== 'admin-dashboard.' . $status ? __('admin-dashboard.' . $status) : ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4">
                    <select id="shipmentFilter" name="shipment_company_id" class="form-select form-select-sm filter-select-modern">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'كل شركات الشحن' : 'All shipping companies' }}</option>
                        @foreach($shipmentCompanies as $company)
                            <option value="{{ $company->id }}" {{ request('shipment_company_id', 'all') == (string)$company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                        <option value="none" {{ request('shipment_company_id') === 'none' ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? 'لا يوجد شركة شحن' : 'No shipping company' }}
                        </option>
                    </select>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('admin.ecommerce-orders') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 orders-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $orderNumberDir = request('sort_by') === 'order_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.ecommerce-orders', array_merge(request()->query(), ['sort_by' => 'order_number', 'sort_dir' => $orderNumberDir])) }}">
                                        <span>{{ __('admin-dashboard.order_number') }}</span>
                                        <i class="fas {{ request('sort_by') === 'order_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.customer') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.shipping_company') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $totalAmountDir = request('sort_by') === 'total_amount' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.ecommerce-orders', array_merge(request()->query(), ['sort_by' => 'total_amount', 'sort_dir' => $totalAmountDir])) }}">
                                        <span>{{ __('admin-dashboard.total_amount') }}</span>
                                        <i class="fas {{ request('sort_by') === 'total_amount' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.ecommerce-orders', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    $companies = $order->items->pluck('shipmentCompany')->filter()->unique('id');
                                    $customerName = $order->user->username ?? 'N/A';
                                    $statusDisplay = is_object($order->status) ? $order->status->name : $order->status;
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $order->order_number }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="order-avatar" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark order-name">{{ $customerName }}</div>
                                                @if ($order->user && $order->user->email)
                                                    <small class="text-muted d-block order-subname">{{ $order->user->email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-pill btn-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'cancelled' ? 'danger' : 'info')) }}">
                                            <span class="status-dot"></span>
                                            {{ ucfirst($statusDisplay) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($companies->isNotEmpty())
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($companies as $company)
                                                    <span class="shipment-pill">{{ $company->name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">{{ __('admin-dashboard.no_shipping_company') }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ __('admin-dashboard.EGP') }}{{ number_format($order->total_amount, 2) }}</td>
                                    <td>@include('admin.partials.date', ['date' => $order->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.ecommerce-orders.show', $order->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.view') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning text-white action-icon-btn" data-bs-toggle="modal"
                                                data-bs-target="#statusModal{{ $order->id }}" title="{{ __('admin-dashboard.status') }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="{{ $order->whatsapp_url }}" target="_blank" class="btn btn-sm btn-success text-white action-icon-btn" title="{{ __('admin-dashboard.whatsapp') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </div>

                                        <!-- Status Update Modal -->
                                        <div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('admin.ecommerce-orders.update-status', $order->id) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('admin-dashboard.update_status') }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="status{{ $order->id }}"
                                                                    class="form-label">{{ __('admin-dashboard.status') }}</label>
                                                                    <select class="form-select" id="status{{ $order->id }}" name="status" required>

                                                                        {{-- Show Pending only if current status is still pending --}}
                                                                        @if($order->status === 'pending')
                                                                            <option value="pending" selected>
                                                                                {{ __('admin-dashboard.pending') }}
                                                                            </option>
                                                                        @endif

                                                                        <option value="accepted"
                                                                            {{ $order->status == 'accepted' ? 'selected' : '' }}>
                                                                            {{ __('admin-dashboard.accepted') }}
                                                                        </option>

                                                                        <option value="cancelled"
                                                                            {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                                                            {{ __('admin-dashboard.cancelled') }}
                                                                        </option>

                                                                    </select>
                                                            </div>
                                                            <p><strong>{{ __('admin-dashboard.order_number') }}:</strong> {{ $order->order_number }}</p>
                                                            <p><strong>{{ __('admin-dashboard.customer') }}:</strong>
                                                                {{ $order->user->username ?? __('admin-dashboard.not_available') }}</p>
                                                            <p><strong>{{ __('admin-dashboard.current_status') }}:</strong>
                                                                <span
                                                                    class="badge bg-secondary">{{ ucfirst($statusDisplay) }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __('admin-dashboard.cancel') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.update') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-shopping-cart empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_ecommerce_orders_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.no_ecommerce_orders_message') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .orders-card {
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

        .orders-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-color: #e5e7eb;
            padding: 0.95rem 1rem;
            box-shadow: inset 0 -1px 0 #e5e7eb;
        }

        .orders-table tbody td {
            padding: 1rem;
            border-color: #edf0f5;
        }

        .orders-table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .orders-table tbody tr:hover {
            background: #f8fafc;
            box-shadow: inset 0 0 0 9999px rgba(248, 250, 252, 0.35);
            transform: translateY(-1px);
        }

        .sortable-col {
            cursor: pointer;
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

        .sortable-col.is-active {
            background: #eef2ff;
            color: #1d4ed8;
        }

        .sortable-col.is-active .sort-indicator {
            opacity: 1;
        }

        .order-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .shipment-pill {
            display: inline-block;
            padding: 0.38rem 0.75rem;
            border-radius: 999px;
            background: #f8fafc;
            color: #334155;
            font-size: 0.88rem;
            border: 1px solid #e2e8f0;
            white-space: nowrap;
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
        }

        .status-pill.btn-success {
            background-color: #10b981 !important;
            color: white !important;
        }

        .status-pill.btn-warning {
            background-color: #f59e0b !important;
            color: white !important;
        }

        .status-pill.btn-danger {
            background-color: #ef4444 !important;
            color: white !important;
        }

        .status-pill.btn-info {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.42);
            flex: 0 0 auto;
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

        .order-name {
            line-height: 1.25;
        }

        .order-subname {
            line-height: 1.2;
        }

        @media (max-width: 991.98px) {
            .rows-counter-badge {
                margin-inline-start: auto;
            }
        }

        @media (max-width: 767.98px) {
            .orders-table thead th,
            .orders-table tbody td {
                padding: 0.8rem 0.85rem;
                font-size: 0.9rem;
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

            .shipment-pill {
                max-width: 100%;
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
