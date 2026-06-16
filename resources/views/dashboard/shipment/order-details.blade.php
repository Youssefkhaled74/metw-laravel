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
<div class="row">

    {{-- ================= ORDER MAIN INFO ================= --}}
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light fw-bold">
                @lang('shipment-dashboard.order_information')
            </div>

            <div class="card-body">
                <div class="row">

                    {{-- LEFT SIDE --}}
                    <div class="col-md-6">
                        <p><strong>@lang('shipment-dashboard.order_number'):</strong> {{ $order->order_number }}</p>

                        <p>
                            <strong>@lang('shipment-dashboard.status'):</strong>
                            <span class="badge bg-{{
                                $order->status === 'delivered' ? 'success' :
                                ($order->status === 'pending' ? 'warning' :
                                ($order->status === 'cancelled' ? 'danger' : 'info'))
                            }}">
                                {{ __('shipment-dashboard.' . $order->status->value) }}
                            </span>
                        </p>

                        <p><strong>@lang('shipment-dashboard.payment_status'):</strong>
                            <span class="badge bg-{{
                                $order->payment_status === 'paid' ? 'success' :
                                ($order->payment_status === 'partial' ? 'warning' : 'secondary')
                            }}">
                                {{ __('shipment-dashboard.payment_' . $order->payment_status) }}
                            </span>
                        </p>

                        <p><strong>@lang('shipment-dashboard.created_at'):</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                        <p><strong>@lang('shipment-dashboard.updated_at'):</strong> {{ $order->updated_at->format('M d, Y H:i') }}</p>
                    </div>

                    {{-- RIGHT SIDE --}}
                    <div class="col-md-6">
                        <p><strong>@lang('shipment-dashboard.customer'):</strong> {{ $order->user->username ?? '-' }}</p>
                        <p><strong>@lang('shipment-dashboard.email'):</strong> {{ $order->user->email ?? '-' }}</p>
                        <p><strong>@lang('shipment-dashboard.phone'):</strong> {{ $order->user->phone ?? '-' }}</p>
                    </div>
                </div>

                <hr>

                {{-- FINANCIAL SUMMARY --}}
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="fw-bold text-primary">
                            {{ __('admin-dashboard.EGP') }} {{ number_format($order->total_price,2) }}
                        </div>
                        <small>@lang('shipment-dashboard.total_price')</small>
                    </div>

                    <div class="col-md-3">
                        <div class="fw-bold text-success">
                            {{ __('admin-dashboard.EGP') }} {{ number_format($order->paid_amount,2) }}
                        </div>
                        <small>@lang('shipment-dashboard.paid')</small>
                    </div>

                    <div class="col-md-3">
                        <div class="fw-bold text-danger">
                            {{ __('admin-dashboard.EGP') }} {{ number_format($order->remaining_amount,2) }}
                        </div>
                        <small>@lang('shipment-dashboard.remaining')</small>
                    </div>

                    <div class="col-md-3">
                        <div class="fw-bold text-dark">
                            {{ __('admin-dashboard.EGP') }} {{ number_format($order->final_price,2) }}
                        </div>
                        <small>@lang('shipment-dashboard.final_price')</small>
                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- ================= ORDER ITEMS ================= --}}
    <div class="col-12">

        @foreach($order->orderItems as $item)

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light fw-bold d-flex justify-content-between">
                <span>@lang('shipment-dashboard.item'): {{ $item->item_number }}</span>
                <span class="badge bg-secondary">{{ __('shipment-dashboard.' . $item->status) }}</span>
            </div>

            <div class="card-body">

                @php
                    $package = $item->package;
                    $details = $package->packageDetails ?? null;
                    $pickup = $package->pickupAddress ?? null;
                    $dropoff = $package->dropoffAddress ?? null;
                @endphp

                {{-- ================= PACKAGE BASIC INFO ================= --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>@lang('shipment-dashboard.package_number'):</strong> {{ $package->package_number }}</p>
                        <p><strong>@lang('shipment-dashboard.weight'):</strong> {{ $package->weight }} @lang('shipment-dashboard.kg')</p>
                        <p><strong>@lang('shipment-dashboard.pieces'):</strong> {{ $package->piece }}</p>
                        <p><strong>@lang('shipment-dashboard.estimated_days'):</strong> {{ $package->est_days ?? '-' }}</p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>@lang('shipment-dashboard.estimated_delivery_date'):</strong>
                            {{ $item->est_date ? \Carbon\Carbon::parse($item->est_date)->format('M d, Y') : '-' }}
                        </p>
                        <p><strong>@lang('shipment-dashboard.estimated_price'):</strong>
                            {{ __('admin-dashboard.EGP') }} {{ number_format($item->est_price,2) }}
                        </p>
                        <p><strong>@lang('shipment-dashboard.delivery_type'):</strong> {{ $package->deliveryType->name ?? '-' }}</p>
                        <p><strong>@lang('shipment-dashboard.consignment_type'):</strong> {{ $package->consignmentType->name ?? '-' }}</p>
                    </div>
                </div>


                {{-- ================= CATEGORY INFO ================= --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>@lang('shipment-dashboard.main_category'):</strong>
                            {{ $package->category->name ?? '-' }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>@lang('shipment-dashboard.sub_category'):</strong>
                            {{ $package->subCategory->name ?? '-' }}
                        </p>
                    </div>
                </div>


                {{-- ================= SENDER & RECEIVER ================= --}}
                <div class="row mb-4">

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold">@lang('shipment-dashboard.sender_information')</h6>
                            <p><strong>@lang('shipment-dashboard.name'):</strong> {{ $details->sender_name ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.phone'):</strong> {{ $details->sender_phone ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.pickup_date'):</strong> {{ $details->pickup_date ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.pickup_time'):</strong> {{ $details->pickup_time ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold">@lang('shipment-dashboard.receiver_information')</h6>
                            <p><strong>@lang('shipment-dashboard.name'):</strong> {{ $details->recive_name ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.phone'):</strong> {{ $details->recive_phone ?? '-' }}</p>
                        </div>
                    </div>

                </div>


                {{-- ================= ADDRESSES ================= --}}
                <div class="row">

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold">@lang('shipment-dashboard.pickup_address')</h6>
                            <p>{{ $pickup->address ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.phone'):</strong> {{ $pickup->phone ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.coordinates'):</strong>
                                {{ $pickup->latitude ?? '-' }},
                                {{ $pickup->longitude ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-bold">@lang('shipment-dashboard.dropoff_address')</h6>
                            <p>{{ $dropoff->address ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.phone'):</strong> {{ $dropoff->phone ?? '-' }}</p>
                            <p><strong>@lang('shipment-dashboard.coordinates'):</strong>
                                {{ $dropoff->latitude ?? '-' }},
                                {{ $dropoff->longitude ?? '-' }}
                            </p>
                        </div>
                    </div>

                </div>

                @if($package->note)
                <div class="mt-4">
                    <strong>@lang('shipment-dashboard.package_note'):</strong>
                    <div class="alert alert-light mt-2">
                        {{ $package->note }}
                    </div>
                </div>
                @endif

            </div>
        </div>

        @endforeach

    </div>


        {{-- ================= ESTIMATED DAYS SECTION ================= --}}
        @if($item->shipment_company_id == Auth::guard('shipment')->id())
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">@lang('shipment-dashboard.estimated_delivery_settings')</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                @if($item->est_date)
                                    <div class="mb-3">
                                        <strong>@lang('shipment-dashboard.current_estimated_delivery'):</strong>
                                        <span class="badge bg-info">
                                            {{ \Carbon\Carbon::parse($item->est_date)->format('M d, Y') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <strong>@lang('shipment-dashboard.current_estimated_days'):</strong>
                                    <span class="badge bg-secondary">
                                        {{ $item->package->est_days ?? __('shipment-dashboard.not_set') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <form action="{{ route('shipment.order-items.update-estimate', $item) }}"
                                    method="POST"
                                    class="d-flex align-items-center gap-2">
                                    @csrf
                                    @method('PATCH')

                                    <div class="flex-grow-1">
                                        <label for="est_days_{{ $item->id }}" class="form-label">
                                            @lang('shipment-dashboard.set_estimated_days'):
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                name="est_days"
                                                id="est_days_{{ $item->id }}"
                                                class="form-control"
                                                value="{{ old('est_days', $item->package->est_days) }}"
                                                min="1"
                                                max="30"
                                                required
                                                placeholder="@lang('shipment-dashboard.enter_days')">
                                            <span class="input-group-text">@lang('shipment-dashboard.days')</span>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-4">
                                        <i class="fas fa-save me-1"></i>
                                        @lang('shipment-dashboard.update')
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($item->est_date)
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            @lang('shipment-dashboard.estimated_delivery_note', [
                                'date' => \Carbon\Carbon::parse($item->est_date)->format('M d, Y')
                            ])
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

</div>

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
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });

                    // Create or update preview element
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
