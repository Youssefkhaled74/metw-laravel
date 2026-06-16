@extends('layouts.admin')

@section('title', $product->name . ' - Product Details')
@section('page-title', __('admin-dashboard.product_details') . ': ' . $product->name)

@section('breadcrumb')
    @php
        $backSource = request()->query('from');
        $backUrl = match ($backSource) {
            'admin.products' => route('admin.products'),
            'admin.vendors.show' => route('admin.vendors.show', $product->vendor_id),
            default => route('admin.vendors.products', $product->vendor_id),
        };

        $sourceLabel = match ($backSource) {
            'admin.products' => __('admin-dashboard.products'),
            'admin.vendors.show' => __('admin-dashboard.vendors'),
            default => __('admin-dashboard.products'),
        };
    @endphp

    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.vendors') }}">{{ __('admin-dashboard.vendors') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.vendors.show', $product->vendor_id) }}">{{ $product->vendor->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ $backUrl }}">{{ $sourceLabel }}</a></li>
    <li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@section('page-actions')
    {{-- <form action="{{ route('admin.products.toggle-status', $product->id) }}" method="POST" class="d-inline">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-{{ $product->is_active ? 'warning' : 'success' }}"
                onclick="return confirm('Are you sure you want to {{ $product->is_active ? 'deactivate' : 'activate' }} this product?')">
            <i class="fas fa-{{ $product->is_active ? 'pause' : 'play' }}"></i>
            {{ $product->is_active ? 'Deactivate' : 'Activate' }} {{ __('admin-dashboard.product') }}
        </button>
    </form> --}}
    <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('admin-dashboard.back_to_products') }}
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Product Information -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header border-0 product-header-primary">
                    <h5 class="mb-0 text-white fw-semibold d-flex align-items-center">
                        <i class="fas fa-box-open me-2"></i>
                        {{ __('admin-dashboard.product_information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($product->images->isNotEmpty())
                            <div class="product-main-image mx-auto">
                                <img src="{{ asset($product->images->first()->url) }}"
                                     alt="{{ $product->name }}">
                            </div>
                        @else
                            <div class="product-main-image placeholder mx-auto d-flex align-items-center justify-content-center">
                                <i class="fas fa-box fa-3x text-muted"></i>
                            </div>
                        @endif

                        <h4 class="mt-3 mb-1">{{ $product->name }}</h4>
                        <span class="badge rounded-pill px-3 py-2 bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                            <i class="fas fa-circle me-1 small"></i>
                            {{ $product->is_active ? __('admin-dashboard.product_active') : __('admin-dashboard.product_inactive') }}
                        </span>
                    </div>

                    <div class="product-details">
                        <div class="mb-3">
                            <h6 class="text-muted">
                                <i class="fas fa-barcode me-1"></i>{{ __('admin-dashboard.sku') }}
                            </h6>
                            <p class="mb-0 fw-semibold">{{ $product->sku ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">
                                <i class="fas fa-folder-open me-1"></i>{{ __('admin-dashboard.category') }}
                            </h6>
                            <p class="mb-0">
                                @if($product->category)
                                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted">{{ __('admin-dashboard.not_available') }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">
                                <i class="fas fa-tag me-1"></i>{{ __('admin-dashboard.price') }}
                            </h6>
                            <p class="mb-0 fw-semibold text-primary">
                                {{ number_format($product->price, 2) }} {{ config('settings.currency_symbol', '$') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">
                                <i class="fas fa-warehouse me-1"></i>{{ __('admin-dashboard.stock') }}
                            </h6>
                            <p class="mb-0">
                                <span class="badge px-3 py-2 rounded-pill bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                                    {{ $product->stock }} {{ __('admin-dashboard.in_stock') ?? 'in stock' }}
                                </span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">
                                <i class="fas fa-store me-1"></i>{{ __('admin-dashboard.vendor') }}
                            </h6>
                            <p class="mb-0">
                                <a href="{{ route('admin.vendors.show', $product->vendor_id) }}">
                                    {{ $product->vendor->name }}
                                </a>
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">
                                <i class="fas fa-align-left me-1"></i>{{ __('admin-dashboard.description') }}
                            </h6>
                            <p class="mb-0 text-muted">
                                {{ $product->description ?? __('admin-dashboard.not_available') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">
                                <i class="far fa-calendar-alt me-1"></i>{{ __('admin-dashboard.registration_date') }}
                            </h6>
                            <p class="mb-0">
                                {{ $product->created_at->format('F j, Y \a\t g:i A') }}
                            </p>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>{{ $product->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            @if($product->images->count() > 1)
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center">
                            <i class="fas fa-images me-2 text-primary"></i>
                            {{ __('admin-dashboard.product_images') }}
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-2">
                            @foreach($product->images as $image)
                                <div class="col-4">
                                    <div class="product-thumb-grid">
                                        <img src="{{ asset($image->url) }}"
                                             alt="{{ $product->name }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Orders -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-shopping-cart me-2 text-primary"></i>
                        {{ __('admin-dashboard.recent_orders') }}
                    </h5>
                    <span class="badge bg-light text-primary fw-semibold">
                        {{ $orders->count() }} {{ __('admin-dashboard.orders') ?? 'Orders' }}
                    </span>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th class="small text-muted text-uppercase">{{ __('admin-dashboard.order_id') }}</th>
                                        <th class="small text-muted text-uppercase">{{ __('admin-dashboard.customer') }}</th>
                                        <th class="small text-muted text-uppercase text-center">{{ __('admin-dashboard.quantity') }}</th>
                                        <th class="small text-muted text-uppercase text-end">{{ __('admin-dashboard.total') }}</th>
                                        <th class="small text-muted text-uppercase text-center">{{ __('admin-dashboard.status') }}</th>
                                        <th class="small text-muted text-uppercase">{{ __('admin-dashboard.date') }}</th>
                                        <th class="small text-muted text-uppercase text-center">{{ __('admin-dashboard.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="text-muted small">#{{ $order->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2 bg-primary rounded-circle d-flex align-items-center justify-content-center text-white">
                                                        {{ strtoupper(substr($order->user->username, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('admin.users.show', $order->user_id) }}" class="fw-semibold d-block">
                                                            {{ $order->user->username }}
                                                        </a>
                                                        <small class="text-muted">{{ $order->user->email ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $item = $order->items->first();
                                                @endphp
                                                <span class="badge bg-light text-dark px-3 py-2">
                                                    {{ $item ? $item->quantity : 0 }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-semibold text-primary">
                                                    {{ $item ? number_format($item->quantity * $item->unit_price, 2) : '0.00' }}
                                                    {{ config('settings.currency_symbol', 'EGP') }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($order->status) {
                                                        'pending' => 'warning',
                                                        'confirmed' => 'info',
                                                        'shipped' => 'primary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger',
                                                        default => 'secondary',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }} px-3 py-2 rounded-pill">
                                                    <i class="fas fa-circle me-1 small"></i>
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <i class="far fa-calendar-alt me-1"></i>
                                                    {{ $order->created_at->format('M d, Y') }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.ecommerce-orders.show', $order->id) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="{{ __('admin-dashboard.view_order') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('admin-dashboard.no_orders_found') }}</h5>
                            <p class="text-muted small">{{ __('admin-dashboard.product_has_not_been_ordered_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .product-details h6 {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        .product-details p {
            margin-bottom: 0.25rem;
            color: #4a5568;
        }

        .product-header-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .product-main-image {
            width: 200px;
            height: 200px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.25);
            border: 2px solid #e5e7eb;
        }

        .product-main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-main-image.placeholder {
            background: #f3f4f6;
            border-style: dashed;
            color: #9ca3af;
        }

        .product-thumb-grid {
            width: 100%;
            height: 100px;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }

        .product-thumb-grid img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card {
            margin-bottom: 1.5rem;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom-width: 1px;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }
    </style>
    @endpush
@endsection
