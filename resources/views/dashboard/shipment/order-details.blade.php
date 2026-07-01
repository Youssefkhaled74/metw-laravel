@extends('layouts.shipment')

@section('title', __('shipment-dashboard.order_details'))
@section('page-title', __('shipment-dashboard.order_details') . ' - ' . $order->order_number)

@section('page-actions')
<a href="{{ route('shipment.orders') }}" class="btn btn-outline-secondary">
    <i class="fas fa-arrow-left me-1"></i>
    @lang('shipment-dashboard.back_to_orders')
</a>
@endsection

@section('content')
<div class="row g-3">

    {{-- ============== 1. PAGE HEADER ============== --}}
    <div class="col-12">
        <div class="card shadow-sm border-0 detail-card">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-1 fw-bold">{{ $order->order_number }}</h5>
                    <small class="text-muted">
                        @lang('shipment-dashboard.created_at'): {{ $order->created_at->format('M d, Y H:i') }}
                        &middot;
                        @lang('shipment-dashboard.updated_at'): {{ $order->updated_at->format('M d, Y H:i') }}
                    </small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="status-pill {{ in_array($order->status->value ?? $order->status, ['delivered']) ? 'status-active' : (in_array($order->status->value ?? $order->status, ['cancelled','returned']) ? 'status-inactive' : 'packages-pill') }}">
                        {{ __('shipment-dashboard.' . ($order->status->value ?? $order->status)) }}
                    </span>
                    <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'partial' ? 'warning' : 'secondary') }}">
                        {{ __('shipment-dashboard.payment_' . $order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============== 2. OPERATIONAL SUMMARY ============== --}}
    <div class="col-12">
        <div class="card shadow-sm border-0 detail-card">
            <div class="card-header bg-white fw-bold">
                <i class="fas fa-chart-simple me-2 text-primary"></i> @lang('shipment-dashboard.operational_summary')
            </div>
            <div class="card-body">
                <div class="row text-center g-2">
                    <div class="col-3">
                        <div class="fw-bold text-primary fs-5">{{ __('admin-dashboard.EGP') }}{{ number_format($order->total_price,2) }}</div>
                        <small class="text-muted">@lang('shipment-dashboard.total_price')</small>
                    </div>
                    <div class="col-3">
                        <div class="fw-bold text-success fs-5">{{ __('admin-dashboard.EGP') }}{{ number_format($order->paid_amount,2) }}</div>
                        <small class="text-muted">@lang('shipment-dashboard.paid')</small>
                    </div>
                    <div class="col-3">
                        <div class="fw-bold text-danger fs-5">{{ __('admin-dashboard.EGP') }}{{ number_format($order->remaining_amount,2) }}</div>
                        <small class="text-muted">@lang('shipment-dashboard.remaining')</small>
                    </div>
                    <div class="col-3">
                        <div class="fw-bold text-dark fs-5">{{ __('admin-dashboard.EGP') }}{{ number_format($order->final_price,2) }}</div>
                        <small class="text-muted">@lang('shipment-dashboard.final_price')</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============== 3. SENDER & RECEIVER ============== --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 detail-card h-100">
            <div class="card-header bg-white fw-bold">
                <i class="fas fa-user me-2 text-info"></i> @lang('shipment-dashboard.customer_information')
            </div>
            <div class="card-body">
                <p><strong>@lang('shipment-dashboard.name'):</strong> {{ $order->user->username ?? '-' }}</p>
                <p><strong>@lang('shipment-dashboard.email'):</strong> {{ $order->user->email ?? '-' }}</p>
                <p><strong>@lang('shipment-dashboard.phone'):</strong> {{ $order->user->phone ?? '-' }}</p>
            </div>
        </div>
    </div>

    @php
        $firstItem = $order->orderItems->first();
        $details = $firstItem?->package?->packageDetails ?? null;
    @endphp

    <div class="col-md-6">
        <div class="card shadow-sm border-0 detail-card h-100">
            <div class="card-header bg-white fw-bold">
                <i class="fas fa-address-card me-2 text-warning"></i> @lang('shipment-dashboard.sender_receiver')
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h6 class="fw-bold">@lang('shipment-dashboard.sender')</h6>
                        <p class="mb-1">{{ $details->sender_name ?? __('shipment-dashboard.na') }}</p>
                        <small class="text-muted">{{ $details->sender_phone ?? '' }}</small>
                    </div>
                    <div class="col-6">
                        <h6 class="fw-bold">@lang('shipment-dashboard.receiver')</h6>
                        <p class="mb-1">{{ $details->recive_name ?? __('shipment-dashboard.na') }}</p>
                        <small class="text-muted">{{ $details->recive_phone ?? '' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============== 4. PACKAGE INFO (per item) ============== --}}
    <div class="col-12">
        <h6 class="fw-bold mt-2"><i class="fas fa-box me-2 text-primary"></i> @lang('shipment-dashboard.package_items')</h6>
    </div>

    @foreach($order->orderItems as $item)
        @php
            $package = $item->package;
            $pkgDetails = $package?->packageDetails ?? null;
            $pickup = $package?->pickupAddress ?? null;
            $dropoff = $package?->dropoffAddress ?? null;
        @endphp
        <div class="col-12">
            <div class="card shadow-sm border-0 detail-card">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-cube me-2 text-secondary"></i> @lang('shipment-dashboard.item'): {{ $item->item_number }}</span>
                    <span class="badge bg-secondary">{{ __('shipment-dashboard.' . $item->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>@lang('shipment-dashboard.package_number'):</strong> {{ $package->package_number ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.type'):</strong> {{ $package->type->name ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.size'):</strong> {{ $package->size?->name ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.weight'):</strong> {{ $package->weight ?? '-' }} @lang('shipment-dashboard.kg')</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>@lang('shipment-dashboard.delivery_type'):</strong> {{ $package->deliveryType->name ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.consignment_type'):</strong> {{ $package->consignmentType->name ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.estimated_price'):</strong> {{ __('admin-dashboard.EGP') }}{{ number_format($item->est_price,2) }}</p>
                            <p><strong>@lang('shipment-dashboard.estimated_delivery_date'):</strong> {{ $item->est_date ? \Carbon\Carbon::parse($item->est_date)->format('M d, Y') : '-' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold">@lang('shipment-dashboard.pickup_address')</h6>
                            <p class="mb-1">{{ $pickup->address ?? '-' }}</p>
                            <small class="text-muted">{{ $pickup->phone ?? '' }}</small>
                            @if($pickup->latitude && $pickup->longitude)
                                <br><small class="text-muted">📍 {{ $pickup->latitude }}, {{ $pickup->longitude }}</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">@lang('shipment-dashboard.dropoff_address')</h6>
                            <p class="mb-1">{{ $dropoff->address ?? '-' }}</p>
                            <small class="text-muted">{{ $dropoff->phone ?? '' }}</small>
                            @if($dropoff->latitude && $dropoff->longitude)
                                <br><small class="text-muted">📍 {{ $dropoff->latitude }}, {{ $dropoff->longitude }}</small>
                            @endif
                        </div>
                    </div>

                    @if($package->note)
                        <div class="mt-3">
                            <strong>@lang('shipment-dashboard.package_note'):</strong>
                            <div class="alert alert-light mt-1 mb-0">{{ $package->note }}</div>
                        </div>
                    @endif

                    @if($item->trackings->count())
                        <div class="mt-3">
                            <strong>@lang('shipment-dashboard.tracking_information'):</strong>
                            <div class="mt-1">
                                @foreach($item->trackings as $tracking)
                                    <span class="badge bg-info me-1">{{ $tracking->location ?? '' }} {{ $tracking->created_at?->format('M d, H:i') }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    {{-- ============== 5. ESTIMATED DELIVERY SETTINGS ============== --}}
    @foreach($order->orderItems as $item)
        @if($item->shipment_company_id == Auth::guard('shipment')->id())
            <div class="col-12">
                <div class="card shadow-sm border-0 detail-card border-primary">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="fas fa-calendar-check me-2"></i> @lang('shipment-dashboard.estimated_delivery_settings')
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                @if($item->est_date)
                                    <div class="mb-3">
                                        <strong>@lang('shipment-dashboard.current_estimated_delivery'):</strong>
                                        <span class="badge bg-info ms-1">{{ \Carbon\Carbon::parse($item->est_date)->format('M d, Y') }}</span>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <strong>@lang('shipment-dashboard.current_estimated_days'):</strong>
                                    <span class="badge bg-secondary ms-1">{{ $item->package->est_days ?? __('shipment-dashboard.not_set') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('shipment.order-items.update-estimate', $item) }}"
                                    method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex-grow-1">
                                        <label for="est_days_{{ $item->id }}" class="form-label">@lang('shipment-dashboard.set_estimated_days'):</label>
                                        <div class="input-group">
                                            <input type="number" name="est_days" id="est_days_{{ $item->id }}"
                                                class="form-control" value="{{ old('est_days', $item->package->est_days) }}"
                                                min="1" max="30" required placeholder="@lang('shipment-dashboard.enter_days')">
                                            <span class="input-group-text">@lang('shipment-dashboard.days')</span>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-4">
                                        <i class="fas fa-save me-1"></i> @lang('shipment-dashboard.update')
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($item->est_date)
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                @lang('shipment-dashboard.estimated_delivery_note', ['date' => \Carbon\Carbon::parse($item->est_date)->format('M d, Y')])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>

<style>
    .detail-card {
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(15,23,42,0.06) !important;
        transition: box-shadow .2s ease;
    }
    .detail-card:hover {
        box-shadow: 0 6px 20px rgba(15,23,42,0.10) !important;
    }
    .detail-card .card-header {
        border-bottom: 1px solid #eef2f6;
        padding: .85rem 1.1rem;
        font-size: .92rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($order->orderItems as $item)
        @if($item->shipment_company_id == Auth::guard('shipment')->id())
        const estDaysInput{{ $item->id }} = document.getElementById('est_days_{{ $item->id }}');
        if (estDaysInput{{ $item->id }}) {
            estDaysInput{{ $item->id }}.addEventListener('input', function() {
                const days = parseInt(this.value) || 0;
                if (days > 0) {
                    const date = new Date();
                    date.setDate(date.getDate() + days);
                    const formattedDate = date.toLocaleDateString('en-US', {
                        year: 'numeric', month: 'short', day: 'numeric'
                    });
                    let preview = document.getElementById('date_preview_{{ $item->id }}');
                    if (!preview) {
                        preview = document.createElement('small');
                        preview.id = 'date_preview_{{ $item->id }}';
                        preview.className = 'text-muted mt-1';
                        this.closest('.input-group').after(preview);
                    }
                    preview.textContent = 'Estimated delivery: ' + formattedDate;
                }
            });
        }
        @endif
    @endforeach
});
</script>
@endsection
