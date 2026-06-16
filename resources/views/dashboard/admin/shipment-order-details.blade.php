@php
    use App\Enum\OrderStatus;
@endphp

@extends('layouts.admin')

@section('title', __('admin-dashboard.shipment_order_details'))
@section('page-title', __('admin-dashboard.shipment_order_details') . ' - ' . $order->order_number)

@section('page-actions')
    <a href="{{ route('admin.shipment-orders') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>
        {{ __('admin-dashboard.back_to_orders') }}
    </a>
@endsection

@section('content')

<div class="row">

    {{-- ================= ORDER SUMMARY ================= --}}
    <div class="col-lg-8">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">
                    {{ __('admin-dashboard.order_information') }}
                </h5>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">
                        <p><strong>#</strong> {{ $order->order_number }}</p>

                        <p>
                            <strong>{{ __('admin-dashboard.status') }}:</strong>
                            <span class="badge bg-{{
                                $order->status->value === 'pending' ? 'warning' :
                                ($order->status->value === 'confirmed' ? 'info' :
                                ($order->status->value === 'on_way' ? 'primary' :
                                ($order->status->value === 'delivered' ? 'success' :
                                ($order->status->value === 'cancelled' ? 'danger' : 'secondary'))))
                            }}">
                                {{ ucfirst(str_replace('_',' ',$order->status->value)) }}
                            </span>
                        </p>

                        <p>
                            <strong>{{ __('admin-dashboard.total_price') }}:</strong>
                            {{ __('admin-dashboard.EGP') }}
                            {{ number_format($order->final_price,2) }}
                        </p>

                        <p>
                            <strong>{{ __('admin-dashboard.created_at') }}:</strong>
                            {{ $order->created_at->format('M d, Y H:i') }}
                        </p>

                        <p>
                            <strong>{{ __('admin-dashboard.updated_at') }}:</strong>
                            {{ $order->updated_at->format('M d, Y H:i') }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>{{ __('admin-dashboard.customer') }}:</strong>
                            {{ $order->user->username ?? 'N/A' }}
                        </p>

                        <p><strong>{{ __('admin-dashboard.email') }}:</strong>
                            {{ $order->user->email ?? 'N/A' }}
                        </p>

                        <p><strong>{{ __('admin-dashboard.phone') }}:</strong>
                            {{ $order->user->phone ?? 'N/A' }}
                        </p>

                        <p>
                            <strong>{{ __('admin-dashboard.total_packages') }}:</strong>
                            {{ $order->orderItems->count() }}
                        </p>
                    </div>

                </div>
            </div>
        </div>


        {{-- ================= ORDER ITEMS ================= --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">
                    {{ __('admin-dashboard.order_items_packages') }}
                </h5>
            </div>

            <div class="card-body">

                @forelse($order->orderItems as $item)

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">
                                {{ __('admin-dashboard.package') }}
                                #{{ $item->package->package_number ?? '-' }}
                            </h6>

                            <span class="badge bg-{{
                                $item->status === OrderStatus::DELIVERED->value ? 'success' :
                                ($item->status === OrderStatus::PENDING->value ? 'warning' :
                                ($item->status === OrderStatus::CANCELLED->value ? 'danger' :
                                ($item->status === OrderStatus::ON_WAY->value ? 'primary' : 'secondary')))
                            }}">
                                {{ ucfirst($item->status ?? OrderStatus::PENDING->value) }}
                            </span>
                        </div>


                        <div class="row">

                            {{-- LEFT --}}
                            <div class="col-md-8">

                                <div class="mb-3">

                                    <p><strong>{{ __('admin-dashboard.shipment_company') }}:</strong>
                                        {{ $item->shipmentCompany->name ?? __('admin-dashboard.not_assigned') }}
                                    </p>

                                    <p><strong>{{ __('admin-dashboard.type') }}:</strong>
                                        {{ $item->package->type->name ?? '-' }}
                                    </p>

                                    <p><strong>{{ __('admin-dashboard.size') }}:</strong>
                                        {{ $item->package->size ?? '-' }}
                                    </p>

                                    <p><strong>{{ __('admin-dashboard.delivery_type') }}:</strong>
                                        {{ $item->package->deliveryType->name ?? '-' }}
                                    </p>

                                    <p><strong>{{ __('admin-dashboard.estimated_days') }}:</strong>
                                        {{ $item->package->est_days ?? '-' }}
                                    </p>

                                </div>

                                {{-- ADDRESSES --}}
                                <div class="row">

                                    {{-- PICKUP --}}
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 bg-light">

                                            <strong>{{ __('admin-dashboard.pickup_address') }}</strong>
                                            <hr>

                                            @if($item->package->pickupAddress)
                                                {{ $item->package->pickupAddress->address }} <br>

                                                {{ optional($item->package->pickupAddress->city)->name ?? '-' }},
                                                {{ optional($item->package->pickupAddress->state)->name ?? '-' }} <br>

                                                {{ optional($item->package->pickupAddress->zone)->name ?? '' }}

                                                @if($item->route && is_array($item->route->from_address))
                                                    @if(($item->route->from_address['is_village'] ?? 0) == 1)
                                                        <span class="badge bg-warning text-dark">
                                                            {{ __('admin-dashboard.village_area') }}
                                                        </span>
                                                    @endif
                                                @endif

                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>

                                    {{-- DROPOFF --}}
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 bg-light">

                                            <strong>{{ __('admin-dashboard.dropoff_address') }}</strong>
                                            <hr>

                                            @if($item->package->dropoffAddress)
                                                {{ $item->package->dropoffAddress->address }} <br>

                                                {{ optional($item->package->dropoffAddress->city)->name ?? '-' }},
                                                {{ optional($item->package->dropoffAddress->state)->name ?? '-' }} <br>

                                                {{ optional($item->package->dropoffAddress->zone)->name ?? '' }}

                                                @if($item->route && is_array($item->route->to_address))
                                                    @if(($item->route->to_address['is_village'] ?? 0) == 1)
                                                        <span class="badge bg-warning text-dark">
                                                            {{ __('admin-dashboard.village_area') }}
                                                        </span>
                                                    @endif
                                                @endif

                                            @else
                                                N/A
                                            @endif

                                        </div>
                                    </div>

                                </div>
                            </div>


                            {{-- RIGHT --}}
                            <div class="col-md-4">

                                <div class="border rounded p-3 bg-white shadow-sm">

                                    <p><strong>{{ __('admin-dashboard.estimated_price') }}:</strong>
                                        {{ __('admin-dashboard.EGP') }}
                                        {{ number_format($item->est_price,2) }}
                                    </p>

                                    <p><strong>{{ __('admin-dashboard.estimated_date') }}:</strong>
                                        {{ $item->est_date ? \Carbon\Carbon::parse($item->est_date)->format('M d, Y') : '-' }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                @empty
                    <p class="text-muted text-center">
                        {{ __('admin-dashboard.no_order_items_found') }}
                    </p>
                @endforelse

            </div>
        </div>

    </div>


    {{-- ================= SIDE PANEL ================= --}}
    <div class="col-lg-4">

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">
                    {{ __('admin-dashboard.navigation') }}
                </h5>
            </div>

            <div class="card-body">
                <a href="{{ route('admin.shipment-orders') }}"
                   class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left me-1"></i>
                    {{ __('admin-dashboard.back_to_orders') }}
                </a>
            </div>
        </div>

    </div>

</div>

@endsection
