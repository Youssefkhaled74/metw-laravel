@extends('layouts.admin')

@section('title', __('admin-dashboard.products'))
@section('page-title', __('admin-dashboard.products_list'))

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('admin-dashboard.back_to_dashboard') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    @php($filters = $filters ?? [])

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('admin-dashboard.all_products') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-boxes me-2"></i>
                    {{ $products->count() }} / {{ $products->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.products') }}" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بالاسم أو SKU أو الوصف...' : 'Search by name, SKU, or description...' }}"
                            value="{{ $filters['search'] ?? '' }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-2">
                    <select id="brand_id" name="brand_id" class="form-select form-select-sm filter-select-modern">
                        <option value="">{{ __('admin-dashboard.all_brands') }}</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(($filters['brand_id'] ?? '') == $brand->id)>
                                {{ $brand->{'name_' . app()->getLocale()} ?? $brand->name ?? __('admin-dashboard.not_available') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select id="vendor_id" name="vendor_id" class="form-select form-select-sm filter-select-modern">
                        <option value="">{{ __('admin-dashboard.all_vendors') }}</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}" @selected(($filters['vendor_id'] ?? '') == $vendor->id)>
                                {{ $vendor->name ?? $vendor->business_name ?? __('admin-dashboard.not_available') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select id="main_category_id" name="main_category_id" class="form-select form-select-sm filter-select-modern">
                        <option value="">{{ __('admin-dashboard.all_main_categories') }}</option>
                        @foreach ($mainCategories as $mainCategory)
                            <option value="{{ $mainCategory->id }}" @selected(($filters['main_category_id'] ?? '') == $mainCategory->id)>
                                {{ $mainCategory->name ?? __('admin-dashboard.not_available') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select id="category_id" name="category_id" class="form-select form-select-sm filter-select-modern">
                        <option value="">{{ __('admin-dashboard.all_categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>
                                {{ $category->name ?? __('admin-dashboard.not_available') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ __('admin-dashboard.apply_filters') }}
                    </button>
                    <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ __('admin-dashboard.reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table products-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col" data-sort-key="product_number">
                                    <span class="sortable-label">{{ __('admin-dashboard.product_number') }} <i class="fas fa-sort sort-icon"></i></span>
                                </th>
                                <th class="text-nowrap text-center">{{ __('admin-dashboard.product_image') }}</th>
                                <th class="text-nowrap sortable-col" data-sort-key="name">
                                    <span class="sortable-label">{{ __('admin-dashboard.product_name') }} <i class="fas fa-sort sort-icon"></i></span>
                                </th>
                                <th class="text-nowrap sortable-col mobile-hide" data-sort-key="vendor">
                                    <span class="sortable-label">{{ __('admin-dashboard.product_vendor') }} <i class="fas fa-sort sort-icon"></i></span>
                                </th>
                                <th class="text-nowrap sortable-col mobile-hide" data-sort-key="brand">
                                    <span class="sortable-label">{{ __('admin-dashboard.product_brand') }} <i class="fas fa-sort sort-icon"></i></span>
                                </th>
                                <th class="text-nowrap sortable-col mobile-hide" data-sort-key="category">
                                    <span class="sortable-label">{{ __('admin-dashboard.product_category') }} <i class="fas fa-sort sort-icon"></i></span>
                                </th>
                                <th class="text-nowrap text-end sortable-col" data-sort-key="price">
                                    <span class="sortable-label justify-content-end">{{ __('admin-dashboard.product_price') }} <i class="fas fa-sort sort-icon"></i></span>
                                </th>
                                <th class="text-nowrap text-center sortable-col" data-sort-key="stock">
                                    <span class="sortable-label justify-content-center">{{ __('admin-dashboard.product_stock') }} <i class="fas fa-sort sort-icon"></i></span>
                                </th>
                                <th class="text-nowrap text-center">{{ __('admin-dashboard.product_status') }}</th>
                                <th class="text-nowrap sortable-col mobile-hide" data-sort-key="created">
                                    <span class="sortable-label">{{ __('admin-dashboard.product_created') }} <i class="fas fa-sort sort-icon"></i></span>
                                </th>
                                <th class="text-nowrap text-center">{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr data-id="{{ $product->id }}"
                                    data-product_number="{{ $product->product_number }}"
                                    data-name="{{ strtolower($product->name ?? '') }}"
                                    data-vendor="{{ strtolower($product->vendor->name ?? '') }}"
                                    data-brand="{{ strtolower($product->brand?->{'name_' . app()->getLocale()} ?? $product->brand?->name ?? '') }}"
                                    data-category="{{ strtolower($product->category->name ?? '') }}"
                                    data-price="{{ (float) ($product->price ?? 0) }}"
                                    data-stock="{{ (int) ($product->stock ?? 0) }}"
                                    data-created="{{ $product->created_at?->timestamp ?? 0 }}">
                                    <td class="fw-semibold text-primary">{{ $product->product_number }}</td>
                                    <td class="align-middle text-center" style="width: 110px;">
                                        @if ($product->media->count() > 0)
                                            <div class="product-thumbnail mx-auto" style="width: 90px; height: 90px; min-width: 90px; overflow: hidden; border-radius: 6px;">
                                                <img src="{{ asset($product->media->first()->url) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                            </div>
                                        @else
                                            <div class="product-thumbnail placeholder d-flex align-items-center justify-content-center mx-auto" style="width: 90px; height: 90px; min-width: 90px; border-radius: 6px;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark entity-name">{{ $product->name }}</div>
                                        <small class="text-muted d-block entity-subname">
                                            <i class="fas fa-barcode me-1"></i>
                                            {{ __('admin-dashboard.product_sku') }}: {{ $product->sku }}
                                        </small>
                                    </td>
                                    <td class="mobile-hide">
                                        <div class="text-truncate" style="max-width: 180px;">
                                            <i class="fas fa-store me-1 text-muted"></i>
                                            {{ $product->vendor->name ?? __('admin-dashboard.not_available') }}
                                        </div>
                                    </td>
                                    <td class="mobile-hide">
                                        <div class="text-truncate" style="max-width: 180px;">
                                            <i class="fas fa-tags me-1 text-muted"></i>
                                            {{ $product->brand?->{'name_' . app()->getLocale()} ?? $product->brand?->name ?? __('admin-dashboard.not_available') }}
                                        </div>
                                    </td>
                                    <td class="mobile-hide">
                                        <div class="text-truncate" style="max-width: 180px;">
                                            <i class="fas fa-folder-open me-1 text-muted"></i>
                                            {{ $product->category->name ?? __('admin-dashboard.not_available') }}
                                        </div>
                                    </td>
                                    <td class="text-end fw-semibold">
                                        <span class="text-primary">{{ __('admin-dashboard.EGP') }} {{ number_format($product->price, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="count-pill {{ $product->stock > 0 ? 'packages-pill' : 'orders-pill' }}">{{ $product->stock }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-pill {{ $product->is_active ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-dot {{ $product->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                            {{ __('admin-dashboard.' . ($product->is_active ? 'product_active' : 'product_inactive')) }}
                                        </span>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $product->created_at])</td>
                                    <td class="text-center">
                                        <div class="actions-group justify-content-center">
                                            <a href="{{ route('admin.products.show', ['product' => $product->id, 'from' => 'admin.products']) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="{{ __('admin-dashboard.product_details') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-box-open empty-icon mb-3"></i>
                        <h5 class="text-muted mb-1">{{ __('admin-dashboard.no_products_found') }}</h5>
                        <p class="text-muted small mb-0">{{ __('admin-dashboard.no_records_message') ?? '' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .product-thumbnail {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f5f5f5;
        overflow: hidden;
    }

    .product-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
    }

    .product-thumbnail.placeholder {
        background-color: #e8e8e8;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchToggle = document.getElementById('searchToggle');
        const searchFilterSection = document.getElementById('searchFilterSection');
        const table = document.querySelector('.products-table');
        const tbody = table ? table.querySelector('tbody') : null;
        const sortableHeaders = table ? table.querySelectorAll('.sortable-col') : [];

        function sortTable(key, direction) {
            if (!tbody) {
                return;
            }

            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                const aVal = a.dataset[key] ?? '';
                const bVal = b.dataset[key] ?? '';

                if (['id', 'image', 'price', 'stock', 'status', 'created'].includes(key)) {
                    const aNum = Number(aVal);
                    const bNum = Number(bVal);
                    return direction === 'asc' ? aNum - bNum : bNum - aNum;
                }

                return direction === 'asc'
                    ? String(aVal).localeCompare(String(bVal))
                    : String(bVal).localeCompare(String(aVal));
            });

            rows.forEach((row) => tbody.appendChild(row));
        }

        sortableHeaders.forEach((header) => {
            header.addEventListener('click', function() {
                const key = this.dataset.sortKey;
                const nextDir = this.dataset.sortDir === 'asc' ? 'desc' : 'asc';

                sortableHeaders.forEach((h) => {
                    h.classList.remove('active');
                    h.dataset.sortDir = '';
                    const icon = h.querySelector('.sort-icon');
                    if (icon) {
                        icon.classList.remove('fa-sort-up', 'fa-sort-down');
                        icon.classList.add('fa-sort');
                    }
                });

                this.classList.add('active');
                this.dataset.sortDir = nextDir;

                const currentIcon = this.querySelector('.sort-icon');
                if (currentIcon) {
                    currentIcon.classList.remove('fa-sort');
                    currentIcon.classList.add(nextDir === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
                }

                sortTable(key, nextDir);
            });
        });

    });
</script>
@endpush
