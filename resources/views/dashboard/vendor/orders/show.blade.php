@extends('layouts.vendor')

@section('title', __('vendor-dashboard.order_details'))
@section('page-title', __('vendor-dashboard.order_details'))

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

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">{{ __('vendor-dashboard.order') }} #{{ $order->order_number }}</h5>
                <small class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</small>
            </div>
            <span class="badge bg-secondary">{{ $order->status }}</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <h6 class="text-muted mb-2">{{ __('vendor-dashboard.customer') }}</h6>
                    <div class="fw-semibold">{{ $order->user->username ?? '-' }}</div>
                    <div class="text-muted small">{{ $order->user->email ?? '' }}</div>
                    <div class="text-muted small">{{ $order->phone ?? '' }}</div>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted mb-2">{{ __('vendor-dashboard.shipping_address') }}</h6>
                    @if($order->userAddress)
                        <div class="text-muted small">{{ optional($order->userAddress->country)->name_en }}</div>
                        <div class="text-muted small">{{ optional($order->userAddress->state)->name_en }}</div>
                        <div class="text-muted small">{{ optional($order->userAddress->city)->name_en }}</div>
                        <div class="text-muted small">{{ optional($order->userAddress->zone)->name_en }}</div>
                        <div class="text-muted small">{{ $order->userAddress->street_name }} {{ $order->userAddress->building }}</div>
                    @else
                        <div class="text-muted small">-</div>
                    @endif
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted mb-2">
                        {{ __('vendor-dashboard.your_financials') }}
                        <small class="text-info">({{ $vendorTotals['items_count'] ?? 0 }} {{ __('vendor-dashboard.items') }})</small>
                    </h6>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>{{ __('vendor-dashboard.subtotal') }}</span>
                        <span>{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($vendorTotals['subtotal'] ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>{{ __('vendor-dashboard.shipping') }}</span>
                        <span>{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($vendorTotals['shipping'] ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>{{ __('vendor-dashboard.discount') }}</span>
                        <span>-{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($vendorTotals['discount'] ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>{{ __('vendor-dashboard.sale_discount') }}</span>
                        <span>-{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($vendorTotals['product_discount'] ?? 0, 2) }}</span>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between fw-semibold">
                        <span>{{ __('vendor-dashboard.total') }}</span>
                        <span>{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($vendorTotals['total'] ?? 0, 2) }}</span>
                    </div>
                    @if(($vendorTotals['total_returned'] ?? 0) > 0)
                        <div class="d-flex justify-content-between text-danger small">
                            <span>{{ __('vendor-dashboard.returned') }}</span>
                            <span>-{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($vendorTotals['total_returned'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-success fw-bold">
                            <span>{{ __('vendor-dashboard.net_total') }}</span>
                            <span>{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($vendorTotals['net_total'] ?? 0, 2) }}</span>
                        </div>
                    @endif
                    <hr class="my-1">
                    <div class="d-flex justify-content-between text-success small">
                        <span>{{ __('vendor-dashboard.paid_amount') }}</span>
                        <span>{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($vendorTotals['paid'] ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-danger small">
                        <span>{{ __('vendor-dashboard.remaining_amount') }}</span>
                        <span>{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($vendorTotals['remaining'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($vendorTotals['items_count'] < $order->items->count())
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle"></i>
                    {{ __('vendor-dashboard.order_has_other_vendors_note', [
                        'your_items' => $vendorTotals['items_count'],
                        'total_items' => $order->items->count()
                    ]) }}
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('vendor-dashboard.items_for_you') }}</h5>
        </div>
        <div class="card-body">
            @if($vendorItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('vendor-dashboard.product') }}</th>
                                <th>{{ __('vendor-dashboard.details') }}</th>
                                <th style="width: 280px;">{{ __('vendor-dashboard.step_accept_pickup') }}</th>
                                <th style="width: 320px;">{{ __('vendor-dashboard.step_assign_company') }}</th>
                                <th style="width: 220px;">{{ __('vendor-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendorItems as $item)
                                @php
                                    $isAccepted = ($item->vendor_status->value !== 'pending') && ($item->vendor_status->value === 'accepted');
                                    $isCancelled = $item->vendor_status->value === 'cancelled';
                                    $isAssigned = !empty($item->shipment_company_id);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                        <div class="text-muted small">SKU: {{ $item->product->sku ?? '-' }}</div>
                                        @if($item->variant)
                                            <div class="text-muted small">Variant: #{{ $item->variant->sku }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="row g-1 small text-muted">
                                            <div class="col-6">{{ __('vendor-dashboard.quantity') }}:
                                                <span class="text-dark">{{ $item->quantity }}</span>
                                                @if(($item->returned_quantity ?? 0) > 0)
                                                    <span class="text-danger">({{ $item->returned_quantity }} {{ __('vendor-dashboard.returned_short') }})</span>
                                                @endif
                                            </div>
                                            <div class="col-6">{{ __('vendor-dashboard.unit_price') }}: <span class="text-dark">{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->unit_price, 2) }}</span></div>
                                            <div class="col-6">{{ __('vendor-dashboard.total_price') }}: <span class="text-dark">{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->total_price, 2) }}</span></div>
                                            <div class="col-6">{{ __('vendor-dashboard.shipping') }}: <span class="text-dark">{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->shipment_price ?? 0, 2) }}</span></div>
                                            <div class="col-6">{{ __('vendor-dashboard.discount') }}: <span class="text-dark">{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->discount_amount ?? 0, 2) }}</span></div>
                                            <div class="col-6">{{ __('vendor-dashboard.final_price') }}: <span class="text-dark">{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->final_price ?? $item->total_price, 2) }}</span></div>
                                            @if(($item->returned_amount ?? 0) > 0)
                                                <div class="col-6">{{ __('vendor-dashboard.returned') }}: <span class="text-danger">-{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->returned_amount, 2) }}</span></div>
                                                <div class="col-6">{{ __('vendor-dashboard.net_amount') }}: <span class="text-success fw-bold">{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->net_amount ?? 0, 2) }}</span></div>
                                            @endif
                                            <div class="col-6">{{ __('vendor-dashboard.paid_amount') }}: <span class="text-success">{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->paid_amount ?? 0, 2) }}</span></div>
                                            <div class="col-6">{{ __('vendor-dashboard.remaining_amount') }}: <span class="text-danger">{{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->remaining_amount ?? 0, 2) }}</span></div>
                                            <div class="col-6">{{ __('vendor-dashboard.status') }}:
                                                <span class="badge bg-{{
                                                    $item->vendor_status->value === 'delivered' ? 'success' : (
                                                    $item->vendor_status->value === 'pickup' ? 'info' : (
                                                    $item->vendor_status->value === 'on_way' ? 'info' : (
                                                    $item->vendor_status->value === 'accepted' ? 'primary' : (
                                                    $item->vendor_status->value === 'shipped' ? 'info' : (
                                                    $item->vendor_status->value === 'cancelled' ? 'danger' : (
                                                    $item->vendor_status->value === 'returned' ? 'warning' : 'secondary'))))))
                                                }}">{{ $item->vendor_status->value ? ucfirst($item->vendor_status->value) : 'pending' }}</span>
                                            </div>
                                            <div class="col-12">{{ __('vendor-dashboard.current_pickup') }}: <span class="text-dark">{{ optional($item->pickupBranch ?? optional($item->product)->branch)->name ?? '-' }}</span></div>
                                            <div class="col-12">{{ __('vendor-dashboard.current_company') }}: <span class="text-dark">{{ optional($item->shipmentCompany)->name ?? '-' }}</span></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="border rounded p-2">
                                            <div class="fw-semibold mb-1">{{ __('vendor-dashboard.pickup_branch') }}</div>
                                            @if(!$isCancelled)
                                                <form method="POST" action="{{ route('vendor.orders.items.accept', [$order->id, $item->id]) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    @php
                                                        $eligible = $item->eligible_companies_with_prices ?? collect();

                                                        $canEditBranchAfterAccept = $isAccepted && !$isAssigned && $eligible->isEmpty();
                                                    @endphp

                                                    <select name="pickup_branch_id"
                                                            class="form-select form-select-sm mb-2"
                                                            {{ ($isAccepted && !$canEditBranchAfterAccept) ? 'disabled' : '' }}>                                                        <option value="">{{ __('vendor-dashboard.default_product_branch') }} - {{ optional($item->product->branch)->name }}</option>
                                                        @foreach($branches as $branch)
                                                            <option value="{{ $branch->id }}" {{ (int)($item->pickup_branch_id ?? optional($item->product->branch)->id) === (int)$branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                        @if(!$isAccepted)
                                                            {{-- First time accept --}}
                                                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                                                {{ __('vendor-dashboard.accept_item') }}
                                                            </button>

                                                        @elseif($canEditBranchAfterAccept)
                                                            {{-- Accepted but no companies found → allow updating branch --}}
                                                            <button type="submit" class="btn btn-sm btn-warning w-100">
                                                                {{ __('vendor-dashboard.update_pickup_branch') }}
                                                            </button>

                                                        @else
                                                            {{-- Accepted and locked --}}
                                                            <div class="alert alert-success py-1 px-2 mb-0">
                                                                {{ __('vendor-dashboard.item_accepted') }}
                                                            </div>
                                                        @endif
                                                </form>
                                            @else
                                                <div class="alert alert-danger py-1 px-2 mb-2">
                                                    <i class="fas fa-ban me-1"></i>
                                                    {{ __('vendor-dashboard.item_cancelled_no_changes') }}
                                                </div>
                                            @endif
                                            <div class="text-muted small mt-2">
                                                {{ __('vendor-dashboard.pickup_is_branch_address') }}
                                                <div class="mt-1">{{ optional($item->pickupBranch ?? optional($item->product)->branch)->full_address ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="border rounded p-2">
                                            <div class="fw-semibold mb-1">
                                                {{ __('vendor-dashboard.shipment_company') }}
                                            </div>

                                            @php($eligible = $item->eligible_companies_with_prices ?? collect())

                                            {{-- ===============================
                                                COMPANY ASSIGNMENT SECTION
                                            =================================== --}}
                                            @if(!$isCancelled)
                                                @if($isAccepted && !$isAssigned)
                                                    {{-- Show form if accepted but not assigned --}}
                                                    <form method="POST"
                                                        action="{{ route('vendor.orders.items.assign-shipment', [$order->id, $item->id]) }}"
                                                        class="shipment-assign-form"
                                                        data-item-id="{{ $item->id }}">
                                                        @csrf
                                                        @method('PATCH')

                                                        <div class="mb-2">
                                                            <select name="shipment_company_id"
                                                                    class="form-select form-select-sm shipment-company-select"
                                                                    data-item-id="{{ $item->id }}">
                                                                <option value="">{{ __('vendor-dashboard.select_company') }}</option>
                                                                @foreach($eligible as $company)
                                                                    <option value="{{ $company['id'] }}"
                                                                            data-price="{{ $company['shipment_price'] }}"
                                                                            data-distance="{{ $company['distance_km'] }}"
                                                                            data-est-days="{{ $company['est_days'] }}">
                                                                        {{ $company['name'] }} -
                                                                        {{ config('settings.currency_symbol','EGP') }}{{ number_format($company['shipment_price'],2) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        {{-- 👇 ADD THIS --}}
                                                        <input type="hidden" name="shipment_price_company" class="shipment-price-input">
                                                        <input type="hidden" name="distance" class="shipment-distance-input">
                                                        <input type="hidden" name="est_days" class="shipment-days-input">

                                                        <button type="submit" class="btn btn-sm btn-success w-100">
                                                            {{ __('vendor-dashboard.assign_shipment') }}
                                                        </button>
                                                    </form>


                                                @elseif($isAssigned)
                                                    {{-- Already assigned --}}
                                                    <div class="alert alert-success py-1 px-2 mb-2">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        {{ __('vendor-dashboard.shipment_assigned') }}
                                                        <br>
                                                        <span class="fw-semibold">
                                                            {{ optional($item->shipmentCompany)->name }}
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">
                                                            Price: {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->shipment_price_company ?? $item->shipment_price, 2) }}
                                                            @if($item->distance)
                                                                ({{ number_format($item->distance, 2) }} km)
                                                            @endif
                                                        </small>
                                                    </div>

                                                    {{-- Show other available companies for comparison --}}
                                                    @if($eligible->count() > 1)
                                                        <div class="mt-2">
                                                            <small class="text-muted d-block mb-1">{{ __('vendor-dashboard.other_available_options') }}:</small>
                                                            <div class="list-group list-group-flush">
                                                                @foreach($eligible as $company)
                                                                    @if($company['id'] != $item->shipment_company_id)
                                                                        <div class="list-group-item list-group-item-action p-1">
                                                                            <div class="d-flex justify-content-between">
                                                                                <span class="small">{{ $company['name'] }}</span>
                                                                                <span class="text-primary fw-semibold">
                                                                                    {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($company['shipment_price'], 2) }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="d-flex justify-content-between text-muted small">
                                                                                <span>{{ number_format($company['distance_km'], 2) }} km</span>
                                                                                <span>{{ $company['est_days'] }} {{ __('vendor-dashboard.days') }}</span>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                @else
                                                    {{-- Not accepted yet --}}
                                                    <div class="text-warning small mt-1">
                                                        {{ __('vendor-dashboard.accept_first_then_assign') }}
                                                    </div>

                                                    {{-- Preview available companies --}}
                                                    @if($eligible->isNotEmpty())
                                                        <div class="mt-2">
                                                            <small class="text-muted d-block mb-1">{{ __('vendor-dashboard.available_shipment_options') }}:</small>
                                                            <div class="list-group list-group-flush">
                                                                @foreach($eligible->take(3) as $company)
                                                                    <div class="list-group-item p-1">
                                                                        <div class="d-flex justify-content-between">
                                                                            <span class="small">{{ $company['name'] }}</span>
                                                                            <span class="text-primary fw-semibold">
                                                                                {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($company['shipment_price'], 2) }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                @if($eligible->count() > 3)
                                                                    <div class="list-group-item p-1 text-center">
                                                                        <small class="text-muted">+{{ $eligible->count() - 3 }} {{ __('vendor-dashboard.more_options') }}</small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            @else
                                                {{-- Item is cancelled - show disabled message --}}
                                                <div class="alert alert-danger py-1 px-2 mb-0">
                                                    <i class="fas fa-ban me-1"></i>
                                                    {{ __('vendor-dashboard.item_cancelled_no_company') }}
                                                </div>
                                            @endif

                                            @if($eligible->isEmpty() && !$isCancelled)
                                                <div class="text-danger small mt-1">
                                                    {{ __('vendor-dashboard.no_eligible_companies') }}
                                                </div>
                                            @endif

                                            {{-- ===============================
                                                User Address Display
                                            =================================== --}}
                                            <div class="text-muted small mt-2">
                                                {{ __('vendor-dashboard.dropoff_is_user_address') }}
                                                @if($order->userAddress)
                                                    <div class="mt-1">
                                                        {{ optional($order->userAddress->zone)->name_en }},
                                                        {{ optional($order->userAddress->city)->name_en }},
                                                        {{ optional($order->userAddress->state)->name_en }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-grid gap-2">
                                            @if(!in_array($item->status, ['pickup','on_way','delivered','cancelled']) && !$isCancelled)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger cancel-item-btn"
                                                        data-item-id="{{ $item->id }}"
                                                        data-cancel-url="{{ route('vendor.orders.items.cancel', [$order->id, $item->id]) }}"
                                                        {{ $order->payment_status === 'paid' ? 'disabled' : '' }}
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="{{ $order->payment_status === 'paid' ? __('vendor-dashboard.item_already_paid') : __('vendor-dashboard.cancel_item') }}">
                                                    {{ __('vendor-dashboard.cancel_item') }}
                                                </button>
                                            @elseif($isCancelled)
                                                <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                                                    {{ __('vendor-dashboard.item_cancelled') }}
                                                </button>
                                                @if($item->cancellation_note)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-info view-note-btn"
                                                            data-bs-toggle="popover"
                                                            data-bs-title="{{ __('vendor-dashboard.cancellation_note') }}"
                                                            data-bs-content="{{ $item->cancellation_note }}"
                                                            data-bs-html="true">
                                                        <i class="fas fa-sticky-note me-1"></i>
                                                        {{ __('vendor-dashboard.view_note') }}
                                                    </button>
                                                @endif
                                            @endif
                                            <a href="{{ route('vendor.orders') }}" class="btn btn-sm btn-secondary">{{ __('vendor-dashboard.back') }}</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <div class="d-flex align-items-center gap-3 small">
                                            <div class="fw-semibold">{{ __('vendor-dashboard.timeline') }}:</div>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-secondary">{{ __('vendor-dashboard.created') }}</span>
                                                <span class="badge bg-info">{{ __('vendor-dashboard.pickup') }}</span>
                                                <span class="badge bg-info">{{ __('vendor-dashboard.on_way') }}</span>
                                                <span class="badge bg-success">{{ __('vendor-dashboard.delivered') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">{{ __('vendor-dashboard.no_items_for_vendor') }}</div>
            @endif
        </div>
    </div>

    {{-- Cancellation Note Modal --}}
    <div class="modal fade" id="cancelItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('vendor-dashboard.cancel_item_confirm') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="cancelItemForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <p class="text-danger mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ __('vendor-dashboard.cancel_item_warning') }}
                        </p>

                        <div class="mb-3">
                            <label for="cancellation_note" class="form-label">
                                {{ __('vendor-dashboard.cancellation_note') }} *
                                <span class="text-muted small">({{ __('vendor-dashboard.required_for_audit') }})</span>
                            </label>
                            <textarea
                                class="form-control"
                                id="cancellation_note"
                                name="cancellation_note"
                                rows="4"
                                placeholder="{{ __('vendor-dashboard.enter_cancellation_reason') }}"
                                required
                                minlength="3"
                                maxlength="500"></textarea>
                            <div class="form-text">{{ __('vendor-dashboard.cancellation_note_help') }}</div>
                            @error('cancellation_note')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('vendor-dashboard.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban me-1"></i>
                            {{ __('vendor-dashboard.confirm_cancellation') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Handle shipment company selection changes
    document.querySelectorAll('.shipment-company-select').forEach(select => {
        select.addEventListener('change', function() {

            const form = this.closest('form');
            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption.value) {

                // Fill hidden inputs
                form.querySelector('.shipment-price-input').value = selectedOption.dataset.price || 0;
                form.querySelector('.shipment-distance-input').value = selectedOption.dataset.distance || 0;
                form.querySelector('.shipment-days-input').value = selectedOption.dataset.estDays || 0;

            } else {
                form.querySelector('.shipment-price-input').value = '';
                form.querySelector('.shipment-distance-input').value = '';
                form.querySelector('.shipment-days-input').value = '';
            }
        });
    });


    // Handle form submission
    document.querySelectorAll('.shipment-assign-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const select = this.querySelector('.shipment-company-select');
            if (!select.value) {
                e.preventDefault();
                alert('{{ __("vendor-dashboard.please_select_company") }}');
                select.focus();
                return false;
            }
            return true;
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelItemModal'));
    let currentForm = null;

    // Handle cancel button clicks
    document.querySelectorAll('.cancel-item-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const itemId = this.dataset.itemId;
            const cancelUrl = this.dataset.cancelUrl;

            // Set up the form action
            const form = document.getElementById('cancelItemForm');
            form.action = cancelUrl;
            currentForm = form;

            // Reset form and show modal
            form.reset();
            cancelModal.show();
        });
    });

    // Handle form submission
    const cancelForm = document.getElementById('cancelItemForm');
    if (cancelForm) {
        cancelForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const noteField = document.getElementById('cancellation_note');
            if (noteField.value.trim().length < 3) {
                alert('{{ __("vendor-dashboard.cancellation_note_min_length") }}');
                noteField.focus();
                return;
            }

            // Submit the form
            this.submit();
        });
    }

    // Initialize popovers for viewing notes
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});
</script>
@endsection
