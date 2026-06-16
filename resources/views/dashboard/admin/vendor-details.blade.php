@extends('layouts.admin')

@section('title', __('admin-dashboard.vendor_details') . ' - ' . $vendor->name)
@section('page-title', __('admin-dashboard.vendor_details_title', ['name' => $vendor->name]))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.vendors') }}">{{ __('admin-dashboard.vendors') }}</a></li>
    <li class="breadcrumb-item active">{{ $vendor->name }}</li>
@endsection

@section('page-actions')
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <a href="{{ route('admin.vendors.products', $vendor->id) }}" class="btn btn-primary">
            <i class="fas fa-boxes me-1"></i>
            <span class="d-none d-md-inline">{{ __('admin-dashboard.view_all_products', ['count' => $vendor->products_count]) }}</span>
            <span class="d-md-none">{{ __('admin-dashboard.view_all_products', ['count' => $vendor->products_count]) }}</span>
        </a>
        <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-1"></i>
            <span class="d-none d-md-inline">{{ __('admin-dashboard.edit_vendor') }}</span>
            <span class="d-md-none">{{ __('admin-dashboard.edit') }}</span>
        </a>
        <form action="{{ route('admin.vendors.toggle-status', $vendor->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-{{ $vendor->is_active ? 'warning' : 'success' }}"
                    onclick="return confirm('{{ $vendor->is_active ? __('admin-dashboard.vendor_confirm_deactivate') : __('admin-dashboard.vendor_confirm_activate') }}')">
                <i class="fas fa-{{ $vendor->is_active ? 'pause' : 'play' }} me-1"></i>
                <span class="d-none d-md-inline">{{ $vendor->is_active ? __('admin-dashboard.deactivate_vendor') : __('admin-dashboard.activate_vendor') }}</span>
                <span class="d-md-none">{{ $vendor->is_active ? __('admin-dashboard.deactivate_vendor') : __('admin-dashboard.activate_vendor') }}</span>
            </button>
        </form>
        <a href="{{ route('admin.vendors.export', $vendor->id) }}" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i>
            <span class="d-none d-md-inline">{{ __('admin-dashboard.export_excel') }}</span>
            <span class="d-md-none">{{ __('admin-dashboard.export_excel') }}</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Vendor Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('admin-dashboard.vendor_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($vendor->logo)
                            <img src="{{ asset(  $vendor->logo) }}" alt="{{ $vendor->name }}"
                                 class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 150px; height: 150px; margin: 0 auto;">
                                <i class="fas fa-store fa-3x text-muted"></i>
                            </div>
                        @endif
                        <h4 class="mt-3">{{ $vendor->name }}</h4>
                        <span class="badge bg-{{ $vendor->is_active ? 'success' : 'danger' }}">
                            {{ $vendor->is_active ? __('admin-dashboard.vendor_active') : __('admin-dashboard.vendor_inactive') }}
                        </span>
                    </div>

                    <div class="vendor-details">
                        <div class="mb-3">
                            <h6 class="text-muted">{{ __('admin-dashboard.contact_information') }}</h6>
                            <p class="mb-1">
                                <i class="fas fa-envelope me-2"></i>
                                <a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-phone me-2"></i>
                                <a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a>
                            </p>
                        </div>

                        @if($vendor->address)
                        <div class="mb-3">
                            <h6 class="text-muted">{{ __('admin-dashboard.vendor_address') }}</h6>
                            <p class="mb-0">{{ $vendor->address }}</p>
                            @if($vendor->latitude && $vendor->longitude)
                                <a href="https://www.google.com/maps?q={{ $vendor->latitude }},{{ $vendor->longitude }}"
                                   target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-map-marker-alt"></i> {{ __('admin-dashboard.view_on_map') }}
                                </a>
                            @endif
                        </div>
                        @endif

                        <div class="mb-3">
                            <h6 class="text-muted">{{ __('admin-dashboard.verification_status') }}</h6>
                            <p class="mb-1">
                                <i class="fas {{ $vendor->email_verified ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-2"></i>
                                {{ $vendor->email_verified ? __('admin-dashboard.email_verified') : __('admin-dashboard.email_not_verified') }}
                            </p>
                            <p class="mb-0">
                                <i class="fas {{ $vendor->phone_verified ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} me-2"></i>
                                {{ $vendor->phone_verified ? __('admin-dashboard.phone_verified') : __('admin-dashboard.phone_not_verified') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">{{ __('admin-dashboard.registration_date') }}</h6>
                            <p class="mb-0">{{ $vendor->created_at->format('F j, Y \a\t g:i A') }}</p>
                            <small class="text-muted">({{ $vendor->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Statistics -->
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">{{ __('admin-dashboard.vendor_total_products') }}</h6>
                                    <h2 class="mb-0">{{ $vendor->products_count }}</h2>
                                </div>
                                <div class="icon-circle">
                                    <i class="fas fa-boxes"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.vendors.products', $vendor->id) }}" class="text-white">{{ __('admin-dashboard.view_products') }}</a>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase mb-1">{{ __('admin-dashboard.vendor_total_orders') }}</h6>
                                    <h2 class="mb-0">{{ $vendor->total_orders_count }}</h2>
                                </div>
                                <div class="icon-circle">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.vendors.orders', $vendor->id) }}" class="text-white">
                                {{ __('admin-dashboard.view_orders') }}
                            </a>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.recent_products') }}</h5>
                </div>
                <div class="card-body">
                    @if($recentProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('admin-dashboard.product_image') }}</th>
                                        <th>{{ __('admin-dashboard.product_name') }}</th>
                                        <th>{{ __('admin-dashboard.product_price') }}</th>
                                        <th>{{ __('admin-dashboard.product_stock') }}</th>
                                        <th>{{ __('admin-dashboard.product_status') }}</th>
                                        <th>{{ __('admin-dashboard.product_actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentProducts as $product)
                                        <tr>
                                            <td>
                                                @if($product->images->isNotEmpty())
                                                    <img src="{{ asset(  $product->images->first()->url) }}"
                                                         alt="{{ $product->name }}"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-box text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ number_format($product->price, 2) }} {{__('admin-dashboard.EGP')}}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>
                                                <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                                    {{ $product->is_active ? __('admin-dashboard.vendor_active') : __('admin-dashboard.vendor_inactive') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.show', ['product' => $product->id, 'from' => 'admin.vendors.show']) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="{{ route('admin.vendors.products', $vendor->id) }}" class="btn btn-outline-primary">
                                {{ __('admin-dashboard.view_all_products', ['count' => $vendor->products_count]) }} <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('admin-dashboard.no_products_found') }}</h5>
                            <p class="text-muted">{{ __('admin-dashboard.no_products_message') }}</p>
                            {{-- <a href="{{ route('admin.products.create', ['vendor_id' => $vendor->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('admin-dashboard.add_product') }}
                            </a> --}}
                        </div>
                    @endif
                </div>
            </div>

            @php
                // Commission to display (Vendor first, fallback to Public)
                $commission = $vendorCommission ?? $publicCommission;
            @endphp

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-coins"></i>
                        {{ __('admin-dashboard.vendor_commissions') }}
                    </h5>

                    @if($vendorCommission)
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
                                <strong>{{ __('admin-dashboard.order_commission_percent') }}</strong>
                                <p>{{ $commission->order_commission_percent }}%</p>
                            </div>

                            <div class="col-md-4">
                                <strong>{{ __('admin-dashboard.min_order_commission_egp') }}</strong>
                                <p>{{ $commission->order_commission_min }} {{ __('admin-dashboard.EGP') }}</p>
                            </div>

                            <div class="col-md-4">
                                <strong>{{ __('admin-dashboard.refund_fee_percent') }}</strong>
                                <p>{{ $commission->refund_fee_percent }}%</p>
                            </div>

                            <div class="col-md-4">
                                <strong>{{ __('admin-dashboard.min_refund_fee_egp') }}</strong>
                                <p>{{ $commission->refund_fee_min }} {{ __('admin-dashboard.EGP') }}</p>
                            </div>

                            <div class="col-md-4">
                                <strong>{{ __('admin-dashboard.annual_target_commission') }}</strong>
                                <p>{{ $commission->annual_target_commission }} {{ __('admin-dashboard.EGP') }}</p>
                        </div>

                        {{-- Info message when public commission is used --}}
                        @if(!$vendorCommission)
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ __('admin-dashboard.using_public_commission') }}
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-coins fa-2x mb-2"></i>
                            <p>{{ __('admin-dashboard.no_commission_found') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Action --}}
            <div class="card-footer text-end">
                <button type="button"
                        class="btn btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#commissionModal">
                    <i class="fas fa-edit"></i>
                    {{ $vendorCommission
                        ? __('admin-dashboard.edit_commission')
                        : __('admin-dashboard.create_custom_commission') }}
                </button>
            </div>
            </div>


        </div>
    </div>

    <div class="modal fade" id="commissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="POST"
                  action="{{ $vendorCommission
                        ? route('admin.vendors.commission.update', $vendor->id)
                        : route('admin.vendors.commission.store', $vendor->id) }}">

                @csrf
                @if($vendorCommission)
                    @method('PATCH')
                @endif

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-coins"></i>
                        {{ __('admin-dashboard.vendor_commissions') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                {{ __('admin-dashboard.annual_subscription_egp') }}
                            </label>
                            <input type="number" step="0.01" name="annual_subscription"
                                   class="form-control"
                                   value="{{ old('annual_subscription', $commission->annual_subscription ?? '') }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                {{ __('admin-dashboard.order_commission_percent') }}
                            </label>
                            <input type="number" step="0.01" name="order_commission_percent"
                                   class="form-control"
                                   value="{{ old('order_commission_percent', $commission->order_commission_percent ?? '') }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                {{ __('admin-dashboard.min_order_commission_egp') }}
                            </label>
                            <input type="number" step="0.01" name="order_commission_min"
                                   class="form-control"
                                   value="{{ old('order_commission_min', $commission->order_commission_min ?? '') }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                {{ __('admin-dashboard.refund_fee_percent') }}
                            </label>
                            <input type="number" step="0.01" name="refund_fee_percent"
                                   class="form-control"
                                   value="{{ old('refund_fee_percent', $commission->refund_fee_percent ?? '') }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                {{ __('admin-dashboard.min_refund_fee_egp') }}
                            </label>
                            <input type="number" step="0.01" name="refund_fee_min"
                                   class="form-control"
                                   value="{{ old('refund_fee_min', $commission->refund_fee_min ?? '') }}"
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                {{ __('admin-dashboard.annual_target_commission') }}
                            </label>
                            <input type="number" step="0.01" name="annual_target_commission"
                                   class="form-control"
                                   value="{{ old('annual_target_commission', $commission->annual_target_commission ?? '') }}"
                                   required>
                        </div>


                    </div>

                    {{-- Info when public commission --}}
                    @if(!$vendorCommission)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ __('admin-dashboard.this_will_create_custom_commission') }}
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
        .vendor-details h6 {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #718096;
        }
        .vendor-details p {
            margin-bottom: 0.25rem;
            color: #4a5568;
            line-height: 1.6;
        }
        .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }
        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.25rem;
        }
        .btn-outline-primary {
            border-width: 1px;
        }
        .table-hover tbody tr {
            transition: background-color 0.15s ease;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .page-actions-group .btn {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
            .card {
                margin-bottom: 1rem;
            }
        }
    </style>
    @endpush
@endsection
