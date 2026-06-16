@extends('layouts.shipment')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3 class="mb-0">{{ __('shipment-dashboard.order_number_display', ['number' => $order->order_number]) }}
                <span class="ms-2 badge bg-secondary text-uppercase">{{ $order->status }}</span>
            </h3>
            <a href="{{ route('shipment.ecommerce.orders') }}" class="btn btn-secondary">{{ __('shipment-dashboard.back_to_orders') }}</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Order Summary Card -->
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('shipment-dashboard.order_summary') }}</h5>
                        <span class="badge bg-info">{{ __('shipment-dashboard.your_items') }}: {{ $companyItems->count() }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>{{ __('shipment-dashboard.order_details') }}</h6>
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.order_number') }}:</strong></td>
                                        <td>{{ $order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.tracking_number') }}:</strong></td>
                                        <td>{{ $order->tracking_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.order_date') }}:</strong></td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.payment_status') }}:</strong></td>
                                        <td>
                                            @php
                                                $status = $order->payment_status?->value ?? '';
                                            @endphp
                                            <span class="badge bg-{{ $status === 'paid' ? 'success' : ($status === 'failed' ? 'danger' : ($status === 'pending' ? 'warning' : 'secondary')) }}">
                                                {{ ucfirst($status ?: 'unknown') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.payment_method') }}:</strong></td>
                                        <td>{{ $order->payment_method ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>{{ __('shipment-dashboard.financial_summary') }}</h6>
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.items_subtotal') }}:</strong></td>
                                        <td>{{ number_format($companyTotals['subtotal'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.shipping_price') }}:</strong></td>
                                        <td>{{ number_format($companyTotals['shipping'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.discount') }}:</strong></td>
                                        <td>- {{ number_format($companyTotals['discount'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.product_discount') }}:</strong></td>
                                        <td>- {{ number_format($companyTotals['product_discount'] ?? 0, 2) }}</td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>{{ __('shipment-dashboard.total_price') }}:</strong></td>
                                        <td><strong>{{ number_format($companyTotals['total'] ?? 0, 2) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('shipment-dashboard.customer_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>{{ __('shipment-dashboard.customer_details') }}</h6>
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.customer_name') }}:</strong></td>
                                        <td>{{ $order->user->username ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.email') }}:</strong></td>
                                        <td>{{ $order->user->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.phone') }}:</strong></td>
                                        <td>{{ $order->user->phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('shipment-dashboard.registered_since') }}:</strong></td>
                                        <td>{{ $order->user->created_at->format('Y-m-d') ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>{{ __('shipment-dashboard.delivery_address') }}</h6>
                                @if ($order->userAddress)
                                    <div class="border rounded p-3">
                                        <strong>{{ $order->userAddress->full_name ?? '' }}</strong><br>
                                        {{ $order->userAddress->street_name ?? '' }},
                                        {{ $order->userAddress->building ?? '' }}<br>
                                        {{ $order->userAddress->city->name ?? '' }}<br>
                                        {{ $order->userAddress->phone ?? '' }}<br>
                                        <small class="text-muted">{{ __('shipment-dashboard.address_type') }}:
                                            {{ $order->userAddress->type ?? 'Standard' }}</small>
                                    </div>
                                @else
                                    <p class="text-muted">{{ __('shipment-dashboard.no_address_provided') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Items Card -->
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('shipment-dashboard.assigned_items') }} ({{ $companyItems->count() }} {{ __('shipment-dashboard.items') }})</h5>
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#itemsDetails">
                            {{ __('shipment-dashboard.show_details') }}
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($companyItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">#</th>
                                            <th>{{ __('shipment-dashboard.product') }}</th>
                                            <th>{{ __('shipment-dashboard.variant') }}</th>
                                            <th>{{ __('shipment-dashboard.qty') }}</th>
                                            <th>{{ __('shipment-dashboard.unit_price') }}</th>
                                            <th>{{ __('shipment-dashboard.shipping') }}</th>
                                            <th>{{ __('shipment-dashboard.distance') }}</th>
                                            <th>{{ __('shipment-dashboard.total') }}</th>
                                            <th>{{ __('shipment-dashboard.status') }}</th>
                                            <th>{{ __('shipment-dashboard.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($companyItems as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td style="min-width:250px">
                                                    <div class="d-flex align-items-center">
                                                        @if ($item->product && $item->product->media->count() > 0)
                                                            <img src="{{ asset($item->product->media->first()->url) }}"
                                                                alt="{{ $item->product->name }}" width="60"
                                                                class="rounded me-2">
                                                        @endif
                                                        <div>
                                                            <strong>{{ $item->product->name ?? 'N/A' }}</strong><br>
                                                            <small class="text-muted">{{ __('shipment-dashboard.sku') }}: {{ $item->product->sku ?? '-' }}</small><br>
                                                            @if ($item->product->vendor)
                                                                <small class="text-info">{{ __('shipment-dashboard.vendor') }}: {{ $item->product->vendor->name }}</small>
                                                            @endif
                                                            <!-- Optional: Show original name for reference -->
                                                            @if(app()->getLocale() !== 'en' && data_get($item->product, 'attributes.name'))
                                                                <br>
                                                                <small class="text-muted">
                                                                    (EN: {{ data_get($item->product, 'attributes.name') }})
                                                                </small>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($item->variant)
                                                        {{ $item->variant->name ?? '' }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        {{ $item->quantity ?? 1 }}<br>
                                                        @if ($item->returned_quantity > 0)
                                                            <small class="text-danger">
                                                                {{ __('shipment-dashboard.returned') }}: {{ $item->returned_quantity }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>{{ number_format($item->unit_price ?? ($item->product->price ?? 0), 2) }}</td>
                                                <td>{{ number_format($item->display_shipping_price ?? ($item->shipment_price_company ?? $item->shipment_price ?? 0), 2) }}</td>                                                <td>{{ number_format($item->distance ?? 0, 2) }} km</td>
                                                <td>
                                                    {{ number_format($item->final_price ?? 0, 2) }}<br>
                                                    @if ($item->returned_amount > 0)
                                                        <small class="text-danger">
                                                            {{ __('shipment-dashboard.refunded') }}: {{ number_format($item->returned_amount, 2) }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php($st = is_object($item->status) ? ($item->status->value ?? '') : ($item->status ?? ''))
                                                    <span class="badge bg-{{ $st === 'delivered' ? 'success' : ($st === 'accepted' ? 'info' : ($st === 'pickup' ? 'warning' : ($st === 'on_way' ? 'primary' : 'secondary'))) }}">
                                                        {{ ucfirst($st) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ( $st !== 'delivered')
                                                        @if ($st === 'accepted')
                                                            <form method="POST" action="{{ route('shipment.ecommerce.orders.update-item-status', $order->id) }}" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                                <input type="hidden" name="status" value="pickup">
                                                                <button type="submit" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-box-open me-1"></i>{{ __('shipment-dashboard.pickup') }}
                                                                </button>
                                                            </form>
                                                        @elseif($st === 'pickup')
                                                            <form method="POST" action="{{ route('shipment.ecommerce.orders.update-item-status', $order->id) }}" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                                <input type="hidden" name="status" value="on_way">
                                                                <button type="submit" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-truck me-1"></i>{{ __('shipment-dashboard.on_way') }}
                                                                </button>
                                                            </form>
                                                        @elseif($st === 'on_way')
                                                            <form method="POST" action="{{ route('shipment.ecommerce.orders.update-item-status', $order->id) }}" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                                <input type="hidden" name="status" value="delivered">
                                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('shipment-dashboard.confirm_delivery') }}');">
                                                                    <i class="fas fa-check-circle me-1"></i>{{ __('shipment-dashboard.delivered') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @elseif($st === 'delivered')
                                                        <span class="badge bg-success">{{ __('shipment-dashboard.completed') }}</span>
                                                    @else
                                                        <small class="text-muted">{{ __('shipment-dashboard.awaiting_payment') }}</small>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- Expanded Details Row -->
                                            <tr class="collapse" id="itemsDetails">
                                                <td colspan="10" class="p-3 bg-light">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>{{ __('shipment-dashboard.pickup_location') }}</h6>
                                                            @php($pickup = $item->pickupBranch ?: optional($item->product)->branch)
                                                            @if ($pickup)
                                                                <strong>{{ $pickup->name ?? 'Branch' }}</strong><br>
                                                                <small>{{ $pickup->full_address ?? '' }}</small><br>
                                                                <small class="text-muted">{{ $pickup->phone ?? '' }}</small>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>{{ __('shipment-dashboard.dropoff_location') }}</h6>
                                                            @if ($order->userAddress)
                                                                <strong>{{ __('shipment-dashboard.delivery_address') }}</strong><br>
                                                                {{ $order->userAddress->street_name ?? '' }} {{ $order->userAddress->building ?? '' }}<br>
                                                                <small>{{ $order->userAddress->city->name ?? '' }}</small><br>
                                                                <small class="text-muted">{{ $order->userAddress->phone ?? '' }}</small>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h6>{{ __('shipment-dashboard.breakdown') }}</h6>
                                                            <div class="row small">
                                                                <div class="col-3">
                                                                    <strong>{{ __('shipment-dashboard.product_price') }}:</strong><br>
                                                                    {{ number_format($item->breakdown['product_price'] ?? 0, 2) }}
                                                                </div>
                                                                <div class="col-3">
                                                                    <strong>{{ __('shipment-dashboard.shipping_price') }}:</strong><br>
                                                                    {{ number_format($item->breakdown['shipping_price'] ?? 0, 2) }}
                                                                </div>
                                                                <div class="col-3">
                                                                    <strong>{{ __('shipment-dashboard.discount') }}:</strong><br>
                                                                    -{{ number_format($item->breakdown['discount'] ?? 0, 2) }}
                                                                </div>
                                                                <div class="col-3">
                                                                    <strong>{{ __('shipment-dashboard.final_price') }}:</strong><br>
                                                                    {{ number_format($item->breakdown['final_price'] ?? 0, 2) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">{{ __('shipment-dashboard.no_items_assigned') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Delivery Timeline Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('shipment-dashboard.delivery_timeline') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item {{ $order->estimated_delivery_from ? 'active' : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>{{ __('shipment-dashboard.estimated_delivery') }}</h6>
                                    <p class="mb-0">
                                        @if ($order->estimated_delivery_from && $order->estimated_delivery_to)
                                            {{ $order->estimated_delivery_from->format('Y-m-d') }} - {{ $order->estimated_delivery_to->format('Y-m-d') }}
                                        @else
                                            {{ __('shipment-dashboard.not_set') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="timeline-item {{ $order->actual_delivery_date ? 'completed' : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6>{{ __('shipment-dashboard.actual_delivery') }}</h6>
                                    <p class="mb-0">
                                        {{ $order->actual_delivery_date ? $order->actual_delivery_date->format('Y-m-d H:i') : __('shipment-dashboard.pending') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Company Financial Summary Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('shipment-dashboard.financial_summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-7">{{ __('shipment-dashboard.items_count') }}</dt>
                            <dd class="col-5 text-end">{{ $companyTotals['items_count'] ?? 0 }}</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.subtotal') }}</dt>
                            <dd class="col-5 text-end">{{ number_format($companyTotals['subtotal'] ?? 0, 2) }}</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.product_discount') }}</dt>
                            <dd class="col-5 text-end">-{{ number_format($companyTotals['product_discount'] ?? 0, 2) }}</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.order_discount') }}</dt>
                            <dd class="col-5 text-end">-{{ number_format($companyTotals['discount'] ?? 0, 2) }}</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.shipping_price') }}</dt>
                            <dd class="col-5 text-end">{{ number_format($companyTotals['shipping'] ?? 0, 2) }}</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.total_distance') }}</dt>
                            <dd class="col-5 text-end">{{ number_format($companyTotals['distance'] ?? 0, 2) }} km</dd>

                            <hr class="my-2">

                            <dt class="col-7"><strong>{{ __('shipment-dashboard.grand_total') }}</strong></dt>
                            <dd class="col-5 text-end"><strong>{{ number_format($companyTotals['total'] ?? 0, 2) }}</strong></dd>

                            <dt class="col-7">{{ __('shipment-dashboard.paid_amount') }}</dt>
                            <dd class="col-5 text-end">{{ number_format($companyTotals['paid'] ?? 0, 2) }}</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.remaining_amount') }}</dt>
                            <dd class="col-5 text-end">{{ number_format($companyTotals['remaining'] ?? 0, 2) }}</dd>

                            <hr class="my-2">

                            <dt class="col-7 text-danger">{{ __('shipment-dashboard.returned_amount') }}</dt>
                            <dd class="col-5 text-end text-danger">-{{ number_format($companyTotals['total_returned'] ?? 0, 2) }}</dd>

                            <dt class="col-7"><strong>{{ __('shipment-dashboard.net_total') }}</strong></dt>
                            <dd class="col-5 text-end"><strong>{{ number_format($companyTotals['net_total'] ?? 0, 2) }}</strong></dd>
                        </dl>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('shipment-dashboard.statistics') }}</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-7">{{ __('shipment-dashboard.total_quantity') }}</dt>
                            <dd class="col-5 text-end">{{ $summaryData['total_quantity'] ?? 0 }}</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.net_quantity') }}</dt>
                            <dd class="col-5 text-end">{{ $summaryData['net_quantity'] ?? 0 }}</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.returned_items') }}</dt>
                            <dd class="col-5 text-end text-danger">{{ $summaryData['total_returns'] ?? 0 }}</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.estimated_weight') }}</dt>
                            <dd class="col-5 text-end">{{ number_format($summaryData['package_weight'] ?? 0, 2) }} kg</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.average_distance') }}</dt>
                            <dd class="col-5 text-end">{{ number_format($summaryData['average_distance'] ?? 0, 2) }} km</dd>

                            <dt class="col-7">{{ __('shipment-dashboard.items_value') }}</dt>
                            <dd class="col-5 text-end">{{ number_format($companyTotals['total'] ?? 0, 2) }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Actions Card -->
                @if ($order->status === 'pending' || $order->items->every(fn($item) => $item->is_shipment_accepted == 1) == false)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('shipment-dashboard.accept_calculate_shipping') }}</h5>
                            <form method="POST" action="{{ route('shipment.ecommerce.orders.accept', $order->id) }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ __('shipment-dashboard.estimated_delivery_days') }}
                                    </label>
                                    <input
                                        type="number"
                                        name="estimated_days"
                                        class="form-control"
                                        min="1"
                                        max="30"
                                        required
                                        placeholder="مثال: 3 أيام">
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    {{ __('shipment-dashboard.accept_order') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-danger">{{ __('shipment-dashboard.cancel_order') }}</h5>
                            <form method="POST" action="{{ route('shipment.ecommerce.orders.cancel', $order->id) }}"
                                onsubmit="return confirm('{{ __('shipment-dashboard.cancel_confirmation') }}');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">{{ __('shipment-dashboard.cancel_order') }}</button>
                            </form>
                            <div class="form-text mt-2">{{ __('shipment-dashboard.cancel_warning') }}</div>
                        </div>
                    </div>
                @elseif(in_array($order->status, ['accepted', 'pickup', 'on_way', 'delivered']))
                    <div class="alert alert-info">{{ __('shipment-dashboard.order_accepted_info') }}</div>
                @elseif($order->status === 'cancelled')
                    <div class="alert alert-warning">{{ __('shipment-dashboard.order_cancelled_info') }}</div>
                @endif

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('shipment-dashboard.quick_actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>{{ __('shipment-dashboard.print_invoice') }}
                            </a>
                            {{-- <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#notesModal">
                                <i class="fas fa-notes me-2"></i>{{ __('shipment-dashboard.add_notes') }}
                            </button> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Modal -->
    {{-- <div class="modal fade" id="notesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('shipment-dashboard.add_order_notes') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="notesForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('shipment-dashboard.notes') }}</label>
                            <textarea class="form-control" rows="4" placeholder="{{ __('shipment-dashboard.enter_notes_here') }}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('shipment-dashboard.close') }}</button>
                    <button type="button" class="btn btn-primary">{{ __('shipment-dashboard.save_notes') }}</button>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #ddd;
    }

    .timeline-item.active .timeline-marker {
        background-color: #28a745;
    }

    .timeline-item.completed .timeline-marker {
        background-color: #007bff;
    }

    .timeline-content h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .timeline-content p {
        margin-bottom: 0;
        color: #666;
    }

    .table th {
        white-space: nowrap;
    }

    .badge {
        font-size: 0.75em;
    }
</style>
@endsection
