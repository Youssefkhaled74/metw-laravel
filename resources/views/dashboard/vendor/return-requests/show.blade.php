@extends('layouts.vendor')

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
        {{-- CUSTOMER INFO --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-light">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-user me-2 text-primary"></i> {{ __('vendor-dashboard.customer_info') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong><i class="fas fa-user-circle me-1 text-secondary"></i>{{ __('vendor-dashboard.name') }}:</strong> {{ $returnRequest->user->username ?? '-' }}</p>
                        <p class="mb-2"><strong><i class="fas fa-phone me-1 text-secondary"></i>{{ __('vendor-dashboard.phone') }}:</strong> {{ $returnRequest->user->phone ?? '-' }}</p>
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

        {{-- RETURNED ITEMS --}}
        <h6 class="fw-bold mb-3">{{ __('vendor-dashboard.returned_items') }}</h6>

        @foreach ($returnRequest->items as $item)
            @php
                $status = $item->status;
                $isApproved = $status === \App\Enum\ReturnStatus::APPROVED;
                $isRejected = $status === \App\Enum\ReturnStatus::REJECTED;
                $isPending = $status === \App\Enum\ReturnStatus::REQUESTED;
            @endphp

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <strong class="text-dark">{{ $item->product->name_en ?? '-' }}</strong>
                        <small class="text-muted ms-1">({{ __('vendor-dashboard.quantity') }}: {{ $item->return_quantity }})</small>
                    </div>

                    <div class="d-flex align-items-center">
                        {{-- STATUS DISPLAY --}}
                        @if(!$isPending)
                            <span class="badge px-3 py-2
                                @if($isApproved) bg-success
                                @elseif($isRejected) bg-danger
                                @else bg-secondary
                                @endif
                            ">
                                <i class="fas
                                    @if($isApproved) fa-check-circle
                                    @elseif($isRejected) fa-times-circle
                                    @else fa-hourglass-half
                                    @endif
                                me-1"></i>
                                    {{ ucfirst(strtolower($status->value)) }}
                            </span>
                        @endif

                        {{-- TOGGLE SWITCH --}}
                        @if($isPending)
                        <form action="{{ route('vendor.return-requests.items.toggle-status', $item->id) }}" method="POST" class="d-inline-flex align-items-center gap-2">
                            @csrf
                            @method('PATCH')

                            <div class="form-check form-switch position-relative">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="statusSwitch{{ $item->id }}"
                                    name="status"
                                    onchange="toggleStatus(this)"
                                    {{ $item->status === 'approved' ? 'checked' : '' }}
                                >
                                <input type="hidden" name="action" value="">
                                <label class="form-check-label fw-semibold ms-2" for="statusSwitch{{ $item->id }}">
                                    {{ $item->status === 'approved' ? 'Approved' : 'Requested' }}
                                </label>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        {{-- LEFT: Reason & Prices --}}
                        <div class="col-md-6">
                            <p class="mb-2"><strong>{{ __('vendor-dashboard.reason') }}:</strong> {{ $item->return_reason }}</p>
                            <p class="mb-2"><strong>{{ __('vendor-dashboard.item_subtotal') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->item_subtotal, 2) }}</p>
                            <p class="mb-2"><strong>{{ __('vendor-dashboard.vendor_refund_commission') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->vendor_refund_commission, 2) }}</p>
                            <p class="mb-2"><strong>{{ __('vendor-dashboard.return_shipping_cost') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->return_shipping_cost, 2) }}</p>
                            <p class="mb-2"><strong>{{ __('vendor-dashboard.shipment_commission') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->shipment_commission, 2) }}</p>
                            <p class="mb-2"><strong>{{ __('vendor-dashboard.customer_refund_amount') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($item->customer_refund_amount, 2) }}</p>
                        </div>

                        {{-- RIGHT: Shipment Company --}}
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
                </div>
            </div>
        @endforeach

        {{-- TOTALS --}}
        <div class="card mb-3 border-0 shadow-sm p-3">
            <h6 class="fw-bold">{{ __('vendor-dashboard.return_totals') }}</h6>
            <p><strong>{{ __('vendor-dashboard.vendor_refund_commission_total') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->vendor_refund_commission_total, 2) }}</p>
            <p><strong>{{ __('vendor-dashboard.vendor_deduction_total') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->vendor_deduction_total, 2) }}</p>
            <p><strong>{{ __('vendor-dashboard.return_shipping_total') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->return_shipping_total, 2) }}</p>
            <p><strong>{{ __('vendor-dashboard.shipment_commission_total') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->shipment_commission_total, 2) }}</p>
            <p><strong>{{ __('vendor-dashboard.shipment_net_total') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->shipment_net_total, 2) }}</p>
            {{-- <p><strong>{{ __('vendor-dashboard.shipping_paid_by') }}:</strong> {{ ucfirst($returnRequest->shipping_paid_by) }}</p> --}}
            <p class="fw-bold"><strong>{{ __('vendor-dashboard.total_refund') }}:</strong> {{ config('settings.currency_symbol', 'EGP') }}{{ number_format($returnRequest->calculateRefundAmount(), 2) }}</p>
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
