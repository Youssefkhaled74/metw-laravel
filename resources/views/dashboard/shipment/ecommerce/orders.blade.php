@extends('layouts.shipment')

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-truck text-primary"></i>
                    {{ __('shipment-dashboard.ecommerce_orders_list') }}
                </h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-shopping-cart me-2"></i>
                    {{ $orders->count() }} / {{ $orders->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('shipment.ecommerce.orders') }}" class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم الطلب أو اسم المستخدم أو الإيميل...' : 'Search by order number, user, or email...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('shipment-dashboard.pending_orders') }}</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>{{ __('shipment-dashboard.accepted') }}</option>
                        <option value="pickup" {{ request('status') === 'pickup' ? 'selected' : '' }}>{{ __('shipment-dashboard.pick_up') }}</option>
                        <option value="on_way" {{ request('status') === 'on_way' ? 'selected' : '' }}>{{ __('shipment-dashboard.on_way') }}</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('shipment-dashboard.delivered') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('shipment-dashboard.cancelled') }}</option>
                        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>{{ __('shipment-dashboard.returned') }}</option>
                    </select>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('shipment.ecommerce.orders') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $orderDir = request('sort_by') === 'order' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.ecommerce.orders', array_merge(request()->except('page'), ['sort_by' => 'order', 'sort_dir' => $orderDir])) }}">
                                        <span>{{ __('shipment-dashboard.order_list_title') }}</span>
                                        <i class="fas {{ request('sort_by') === 'order' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $userDir = request('sort_by') === 'user' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.ecommerce.orders', array_merge(request()->except('page'), ['sort_by' => 'user', 'sort_dir' => $userDir])) }}">
                                        <span>{{ __('shipment-dashboard.order_user') }}</span>
                                        <i class="fas {{ request('sort_by') === 'user' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>{{ __('shipment-dashboard.order_status') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $itemsDir = request('sort_by') === 'items' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.ecommerce.orders', array_merge(request()->except('page'), ['sort_by' => 'items', 'sort_dir' => $itemsDir])) }}">
                                        <span>{{ __('shipment-dashboard.items') }}</span>
                                        <i class="fas {{ request('sort_by') === 'items' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $totalDir = request('sort_by') === 'total' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.ecommerce.orders', array_merge(request()->except('page'), ['sort_by' => 'total', 'sort_dir' => $totalDir])) }}">
                                        <span>{{ __('shipment-dashboard.order_total') }}</span>
                                        <i class="fas {{ request('sort_by') === 'total' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $shippingDir = request('sort_by') === 'shipping' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.ecommerce.orders', array_merge(request()->except('page'), ['sort_by' => 'shipping', 'sort_dir' => $shippingDir])) }}">
                                        <span>{{ __('shipment-dashboard.order_shipping') }}</span>
                                        <i class="fas {{ request('sort_by') === 'shipping' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $createdDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.ecommerce.orders', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdDir])) }}">
                                        <span>{{ __('shipment-dashboard.order_created') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-end">{{ __('shipment-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                @php
                                    $statusValue = $order->status->value ?? (string) $order->status;
                                @endphp
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $order->user?->username ?? ($order->user?->email ?? '-') }}
                                    </td>
                                    <td>
                                        <span class="status-pill {{ in_array($statusValue, ['delivered']) ? 'status-active' : (in_array($statusValue, ['cancelled','returned']) ? 'status-inactive' : 'packages-pill') }}">
                                            {{ ucfirst(str_replace('_', ' ', $statusValue)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="count-pill packages-pill">{{ $order->company_items_count ?? 0 }}</span>
                                    </td>
                                    <td>{{ __('admin-dashboard.EGP') }}{{ number_format($order->company_total ?? 0, 2) }}</td>
                                    <td>
                                        {{ __('admin-dashboard.EGP') }}{{ number_format($order->company_shipping ?? 0, 2) }}
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $order->created_at])</td>
                                    <td class="text-end">
                                        <div class="actions-group justify-content-end">
                                            <a href="{{ route('shipment.ecommerce.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-primary text-white action-icon-btn"
                                                title="{{ __('shipment-dashboard.view_order') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($order->requires_delivery_otp && !$order->otp_verified)
                                                <button class="btn btn-sm btn-success text-white action-icon-btn"
                                                        onclick="openDeliveryModal({{ $order->id }})"
                                                        title="{{ __('shipment-dashboard.deliver') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-shield-alt"></i>
                                                </button>
                                            @elseif(!$order->requires_delivery_otp && $statusValue !== 'delivered')
                                                <button class="btn btn-sm btn-success text-white action-icon-btn"
                                                        onclick="directDeliver({{ $order->id }})"
                                                        title="{{ __('shipment-dashboard.accept_delivery') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
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
                        <h5 class="text-muted">{{ __('shipment-dashboard.no_orders_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('shipment-dashboard.no_orders_found') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

{{-- ================= OTP MODAL ================= --}}
<div class="modal fade" id="deliveryOtpModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shield-alt text-success me-2"></i>
                    {{ __('shipment-dashboard.confirm_delivery') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="otpOrderId">

                {{-- STEP 1 --}}
                <div id="sendOtpSection">
                    <p class="text-muted mb-3">
                        {{ __('shipment-dashboard.send_otp_description') }}
                    </p>

                    <button class="btn btn-primary w-100"
                            onclick="sendOtp()">
                        <i class="fas fa-paper-plane"></i>
                        {{ __('shipment-dashboard.send_otp') }}
                    </button>
                </div>

                {{-- STEP 2 --}}
                <div id="confirmOtpSection" class="d-none">
                    <div class="mb-3">
                        <label class="form-label">
                            {{ __('shipment-dashboard.enter_otp') }}
                        </label>
                        <input type="text"
                               id="deliveryOtp"
                               class="form-control text-center fs-5"
                               placeholder="******">
                    </div>

                    <button class="btn btn-success w-100"
                            onclick="confirmDelivery()">
                        <i class="fas fa-check"></i>
                        {{ __('shipment-dashboard.confirm_delivery') }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ================= JS ================= --}}
<script>
let deliveryModal;

function openDeliveryModal(orderId) {
    document.getElementById('otpOrderId').value = orderId;

    document.getElementById('sendOtpSection').classList.remove('d-none');
    document.getElementById('confirmOtpSection').classList.add('d-none');
    document.getElementById('deliveryOtp').value = '';

    deliveryModal = new bootstrap.Modal(
        document.getElementById('deliveryOtpModal')
    );
    deliveryModal.show();
}

function sendOtp() {
    const orderId = document.getElementById('otpOrderId').value;

    fetch(`/shipment/ecommerce/orders/${orderId}/send-otp`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('sendOtpSection').classList.add('d-none');
        document.getElementById('confirmOtpSection').classList.remove('d-none');
    });
}

function confirmDelivery() {
    const orderId = document.getElementById('otpOrderId').value;
    const otp = document.getElementById('deliveryOtp').value;

    fetch(`/shipment/ecommerce/orders/${orderId}/confirm-delivery`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ otp })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        location.reload();
    });
}

    function directDeliver(orderId) {
        if (!confirm('Are you sure you want to mark this order as delivered?')) {
            return;
        }

        fetch(`/shipment/ecommerce/orders/${orderId}/direct-delivery`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            location.reload();
        });
    }
</script>
@endsection
