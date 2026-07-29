@extends('layouts.admin')

@section('title', $vendor->name . ' - Products')
@section('page-title', $vendor->name . ' - Products')

@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';

    $text = static fn (string $en, string $ar) => $isArabic ? $ar : $en;

    $visibleCount = $products->count();
    $totalCount = method_exists($products, 'total') ? $products->total() : $products->count();

    $activeVisibleCount = $products->filter(fn ($product) => (bool) $product->is_active)->count();
    $inactiveVisibleCount = $products->filter(fn ($product) => ! (bool) $product->is_active)->count();
    $inStockVisibleCount = $products->filter(fn ($product) => (int) $product->stock > 0)->count();
    $outOfStockVisibleCount = $products->filter(fn ($product) => (int) $product->stock <= 0)->count();

    $productScore = static function ($product) {
        $checks = [
            filled($product->name),
            filled($product->sku),
            (bool) $product->category,
            filled($product->price),
            (int) $product->stock > 0,
            (bool) $product->is_active,
            $product->images && $product->images->isNotEmpty(),
        ];

        return (int) round((collect($checks)->filter()->count() / count($checks)) * 100);
    };

    $baseUrl = route('admin.vendors.products', $vendor->id);

    $quickFilters = [
        [
            'label' => $text('All', 'الكل'),
            'params' => [],
            'active' => ! request()->hasAny(['status', 'stock']),
        ],
        [
            'label' => $text('Active', 'نشط'),
            'params' => ['status' => 'active'],
            'active' => request('status') === 'active',
        ],
        [
            'label' => $text('Inactive', 'غير نشط'),
            'params' => ['status' => 'inactive'],
            'active' => request('status') === 'inactive',
        ],
        [
            'label' => $text('In stock', 'متوفر'),
            'params' => ['stock' => 'in_stock'],
            'active' => request('stock') === 'in_stock',
        ],
        [
            'label' => $text('Out of stock', 'غير متوفر'),
            'params' => ['stock' => 'out_of_stock'],
            'active' => request('stock') === 'out_of_stock',
        ],
    ];
@endphp

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">{{ $text('Dashboard', 'لوحة التحكم') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.vendors') }}">{{ $text('Vendors', 'الموردين') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.vendors.show', $vendor->id) }}">{{ $vendor->name }}</a>
    </li>
    <li class="breadcrumb-item active">{{ $text('Products', 'المنتجات') }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn vpr-back-btn">
        <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
        {{ $text('Back to Vendor', 'العودة للبائع') }}
    </a>
@endsection

@section('content')
    <div class="vpr-page" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        <section class="vpr-hero-card">
            <div class="vpr-hero-main">
                @if($vendor->logo)
                    <img src="{{ asset($vendor->logo) }}" alt="{{ $vendor->name }}" class="vpr-hero-logo">
                @else
                    <span class="vpr-hero-avatar">
                        {{ mb_substr($vendor->name, 0, 1) }}
                    </span>
                @endif

                <div class="vpr-hero-text">
                    <span class="vpr-chip">
                        {{ $text('Vendor products', 'منتجات المورد') }}
                    </span>

                    <h3>{{ $vendor->name }}</h3>

                    <p>
                        {{ $text(
                            'Review product stock, status, category, price, and open product details from one clean screen.',
                            'راجع المخزون والحالة والتصنيف والسعر وافتح تفاصيل المنتج من شاشة واحدة واضحة.'
                        ) }}
                    </p>
                </div>
            </div>

            <div class="vpr-hero-side">
                <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn vpr-light-btn">
                    <i class="fas {{ $isArabic ? 'fa-arrow-right ms-1' : 'fa-arrow-left me-1' }}"></i>
                    {{ $text('Vendor profile', 'ملف المورد') }}
                </a>
            </div>
        </section>

        <section class="vpr-metrics">
            <div class="vpr-metric-item">
                <span>{{ $text('Visible', 'المعروض') }}</span>
                <strong>{{ $visibleCount }}</strong>
            </div>

            <div class="vpr-metric-item">
                <span>{{ $text('Total', 'الإجمالي') }}</span>
                <strong>{{ $totalCount }}</strong>
            </div>

            <div class="vpr-metric-item">
                <span>{{ $text('Active', 'نشط') }}</span>
                <strong>{{ $activeVisibleCount }}</strong>
            </div>

            <div class="vpr-metric-item">
                <span>{{ $text('Out of stock', 'غير متوفر') }}</span>
                <strong>{{ $outOfStockVisibleCount }}</strong>
            </div>
        </section>

        <section class="vpr-filter-card">
            <div class="vpr-section-head">
                <div>
                    <h5>{{ $text('Search and filters', 'البحث والفلاتر') }}</h5>
                    <p>{{ $text('Search products and use quick filters to scan vendor inventory faster.', 'ابحث في المنتجات واستخدم الفلاتر السريعة لمراجعة مخزون المورد بسرعة.') }}</p>
                </div>

                @if(request('search') || request('status') || request('stock'))
                    <a href="{{ $baseUrl }}" class="btn btn-sm vpr-clear-btn">
                        <i class="fas fa-times {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                        {{ $text('Reset', 'إعادة ضبط') }}
                    </a>
                @endif
            </div>

            <div class="vpr-quick-filters">
                @foreach($quickFilters as $filter)
                    @php
                        $url = $baseUrl . (count($filter['params']) ? '?' . http_build_query(array_merge(request()->except(['page', 'status', 'stock']), $filter['params'])) : '');
                    @endphp

                    <a href="{{ $url }}" class="vpr-quick-chip {{ $filter['active'] ? 'active' : '' }}">
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </div>

            <form action="{{ $baseUrl }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-8">
                        <label class="vpr-label" for="productsSearch">
                            {{ $text('Search products', 'بحث المنتجات') }}
                        </label>

                        <div class="vpr-input-icon">
                            <i class="fas fa-search"></i>

                            <input
                                id="productsSearch"
                                type="text"
                                name="search"
                                class="form-control vpr-control"
                                placeholder="{{ $text('Search by product name, SKU, or category...', 'ابحث باسم المنتج أو SKU أو التصنيف...') }}"
                                value="{{ request('search') }}"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    @if(request('stock'))
                        <input type="hidden" name="stock" value="{{ request('stock') }}">
                    @endif

                    <div class="col-12 col-lg-4">
                        <div class="vpr-filter-actions">
                            <button class="btn btn-primary vpr-submit-btn" type="submit">
                                <i class="fas fa-search {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Search', 'بحث') }}
                            </button>

                            <a href="{{ $baseUrl }}" class="btn vpr-reset-btn">
                                <i class="fas fa-undo {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                {{ $text('Reset', 'إعادة ضبط') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <section class="vpr-table-card">
            <div class="vpr-section-head vpr-table-head">
                <div>
                    <h5>{{ $text('All Products', 'كل المنتجات') }}</h5>
                    <p>{{ $text('Important product information is grouped to keep the table easy to scan.', 'تم تجميع أهم بيانات المنتج لتسهيل قراءة الجدول.') }}</p>
                </div>

                <span class="vpr-page-count">
                    <i class="fas fa-box {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                    {{ $visibleCount }} / {{ $totalCount }}
                </span>
            </div>

            @if($products->count() > 0)
                <div class="table-responsive vpr-table-wrap">
                    <table class="table align-middle mb-0 vpr-table">
                        <thead>
                            <tr>
                                <th class="vpr-col-product">{{ $text('Product', 'المنتج') }}</th>
                                <th class="vpr-col-sku">{{ $text('SKU / Category', 'SKU / التصنيف') }}</th>
                                <th class="vpr-col-price">{{ $text('Price', 'السعر') }}</th>
                                <th class="vpr-col-stock">{{ $text('Stock', 'المخزون') }}</th>
                                <th class="vpr-col-health">{{ $text('Health', 'الجاهزية') }}</th>
                                <th class="vpr-col-status">{{ $text('Status', 'الحالة') }}</th>
                                <th class="vpr-col-date">{{ $text('Created', 'تاريخ الإضافة') }}</th>
                                <th class="vpr-col-actions text-end">{{ $text('Actions', 'الإجراءات') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($products as $product)
                                @php
                                    $score = $productScore($product);
                                @endphp

                                <tr>
                                    <td>
                                        <div class="vpr-product-cell">
                                            @if($product->images->isNotEmpty())
                                                <img src="{{ asset($product->images->first()->url) }}" alt="{{ $product->name }}">
                                            @else
                                                <span>
                                                    <i class="fas fa-box"></i>
                                                </span>
                                            @endif

                                            <div class="vpr-product-info">
                                                <a href="{{ route('admin.products.show', ['product' => $product->id, 'from' => 'admin.vendors.products']) }}">
                                                    {{ Str::limit($product->name, 38) }}
                                                </a>

                                                <small>#{{ $product->id }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="vpr-sku-box">
                                            <span>{{ $product->sku ?? 'N/A' }}</span>

                                            @if($product->category)
                                                <strong>{{ $product->category->name }}</strong>
                                            @else
                                                <strong class="text-muted">{{ $text('Uncategorized', 'بدون تصنيف') }}</strong>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="vpr-price">
                                            {{ number_format($product->price, 2) }}
                                            {{ __('admin-dashboard.EGP') }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="vpr-stock-pill {{ $product->stock > 0 ? 'in-stock' : 'out-stock' }}">
                                            <i class="fas fa-boxes"></i>
                                            {{ $product->stock }}
                                            {{ $text('in stock', 'في المخزون') }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="vpr-score">
                                            <div class="vpr-score-top">
                                                <span>{{ $text('Ready', 'جاهز') }}</span>
                                                <strong>{{ $score }}%</strong>
                                            </div>

                                            <div class="vpr-score-bar">
                                                <span style="width: {{ $score }}%"></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            class="vpr-status vpr-status-{{ $product->is_active ? 'active' : 'inactive' }} js-product-status-btn"
                                            data-action="{{ route('admin.products.toggle-status', $product->id) }}"
                                            data-product-name="{{ $product->name }}"
                                            data-message="{{ $text(
                                                'Are you sure you want to ' . ($product->is_active ? 'deactivate' : 'activate') . ' this product?',
                                                'هل أنت متأكد أنك تريد ' . ($product->is_active ? 'إيقاف' : 'تفعيل') . ' هذا المنتج؟'
                                            ) }}"
                                            data-confirm-label="{{ $product->is_active ? $text('Deactivate', 'إيقاف') : $text('Activate', 'تفعيل') }}"
                                            data-mode="{{ $product->is_active ? 'deactivate' : 'activate' }}"
                                        >
                                            <i></i>
                                            {{ $product->is_active ? $text('Active', 'نشط') : $text('Inactive', 'غير نشط') }}
                                        </button>
                                    </td>

                                    <td>
                                        <div class="vpr-date">
                                            {{ $product->created_at->format('M d, Y') }}
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <a
                                            href="{{ route('admin.products.show', ['product' => $product->id, 'from' => 'admin.vendors.products']) }}"
                                            class="btn btn-sm vpr-view-btn"
                                            title="{{ $text('View', 'عرض') }}"
                                        >
                                            <i class="fas fa-eye {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                                            {{ $text('View', 'عرض') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="vpr-pagination">
                    {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="vpr-empty">
                    <div class="vpr-empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>

                    <h5>{{ $text('No Products Found', 'لا توجد منتجات') }}</h5>

                    <p>
                        {{ request('search')
                            ? $text('No products match your current search.', 'لا توجد منتجات مطابقة للبحث الحالي.')
                            : $text("This vendor hasn't added any products yet.", 'هذا المورد لم يضف أي منتجات حتى الآن.')
                        }}
                    </p>

                    @if(request('search') || request('status') || request('stock'))
                        <a href="{{ $baseUrl }}" class="btn vpr-view-btn">
                            <i class="fas fa-times {{ $isArabic ? 'ms-1' : 'me-1' }}"></i>
                            {{ $text('Clear filters', 'مسح الفلاتر') }}
                        </a>
                    @endif
                </div>
            @endif
        </section>
    </div>

    <div class="modal fade" id="productStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content vpr-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            {{ $text('Confirm status change', 'تأكيد تغيير الحالة') }}
                        </h5>

                        <p class="vpr-modal-subtitle mb-0" id="productStatusModalSubtitle"></p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" id="productStatusForm">
                    @csrf
                    @method('PATCH')

                    <div class="modal-body">
                        <div class="vpr-modal-warning">
                            <div>
                                <i class="fas fa-box"></i>
                            </div>

                            <p id="productStatusModalMessage"></p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn vpr-reset-btn" data-bs-dismiss="modal">
                            {{ $text('Cancel', 'إلغاء') }}
                        </button>

                        <button type="submit" class="btn vpr-modal-confirm" id="productStatusConfirmBtn">
                            {{ $text('Confirm', 'تأكيد') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .vpr-page {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .vpr-hero-card,
            .vpr-filter-card,
            .vpr-table-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 20px;
                box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
            }

            .vpr-hero-card,
            .vpr-filter-card {
                padding: 1.25rem;
            }

            .vpr-hero-card {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
            }

            .vpr-hero-main {
                display: flex;
                align-items: flex-start;
                gap: 1rem;
                min-width: 0;
            }

            .vpr-hero-logo,
            .vpr-hero-avatar {
                width: 64px;
                height: 64px;
                border-radius: 22px;
                flex-shrink: 0;
            }

            .vpr-hero-logo {
                object-fit: cover;
                border: 1px solid #e2e8f0;
                background: #fff;
            }

            .vpr-hero-avatar {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #eff6ff;
                border: 1px solid #dbeafe;
                color: #2563eb;
                font-size: 1.6rem;
                font-weight: 900;
                text-transform: uppercase;
            }

            .vpr-chip {
                display: inline-flex;
                width: fit-content;
                padding: .28rem .7rem;
                margin-bottom: .45rem;
                border-radius: 999px;
                background: #eff6ff;
                border: 1px solid #dbeafe;
                color: #2563eb;
                font-size: .78rem;
                font-weight: 900;
            }

            .vpr-hero-text h3 {
                margin: 0;
                color: #111827;
                font-size: 1.45rem;
                font-weight: 900;
                letter-spacing: -.02em;
            }

            .vpr-hero-text p {
                margin: .45rem 0 0;
                max-width: 820px;
                color: #64748b;
                font-size: .95rem;
                line-height: 1.8;
            }

            .vpr-hero-side {
                flex-shrink: 0;
            }

            .vpr-back-btn,
            .vpr-light-btn,
            .vpr-reset-btn,
            .vpr-clear-btn {
                min-height: 42px;
                border-radius: 999px;
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #475569;
                font-weight: 900;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .vpr-back-btn:hover,
            .vpr-light-btn:hover,
            .vpr-reset-btn:hover,
            .vpr-clear-btn:hover {
                background: #f8fafc;
                color: #111827;
            }

            .vpr-metrics {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: .75rem;
            }

            .vpr-metric-item {
                min-height: 78px;
                padding: .9rem 1rem;
                border-radius: 18px;
                border: 1px solid #e5e7eb;
                background: #fff;
                box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .vpr-metric-item span {
                color: #64748b;
                font-size: .78rem;
                font-weight: 900;
                margin-bottom: .35rem;
            }

            .vpr-metric-item strong {
                color: #111827;
                font-size: 1.4rem;
                font-weight: 900;
                line-height: 1;
            }

            .vpr-section-head {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .vpr-section-head h5 {
                margin: 0;
                color: #111827;
                font-size: 1rem;
                font-weight: 900;
            }

            .vpr-section-head p {
                margin: .25rem 0 0;
                color: #64748b;
                font-size: .88rem;
                line-height: 1.6;
            }

            .vpr-quick-filters {
                display: flex;
                flex-wrap: wrap;
                gap: .5rem;
                margin-bottom: 1rem;
            }

            .vpr-quick-chip {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 34px;
                padding: .35rem .8rem;
                border-radius: 999px;
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #475569;
                font-size: .82rem;
                font-weight: 800;
                text-decoration: none;
            }

            .vpr-quick-chip:hover,
            .vpr-quick-chip.active {
                background: #eff6ff;
                border-color: #bfdbfe;
                color: #1d4ed8;
            }

            .vpr-label {
                display: block;
                margin-bottom: .4rem;
                color: #475569;
                font-size: .8rem;
                font-weight: 900;
            }

            .vpr-control {
                min-height: 44px;
                border-radius: 13px;
                border-color: #dbe3ea;
                color: #111827;
                font-size: .9rem;
                box-shadow: none;
            }

            .vpr-control:focus {
                border-color: #60a5fa;
                box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1);
            }

            .vpr-input-icon {
                position: relative;
            }

            .vpr-input-icon i {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                z-index: 2;
            }

            [dir="ltr"] .vpr-input-icon i {
                left: .85rem;
            }

            [dir="rtl"] .vpr-input-icon i {
                right: .85rem;
            }

            [dir="ltr"] .vpr-input-icon .vpr-control {
                padding-left: 2.5rem;
            }

            [dir="rtl"] .vpr-input-icon .vpr-control {
                padding-right: 2.5rem;
            }

            .vpr-filter-actions {
                display: flex;
                justify-content: flex-end;
                gap: .65rem;
                flex-wrap: wrap;
            }

            .vpr-submit-btn {
                min-height: 44px;
                border-radius: 13px;
                font-weight: 900;
                box-shadow: 0 10px 18px rgba(37, 99, 235, .12);
            }

            .vpr-table-card {
                overflow: hidden;
            }

            .vpr-table-head {
                padding: 1.25rem 1.25rem 0;
            }

            .vpr-page-count {
                display: inline-flex;
                align-items: center;
                width: fit-content;
                padding: .42rem .8rem;
                border-radius: 999px;
                background: #f8fafc;
                border: 1px solid #e5e7eb;
                color: #475569;
                font-size: .8rem;
                font-weight: 800;
                white-space: nowrap;
            }

            .vpr-table-wrap {
                border-top: 1px solid #e5e7eb;
            }

            .vpr-table {
                min-width: 1020px;
                table-layout: fixed;
            }

            .vpr-table thead th {
                padding: .85rem 1rem;
                background: #f8fafc;
                color: #475569;
                border-bottom: 1px solid #e5e7eb;
                font-size: .76rem;
                font-weight: 900;
                white-space: nowrap;
            }

            .vpr-table tbody td {
                padding: 1rem;
                border-color: #eef2f7;
                vertical-align: middle;
            }

            .vpr-table tbody tr:hover {
                background: #fbfdff;
            }

            .vpr-col-product {
                width: 25%;
            }

            .vpr-col-sku {
                width: 16%;
            }

            .vpr-col-price {
                width: 11%;
            }

            .vpr-col-stock {
                width: 13%;
            }

            .vpr-col-health {
                width: 11%;
            }

            .vpr-col-status {
                width: 10%;
            }

            .vpr-col-date {
                width: 8%;
            }

            .vpr-col-actions {
                width: 6%;
            }

            .vpr-product-cell {
                display: flex;
                align-items: center;
                gap: .75rem;
                min-width: 0;
            }

            .vpr-product-cell img,
            .vpr-product-cell > span {
                width: 48px;
                height: 48px;
                border-radius: 16px;
                flex-shrink: 0;
            }

            .vpr-product-cell img {
                object-fit: cover;
                border: 1px solid #e2e8f0;
                background: #fff;
            }

            .vpr-product-cell > span {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                color: #64748b;
            }

            .vpr-product-info {
                min-width: 0;
            }

            .vpr-product-info a {
                display: block;
                color: #111827;
                font-size: .92rem;
                font-weight: 900;
                line-height: 1.4;
                text-decoration: none;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .vpr-product-info a:hover {
                color: #1d4ed8;
            }

            .vpr-product-info small {
                display: block;
                color: #64748b;
                font-size: .76rem;
                margin-top: .1rem;
            }

            .vpr-sku-box span {
                display: block;
                color: #1d4ed8;
                font-size: .82rem;
                font-weight: 900;
                line-height: 1.5;
            }

            .vpr-sku-box strong {
                display: inline-flex;
                margin-top: .25rem;
                color: #475569;
                font-size: .76rem;
                font-weight: 900;
                line-height: 1.4;
            }

            .vpr-price {
                color: #111827;
                font-size: .9rem;
                font-weight: 900;
            }

            .vpr-stock-pill {
                display: inline-flex;
                align-items: center;
                gap: .35rem;
                padding: .35rem .65rem;
                border-radius: 999px;
                font-size: .75rem;
                font-weight: 900;
                white-space: nowrap;
            }

            .vpr-stock-pill.in-stock {
                background: #ecfdf5;
                border: 1px solid #a7f3d0;
                color: #047857;
            }

            .vpr-stock-pill.out-stock {
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #b91c1c;
            }

            .vpr-score {
                min-width: 90px;
            }

            .vpr-score-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: .5rem;
                margin-bottom: .35rem;
            }

            .vpr-score-top span {
                color: #64748b;
                font-size: .72rem;
                font-weight: 900;
            }

            .vpr-score-top strong {
                color: #111827;
                font-size: .78rem;
                font-weight: 900;
            }

            .vpr-score-bar {
                height: 7px;
                border-radius: 999px;
                background: #e5e7eb;
                overflow: hidden;
            }

            .vpr-score-bar span {
                display: block;
                height: 100%;
                border-radius: inherit;
                background: #2563eb;
            }

            .vpr-status {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: .4rem;
                padding: .42rem .75rem;
                border-radius: 999px;
                border: 1px solid transparent;
                font-size: .78rem;
                font-weight: 900;
                white-space: nowrap;
                cursor: pointer;
            }

            .vpr-status i {
                width: .45rem;
                height: .45rem;
                border-radius: 999px;
                background: currentColor;
            }

            .vpr-status-active {
                background: #ecfdf5;
                color: #047857;
                border-color: #a7f3d0;
            }

            .vpr-status-inactive {
                background: #f8fafc;
                color: #475569;
                border-color: #cbd5e1;
            }

            .vpr-status:hover {
                filter: brightness(.98);
            }

            .vpr-date {
                color: #475569;
                font-size: .8rem;
                font-weight: 700;
            }

            .vpr-view-btn {
                border-radius: 999px;
                border: 1px solid #bfdbfe;
                background: #eff6ff;
                color: #1d4ed8;
                font-weight: 900;
            }

            .vpr-view-btn:hover {
                background: #2563eb;
                border-color: #2563eb;
                color: #fff;
            }

            .vpr-pagination {
                padding: 1rem 1.25rem;
                border-top: 1px solid #e5e7eb;
                background: #fff;
                display: flex;
                justify-content: center;
            }

            .vpr-empty {
                padding: 4rem 1.5rem;
                text-align: center;
                border-top: 1px solid #e5e7eb;
            }

            .vpr-empty-icon {
                width: 78px;
                height: 78px;
                margin: 0 auto 1rem;
                border-radius: 26px;
                background: #f8fafc;
                border: 1px solid #e5e7eb;
                color: #94a3b8;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.9rem;
            }

            .vpr-empty h5 {
                color: #111827;
                font-weight: 900;
                margin-bottom: .45rem;
            }

            .vpr-empty p {
                max-width: 520px;
                margin: 0 auto 1rem;
                color: #64748b;
                line-height: 1.8;
            }

            .vpr-modal {
                border: 0;
                border-radius: 22px;
                overflow: hidden;
                box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
            }

            .vpr-modal .modal-header,
            .vpr-modal .modal-footer {
                border-color: #e5e7eb;
            }

            .vpr-modal .modal-title {
                color: #111827;
                font-weight: 900;
            }

            .vpr-modal-subtitle {
                color: #64748b;
                font-size: .82rem;
                margin-top: .2rem;
            }

            .vpr-modal-warning {
                display: flex;
                align-items: flex-start;
                gap: .85rem;
                padding: 1rem;
                border-radius: 18px;
                background: #f8fafc;
                border: 1px dashed #cbd5e1;
            }

            .vpr-modal-warning div {
                width: 44px;
                height: 44px;
                border-radius: 16px;
                background: #eff6ff;
                color: #2563eb;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .vpr-modal-warning p {
                margin: 0;
                color: #334155;
                line-height: 1.7;
                font-weight: 700;
            }

            .vpr-modal-confirm {
                border-radius: 999px;
                background: #2563eb;
                border-color: #2563eb;
                color: #fff;
                font-weight: 900;
            }

            .vpr-modal-confirm.is-danger {
                background: #b45309;
                border-color: #b45309;
            }

            .vpr-modal-confirm.is-success {
                background: #047857;
                border-color: #047857;
            }

            @media (max-width: 1199.98px) {
                .vpr-table {
                    min-width: 980px;
                }

                .vpr-hero-card {
                    align-items: stretch;
                    flex-direction: column;
                }
            }

            @media (max-width: 767.98px) {
                .vpr-hero-card,
                .vpr-filter-card {
                    padding: 1rem;
                    border-radius: 18px;
                }

                .vpr-hero-main {
                    flex-direction: column;
                }

                .vpr-hero-text h3 {
                    font-size: 1.2rem;
                }

                .vpr-metrics {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .vpr-section-head {
                    flex-direction: column;
                    align-items: stretch;
                }

                .vpr-filter-actions {
                    justify-content: flex-start;
                }

                .vpr-table-head {
                    padding: 1rem 1rem 0;
                }

                .vpr-table {
                    min-width: 920px;
                }
            }
        </style>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('productStatusModal');

            if (!modalEl || typeof bootstrap === 'undefined') {
                return;
            }

            const modal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('productStatusForm');
            const subtitle = document.getElementById('productStatusModalSubtitle');
            const message = document.getElementById('productStatusModalMessage');
            const confirmBtn = document.getElementById('productStatusConfirmBtn');

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.js-product-status-btn');

                if (!button) {
                    return;
                }

                event.preventDefault();

                form.action = button.getAttribute('data-action');
                subtitle.textContent = button.getAttribute('data-product-name') || '';
                message.textContent = button.getAttribute('data-message') || '';
                confirmBtn.textContent = button.getAttribute('data-confirm-label') || '{{ $text('Confirm', 'تأكيد') }}';

                confirmBtn.classList.remove('is-danger', 'is-success');

                if (button.getAttribute('data-mode') === 'deactivate') {
                    confirmBtn.classList.add('is-danger');
                } else {
                    confirmBtn.classList.add('is-success');
                }

                modal.show();
            });
        });
    </script>
@endsection