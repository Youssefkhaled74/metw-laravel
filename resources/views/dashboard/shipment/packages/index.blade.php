@extends('layouts.shipment')

@section('title', __('shipment-dashboard.packages_page_title'))
@section('page-title', __('shipment-dashboard.my_packages'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('shipment.dashboard') }}">@lang('shipment-dashboard.dashboard')</a></li>
    <li class="breadcrumb-item active">@lang('shipment-dashboard.packages')</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">@lang('shipment-dashboard.all_orders')</h5>
        </div>
        <div class="card-body">
            @if ($packages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('shipment-dashboard.package_number')</th>
                                        <th>@lang('shipment-dashboard.type_label')</th>
                                        <th>@lang('shipment-dashboard.size')</th>
                                        <th>@lang('shipment-dashboard.pickup')</th>
                                        <th>@lang('shipment-dashboard.dropoff')</th>
                                        <th>@lang('shipment-dashboard.sender_receiver')</th>
                                        <th>@lang('shipment-dashboard.images')</th>
                                        <th>@lang('shipment-dashboard.tracking')</th>
                                        <th>@lang('shipment-dashboard.created_at')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packages as $index => $package)
                                        <tr>
                                            <td>{{ $packages->firstItem() + $index }}</td>
                                            <td><strong>{{ $package->package_number }}</strong></td>

                                            <td>{{ $package->type?->name ?? '-' }}</td>
                                            <td>{{ $package->size?->title ?? '-' }}</td>

                                            <td>
                                                @if($package->pickupAddress)
                                                    <small>
                                                        {{ $package->pickupAddress->city ?? '' }},
                                                        {{ $package->pickupAddress->country ?? '' }}<br>
                                                        {{ $package->pickupAddress->address }}
                                                    </small>
                                                @endif
                                            </td>

                                            <td>
                                                @if($package->dropoffAddress)
                                                    <small>
                                                        {{ $package->dropoffAddress->city ?? '' }},
                                                        {{ $package->dropoffAddress->country ?? '' }}<br>
                                                        {{ $package->dropoffAddress->address }}
                                                    </small>
                                                @endif
                                            </td>

                                            <td>
                                                @if($package->packageDetails)
                                                    <small>
                                                        <strong>@lang('shipment-dashboard.sender'):</strong> {{ $package->packageDetails->sender_name }}<br>
                                                        <strong>@lang('shipment-dashboard.phone'):</strong> {{ $package->packageDetails->sender_phone }}<br>
                                                        <strong>@lang('shipment-dashboard.receiver'):</strong> {{ $package->packageDetails->recive_name }}<br>
                                                        <strong>@lang('shipment-dashboard.phone'):</strong> {{ $package->packageDetails->recive_phone }}
                                                    </small>
                                                @endif
                                            </td>

                                            <td>
                                                @if($package->images->count())
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($package->images as $img)
                                                            <img src="{{ asset($img->image) }}"
                                                                 alt="Package Image"
                                                                 class="img-thumbnail"
                                                                 style="width:50px;height:50px;object-fit:cover;">
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">@lang('shipment-dashboard.no_images')</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($package->trackings->count())
                                                    <ul class="list-unstyled mb-0 small">
                                                        @foreach($package->trackings as $tracking)
                                                            <li>
                                                                <strong>{{ ucfirst($tracking->status->value) }}</strong>
                                                                ({{ $tracking->occurred_at?->format('Y-m-d H:i') }})<br>
                                                                {{ $tracking->location }} - {{ $tracking->description }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">@lang('shipment-dashboard.no_tracking_short')</span>
                                                @endif
                                            </td>

                                            <td>{{ $package->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $packages->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">@lang('shipment-dashboard.no_packages_found')</h5>
                    <p class="text-muted">@lang('shipment-dashboard.no_packages_yet')</p>
                </div>
            @endif
        </div>
    </div>
@endsection
