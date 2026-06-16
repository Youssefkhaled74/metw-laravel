@extends('layouts.admin')

@section('title', __('admin-dashboard.return_request_details'))
@section('page-title', 'Return Request - #' . $returnRequest->id)

@section('page-actions')
    <a href="{{ route('admin.return-requests') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_return_requests') }}
    </a>
@endsection

@section('content')
    @php
        $customer = $returnRequest->order?->user ?? $returnRequest->user;
        $cashBack = $returnRequest->cashBack;
    @endphp
    <div class="row">
        <!-- Return Request Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.return_request_information') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>{{ __('admin-dashboard.return_id') }}:</strong></td>
                            <td>#{{ $returnRequest->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('admin-dashboard.order_number') }}:</strong></td>
                            <td>{{ $returnRequest->order->order_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('admin-dashboard.customer_information') }}:</strong></td>
                            <td>
                                @if($customer)
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $customer->name ?? $customer->username ?? __('admin-dashboard.not_available') }}</span>
                                        @if(!empty($customer->username))
                                            <small class="text-muted">{{ __('admin-dashboard.username') }}: {{ $customer->username }}</small>
                                        @endif
                                        @if(!empty($customer->email))
                                            <small class="text-muted">{{ __('admin-dashboard.email') }}: {{ $customer->email }}</small>
                                        @endif
                                        @if(!empty($customer->phone))
                                            <small class="text-muted">{{ __('admin-dashboard.phone') }}: {{ $customer->phone }}</small>
                                        @endif
                                    </div>
                                @else
                                    {{ __('admin-dashboard.not_available') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('admin-dashboard.status') }}:</strong></td>
                            <td>
                                <span
                                    class="badge bg-{{ $returnRequest->status->value === 'approved'
                                        ? 'success'
                                        : ($returnRequest->status->value === 'requested'
                                            ? 'warning'
                                            : ($returnRequest->status->value === 'rejected'
                                                ? 'danger'
                                                : 'info')) }}">
                                    {{ __('admin-dashboard.status_' . strtoupper($returnRequest->status->value)) }}
                                </span>

                            </td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('admin-dashboard.request_created_at') }}:</strong></td>
                            <td>{{ $returnRequest->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('admin-dashboard.updated_at') }}:</strong></td>
                            <td>{{ $returnRequest->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('admin-dashboard.request_reason') }}:</strong></td>
                            <td>{{ $returnRequest->reason ?? __('admin-dashboard.not_available') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Refund / Cash Back Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.refund_information') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td style="width: 220px"><strong>{{ __('admin-dashboard.refund_type') }}:</strong></td>
                            <td>
                                @if($returnRequest->refund_type)
                                    <span class="badge bg-info">
                                        {{ __('admin-dashboard.refund_type_' . strtoupper($returnRequest->refund_type)) }}
                                    </span>
                                @else
                                    {{ __('admin-dashboard.not_available') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('admin-dashboard.refund_amount') }}:</strong></td>
                            <td>{{ number_format((float) ($returnRequest->refund_amount ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('admin-dashboard.cash_back') }}:</strong></td>
                            <td>
                                @if($cashBack)
                                    <div class="d-flex flex-column">
                                        <span>{{ __('admin-dashboard.cash_back_method') }}: <strong>{{ $cashBack->cash_back_method ?? '-' }}</strong></span>
                                        <span>{{ __('admin-dashboard.cash_back_value') }}: <strong>{{ $cashBack->value }}</strong></span>
                                    </div>
                                @else
                                    <span class="text-muted">{{ __('admin-dashboard.no_cash_back') }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.returned_items') }}</h5>
                </div>
                <div class="card-body">
                    @if ($returnRequest->order->items->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('admin-dashboard.product') }}</th>
                                        <th>{{ __('admin-dashboard.quantity') }}</th>
                                        <th>{{ __('admin-dashboard.unit_price') }}</th>
                                        <th>{{ __('admin-dashboard.total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($returnRequest->order->items as $item)
                                        <tr>
                                            <td style="min-width:220px">
                                                <div class="d-flex align-items-center">
                                                    @if ($item->product && $item->product->media->count() > 0)
                                                        <img src="{{ asset($item->product->media->first()->url) }}"
                                                            alt="{{ $item->product->name }}" width="60"
                                                            class="rounded me-2 mb-2">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $item->product->name ?? __('admin-dashboard.not_available') }}</strong><br>
                                                        <small>{{ __('admin-dashboard.sku') }}: {{ $item->product->sku ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->quantity ?? 1 }}</td>
                                            <td>{{__('admin-dashboard.EGP')}}{{ number_format($item->unit_price ?? ($item->product->price ?? 0), 2) }}</td>
                                            <td>{{__('admin-dashboard.EGP')}}{{ number_format(($item->unit_price ?? ($item->product->price ?? 0)) * ($item->quantity ?? 1), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">{{ __('admin-dashboard.no_return_items_found') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.manage_return_request') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.return-requests.update-status', $returnRequest->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('admin-dashboard.update_status') }}</label>
                            <select name="status" id="status" class="form-select">
                                @foreach (\App\Enum\ReturnStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ $returnRequest->status === $status->value ? 'selected' : '' }}>
                                        {{ __('admin-dashboard.status_' . strtoupper($status->value)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sync-alt"></i> {{ __('admin-dashboard.update_status_button') }}
                        </button>
                    </form>
                    <br>
                    <br>
                    <form action="{{ route('admin.return-requests.update-reason', $returnRequest->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="reason" class="form-label">{{ __('admin-dashboard.update_reason') }}</label>
                            <select name="reason" id="reason" class="form-select">
                                @foreach (\App\Enum\ReturnReason::cases() as $reason)
                                    <option value="{{ $reason->value }}" {{ $returnRequest->reason === $reason->value ? 'selected' : '' }}>
                                        {{ __('admin-dashboard.reason_' . strtoupper($reason->value)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sync-alt"></i> {{ __('admin-dashboard.update_reason_button') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
