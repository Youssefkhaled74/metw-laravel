@extends('layouts.vendor')

@section('title', __('vendor-dashboard.product_reviews'))
@section('page-title', __('vendor-dashboard.product_reviews'))

@section('content')
    <x-admin.shared-table-assets />

    <div class="vendor-product-reviews-page">
        <div class="card shadow-sm border-0 data-card">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h5 class="mb-0 fw-semibold">{{ __('vendor-dashboard.all_product_reviews') }}</h5>
                    <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                        <i class="fas fa-star me-2"></i>
                        {{ $reviews->count() }} / {{ $reviews->total() }}
                    </span>
                </div>

                <form method="GET" action="{{ route('vendor.product_reviews') }}" class="row g-2 align-items-center">
                    <div class="col-lg-8">
                        <div class="input-group input-group-sm search-shell">
                            <span class="input-group-text bg-white border-end-0 search-icon-shell">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input
                                type="text"
                                name="search"
                                class="form-control border-start-0 search-input-modern"
                                placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم المنتج أو الاسم أو التعليق أو المستخدم...' : 'Search by product number, name, review comment, or user...' }}"
                                value="{{ request('search') }}"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                    <div class="col-lg-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search me-1"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                        </button>
                        <a href="{{ route('vendor.product_reviews') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-body p-0 table-wrap">
                @if ($reviews->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table" width="100%">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $numberDir = request('sort_by') === 'product_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.product_reviews', array_merge(request()->except('page'), ['sort_by' => 'product_number', 'sort_dir' => $numberDir])) }}">
                                        <span>{{ app()->getLocale() === 'ar' ? 'رقم المنتج' : 'Product #' }}</span>
                                        <i class="fas {{ request('sort_by') === 'product_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $nameDir = request('sort_by') === 'name' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.product_reviews', array_merge(request()->except('page'), ['sort_by' => 'name', 'sort_dir' => $nameDir])) }}">
                                        <span>{{ __('vendor-dashboard.product') }}</span>
                                        <i class="fas {{ request('sort_by') === 'name' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>{{ __('vendor-dashboard.image') }}</th>
                                <th>{{ __('vendor-dashboard.user') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $ratingDir = request('sort_by') === 'rating' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.product_reviews', array_merge(request()->except('page'), ['sort_by' => 'rating', 'sort_dir' => $ratingDir])) }}">
                                        <span>{{ __('vendor-dashboard.rating') }}</span>
                                        <i class="fas {{ request('sort_by') === 'rating' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>{{ __('vendor-dashboard.comment') }}</th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('vendor.product_reviews', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('vendor-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reviews as $review)
                                @php
                                    $product = $review->product;
                                @endphp
                                @if($product)
                                    <tr>
                                        <td class="fw-semibold text-primary">{{ $product->product_number ?? '-' }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark entity-name">{{ $product->translation('en')->name ?? '-' }}</div>
                                            <small class="text-muted d-block entity-subname">{{ $product->translation('ar')->name ?? '-' }}</small>
                                        </td>
                                        <td>
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
                                        <td>{{ $review->user->username ?? __('vendor-dashboard.unknown_user') }}</td>
                                        <td>
                                            <span class="status-pill" style="background:#fef3c7;color:#92400e;border-color:#fcd34d;">
                                                ⭐ {{ $review->rating }}
                                            </span>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($review->comment, 90) }}</td>
                                        <td>@include('admin.partials.date', ['date' => $review->created_at])</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $reviews->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-star empty-icon mb-3"></i>
                        <h5 class="text-muted mb-1">{{ __('vendor-dashboard.no_reviews_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('vendor-dashboard.no_reviews_yet') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>
@endsection
