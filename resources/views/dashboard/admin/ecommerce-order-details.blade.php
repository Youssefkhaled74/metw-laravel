@extends('layouts.admin')

@section('title', __('admin-dashboard.ecommerce_order_details'))
@section('page-title', __('admin-dashboard.ecommerce_order_details') . ' - ' . $order->order_number)

@section('page-actions')
    <a href="{{ route('admin.ecommerce-orders') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_orders') }}
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('admin-dashboard.order_information') }}</h5>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addOrderPaymentModal">
                        <i class="fas fa-money-bill-wave"></i> {{ __('admin-dashboard.add_order_payment') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>{{ __('admin-dashboard.order_number') }}:</strong></td>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.order_date') }}:</strong></td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.order_status') }}:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($order->status ?? 'pending') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.customer') }}:</strong></td>
                                    <td>
                                        @if($order->user)
                                            <a href="{{ route('admin.users.show', $order->user_id) }}">
                                                {{ $order->user->username }}
                                            </a>
                                        @else
                                            {{ __('admin-dashboard.guest') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.email') }}:</strong></td>
                                    <td>{{ $order->user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.phone') }}:</strong></td>
                                    <td>{{ $order->phone ?? $order->user->phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>{{ __('admin-dashboard.total_amount') }}:</strong></td>
                                    <td>
                                        <span class="fw-bold">
                                            {{__('admin-dashboard.EGP')}}{{ number_format($order->total_amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.paid_amount') }}:</strong></td>
                                    <td>
                                        <span class="text-success">
                                            {{__('admin-dashboard.EGP')}}{{ number_format($order->paid_amount ?? 0, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.remaining_amount') }}:</strong></td>
                                    <td>
                                        <span class="{{ ($order->remaining_amount ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                            {{__('admin-dashboard.EGP')}}{{ number_format($order->remaining_amount ?? 0, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.payment_method') }}:</strong></td>
                                    <td>{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.shipping_method') }}:</strong></td>
                                    <td>{{ $order->shipmentCompany->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('admin-dashboard.warehouse') }}:</strong></td>
                                    <td>{{ $order->warehouse->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            {{-- @if($order->userAddress)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('admin-dashboard.shipping_address') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>{{ $order->userAddress->name ?? $order->user->name }}</strong></p>
                                <p class="mb-1">{{ $order->userAddress->phone ?? $order->phone }}</p>
                                <p class="mb-1">{{ $order->userAddress->address }}</p>
                                <p class="mb-1">{{ $order->userAddress->city }}, {{ $order->userAddress->state }}</p>
                                <p class="mb-0">{{ $order->userAddress->country }}, {{ $order->userAddress->postal_code }}</p>
                            </div>
                            @if($order->userAddress->landmark || $order->userAddress->additional_info)
                                <div class="col-md-6">
                                    @if($order->userAddress->landmark)
                                        <p class="mb-1"><strong>{{ __('admin-dashboard.landmark') }}:</strong> {{ $order->userAddress->landmark }}</p>
                                    @endif
                                    @if($order->userAddress->additional_info)
                                        <p class="mb-0"><strong>{{ __('admin-dashboard.additional_info') }}:</strong> {{ $order->userAddress->additional_info }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif --}}

            <!-- Vendor Cancellation Information -->
            @php
                $cancelledItems = $order->items->filter(function($item) {
                    return $item->vendor_status->value === 'cancelled';
                });
            @endphp

            {{-- @if($cancelledItems->count() > 0)
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-ban me-2"></i>
                            {{ __('admin-dashboard.vendor_cancelled_items') }} ({{ $cancelledItems->count() }})
                        </h5>
                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#cancelledItemsModal">
                            <i class="fas fa-eye"></i> {{ __('admin-dashboard.view_details') }}
                        </button>
                    </div>
                </div>
            @endif --}}

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.order_items') }}</h5>
                </div>
                <div class="card-body">
                    @if ($order->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin-dashboard.product') }}</th>
                                        <th>{{ __('admin-dashboard.variant') }}</th>
                                        <th>{{ __('admin-dashboard.quantity') }}</th>
                                        <th>{{ __('admin-dashboard.unit_price') }}</th>
                                        <th>{{ __('admin-dashboard.total') }}</th>
                                        <th>{{ __('admin-dashboard.vendor_status') }}</th>
                                        <th>{{ __('admin-dashboard.payment_status') }}</th>
                                        <th>{{ __('admin-dashboard.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        @php
                                            $isCancelled = $item->vendor_status->value === 'cancelled';
                                        @endphp
                                        <tr class="{{ $isCancelled ? 'table-danger' : '' }}">
                                            <td style="min-width:220px">
                                                <div class="d-flex align-items-center">
                                                    @if ($item->product && $item->product->media->count() > 0)
                                                        <img src="{{ asset($item->product->media->first()->url) }}"
                                                            alt="{{ $item->product->name }}" width="60"
                                                            class="rounded me-2 mb-2">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $item->product->name ?? __('admin-dashboard.not_available') }}</strong><br>
                                                        <small>{{ __('admin-dashboard.sku') }}: {{ $item->product->sku ?? '-' }}</small><br>
                                                        @if($item->product->has_deposit)
                                                            <span class="badge bg-info">{{ __('admin-dashboard.deposit') }}: {{ $item->product->deposit_percentage }}%</span>
                                                        @endif
                                                        @if($item->product->vendor)
                                                            <div class="mt-1">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-store"></i>
                                                                    {{ $item->product->vendor->name }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($item->variant)
                                                    {{ $item->variant->name ?? '' }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $item->quantity ?? 1 }}</td>
                                            <td>{{__('admin-dashboard.EGP')}}{{ number_format($item->unit_price ?? ($item->product->price ?? 0), 2) }}</td>
                                            <td>{{__('admin-dashboard.EGP')}}{{ number_format($item->final_price ?? (($item->unit_price ?? ($item->product->price ?? 0)) * ($item->quantity ?? 1)), 2) }}</td>
                                            <td>
                                                @if($isCancelled)
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-ban me-1"></i>
                                                        {{ __('admin-dashboard.cancelled_by_vendor') }}
                                                    </span>
                                                    @if($item->vendor_status_updated_at)
                                                        <div class="text-muted small mt-1">
                                                            {{ $item->vendor_status_updated_at->format('M d, Y H:i') }}
                                                        </div>
                                                    @endif
                                                    @if($item->cancellation_note)
                                                        <div class="mt-2">
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-info btn-sm"
                                                                    data-bs-toggle="popover"
                                                                    data-bs-title="{{ __('admin-dashboard.cancellation_note') }}"
                                                                    data-bs-content="<strong>{{ __('admin-dashboard.vendor') }}:</strong> {{ $item->product->vendor->name ?? 'N/A' }}<br><br><strong>{{ __('admin-dashboard.note') }}:</strong><br>{{ $item->cancellation_note }}"
                                                                    data-bs-html="true"
                                                                    data-bs-trigger="hover">
                                                                <i class="fas fa-sticky-note me-1"></i>
                                                                {{ __('admin-dashboard.view_note') }}
                                                            </button>
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge bg-{{
                                                        $item->vendor_status->value === 'delivered' ? 'success' : (
                                                        $item->vendor_status->value === 'pickup' ? 'info' : (
                                                        $item->vendor_status->value === 'on_way' ? 'info' : (
                                                        $item->vendor_status->value === 'accepted' ? 'primary' : (
                                                        $item->vendor_status->value === 'shipped' ? 'info' : (
                                                        $item->vendor_status->value === 'returned' ? 'warning' : 'secondary')))))
                                                    }}">
                                                        {{ $item->vendor_status->value ? ucfirst($item->vendor_status->value) : 'pending' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="payment-status">
                                                    <div>
                                                        <strong>{{ __('admin-dashboard.paid') }}:</strong>
                                                        <span class="text-success">{{__('admin-dashboard.EGP')}}{{ number_format($item->paid_amount ?? 0, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <strong>{{ __('admin-dashboard.remaining') }}:</strong>
                                                        <span class="{{ ($item->remaining_amount ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                                            {{__('admin-dashboard.EGP')}}{{ number_format($item->remaining_amount ?? 0, 2) }}
                                                        </span>
                                                    </div>
                                                    @if($item->product->has_deposit)
                                                        <div>
                                                            <small class="text-info">
                                                                {{ __('admin-dashboard.deposit_amount') }}: {{__('admin-dashboard.EGP')}}{{ number_format($item->getDepositAmount(), 2) }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if(!$isCancelled)
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#addPaymentModal{{ $item->id }}"
                                                            @if($item->remaining_amount <= 0) disabled title="{{ __('admin-dashboard.fully_paid') }}" @endif>
                                                        <i class="fas fa-plus-circle"></i> {{ __('admin-dashboard.add_payment') }}
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                                                        <i class="fas fa-ban"></i> {{ __('admin-dashboard.cancelled') }}
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">{{ __('admin-dashboard.no_order_items_found') }}</p>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.payment_history') }}</h5>
                </div>
                <div class="card-body">
                    @if($order->paymentRecords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('admin-dashboard.date') }}</th>
                                        <th>{{ __('admin-dashboard.item') }}</th>
                                        <th>{{ __('admin-dashboard.amount') }}</th>
                                        <th>{{ __('admin-dashboard.method') }}</th>
                                        <th>{{ __('admin-dashboard.reference') }}</th>
                                        <th>{{ __('admin-dashboard.admin') }}</th>
                                        <th>{{ __('admin-dashboard.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->paymentRecords()->with(['orderItem.product', 'admin'])->latest()->get() as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if($payment->orderItem)
                                                    {{ $payment->orderItem->product->name ?? 'N/A' }}
                                                @else
                                                    <em>{{ __('admin-dashboard.order_payment') }}</em>
                                                @endif
                                            </td>
                                            <td class="text-success">{{__('admin-dashboard.EGP')}}{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                {{ __('admin-dashboard.payment_methods.' . ($payment->payment_method ?? 'cash')) }}
                                            </td>
                                            <td>{{ $payment->reference_number ?? '-' }}</td>
                                            <td>{{ $payment->admin->name ?? 'System' }}</td>
                                            <td>{{ $payment->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">{{ __('admin-dashboard.no_payment_records_found') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- Sidebar with additional info -->
        <div class="col-lg-4">
            <!-- Customer Wallet Card -->
            @if($order->user)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('admin-dashboard.customer_wallet') }}</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addToWalletModal">
                            <i class="fas fa-wallet"></i> {{ __('admin-dashboard.add_to_wallet') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">{{ __('admin-dashboard.customer') }}:</span>
                                <strong>{{ $order->user->username }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">{{ __('admin-dashboard.email') }}:</span>
                                <small>{{ $order->user->email }}</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">{{ __('admin-dashboard.current_balance') }}:</span>
                                <strong class="{{ optional($order->user->wallet)->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{__('admin-dashboard.EGP')}}{{ number_format(optional($order->user->wallet)->balance ?? 0, 2) }}
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">{{ __('admin-dashboard.currency') }}:</span>
                                <strong>{{ optional($order->user->wallet)->currency ?? 'EGP' }}</strong>
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                {{ __('admin-dashboard.wallet_operation_info') }}
                            </small>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.order_summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin-dashboard.total_items') }}:</span>
                            <strong>{{ $order->items->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin-dashboard.cancelled_items') }}:</span>
                            <strong class="text-danger">{{ $cancelledItems->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin-dashboard.active_items') }}:</span>
                            <strong>{{ $order->items->count() - $cancelledItems->count() }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin-dashboard.total_vendors') }}:</span>
                            <strong>{{ $order->items->unique('product.vendor_id')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin-dashboard.deposit_items') }}:</span>
                            <strong>{{ $order->items->filter(function($item) { return $item->product && $item->product->has_deposit; })->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.payment_summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin-dashboard.total_order_amount') }}:</span>
                            <strong>{{__('admin-dashboard.EGP')}}{{ number_format($order->total_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin-dashboard.total_paid') }}:</span>
                            <strong class="text-success">{{__('admin-dashboard.EGP')}}{{ number_format($order->paid_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('admin-dashboard.remaining_balance') }}:</span>
                            <strong class="{{ $order->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                               {{__('admin-dashboard.EGP')}}{{ number_format($order->remaining_amount, 2) }}
                            </strong>
                        </div>
                        <hr>
                        <div class="progress mb-3">
                            @php
                                $paymentPercentage = $order->total_amount > 0 ? ($order->paid_amount / $order->total_amount) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: {{ $paymentPercentage }}%">
                                {{ number_format($paymentPercentage, 1) }}%
                            </div>
                        </div>
                        <div class="small text-muted">
                            {{ __('admin-dashboard.payment_progress') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            {{-- <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.order_actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.ecommerce-orders') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_orders') }}
                        </a>

                        <form action="{{ route('admin.ecommerce-orders.update-status', $order->id) }}" method="POST" class="d-grid">
                            @csrf
                            @method('PATCH')
                            <div class="input-group mb-2">
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.update') }}</button>
                            </div>
                        </form>

                        @if($cancelledItems->count() > 0)
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelledItemsModal">
                                <i class="fas fa-exclamation-triangle"></i> {{ __('admin-dashboard.view_cancelled_items') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div> --}}

            <!-- Shipping Assignment -->
            {{-- <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.shipping_assignment') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ecommerce-orders.assign-shipping', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="shipment_company_id" class="form-label">{{ __('admin-dashboard.shipping_company') }}</label>
                            <select name="shipment_company_id" id="shipment_company_id" class="form-select" required>
                                <option value="">{{ __('admin-dashboard.select_shipping_company') }}</option>
                                @foreach($shipmentCompanies as $company)
                                    <option value="{{ $company->id }}" {{ $order->shipment_company_id == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="warehouse_id" class="form-label">{{ __('admin-dashboard.warehouse') }}</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                                <option value="">{{ __('admin-dashboard.select_warehouse') }}</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ $order->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }} {{ $warehouse->is_main ? '(Main)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.assign_shipping') }}</button>
                        </div>
                    </form>
                </div>
            </div> --}}
        </div>
    </div>

    <!-- Cancelled Items Modal with Notes -->
    @if($cancelledItems->count() > 0)
        <div class="modal fade" id="cancelledItemsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-ban me-2"></i>
                            {{ __('admin-dashboard.vendor_cancelled_items_details') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('admin-dashboard.cancelled_items_info') }}
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th width="25%">{{ __('admin-dashboard.product') }}</th>
                                        <th width="20%">{{ __('admin-dashboard.vendor') }}</th>
                                        <th width="10%">{{ __('admin-dashboard.quantity') }}</th>
                                        <th width="15%">{{ __('admin-dashboard.amount') }}</th>
                                        <th width="15%">{{ __('admin-dashboard.cancelled_at') }}</th>
                                        <th width="15%">{{ __('admin-dashboard.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cancelledItems as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $item->product->name ?? 'N/A' }}</div>
                                                <small class="text-muted">SKU: {{ $item->product->sku ?? '-' }}</small>
                                            </td>
                                            <td>
                                                @if($item->product->vendor)
                                                    <div class="fw-semibold">{{ $item->product->vendor->name }}</div>
                                                    <small class="text-muted">{{ $item->product->vendor->email }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td class="text-danger fw-bold">
                                                {{__('admin-dashboard.EGP')}}{{ number_format($item->final_price, 2) }}
                                            </td>
                                            <td>
                                                @if($item->cancelled_at)
                                                    <div>{{ $item->cancelled_at->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $item->cancelled_at->format('H:i') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->cancellation_note)
                                                    <button type="button" class="btn btn-sm btn-info view-note-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cancellationNoteModal"
                                                            data-product="{{ $item->product->name ?? 'N/A' }}"
                                                            data-vendor="{{ $item->product->vendor->name ?? 'N/A' }}"
                                                            data-note="{{ $item->cancellation_note }}"
                                                            data-date="{{ $item->cancelled_at ? $item->cancelled_at->format('M d, Y H:i') : 'N/A' }}">
                                                        <i class="fas fa-sticky-note"></i> {{ __('admin-dashboard.view_note') }}
                                                    </button>
                                                @else
                                                    <span class="text-muted small">{{ __('admin-dashboard.no_note') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-danger">
                                        <td colspan="3" class="fw-bold">{{ __('admin-dashboard.total') }}:</td>
                                        <td class="fw-bold text-danger">
                                            {{__('admin-dashboard.EGP')}}{{ number_format($cancelledItems->sum('final_price'), 2) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin-dashboard.close') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancellation Note Detail Modal -->
        <div class="modal fade" id="cancellationNoteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-sticky-note text-info me-2"></i>
                            {{ __('admin-dashboard.cancellation_note_details') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">{{ __('admin-dashboard.product_information') }}</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('admin-dashboard.product') }}:</span>
                                <span id="noteProductName" class="fw-bold"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('admin-dashboard.vendor') }}:</span>
                                <span id="noteVendorName" class="fw-bold"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">{{ __('admin-dashboard.cancelled_at') }}:</span>
                                <span id="noteCancelledDate" class="text-muted"></span>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">{{ __('admin-dashboard.cancellation_note') }}</h6>
                            <div class="card">
                                <div class="card-body">
                                    <p id="noteContent" class="mb-0"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin-dashboard.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Order to Wallet Modal -->
    <div class="modal fade" id="addToWalletModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.ecommerce-orders.add-to-wallet', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('admin-dashboard.add_order_to_wallet') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('admin-dashboard.wallet_operation_info_full') }}
                        </div>

                        <div class="mb-3">
                            <label for="wallet_operation" class="form-label">{{ __('admin-dashboard.operation') }} *</label>
                            <select class="form-select" id="wallet_operation" name="operation" required>
                                <option value="">{{ __('admin-dashboard.select_operation') }}</option>
                                <option value="add">{{ __('admin-dashboard.add_to_wallet') }}</option>
                                <option value="subtract">{{ __('admin-dashboard.deduct_from_wallet') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="wallet_amount" class="form-label">{{ __('admin-dashboard.amount') }} *</label>
                            <div class="input-group">
                                <span class="input-group-text">{{__('admin-dashboard.EGP')}}</span>
                                <input type="number" class="form-control" id="wallet_amount"
                                       name="amount" step="0.01" min="0.01"
                                       max="{{ $order->remaining_amount > 0 ? $order->remaining_amount : $order->total_amount }}"
                                       value="{{ $order->remaining_amount > 0 ? $order->remaining_amount : $order->total_amount }}"
                                       required>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('admin-dashboard.max_amount') }}:
                                {{__('admin-dashboard.EGP')}}{{ number_format($order->remaining_amount > 0 ? $order->remaining_amount : $order->total_amount, 2) }}
                            </small>
                            <input type="hidden" name="max_amount" value="{{ $order->remaining_amount > 0 ? $order->remaining_amount : $order->total_amount }}">
                        </div>

                        <div class="mb-3">
                            <label for="wallet_notes" class="form-label">{{ __('admin-dashboard.notes') }}</label>
                            <textarea class="form-control" id="wallet_notes" name="notes"
                                      rows="3" placeholder="{{ __('admin-dashboard.wallet_notes_placeholder') }}"></textarea>
                            <small class="form-text text-muted">
                                {{ __('admin-dashboard.wallet_notes_hint') }}
                            </small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ __('admin-dashboard.wallet_operation_warning') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('admin-dashboard.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-wallet me-2"></i> {{ __('admin-dashboard.confirm_wallet_operation') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Order Payment Modal -->
    <div class="modal fade" id="addOrderPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.ecommerce-orders.add-payment', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('admin-dashboard.add_order_payment') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    @php
                        // Sum unpaid deposits for items that require deposit
                        $orderMinAmount = $order->items->filter(function($item) {
                            return $item->vendor_status->value !== 'cancelled';
                        })->sum(function ($item) {
                            if ($item->product && $item->product->has_deposit) {
                                return max(
                                    0,
                                    $item->getDepositAmount() - ($item->paid_amount ?? 0)
                                );
                            }
                            return 0;
                        });

                        // Fallback if no deposit required
                        $orderMinAmount = $orderMinAmount > 0 ? $orderMinAmount : 0.01;

                        $orderMaxAmount = $order->remaining_amount > 0
                            ? $order->remaining_amount
                            : $order->total_amount;
                    @endphp

                    <div class="modal-body">
                        @if($cancelledItems->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ __('admin-dashboard.cancelled_items_payment_warning', ['count' => $cancelledItems->count()]) }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="order_payment_amount" class="form-label">
                                {{ __('admin-dashboard.payment_amount') }} *
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">{{__('admin-dashboard.EGP')}}</span>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="order_payment_amount"
                                    name="amount"
                                    step="0.01"
                                    min="{{ $orderMinAmount }}"
                                    max="{{ $orderMaxAmount }}"
                                    value="{{ $orderMinAmount }}"
                                    required
                                >
                            </div>

                            <small class="form-text text-muted">
                                {{ __('admin-dashboard.min_amount') }}:
                                {{__('admin-dashboard.EGP')}} {{ number_format($orderMinAmount, 2) }}
                                — {{ __('admin-dashboard.remaining_balance') }}:
                                {{__('admin-dashboard.EGP')}} {{ number_format($orderMaxAmount, 2) }}
                            </small>
                        </div>
                        <div class="mb-3">
                            <label for="order_payment_method" class="form-label">{{ __('admin-dashboard.payment_method') }}</label>
                            <select class="form-select" id="order_payment_method" name="payment_method">
                                <option value="">{{ __('admin-dashboard.select_payment_method') }}</option>
                                <option value="cash">{{ __('admin-dashboard.gift_card') }}</option>
                                <option value="bank_transfer">{{ __('admin-dashboard.cash_transfer') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="order_reference_number" class="form-label">{{ __('admin-dashboard.reference_number') }}</label>
                            <input type="text" class="form-control" id="order_reference_number"
                                   name="reference_number" placeholder="e.g., Check #12345">
                        </div>
                        <div class="mb-3">
                            <label for="order_payment_notes" class="form-label">{{ __('admin-dashboard.notes') }}</label>
                            <textarea class="form-control" id="order_payment_notes" name="notes"
                                      rows="3" placeholder="Any additional notes..."></textarea>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                {{ __('admin-dashboard.remaining_balance') }}: {{__('admin-dashboard.EGP')}}{{ number_format($order->remaining_amount, 2) }}.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin-dashboard.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('admin-dashboard.record_payment') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Item Payment Modals -->
    @foreach($order->items as $item)
        @if($item->vendor_status->value !== 'cancelled')
            <div class="modal fade" id="addPaymentModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.ecommerce-orders.items.add-payment', ['order' => $order->id, 'item' => $item->id]) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('admin-dashboard.add_payment_for') }} {{ $item->product->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body p-3">
                                                    <small class="text-muted d-block">{{ __('admin-dashboard.total') }}</small>
                                                    <strong>{{__('admin-dashboard.EGP')}}{{ number_format($item->final_price, 2) }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body p-3">
                                                    <small class="text-muted d-block">{{ __('admin-dashboard.remaining') }}</small>
                                                    <strong class="{{ $item->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                                        {{__('admin-dashboard.EGP')}}{{ number_format($item->remaining_amount, 2) }}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($item->product->has_deposit)
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('admin-dashboard.product_has_deposit') }} {{ $item->product->deposit_percentage }}% deposit.
                                        {{ __('admin-dashboard.deposit_amount') }}: <strong>{{__('admin-dashboard.EGP')}} {{ number_format($item->getDepositAmount(), 2) }}</strong>
                                    </div>
                                @endif

                                @php
                                    $minAmount = $item->product->has_deposit
                                        ? $item->getDepositAmount()
                                        : 0.01;

                                    $maxAmount = $item->remaining_amount > 0
                                        ? $item->remaining_amount
                                        : $item->final_price;
                                @endphp

                                <div class="mb-3">
                                    <label for="item_payment_amount_{{ $item->id }}" class="form-label">
                                        {{ __('admin-dashboard.amount') }} *
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">{{__('admin-dashboard.EGP')}}</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="item_payment_amount_{{ $item->id }}"
                                            name="amount"
                                            step="0.01"
                                            {{-- min="{{ $minAmount }}" --}}
                                            max="{{ $maxAmount }}"
                                            value="{{ $minAmount }}"
                                            required
                                        >
                                    </div>

                                    <small class="form-text text-muted">
                                        {{ __('admin-dashboard.min_amount')}}: {{__('admin-dashboard.EGP')}} {{ number_format($minAmount, 2) }}
                                    </small>
                                </div>


                                <div class="mb-3">
                                    <label for="item_payment_method_{{ $item->id }}" class="form-label">{{ __('admin-dashboard.payment_method') }}</label>
                                    <select class="form-select" id="item_payment_method_{{ $item->id }}" name="payment_method">
                                        <option value="">{{ __('admin-dashboard.select_payment_method') }}</option>
                                        <option value="gift_card">{{ __('admin-dashboard.gift_card') }}</option>
                                        <option value="bank_transfer">{{ __('admin-dashboard.cash_transfer') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="item_reference_number_{{ $item->id }}" class="form-label">{{ __('admin-dashboard.reference_number') }}</label>
                                    <input type="text" class="form-control" id="item_reference_number_{{ $item->id }}"
                                           name="reference_number" placeholder="e.g., Check #12345">
                                </div>
                                <div class="mb-3">
                                    <label for="item_payment_notes_{{ $item->id }}" class="form-label">{{ __('admin-dashboard.notes') }}</label>
                                    <textarea class="form-control" id="item_payment_notes_{{ $item->id }}" name="notes"
                                              rows="3" placeholder="Any additional notes..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin-dashboard.cancel') }}</button>
                            <button type="submit" class="btn btn-success">{{ __('admin-dashboard.record_payment') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    <script>

        const cancellationNoteModal = document.getElementById('cancellationNoteModal');
        if (cancellationNoteModal) {
            cancellationNoteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                // Extract data from button
                const productName = button.getAttribute('data-product');
                const vendorName = button.getAttribute('data-vendor');
                const note = button.getAttribute('data-note');
                const date = button.getAttribute('data-date');

                // Update modal content
                document.getElementById('noteProductName').textContent = productName;
                document.getElementById('noteVendorName').textContent = vendorName;
                document.getElementById('noteCancelledDate').textContent = date;
                document.getElementById('noteContent').textContent = note;
            });
        }

        // Initialize popovers for inline notes
        document.addEventListener('DOMContentLoaded', function() {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
        });


        document.addEventListener('DOMContentLoaded', function() {
            // Order payment amount max
            const orderPaymentInput = document.getElementById('order_payment_amount');
            if (orderPaymentInput) {
                const maxAmount = parseFloat(orderPaymentInput.max);
                orderPaymentInput.addEventListener('change', function() {
                    if (parseFloat(this.value) > maxAmount) {
                        this.value = maxAmount;
                    }
                });
            }

            // Wallet amount validation
            const walletAmountInput = document.getElementById('wallet_amount');
            const walletOperationSelect = document.getElementById('wallet_operation');

            if (walletAmountInput) {
                const maxAmount = parseFloat(walletAmountInput.max);

                walletAmountInput.addEventListener('change', function() {
                    if (parseFloat(this.value) > maxAmount) {
                        this.value = maxAmount;
                    }
                });

                walletAmountInput.addEventListener('input', function() {
                    if (parseFloat(this.value) < 0.01) {
                        this.value = 0.01;
                    }
                });
            }

            // Show warning for subtract operation
            if (walletOperationSelect) {
                walletOperationSelect.addEventListener('change', function() {
                    if (this.value === 'subtract') {
                        const currentBalance = {{ optional($order->user->wallet)->balance ?? 0 }};
                        if (currentBalance <= 0) {
                            alert('{{ __("admin-dashboard.wallet_empty_warning") }}');
                        }
                    }
                });
            }

            // Item payment amount max for each modal
            @foreach($order->items as $item)
                @if($item->vendor_status->value !== 'cancelled')
                    const itemPaymentInput{{ $item->id }} = document.getElementById('item_payment_amount_{{ $item->id }}');
                    if (itemPaymentInput{{ $item->id }}) {
                        const maxAmount{{ $item->id }} = parseFloat(itemPaymentInput{{ $item->id }}.max);
                        itemPaymentInput{{ $item->id }}.addEventListener('change', function() {
                            if (parseFloat(this.value) > maxAmount{{ $item->id }}) {
                                this.value = maxAmount{{ $item->id }};
                            }
                        });
                    }
                @endif
            @endforeach
        });
    </script>
@endsection

<style>
.payment-status {
    font-size: 0.9em;
}
.payment-status div {
    margin-bottom: 2px;
}
.table-danger {
    background-color: rgba(220, 53, 69, 0.05);
}
.card-header .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
