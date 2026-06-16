@extends('layouts.shipment')

@section('title', __('vendor-dashboard.return_request_details'))
@section('page-title', __('vendor-dashboard.return_request_details'))

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            {{ __('vendor-dashboard.return_number') }}: {{ $returnRequest->return_number }}
        </h5>
    </div>

    <div class="card-body">
        {{-- Customer Info --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-user me-2 text-primary"></i> {{ __('vendor-dashboard.customer_info') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('vendor-dashboard.name') }}:</strong> {{ $returnRequest->user->username ?? '-' }}</p>
                        <p><strong>{{ __('vendor-dashboard.phone') }}:</strong> {{ $returnRequest->user->phone ?? '-' }}</p>
                    </div>
                </div>

                @if($returnRequest->pickupaddress)
                    <hr>
                    <h6 class="fw-bold mb-3 text-primary">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ __('vendor-dashboard.address_details') }}
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('vendor-dashboard.country') }}:</strong> {{ $returnRequest->pickupaddress->country?->{'name_'.app()->getLocale()} ?? '-' }}</p>
                            <p><strong>{{ __('vendor-dashboard.state') }}:</strong> {{ $returnRequest->pickupaddress->state?->{'name_'.app()->getLocale()} ?? '-' }}</p>
                            <p><strong>{{ __('vendor-dashboard.city') }}:</strong> {{ $returnRequest->pickupaddress->city?->{'name_'.app()->getLocale()} ?? '-' }}</p>
                            <p><strong>{{ __('vendor-dashboard.zone') }}:</strong> {{ $returnRequest->pickupaddress->zone?->{'name_'.app()->getLocale()} ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('vendor-dashboard.street_name') }}:</strong> {{ $returnRequest->pickupaddress->street_name ?? '-' }}</p>
                            <p><strong>{{ __('vendor-dashboard.building') }}:</strong> {{ $returnRequest->pickupaddress->building ?? '-' }}</p>
                            <p><strong>{{ __('vendor-dashboard.floor') }}:</strong> {{ $returnRequest->pickupaddress->floor ?? '-' }}</p>
                            <p><strong>{{ __('vendor-dashboard.landmark') }}:</strong> {{ $returnRequest->pickupaddress->landmark ?? '-' }}</p>
                        </div>
                    </div>
                    <p class="mt-2"><strong>{{ __('vendor-dashboard.full_address') }}:</strong> {{ $returnRequest->pickupaddress->full_address ?? '-' }}</p>
                @else
                    <p class="text-muted mb-0">{{ __('vendor-dashboard.no_address_available') }}</p>
                @endif
            </div>
        </div>

        <hr>

        {{-- Returned Items --}}
        <h6 class="fw-bold mb-3">{{ __('vendor-dashboard.returned_items') }}</h6>

        @foreach ($returnRequest->items as $item)
            @php
                $status = $item->status;
                $isApproved = $status === \App\Enum\ReturnStatus::APPROVED;
                $isRejected = $status === \App\Enum\ReturnStatus::REJECTED;
                $isPending = $status === \App\Enum\ReturnStatus::REQUESTED;
                $isPickup = $status === \App\Enum\ReturnStatus::PICKUP;
                $isProccessing = $status === \App\Enum\ReturnStatus::PROCESSING;
                $isRefunded = $status === \App\Enum\ReturnStatus::REFUNDED;
            @endphp

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <strong class="text-dark">{{ $item->product->name_en ?? '-' }}</strong>
                        <small class="text-muted ms-1">({{ __('vendor-dashboard.quantity') }}: {{ $item->return_quantity }})</small>
                    </div>
                <div class="d-flex align-items-center">
                    {{-- Show dropdown only if item is approved --}}
                    @if($isApproved || $isRefunded || $isProccessing || $isPickup)
                        <form action="{{ route('shipment.return-requests.items.toggle-status', $item->id) }}" method="POST" class="d-inline-flex align-items-center gap-2">
                            @csrf
                            @method('PATCH')

                            <select name="action" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                <option value="">{{ __('vendor-dashboard.select_status') }}</option>

                                @foreach (['pickup', 'processing', 'refunded'] as $nextStatus)
                                    <option value="{{ $nextStatus }}" {{ $status->value === $nextStatus ? 'selected' : '' }}>
                                        {{ ucfirst($nextStatus) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                        @if($isPending)
                            <span class="badge bg-warning text-dark">
                                {{ __('vendor-dashboard.status_not_accepted_yet') }}
                            </span>
                        @endif

                    </div>
                    <div>
                        <span class="badge bg-info">{{ ucfirst($status->value) }}</span>                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>{{ __('vendor-dashboard.reason') }}:</strong> {{ $item->return_reason }}</p>
                            <p><strong>{{ __('vendor-dashboard.item_subtotal') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->item_subtotal ?? 0, 2) }}</p>
                            <p><strong>{{ __('vendor-dashboard.vendor_refund_commission') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->vendor_refund_commission ?? 0, 2) }}</p>
                            <p><strong>{{ __('vendor-dashboard.return_shipping_cost') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->return_shipping_cost ?? 0, 2) }}</p>
                            <p><strong>{{ __('vendor-dashboard.shipment_commission') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->shipment_commission ?? 0, 2) }}</p>
                            <p><strong>{{ __('vendor-dashboard.customer_refund_amount') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->customer_refund_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2"><i class="fas fa-truck me-1 text-primary"></i>{{ __('vendor-dashboard.shipment_company') }}</h6>
                            @php $company = $item->orderItem->shipmentCompany; @endphp
                            @if($company)
                                <p class="mb-1"><strong>{{ __('vendor-dashboard.name') }}:</strong> {{ $company->name }}</p>
                                <p class="mb-1"><strong>{{ __('vendor-dashboard.phone') }}:</strong> {{ $company->phone }}</p>
                                <p class="mb-1"><strong>{{ __('vendor-dashboard.address') }}:</strong> {{ $company->address }}</p>
                            @else
                                <p class="text-muted mb-0">{{ __('vendor-dashboard.no_shipment_company') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="border-top pt-2 text-end">
                        <strong>{{ __('vendor-dashboard.refund_amount') }}:</strong>
                        {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->customer_refund_amount ?? $item->return_price, 2) }}
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Totals --}}
        <div class="text-end mt-3">
            <p><strong>{{ __('vendor-dashboard.total_refund') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->calculateRefundAmount(), 2) }}</p>
            <p><strong>{{ __('vendor-dashboard.total_vendor_deduction') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->vendor_deduction_total ?? 0, 2) }}</p>
            <p><strong>{{ __('vendor-dashboard.total_return_shipping') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->return_shipping_total ?? 0, 2) }}</p>
            <p><strong>{{ __('vendor-dashboard.total_shipment_commission') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->shipment_commission_total ?? 0, 2) }}</p>
        </div>
    </div>
</div>
<script>
function toggleStatus(checkbox) {
    const form = checkbox.closest('form');
    const label = form.querySelector('label');
    const hiddenInput = form.querySelector('input[name="action"]');

    if (checkbox.checked) {
        hiddenInput.value = 'approve';
        label.textContent = 'Approved';
        label.classList.remove('text-danger');
        label.classList.add('text-success');
    } else {
        hiddenInput.value = 'reject';
        label.textContent = 'Rejected';
        label.classList.remove('text-success');
        label.classList.add('text-danger');
    }

    form.submit();
}
</script>

<style>
.form-check-input {
    width: 3rem;
    height: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.form-check-input:not(:checked) {
    background-color: #dc3545;
    border-color: #dc3545;
}

.form-check-label {
    transition: color 0.3s ease;
}
</style>
@endsection
