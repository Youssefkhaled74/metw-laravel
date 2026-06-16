@extends('layouts.shipment')

@section('title', __('vendor-dashboard.return_requests'))
@section('page-title', __('vendor-dashboard.return_requests_management'))

@section('content')
    <x-admin.shared-table-assets />

    <div class="card shadow-sm border-0 data-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ __('vendor-dashboard.all_return_requests') }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2 rows-counter-badge">
                    <i class="fas fa-undo me-2"></i>
                    {{ $returnRequests->count() }} / {{ $returnRequests->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('shipment.return-requests') }}" class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <div class="input-group input-group-sm search-shell">
                        <span class="input-group-text bg-white border-end-0 search-icon-shell">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0 search-input-modern"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم المرتجع أو الطلب أو العميل...' : 'Search by return #, order #, customer...' }}"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select form-select-sm filter-select-modern">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="requested" {{ request('status') === 'requested' ? 'selected' : '' }}>{{ __('vendor-dashboard.requested') }}</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('vendor-dashboard.approved') }}</option>
                        <option value="pickup" {{ request('status') === 'pickup' ? 'selected' : '' }}>{{ __('vendor-dashboard.pickup') }}</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>{{ __('vendor-dashboard.processing') }}</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>{{ __('vendor-dashboard.refunded') }}</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('vendor-dashboard.rejected') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('vendor-dashboard.cancelled') }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('vendor-dashboard.completed') }}</option>
                    </select>
                </div>

                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق' : 'Apply' }}
                    </button>
                    <a href="{{ route('shipment.return-requests') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-wrap">
            @if ($returnRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                            <tr>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $returnNumberDir = request('sort_by') === 'return_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.return-requests', array_merge(request()->except('page'), ['sort_by' => 'return_number', 'sort_dir' => $returnNumberDir])) }}">
                                        <span>{{ __('vendor-dashboard.return_number') }}</span>
                                        <i class="fas {{ request('sort_by') === 'return_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $customerDir = request('sort_by') === 'customer' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.return-requests', array_merge(request()->except('page'), ['sort_by' => 'customer', 'sort_dir' => $customerDir])) }}">
                                        <span>{{ __('vendor-dashboard.customer') }}</span>
                                        <i class="fas {{ request('sort_by') === 'customer' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $orderNumberDir = request('sort_by') === 'order_number' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.return-requests', array_merge(request()->except('page'), ['sort_by' => 'order_number', 'sort_dir' => $orderNumberDir])) }}">
                                        <span>{{ __('vendor-dashboard.order_number') }}</span>
                                        <i class="fas {{ request('sort_by') === 'order_number' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $itemsDir = request('sort_by') === 'items' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.return-requests', array_merge(request()->except('page'), ['sort_by' => 'items', 'sort_dir' => $itemsDir])) }}">
                                        <span>{{ __('vendor-dashboard.total_items') }}</span>
                                        <i class="fas {{ request('sort_by') === 'items' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col">
                                    @php
                                        $refundDir = request('sort_by') === 'refund_amount' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.return-requests', array_merge(request()->except('page'), ['sort_by' => 'refund_amount', 'sort_dir' => $refundDir])) }}">
                                        <span>{{ __('vendor-dashboard.refund_amount') }}</span>
                                        <i class="fas {{ request('sort_by') === 'refund_amount' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="mobile-hide">{{ __('vendor-dashboard.reason') }}</th>
                                <th>{{ __('vendor-dashboard.status') }}</th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $pickupDateDir = request('sort_by') === 'pickup_date' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.return-requests', array_merge(request()->except('page'), ['sort_by' => 'pickup_date', 'sort_dir' => $pickupDateDir])) }}">
                                        <span>{{ __('vendor-dashboard.pickup_date') }}</span>
                                        <i class="fas {{ request('sort_by') === 'pickup_date' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th class="text-nowrap sortable-col mobile-hide">
                                    @php
                                        $createdAtDir = request('sort_by', 'created_at') === 'created_at' && request('sort_dir', 'desc') === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a class="text-decoration-none text-reset" href="{{ route('shipment.return-requests', array_merge(request()->except('page'), ['sort_by' => 'created_at', 'sort_dir' => $createdAtDir])) }}">
                                        <span>{{ __('vendor-dashboard.created_at') }}</span>
                                        <i class="fas {{ request('sort_by', 'created_at') === 'created_at' ? (request('sort_dir', 'desc') === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} sort-indicator"></i>
                                    </a>
                                </th>
                                <th>{{ __('vendor-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returnRequests as $request)
                                <tr>
                                    <td><strong class="text-primary">{{ $request->return_number }}</strong></td>
                                    <td>{{ $request->user->username ?? '-' }}</td>
                                    <td>#{{ $request->order->order_number ?? '-' }}</td>
                                    <td><span class="count-pill packages-pill">{{ $request->company_items_count ?? $request->items->count() }}</span></td>
                                    <td>{{__('admin-dashboard.EGP')}}{{ number_format($request->company_refund_amount_sum ?? $request->refund_amount ?? $request->calculateRefundAmount(), 2) }}</td>
                                    <td class="mobile-hide">{{ \Illuminate\Support\Str::limit($request->reason, 35) ?? '-' }}</td>

                                    <td>
                                        @php
                                            $statusValue = $request->status->value ?? $request->status;
                                            $statusKey = 'vendor-dashboard.' . $statusValue;
                                            $statusLabel = __($statusKey);
                                            if ($statusLabel === $statusKey) {
                                                $statusLabel = ucfirst(str_replace('_', ' ', $statusValue));
                                            }
                                        @endphp
                                        <span class="status-pill {{ in_array($statusValue, ['refunded','completed']) ? 'status-active' : ($statusValue === 'rejected' || $statusValue === 'cancelled' ? 'status-inactive' : 'packages-pill') }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>

                                    <td class="mobile-hide">{{ $request->pickup_date ? $request->pickup_date->format('M d, Y') : '-' }}</td>
                                    <td class="mobile-hide">@include('admin.partials.date', ['date' => $request->created_at])</td>

                                    <td>
                                        <div class="actions-group">
                                            <a href="{{ route('shipment.return-requests.show', $request->id) }}" class="btn btn-sm btn-primary text-white action-icon-btn"
                                               title="{{ __('vendor-dashboard.view') }}" data-bs-toggle="tooltip" data-bs-placement="top">
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
                    {{ $returnRequests->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-undo empty-icon mb-3"></i>
                        <h5 class="text-muted">{{ __('vendor-dashboard.no_return_requests_found') }}</h5>
                        <p class="text-muted mb-0">{{ __('vendor-dashboard.no_returns_yet') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
