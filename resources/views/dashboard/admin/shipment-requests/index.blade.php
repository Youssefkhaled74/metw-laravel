@extends('layouts.admin')

@section('title', __('admin-dashboard.shipment_requests'))
@section('page-title', __('admin-dashboard.shipment_requests_management'))

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_dashboard') }}
    </a>
@endsection

@section('content')
    <div class="card shadow-sm border-0 orders-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_shipment_requests') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-list me-2"></i>
                    {{ $requests->count() }} / {{ $requests->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.shipment-requests.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input name="search" type="text" class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم الطلب أو الاسم أو الهاتف...' : 'Search by request number, name, or phone...' }}"
                            value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status', 'all') === $status ? 'selected' : '' }}>
                                {{ __('admin-dashboard.' . $status) !== 'admin-dashboard.' . $status ? __('admin-dashboard.' . $status) : ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <input type="date" name="date_from" class="form-control form-select-sm filter-select-modern"
                        value="{{ request('date_from') }}"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'من تاريخ' : 'From date' }}">
                </div>

                <div class="col-lg-2">
                    <input type="date" name="date_to" class="form-control form-select-sm filter-select-modern"
                        value="{{ request('date_to') }}"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'إلى تاريخ' : 'To date' }}">
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('admin.shipment-requests.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 orders-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap">{{ __('admin-dashboard.request_number') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.customer') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.sender') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.receiver') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.route') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.packages') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.status') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.created_at') }}</th>
                                <th class="text-nowrap">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $sr)
                                @php
                                    $statusValue = $sr->status?->value ?? 'unknown';
                                    $statusColor = match ($statusValue) {
                                        'submitted' => 'warning',
                                        'draft' => 'secondary',
                                        'assigned' => 'info',
                                        'accepted' => 'primary',
                                        'picked_up' => 'dark',
                                        'in_transit' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        'rejected' => 'danger',
                                        default => 'secondary',
                                    };
                                    $sender = $sr->senderContact;
                                    $receiver = $sr->receiverContact;
                                    $senderAddress = $sender?->primaryAddress;
                                    $receiverAddress = $receiver?->primaryAddress;
                                    $packageCount = $sr->packages->count();
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $sr->request_number ?? '--' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="order-avatar" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark order-name">{{ $sr->user->username ?? __('admin-dashboard.guest') }}</div>
                                                @if ($sr->user && $sr->user->phone)
                                                    <small class="text-muted d-block order-subname">{{ $sr->user->phone }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($sender)
                                            <div class="fw-semibold">{{ $sender->full_name }}</div>
                                            <small class="text-muted d-block">{{ $sender->primary_mobile }}</small>
                                            @if ($senderAddress && $senderAddress->city)
                                                <small class="text-muted">{{ $senderAddress->city->name ?? '' }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted small">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($receiver)
                                            <div class="fw-semibold">{{ $receiver->full_name }}</div>
                                            <small class="text-muted d-block">{{ $receiver->primary_mobile }}</small>
                                            @if ($receiverAddress && $receiverAddress->city)
                                                <small class="text-muted">{{ $receiverAddress->city->name ?? '' }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted small">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($senderAddress && $receiverAddress)
                                            <div class="d-flex align-items-center gap-1 small">
                                                <span class="text-muted">{{ $senderAddress->city?->name ?? '--' }}</span>
                                                <i class="fas fa-arrow-right text-primary mx-1"></i>
                                                <span class="text-muted">{{ $receiverAddress->city?->name ?? '--' }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted small">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="shipment-pill">{{ $packageCount }} {{ __('admin-dashboard.package_unit') }}</span>
                                    </td>
                                    <td>
                                        <span class="status-pill btn-{{ $statusColor }}">
                                            <span class="status-dot"></span>
                                            {{ __('admin-dashboard.' . $statusValue) !== 'admin-dashboard.' . $statusValue ? __('admin-dashboard.' . $statusValue) : ucfirst(str_replace('_', ' ', $statusValue)) }}
                                        </span>
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $sr->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('admin.shipment-requests.show', $sr->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.view') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    <x-pagination :paginator="$requests" />
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-shipping-fast empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('admin-dashboard.no_shipment_requests_found') }}</h5>
                        <p class="text-muted mb-2">{{ __('admin-dashboard.no_shipment_requests_message') }}</p>
                        @if(request('search') || request('status') !== 'all' || request('date_from') || request('date_to'))
                            <a href="{{ route('admin.shipment-requests.index') }}" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'مسح الفلاتر' : 'Clear filters' }}
                            </a>
                        @endif
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
        .table-wrap { background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%); }
        .search-shell .input-group-text, .search-shell .form-control, .filter-select-modern {
            border-color: #e5e7eb;
            min-height: 44px;
        }
        .search-shell .input-group-text { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .search-input-modern { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
        .search-input-modern:focus, .filter-select-modern:focus {
            box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.12);
            border-color: #93c5fd;
        }
        .filter-select-modern {
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }
        .rows-counter-badge { min-height: 42px; display: inline-flex; align-items: center; }
        .orders-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            border-color: #e5e7eb;
            padding: 0.95rem 1rem;
            box-shadow: inset 0 -1px 0 #e5e7eb;
        }
        .orders-table tbody td { padding: 1rem; border-color: #edf0f5; }
        .orders-table tbody tr { transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease; }
        .orders-table tbody tr:hover {
            background: #f8fafc;
            box-shadow: inset 0 0 0 9999px rgba(248, 250, 252, 0.35);
            transform: translateY(-1px);
        }
        .order-avatar {
            width: 42px; height: 42px; border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            flex: 0 0 auto;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.7);
        }
        .shipment-pill {
            display: inline-block; padding: 0.38rem 0.75rem;
            border-radius: 999px; background: #f8fafc; color: #334155;
            font-size: 0.88rem; border: 1px solid #e2e8f0; white-space: nowrap;
        }
        .status-pill {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding-inline: 0.85rem; border-radius: 999px;
            min-height: 36px; font-weight: 600;
            box-shadow: 0 8px 20px rgba(15,23,42,0.06);
        }
        .status-pill.btn-success { background-color: #10b981 !important; color: white !important; }
        .status-pill.btn-warning { background-color: #f59e0b !important; color: white !important; }
        .status-pill.btn-danger { background-color: #ef4444 !important; color: white !important; }
        .status-pill.btn-info { background-color: #3b82f6 !important; color: white !important; }
        .status-pill.btn-primary { background-color: #6366f1 !important; color: white !important; }
        .status-pill.btn-dark { background-color: #1e293b !important; color: white !important; }
        .status-pill.btn-secondary { background-color: #94a3b8 !important; color: white !important; }
        .status-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: currentColor; box-shadow: 0 0 0 3px rgba(255,255,255,0.42);
            flex: 0 0 auto;
        }
        .actions-group { display: inline-flex; flex-wrap: nowrap; align-items: center; gap: 0.45rem; }
        .action-icon-btn {
            width: 38px; min-width: 38px; height: 38px; padding: 0;
            box-shadow: 0 8px 18px rgba(15,23,42,0.05);
            border-radius: 999px !important;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .empty-state { background: linear-gradient(180deg, #fafafa 0%, #f8fafc 100%); border: 1px dashed #d1d5db; border-radius: 18px; }
        .empty-icon { font-size: 2.25rem; color: #94a3b8; }
        .order-name { line-height: 1.25; }
        .order-subname { line-height: 1.2; }
        @media (max-width: 991.98px) { .rows-counter-badge { margin-inline-start: auto; } }
        @media (max-width: 767.98px) {
            .orders-table thead th, .orders-table tbody td { padding: 0.8rem 0.85rem; font-size: 0.9rem; }
            .actions-group { width: 100%; flex-wrap: wrap; }
            .actions-group .action-icon-btn { min-width: 0; width: auto; flex: 1 1 calc(50% - 0.25rem); }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el, { placement: 'top', fallbackPlacements: [] }); });
        });
    </script>
