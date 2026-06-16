@extends('layouts.vendor')

@section('title', __('vendor-dashboard.products'))
@section('page-title', __('vendor-dashboard.products_management'))

@section('page-actions')
    <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('vendor-dashboard.add_new_product') }}
    </a>
@endsection

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('vendor-dashboard.all_products') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-box-open me-2"></i>
                    {{ $products->count() }} / {{ $products->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('vendor.products') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم المنتج أو الاسم أو SKU...' : 'Search by product number, name, SKU...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-2">
                    <select name="brand_id" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('brand_id', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل البراندات' : 'All brands' }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ (string) request('brand_id') === (string) $brand->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? ($brand->name_ar ?: $brand->name_en) : ($brand->name_en ?: $brand->name_ar) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="main_category_id" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('main_category_id', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الأقسام الرئيسية' : 'All main categories' }}</option>
                        @foreach($mainCategories as $mainCategory)
                            <option value="{{ $mainCategory->id }}" {{ (string) request('main_category_id') === (string) $mainCategory->id ? 'selected' : '' }}>
                                {{ $mainCategory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="category_id" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('category_id', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الأقسام الفرعية' : 'All subcategories' }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('vendor-dashboard.active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('vendor-dashboard.inactive') }}</option>
                    </select>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('vendor.products') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $numberDir = request('sort_by') === 'product_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.products', array_merge(request()->except('page'), ['sort_by' => 'product_number', 'sort_dir' => $numberDir])) }}">
                                        <span>{{ app()->getLocale() === 'ar' ? 'رقم المنتج' : 'Product Number' }}</span>
                                        <i class="fas {{ request('sort_by') === 'product_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="mobile-hide">{{ __('vendor-dashboard.image') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $nameDir = request('sort_by') === 'name' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.products', array_merge(request()->except('page'), ['sort_by' => 'name', 'sort_dir' => $nameDir])) }}">
                                        <span>{{ __('vendor-dashboard.name') }}</span>
                                        <i class="fas {{ request('sort_by') === 'name' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $categoryDir = request('sort_by') === 'category' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.products', array_merge(request()->except('page'), ['sort_by' => 'category', 'sort_dir' => $categoryDir])) }}">
                                        <span>{{ __('vendor-dashboard.category') }}</span>
                                        <i class="fas {{ request('sort_by') === 'category' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $brandDir = request('sort_by') === 'brand' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.products', array_merge(request()->except('page'), ['sort_by' => 'brand', 'sort_dir' => $brandDir])) }}">
                                        <span>{{ __('vendor-dashboard.brand') }}</span>
                                        <i class="fas {{ request('sort_by') === 'brand' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $priceDir = request('sort_by') === 'price' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.products', array_merge(request()->except('page'), ['sort_by' => 'price', 'sort_dir' => $priceDir])) }}">
                                        <span>{{ __('vendor-dashboard.price') }}</span>
                                        <i class="fas {{ request('sort_by') === 'price' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('vendor-dashboard.status') }}</th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.products', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('vendor-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap">{{ __('vendor-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="fw-semibold text-primary">{{ $product->product_number ?? '-' }}</td>
                                    <td class="mobile-hide">
                                        @php
                                            $firstImage = $product->media->firstWhere('type', \App\Enum\ProductMediaType::IMAGE);
                                        @endphp
                                        @if($firstImage)
                                            <img src="{{ asset($firstImage->url) }}"
                                                 alt="{{ $product->name }}"
                                                 class="entity-logo rounded"
                                                 width="42"
                                                 height="42">
                                        @else
                                            <div class="entity-avatar">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark entity-name">{{ $product->translation('en')->name ?? '-' }}</div>
                                        <small class="text-muted d-block entity-subname">{{ $product->translation('ar')->name ?? '-' }}</small>
                                    </td>
                                    <td class="mobile-hide">{{ $product->category->name ?? __('vendor-dashboard.no_category') }}</td>
                                    <td class="mobile-hide">{{ optional($product->brand)->name_en ?? __('vendor-dashboard.no_brand') }}</td>
                                    <td class="fw-semibold">{{ __('admin-dashboard.EGP') }}{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <span class="status-pill {{ $product->is_active ? 'status-active' : 'status-inactive' }}">
                                            <span class="status-dot {{ $product->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                            {{ $product->is_active ? __('vendor-dashboard.active') : __('vendor-dashboard.inactive') }}
                                        </span>
                                    </td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $product->created_at])</td>
                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('vendor.products.edit', $product->id) }}"
                                               class="btn btn-sm btn-primary text-white action-icon-btn"
                                               title="{{ __('vendor-dashboard.edit') }}"
                                               data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('vendor.products.toggle-status', $product->id) }}"
                                                  method="POST"
                                                  class="d-inline m-0"
                                                  onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? ($product->is_active ? 'هل تريد تعطيل هذا المنتج؟' : 'هل تريد تفعيل هذا المنتج؟') : ($product->is_active ? 'Are you sure you want to disable this product?' : 'Are you sure you want to enable this product?') }}');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="btn btn-sm btn-{{ $product->is_active ? 'warning' : 'success' }} text-white action-icon-btn"
                                                        title="{{ $product->is_active ? __('vendor-dashboard.disable') : __('vendor-dashboard.enable') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                    <i class="fas fa-{{ $product->is_active ? 'ban' : 'check' }}"></i>
                                                </button>
                                            </form>
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
                        <i class="fas fa-box empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('vendor-dashboard.no_products_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('vendor-dashboard.start_by_adding') }}</p>
                        <div class="mt-3">
                            <a href="{{ route('vendor.products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('vendor-dashboard.add_new_product') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
