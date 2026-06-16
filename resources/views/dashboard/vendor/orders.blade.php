@extends('layouts.vendor')

@section('title', __('vendor-dashboard.orders_management'))
@section('page-title', __('vendor-dashboard.orders_management'))

@section('page-actions')
    <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('vendor-dashboard.back_to_dashboard') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('vendor-dashboard.all_orders') }}</h5>
        </div>
        <div class="card-body">
            @if ($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('vendor-dashboard.order_number') }}</th>
                                <th>{{ __('vendor-dashboard.customer') }}</th>
                                <th>{{ __('vendor-dashboard.status') }}</th>
                                <th>{{ __('vendor-dashboard.total_amount') }}</th>
                                <th>{{ __('vendor-dashboard.items') }}</th>
                                <th>{{ __('vendor-dashboard.created_at') }}</th>
                                <th>{{ __('vendor-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        {{ $order->user->username ?? __('vendor-dashboard.not_available') }}
                                        @if ($order->user->email)
                                            <br><small class="text-muted">{{ $order->user->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $order->status === 'delivered'
                                                ? 'success'
                                                : ($order->status === 'pending'
                                                    ? 'warning'
                                                    : ($order->status === 'cancelled'
                                                        ? 'danger'
                                                        : 'info')) }}">
                                            {{ ucfirst(__("vendor-dashboard.statuses.$order->status")) }}
                                        </span>
                                    </td>
                                    <td>{{__('admin-dashboard.EGP')}}{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $order->items->count() }}</span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('vendor.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> {{ __('vendor-dashboard.view') }}
                                            </a>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#statusModal{{ $order->id }}">
                                                <i class="fas fa-edit"></i> {{ __('vendor-dashboard.status') }}
                                            </button>
                                        </div>

                                        <!-- Status Update Modal -->
                                        <div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('vendor.orders.update-status', $order->id) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('vendor-dashboard.update_order_status') }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="status{{ $order->id }}"
                                                                    class="form-label">{{ __('vendor-dashboard.status') }}</label>
                                                                <select class="form-select" id="status{{ $order->id }}"
                                                                    name="status" required>
                                                                    <option value="pending"
                                                                        {{ $order->status === 'pending' ? 'selected' : '' }}>
                                                                        {{ __('vendor-dashboard.pending') }}</option>
                                                                    <option value="confirmed"
                                                                        {{ $order->status === 'confirmed' ? 'selected' : '' }}>
                                                                        {{ __('vendor-dashboard.confirmed') }}</option>
                                                                    <option value="shipped"
                                                                        {{ $order->status === 'shipped' ? 'selected' : '' }}>
                                                                        {{ __('vendor-dashboard.shipped') }}</option>
                                                                    <option value="delivered"
                                                                        {{ $order->status === 'delivered' ? 'selected' : '' }}>
                                                                        {{ __('vendor-dashboard.delivered') }}</option>
                                                                    <option value="cancelled"
                                                                        {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                                                                        {{ __('vendor-dashboard.cancelled') }}</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="tracking_number{{ $order->id }}"
                                                                    class="form-label">{{ __('vendor-dashboard.tracking_number') }}</label>
                                                                <input type="text" class="form-control"
                                                                    id="tracking_number{{ $order->id }}"
                                                                    name="tracking_number"
                                                                    value="{{ $order->tracking_number }}">
                                                            </div>
                                                            <p><strong>{{ __('vendor-dashboard.order') }}:</strong> {{ $order->order_number }}</p>
                                                            <p><strong>{{ __('vendor-dashboard.customer') }}:</strong>
                                                                {{ $order->user->username ?? __('vendor-dashboard.not_available') }}</p>
                                                            <p><strong>{{ __('vendor-dashboard.current_status') }}:</strong>
                                                                <span
                                                                    class="badge bg-secondary">{{ ucfirst(__("vendor-dashboard.statuses.$order->status")) }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __('vendor-dashboard.cancel') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('vendor-dashboard.update_status') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('vendor-dashboard.no_orders_found') }}</h5>
                    <p class="text-muted">{{ __('vendor-dashboard.no_orders_yet') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
