@extends('layouts.vendor')

@section('title', __('vendor-dashboard.branch_details'))
@section('page-title', __('vendor-dashboard.branch_details'))

@section('content')
<style>
    .info-card {
        border-left: 4px solid #007bff;
        background: #f8f9fa;
    }
    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1rem;
        color: #212529;
        margin-bottom: 1rem;
    }
    .badge-xl {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }
    .map-container {
        height: 400px;
        border-radius: 8px;
        overflow: hidden;
    }
    .section-title {
        border-bottom: 2px solid #007bff;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="mb-2">{{ $branch->name }}</h3>
                            <div class="d-flex gap-2">
                                @if($branch->status)
                                    <span class="badge badge-xl bg-success">
                                        <i class="fas fa-check-circle"></i> {{ __('vendor-dashboard.active') }}
                                    </span>
                                @else
                                    <span class="badge badge-xl bg-danger">
                                        <i class="fas fa-times-circle"></i> {{ __('vendor-dashboard.inactive') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('vendor.branches.edit', $branch->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> {{ __('vendor-dashboard.edit') }}
                            </a>
                            <a href="{{ route('vendor.branches') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('vendor-dashboard.back') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Location Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 info-card">
                        <div class="card-body">
                            <h5 class="section-title">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                                {{ __('vendor-dashboard.location_information') }}
                            </h5>

                            <div class="mb-3">
                                <div class="info-label">
                                    <i class="fas fa-map"></i> {{ __('vendor-dashboard.state') }}
                                </div>
                                <div class="info-value">
                                    {{ $branch->state->name ?? __('vendor-dashboard.not_available') }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">
                                    <i class="fas fa-city"></i> {{ __('vendor-dashboard.city') }}
                                </div>
                                <div class="info-value">
                                    {{ $branch->city->name ?? __('vendor-dashboard.not_available') }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">
                                    <i class="fas fa-map-pin"></i> {{ __('vendor-dashboard.zone') }}
                                </div>
                                <div class="info-value">
                                    {{ app()->getLocale() == 'ar' ? $branch->zone->name_ar : $branch->zone->name_en }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Building Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 info-card">
                        <div class="card-body">
                            <h5 class="section-title">
                                <i class="fas fa-building text-primary"></i>
                                {{ __('vendor-dashboard.building_information') }}
                            </h5>

                            <div class="mb-3">
                                <div class="info-label">
                                    <i class="fas fa-hashtag"></i> {{ __('vendor-dashboard.building_number') }}
                                </div>
                                <div class="info-value">
                                    {{ $branch->building }}
                                </div>
                            </div>

                            @if($branch->building_name)
                                <div class="mb-3">
                                    <div class="info-label">
                                        <i class="fas fa-tag"></i> {{ __('vendor-dashboard.building_name') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $branch->building_name }}
                                    </div>
                                </div>
                            @endif

                            @if($branch->floor)
                                <div class="mb-3">
                                    <div class="info-label">
                                        <i class="fas fa-layer-group"></i> {{ __('vendor-dashboard.floor') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $branch->floor }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Street Details -->
                <div class="col-md-12 mb-4">
                    <div class="card info-card">
                        <div class="card-body">
                            <h5 class="section-title">
                                <i class="fas fa-road text-primary"></i>
                                {{ __('vendor-dashboard.street_details') }}
                            </h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="info-label">
                                        {{ __('vendor-dashboard.main_street') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $branch->street_main }}
                                    </div>
                                </div>

                                @if($branch->street_sub)
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">
                                            {{ __('vendor-dashboard.sub_street') }}
                                        </div>
                                        <div class="info-value">
                                            {{ $branch->street_sub }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i>
                                <strong>{{ __('vendor-dashboard.full_address') }}:</strong><br>
                                {{ $branch->full_address }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Section -->
                @if($branch->latitude && $branch->longitude)
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="section-title">
                                    <i class="fas fa-map-marked-alt text-primary"></i>
                                    {{ __('vendor-dashboard.location_on_map') }}
                                </h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="info-label">
                                            <i class="fas fa-compass"></i> {{ __('vendor-dashboard.latitude') }}
                                        </div>
                                        <div class="info-value">
                                            {{ $branch->latitude }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">
                                            <i class="fas fa-compass"></i> {{ __('vendor-dashboard.longitude') }}
                                        </div>
                                        <div class="info-value">
                                            {{ $branch->longitude }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Google Maps Embed -->
                                <div class="map-container">
                                    <iframe
                                        width="100%"
                                        height="100%"
                                        frameborder="0"
                                        style="border:0"
                                        src="https://maps.google.com/maps?q={{ $branch->latitude }},{{ $branch->longitude }}&t=&z=15&ie=UTF8&iwloc=&output=embed"
                                        allowfullscreen>
                                    </iframe>
                                </div>

                                <div class="mt-3 text-center">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $branch->latitude }},{{ $branch->longitude }}"
                                       target="_blank"
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i>
                                        {{ __('vendor-dashboard.open_in_google_maps') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="section-title">
                                <i class="fas fa-clock text-primary"></i>
                                {{ __('vendor-dashboard.timestamps') }}
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-plus"></i> {{ __('vendor-dashboard.created_at') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $branch->created_at->format('d M Y, h:i A') }}
                                        <small class="text-muted">({{ $branch->created_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-check"></i> {{ __('vendor-dashboard.updated_at') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $branch->updated_at->format('d M Y, h:i A') }}
                                        <small class="text-muted">({{ $branch->updated_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('vendor.branches') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('vendor-dashboard.back_to_list') }}
                        </a>

                        <div>
                            <a href="{{ route('vendor.branches.edit', $branch->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> {{ __('vendor-dashboard.edit_branch') }}
                            </a>

                            <form action="{{ route('vendor.branches.destroy', $branch->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('{{ __('vendor-dashboard.confirm_delete_branch') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> {{ __('vendor-dashboard.delete_branch') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
