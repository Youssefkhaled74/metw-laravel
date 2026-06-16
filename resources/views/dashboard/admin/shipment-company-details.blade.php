@extends('layouts.admin')

@section('title', __('admin-dashboard.shipment_company_details_title', ['name' => $company->name]))
@section('page-title', __('admin-dashboard.shipment_company_details_heading', ['name' => $company->name]))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.shipment-companies') }}">{{ __('admin-dashboard.shipment_companies') }}</a></li>
    <li class="breadcrumb-item active">{{ $company->name }}</li>
@endsection

@section('page-actions')
    <form action="{{ route('admin.shipment-companies.toggle-status', $company->id) }}" method="POST" class="d-inline">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-{{ $company->is_active ? 'warning' : 'success' }}"
                onclick="return confirm(__('admin-dashboard.toggle_status_confirm', ['action' => $company->is_active ? __('admin-dashboard.deactivate') : __('admin-dashboard.activate')]))">
            <i class="fas fa-{{ $company->is_active ? 'pause' : 'play' }}"></i>
            {{ __($company->is_active ? 'admin-dashboard.deactivate_company' : 'admin-dashboard.activate_company') }}
        </button>
    </form>
        <a href="{{ route('admin.shipment-companies.export', $company->id) }}"
        class="btn btn-success">
            <i class="fas fa-file-excel"></i>
            {{ __('admin-dashboard.export_excel') }}
        </a>

@endsection

@section('content')
    <div class="row">
        <!-- Company Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('admin-dashboard.company_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($company->logo)
                            <img src="{{ asset(  $company->logo) }}" alt="{{ $company->name }}"
                                 class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 150px; height: 150px; margin: 0 auto;">
                                <i class="fas fa-truck fa-3x text-muted"></i>
                            </div>
                        @endif
                        <h4 class="mt-3">{{ $company->name }}</h4>
                        <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                            {{ __($company->is_active ? 'admin-dashboard.company_active' : 'admin-dashboard.company_inactive') }}
                        </span>
                    </div>

                    <div class="company-details">
                        <div class="mb-3">
                            <h6 class="text-muted">{{ __('admin-dashboard.company_contact_info') }}</h6>
                            <p class="mb-1">
                                <i class="fas fa-envelope me-2"></i>
                                <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-phone me-2"></i>
                                <a href="tel:{{ $company->phone }}">{{ $company->phone }}</a>
                            </p>
                        </div>

                        @if($company->address)
                        <div class="mb-3">
                            <h6 class="text-muted">{{ __('admin-dashboard.address_label') }}</h6>
                            <p class="mb-0">{{ $company->address }}</p>
                        </div>
                        @endif

                        <div class="mb-3">
                            <h6 class="text-muted">{{ __('admin-dashboard.company_social_media') }}</h6>
                            @if($company->facebook)
                                <p class="mb-1">
                                    <i class="fab fa-facebook me-2"></i>
                                    <a href="{{ $company->facebook }}" target="_blank">{{ __('admin-dashboard.company_facebook_page') }}</a>
                                </p>
                            @endif
                            @if($company->whatsapp)
                                <p class="mb-0">
                                    <i class="fab fa-whatsapp me-2"></i>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $company->whatsapp) }}" target="_blank">
                                        {{ $company->whatsapp }}
                                    </a>
                                </p>
                            @endif
                        </div>

                        @if($company->description)
                        <div class="mb-3">
                            <h6 class="text-muted">{{ __('admin-dashboard.description_label') }}</h6>
                            <p class="mb-0">{{ $company->description }}</p>
                        </div>
                        @endif

                        <div class="mb-3">
                            <h6 class="text-muted">{{ __('admin-dashboard.company_registration_date') }}</h6>
                            <p class="mb-0">{{ $company->created_at->format('F j, Y \a\t g:i A') }}</p>
                            <small class="text-muted">({{ $company->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics and Recent Orders -->
        <div class="col-md-8">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">{{ __('admin-dashboard.company_total_orders') }}</h6>
                                    <h2 class="mb-0">{{ $company->orders_count }}</h2>
                                </div>
                                <div class="icon-circle">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">{{ __('admin-dashboard.company_total_packages') }}</h6>
                                    <h2 class="mb-0">{{ $company->packages_count }}</h2>
                                </div>
                                <div class="icon-circle">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.company_recent_orders') }}</h5>
                </div>
                <div class="card-body">
                    @if($company_orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('admin-dashboard.order_id') }}</th>
                                        <th>{{ __('admin-dashboard.customer') }}</th>
                                        <th>{{ __('admin-dashboard.total_items') }}</th>
                                        <th>{{ __('admin-dashboard.order_status') }}</th>
                                        <th>{{ __('admin-dashboard.order_date') }}</th>
                                        <th>{{ __('admin-dashboard.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($company_orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $order->user_id) }}">
                                                    {{ $order->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $order->orderItems->count() }}</td>
                                            <td>
                                                <span class="badge bg-{{
                                                    $order->status === 'pending' ? 'warning' :
                                                    ($order->status === 'confirmed' ? 'info' :
                                                    ($order->status === 'in_transit' ? 'primary' :
                                                    ($order->status === 'delivered' ? 'success' : 'danger')))
                                                }}">
                                                    {{ __('admin-dashboard.' . $order->status->name) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.shipment-orders.show', $order->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> {{ __('admin-dashboard.view_order') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('admin-dashboard.no_orders_found') }}</h5>
                            <p class="text-muted">{{ __('admin-dashboard.no_orders_message') }}</p>
                        </div>
                    @endif
                </div>
            </div>


            @php
                // Shipment commission to display
                $commission = $shipmentCommission ?? $publicCommission;
            @endphp

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-coins"></i>
                        {{ __('admin-dashboard.shipment_commissions') }}
                    </h5>

                    @if($shipmentCommission)
                        <span class="badge bg-success">
                            {{ __('admin-dashboard.custom_commission') }}
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            {{ __('admin-dashboard.public_commission') }}
                        </span>
                    @endif
                </div>

                <div class="card-body">
                    @if($commission)
                        <div class="row g-3">
                            <div class="col-md-4">
                                <strong>{{ __('admin-dashboard.annual_subscription_egp') }}</strong>
                                <p>{{ $commission->annual_subscription }} {{ __('admin-dashboard.EGP') }}</p>
                            </div>

                            <div class="col-md-4">
                                <strong>{{ __('admin-dashboard.shipment_commission_percent') }}</strong>
                                <p>{{ $commission->shipment_commission_percent }}%</p>
                            </div>

                            <div class="col-md-4">
                                <strong>{{ __('admin-dashboard.min_shipment_commission_egp') }}</strong>
                                <p>{{ $commission->shipment_commission_min }} {{ __('admin-dashboard.EGP') }}</p>
                            </div>

                            <div class="col-md-4">
                                <strong>{{ __('admin-dashboard.annual_target') }}</strong>
                                <p>{{ $commission->annual_target }} {{ __('admin-dashboard.EGP') }}</p>
                            </div>
                        </div>

                        @if(!$shipmentCommission)
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ __('admin-dashboard.using_shipment_public_commission') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-coins fa-2x mb-2"></i>
                            <p>{{ __('admin-dashboard.no_shipment_commission_found') }}</p>
                        </div>
                    @endif
                </div>

                <div class="card-footer text-end">
                    <button type="button"
                            class="btn btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#shipmentCommissionModal">
                        <i class="fas fa-edit"></i>
                        {{ $shipmentCommission
                            ? __('admin-dashboard.edit_commission')
                            : __('admin-dashboard.create_custom_commission') }}
                    </button>
                </div>
            </div>

            <div class="modal fade" id="shipmentCommissionModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <form method="POST"
                            action="{{ $shipmentCommission
                                    ? route('admin.shipments.commission.update', $company->id)
                                    : route('admin.shipments.commission.store', $company->id) }}">

                            @csrf
                            @if($shipmentCommission)
                                @method('PATCH')
                            @endif

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-coins"></i>
                                    {{ __('admin-dashboard.shipment_commissions') }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('admin-dashboard.annual_subscription_egp') }}</label>
                                        <input type="number" step="0.01" name="annual_subscription"
                                            class="form-control"
                                            value="{{ old('annual_subscription', $commission->annual_subscription ?? '') }}"
                                            required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('admin-dashboard.shipment_commission_percent') }}</label>
                                        <input type="number" step="0.01" name="shipment_commission_percent"
                                            class="form-control"
                                            value="{{ old('shipment_commission_percent', $commission->shipment_commission_percent ?? '') }}"
                                            required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('admin-dashboard.min_shipment_commission_egp') }}</label>
                                        <input type="number" step="0.01" name="shipment_commission_min"
                                            class="form-control"
                                            value="{{ old('shipment_commission_min', $commission->shipment_commission_min ?? '') }}"
                                            required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('admin-dashboard.annual_target') }}</label>
                                        <input type="number" step="0.01" name="annual_target"
                                            class="form-control"
                                            value="{{ old('annual_target', $commission->annual_target ?? '') }}"
                                            required>
                                    </div>

                                </div>

                                @if(!$shipmentCommission)
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ __('admin-dashboard.this_shipment_will_create_custom_commission') }}
                                    </div>
                                @endif
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                    {{ __('admin-dashboard.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    {{ __('admin-dashboard.save') }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>



        </div>
    </div>

    <!-- Add some custom styles -->
    @push('styles')
    <style>
        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .company-details h6 {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        .company-details p {
            margin-bottom: 0.25rem;
            color: #4a5568;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
            margin-bottom: 1.5rem;
        }
        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
    </style>
    @endpush
@endsection
