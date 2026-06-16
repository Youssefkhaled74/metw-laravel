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
            'admin.vendors.show' => __('admin-dashboard.vendor_details') ?? __('admin-dashboard.vendors'),
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
    <div class="card shadow-sm border-0 rounded-3 mb-4 product-hero-card">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3 gap-sm-4">
                    <div class="product-hero-image">
                        @if($product->images->isNotEmpty())
                            <img src="{{ asset($product->images->first()->url) }}" alt="{{ $product->name }}">
                        @else
                            <div class="product-hero-placeholder">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        @endif
                    </div>

                    <div class="text-center text-sm-start">
                        <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-2 mb-2">
                            <span class="badge rounded-pill px-3 py-2 bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                <i class="fas fa-circle me-1 small"></i>
                                {{ $product->is_active ? __('admin-dashboard.product_active') : __('admin-dashboard.product_inactive') }}
                            </span>
                            <span class="badge rounded-pill px-3 py-2 bg-info-subtle text-info">
                                #{{ $product->id }}
                            </span>
                        </div>
                        <h3 class="mb-2 fw-bold product-title">{{ $product->name }}</h3>
                        <div class="text-muted mb-2">
                            <i class="fas fa-store me-1"></i>
                            <a href="{{ route('admin.vendors.show', $product->vendor_id) }}" class="text-decoration-none">
                                {{ $product->vendor->name }}
                            </a>
                        </div>
                        <div class="text-muted small">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ $product->created_at->format('M d, Y') }}
                            <span class="mx-2">•</span>
                            {{ $product->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>

                <div class="product-quick-metrics">
                    <div class="metric-chip">
                        <span>{{ __('admin-dashboard.price') }}</span>
                        <strong>{{ number_format($product->price, 2) }} {{ config('settings.currency_symbol', '$') }}</strong>
                    </div>
                    <div class="metric-chip">
                        <span>{{ __('admin-dashboard.stock') }}</span>
                        <strong>{{ $product->stock }}</strong>
                    </div>
                    <div class="metric-chip">
                        <span>{{ __('admin-dashboard.sku') }}</span>
                        <strong>{{ $product->sku ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $currentTranslation = $product->translations->firstWhere('locale', app()->getLocale())
            ?? $product->translations->firstWhere('locale', 'en')
            ?? $product->translations->firstWhere('locale', 'ar');

        $featuresList = collect($product->features ?? [])->filter()->values();
        $productInfoList = collect($product->product_info ?? [])->filter()->values();
        $storageConditionsList = collect($product->storage_conditions ?? [])->filter()->values();
        $deliveryZonesList = collect($product->delivery_zones ?? [])->filter()->values();
        $deliveryOptionsList = collect($product->delivery_options ?? [])->filter()->values();
        $availableSizesList = collect($product->available_sizes ?? [])->filter()->values();
        $availableColorsList = collect($product->available_colors ?? [])->filter()->values();
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 h-100 summary-card">
                <div class="card-body p-4">
                    <div class="summary-item">
                        <span>{{ __('admin-dashboard.product') }}</span>
                        <strong>{{ $product->name }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>{{ __('admin-dashboard.vendor') }}</span>
                        <strong>{{ $product->vendor->name ?? '-' }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>{{ __('admin-dashboard.product_brand') }}</span>
                        <strong>{{ $product->brand?->{'name_' . app()->getLocale()} ?? $product->brand?->name ?? '-' }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>{{ __('admin-dashboard.category') }}</span>
                        <strong>{{ $product->category->name ?? '-' }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>{{ __('admin-dashboard.price') }}</span>
                        <strong>{{ number_format($product->price, 2) }} {{ config('settings.currency_symbol', '$') }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>{{ __('admin-dashboard.stock') }}</span>
                        <strong>{{ $product->stock }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>{{ __('admin-dashboard.views') ?? 'Views' }}</span>
                        <strong>{{ $product->view_count ?? 0 }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>{{ __('admin-dashboard.sales') ?? 'Sales' }}</span>
                        <strong>{{ $product->sold_count ?? 0 }}</strong>
                    </div>
                    <div class="summary-item">
                        <span>{{ __('admin-dashboard.rating') ?? 'Rating' }}</span>
                        <strong>{{ number_format((float) ($product->rating ?? 0), 1) }} / 5</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3 h-100 summary-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-semibold"><i class="fas fa-language me-2 text-primary"></i>{{ __('admin-dashboard.translations') ?? 'Translations' }}</h5>
                        <span class="badge bg-light text-primary">{{ $currentTranslation->locale ?? app()->getLocale() }}</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-box h-100">
                                <div class="detail-box-label">{{ __('admin-dashboard.short_description') ?? 'Short Description' }}</div>
                                <div class="detail-box-value">{{ $product->short_description ?: __('admin-dashboard.not_available') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-box h-100">
                                <div class="detail-box-label">{{ __('admin-dashboard.description') }}</div>
                                <div class="detail-box-value">{{ $product->description ?: __('admin-dashboard.not_available') }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="detail-box">
                                <div class="detail-box-label">{{ __('vendor-dashboard.usage_description') ?? 'Usage Description' }}</div>
                                <div class="detail-box-value">{{ $product->usage_description ?: __('admin-dashboard.not_available') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100 details-section-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-layer-group me-2 text-primary"></i>{{ __('admin-dashboard.classification') ?? 'Classification' }}</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.main_category') ?? 'Main Category' }}</span><strong>{{ $product->maincategory->name ?? $product->main_category_id ?? '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('admin-dashboard.category') }}</span><strong>{{ $product->category->name ?? '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.second_main_category_optional') ?? 'Second Main Category' }}</span><strong>{{ $product->main_category_id_2 ?? '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.second_category_optional') ?? 'Second Category' }}</span><strong>{{ $product->category_id_2 ?? '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('admin-dashboard.product_brand') }}</span><strong>{{ $product->brand?->{'name_' . app()->getLocale()} ?? $product->brand?->name ?? '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.branch') ?? 'Branch' }}</span><strong>{{ $product->branch->name ?? '-' }}</strong></div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100 details-section-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-ruler-combined me-2 text-primary"></i>{{ __('vendor-dashboard.product_specifications') ?? 'Specifications' }}</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.dimensions') ?? 'Dimensions' }}</span><strong>{{ $product->dimensions ?: ($product->package_length || $product->package_width || $product->package_height ? trim(($product->package_length ?? '-') . ' x ' . ($product->package_width ?? '-') . ' x ' . ($product->package_height ?? '-')) : '-') }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.weight') ?? 'Weight' }}</span><strong>{{ $product->weight ?? $product->package_weight ?? '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.volume') ?? 'Volume' }}</span><strong>{{ $product->volume ?: '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.country_of_origin') ?? 'Origin Country' }}</span><strong>{{ $product->origin_country ?: '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.manufacturer') ?? 'Manufacturer' }}</span><strong>{{ $product->manufacturer ?: '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.model') ?? 'Model' }}</span><strong>{{ $product->model ?: '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.expiry_period') ?? 'Expiry Period' }}</span><strong>{{ $product->expiry_period ?: '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.piece_type') ?? 'Piece Type' }}</span><strong>{{ $product->piece_type ?: '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.pieces_per_package') ?? 'Pieces / Package' }}</span><strong>{{ $product->pieces_per_package ?? '-' }}</strong></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100 details-section-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-percent me-2 text-primary"></i>{{ __('vendor-dashboard.discount') }}</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.discount_percentage') }}</span><strong>{{ $product->discount_percentage !== null ? $product->discount_percentage . '%' : '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.discounted_price') ?? 'Discounted Price' }}</span><strong>{{ $product->discounted_price !== null ? number_format($product->discounted_price, 2) : '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.discount_start') }}</span><strong>{{ $product->discount_start ? $product->discount_start->format('M d, Y') : '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.discount_end') }}</span><strong>{{ $product->discount_end ? $product->discount_end->format('M d, Y') : '-' }}</strong></div></div>
                        <div class="col-12"><div class="mini-info"><span>{{ __('vendor-dashboard.auto_discount_end_date') ?? 'Auto Discount End Date' }}</span><strong>{{ $product->auto_discount_end_date ? $product->auto_discount_end_date->format('M d, Y') : '-' }}</strong></div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100 details-section-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-truck me-2 text-primary"></i>{{ __('vendor-dashboard.shipping') }}</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.free_delivery_eligible') }}</span><strong>{{ $product->free_shipping ? ($product->free_shipping === 'price' ? __('vendor-dashboard.based_on_specific_price') : __('vendor-dashboard.yes')) : __('vendor-dashboard.no') }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.free_shipping_price') }}</span><strong>{{ $product->free_shipping_price !== null ? number_format($product->free_shipping_price, 2) : '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.shipment_type') }}</span><strong>{{ $product->shipment_type ?: '-' }}</strong></div></div>
                        <div class="col-6"><div class="mini-info"><span>{{ __('vendor-dashboard.shipment_weight_kg') }}</span><strong>{{ $product->shipment_weight ?? $product->package_weight ?? '-' }}</strong></div></div>
                        <div class="col-12"><div class="mini-info"><span>{{ __('vendor-dashboard.shipment_description') }}</span><strong>{{ $product->shipment_description ?: '-' }}</strong></div></div>
                        <div class="col-12"><div class="mini-info"><span>{{ __('vendor-dashboard.storage_shipping_conditions') }}</span><strong>{{ $storageConditionsList->isNotEmpty() ? $storageConditionsList->join(', ') : '-' }}</strong></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100 details-section-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-tags me-2 text-primary"></i>{{ __('vendor-dashboard.features') ?? 'Features' }}</h6>
                </div>
                <div class="card-body p-4">
                    @if($featuresList->isNotEmpty())
                        <ul class="detail-list">
                            @foreach($featuresList as $feature)
                                <li>{{ is_array($feature) ? implode(' - ', array_filter($feature)) : $feature }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">{{ __('admin-dashboard.not_available') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100 details-section-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-boxes-stacked me-2 text-primary"></i>{{ __('vendor-dashboard.product_info') ?? 'Product Info' }}</h6>
                </div>
                <div class="card-body p-4">
                    @if($productInfoList->isNotEmpty())
                        <ul class="detail-list">
                            @foreach($productInfoList as $info)
                                <li>{{ is_array($info) ? implode(' - ', array_filter($info)) : $info }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">{{ __('admin-dashboard.not_available') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100 details-section-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-cubes me-2 text-primary"></i>{{ __('vendor-dashboard.available_sizes') ?? 'Available Sizes' }}</h6>
                </div>
                <div class="card-body p-4">
                    @if($availableSizesList->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($availableSizesList as $size)
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill product-pill">{{ is_array($size) ? implode(' - ', array_filter($size)) : $size }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('admin-dashboard.not_available') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100 details-section-card">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="fas fa-palette me-2 text-primary"></i>{{ __('vendor-dashboard.available_colors') ?? 'Available Colors' }}</h6>
                </div>
                <div class="card-body p-4">
                    @if($availableColorsList->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($availableColorsList as $color)
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill product-pill">{{ is_array($color) ? implode(' - ', array_filter($color)) : $color }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">{{ __('admin-dashboard.not_available') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Product Information -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 h-100 product-info-card">
                <div class="card-header border-0 product-header-primary">
                    <h5 class="mb-0 text-white fw-semibold d-flex align-items-center">
                        <i class="fas fa-box-open me-2"></i>
                        {{ __('admin-dashboard.product_information') }}
                    </h5>
                </div>
                <div class="card-body p-4">
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

                        <h4 class="mt-3 mb-1 product-card-title">{{ $product->name }}</h4>
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
                                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill product-pill">
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
                                <span class="badge px-3 py-2 rounded-pill bg-{{ $product->stock > 0 ? 'success' : 'danger' }} product-pill">
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
            <div class="card shadow-sm border-0 rounded-3 h-100 product-orders-card">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-semibold d-flex align-items-center">
                        <i class="fas fa-shopping-cart me-2 text-primary"></i>
                        {{ __('admin-dashboard.recent_orders') }}
                    </h5>
                    <span class="badge bg-light text-primary fw-semibold">
                        {{ $orders->count() }} {{ __('admin-dashboard.orders') ?? 'Orders' }}
                    </span>
                </div>
                <div class="card-body p-4">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle orders-table">
                                <thead class="table-light">
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
                                                <span class="badge bg-light text-dark px-3 py-2 product-pill">
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
                                                <span class="badge bg-{{ $statusClass }} px-3 py-2 rounded-pill product-pill">
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
        .product-hero-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid #e5e7eb;
        }

        .product-hero-image {
            width: 88px;
            height: 88px;
            border-radius: 20px;
            overflow: hidden;
            background: #f3f4f6;
            border: 2px solid #e5e7eb;
            flex: 0 0 auto;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        }

        .product-hero-image img,
        .product-hero-placeholder {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-hero-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            background: #f8fafc;
        }

        .product-title {
            color: #0f172a;
            line-height: 1.15;
        }

        .product-quick-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
            min-width: min(100%, 360px);
        }

        .metric-chip {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 0.85rem 1rem;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.04);
        }

        .metric-chip span {
            display: block;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .metric-chip strong {
            display: block;
            color: #0f172a;
            font-size: 0.96rem;
            font-weight: 700;
            word-break: break-word;
        }

        .product-info-card,
        .product-orders-card {
            overflow: hidden;
        }

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

        .product-card-title {
            font-size: 1.15rem;
        }

        .product-pill {
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.06);
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

        .orders-table thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .orders-table td,
        .orders-table th {
            white-space: nowrap;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        @media (max-width: 991.98px) {
            .product-quick-metrics {
                grid-template-columns: 1fr;
                min-width: 100%;
            }
        }

        @media (max-width: 575.98px) {
            .product-hero-image {
                width: 72px;
                height: 72px;
                border-radius: 16px;
            }

            .product-card-title {
                font-size: 1rem;
            }
        }
    </style>
    @endpush
@endsection
