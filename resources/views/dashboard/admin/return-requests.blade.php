@extends('layouts.admin')

@section('title', __('admin-dashboard.return_requests'))
@section('page-title', __('admin-dashboard.return_requests_management'))

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_dashboard') }}
    </a>
@endsection

@section('content')
    <div class="card shadow-sm border-0 return-requests-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_return_requests') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-list me-2"></i>
                    {{ $returnRequests->count() }} / {{ $returnRequests->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.return-requests') }}" class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            id="requestsSearch"
                            name="search"
                            type="text"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم الريترن أو رقم الطلب أو اسم العميل...' : 'Search by return number, order number, or customer name...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-4">
                    <select id="statusFilter" name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ request('status', 'all') === $status ? 'selected' : '' }}>
                                {{ __('admin-dashboard.status_' . strtoupper($status)) !== 'admin-dashboard.status_' . strtoupper($status) ? __('admin-dashboard.status_' . strtoupper($status)) : ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('admin.return-requests') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($returnRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 requests-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $returnNumberDir = request('sort_by') === 'return_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.return-requests', array_merge(request()->except('page'), ['sort_by' => 'return_number', 'sort_dir' => $returnNumberDir])) }}">
                                        <span>{{ __('admin-dashboard.request_number') }}</span>
                                        <i class="fas {{ request('sort_by') === 'return_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $orderNumberDir = request('sort_by') === 'order_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.return-requests', array_merge(request()->except('page'), ['sort_by' => 'order_number', 'sort_dir' => $orderNumberDir])) }}">
                                        <span>{{ __('admin-dashboard.order_number') }}</span>
                                        <i class="fas {{ request('sort_by') === 'order_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.customer') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.request_status') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.request_reason') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('admin.return-requests', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('admin-dashboard.request_created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returnRequests as $request)
                                @php
                                    $statusValue = is_object($request->status) ? $request->status->value : (string) $request->status;
                                    $statusColor = match ($statusValue) {
                                        'completed', 'approved', 'refunded' => 'success',
                                        'pending', 'requested', 'pickup', 'processing' => 'warning',
                                        'rejected', 'cancelled' => 'danger',
                                        default => 'info',
                                    };
                                    $customerName = $request->order->user?->username ?? __('admin-dashboard.not_available');
                                    $orderNumber = $request->order->order_number ?? __('admin-dashboard.not_available');
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $request->return_number }}</td>
                                    <td>{{ $orderNumber }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="request-avatar" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark request-name">{{ $customerName }}</div>
                                                @if ($request->order->user?->email)
                                                    <small class="text-muted d-block request-subname">{{ $request->order->user->email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-pill btn-{{ $statusColor }}">
                                            <span class="status-dot"></span>
                                            {{ __('admin-dashboard.status_' . strtoupper($statusValue)) !== 'admin-dashboard.status_' . strtoupper($statusValue) ? __('admin-dashboard.status_' . strtoupper($statusValue)) : ucfirst(str_replace('_', ' ', $statusValue)) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $request->reason ? $request->reason : '-' }}
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $request->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.return-requests.show', $request->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.view') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning text-white action-icon-btn" data-bs-toggle="modal" data-bs-target="#statusModal{{ $request->id }}" title="{{ __('admin-dashboard.update_status') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <div class="modal fade" id="statusModal{{ $request->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('admin.return-requests.update-status', $request->id) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('admin-dashboard.update_return_request_status') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="status{{ $request->id }}" class="form-label">{{ __('admin-dashboard.status_label') }}</label>
                                                                    <select class="form-select" id="status{{ $request->id }}" name="status" required>
                                                                        @foreach (\App\Enum\ReturnStatus::cases() as $status)
                                                                            <option value="{{ $status->value }}" {{ $statusValue === $status->value ? 'selected' : '' }}>
                                                                                {{ __('admin-dashboard.status_' . strtoupper($status->value)) !== 'admin-dashboard.status_' . strtoupper($status->value) ? __('admin-dashboard.status_' . strtoupper($status->value)) : ucfirst(str_replace('_', ' ', $status->value)) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <p><strong>{{ __('admin-dashboard.request_number') }}:</strong> {{ $request->return_number }}</p>
                                                                <p><strong>{{ __('admin-dashboard.order_number') }}:</strong> {{ $orderNumber }}</p>
                                                                <p><strong>{{ __('admin-dashboard.customer') }}:</strong> {{ $customerName }}</p>
                                                                <p><strong>{{ __('admin-dashboard.current_status') }}:</strong> <span class="badge bg-secondary">{{ __('admin-dashboard.status_' . strtoupper($statusValue)) !== 'admin-dashboard.status_' . strtoupper($statusValue) ? __('admin-dashboard.status_' . strtoupper($statusValue)) : ucfirst(str_replace('_', ' ', $statusValue)) }}</span></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin-dashboard.cancel') }}</button>
                                                                <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.update_status_button') }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
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
                    {{ $returnRequests->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-undo-alt empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_return_requests_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('admin-dashboard.no_return_requests_message') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .return-requests-card {
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

        .requests-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-color: #e5e7eb;
            padding: 0.95rem 1rem;
            box-shadow: inset 0 -1px 0 #e5e7eb;
        }

        .requests-table tbody td {
            padding: 1rem;
            border-color: #edf0f5;
        }

        .requests-table tbody tr {
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }

        .requests-table tbody tr:hover {
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

        .request-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
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

        .request-name {
            line-height: 1.25;
        }

        .request-subname {
            line-height: 1.2;
        }

        @media (max-width: 991.98px) {
            .rows-counter-badge {
                margin-inline-start: auto;
            }
        }

        @media (max-width: 767.98px) {
            .requests-table thead th,
            .requests-table tbody td {
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
