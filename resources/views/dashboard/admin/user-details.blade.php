@extends('layouts.admin')

@section('title', __('admin-dashboard.users_management'))
@section('page-title', __('admin-dashboard.users_management'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.users') }}</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-user-circle me-2 text-primary"></i>
                        {{ __('admin-dashboard.user_details') }}
                    </h5>
                    <small class="text-muted">
                        <i class="fas fa-hashtag me-1"></i>
                        ID: {{ $user->id }}
                    </small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if($user->phone_verified_at)
                        <span class="badge bg-success px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ __('admin-dashboard.phone_verified') }}
                        </span>
                    @else
                        <span class="badge bg-danger px-3 py-2">
                            <i class="fas fa-times-circle me-1"></i>
                            {{ __('admin-dashboard.phone_not_verified') }}
                        </span>
                    @endif
                    @if($user->email_verified_at)
                        <span class="badge bg-success px-3 py-2">
                            <i class="fas fa-envelope me-1"></i>
                            {{ __('admin-dashboard.email_verified') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Left Column - User Profile -->
                <div class="col-md-4">
                    <div class="text-center mb-4">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}"
                                 alt="{{ $user->name }}"
                                 class="rounded-circle mb-3 border shadow-sm"
                                 style="width: 140px; height: 140px; object-fit: cover;">
                        @else
                            <div class="bg-gradient rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto border shadow-sm"
                                 style="width: 140px; height: 140px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-user fa-4x text-white"></i>
                            </div>
                        @endif
                        <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                        @if($user->username)
                            <small class="text-muted d-block mb-3">
                                <i class="fas fa-at me-1"></i>
                                {{ $user->username }}
                            </small>
                        @endif
                    </div>

                    <!-- User Stats -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row text-center g-3">
                                <div class="col-6">
                                    <div class="p-3 rounded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <div class="text-white mb-2">
                                            <i class="fas fa-shipping-fast fa-2x"></i>
                                        </div>
                                        <div class="h3 mb-0 text-white fw-bold">{{ $user->orders_count }}</div>
                                        <small class="text-white opacity-75">{{ __('admin-dashboard.shipments') }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                        <div class="text-white mb-2">
                                            <i class="fas fa-shopping-cart fa-2x"></i>
                                        </div>
                                        <div class="h3 mb-0 text-white fw-bold">{{ $user->ecommerce_orders_count }}</div>
                                        <small class="text-white opacity-75">{{ __('admin-dashboard.ecommerce_orders') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item border-0 d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        <span class="fw-medium">{{ __('admin-dashboard.join_date') }}</span>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">{{ $user->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @if($user->default_lang)
                                <div class="list-group-item border-0 d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span class="fw-medium">{{ __('admin-dashboard.default_language') }}</span>
                                    </div>
                                    <span class="badge bg-info px-3 py-2">{{ strtoupper($user->default_lang) }}</span>
                                </div>
                                @endif
                                @if($user->notifications_enabled !== null)
                                <div class="list-group-item border-0 d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <i class="fas fa-bell text-primary me-2"></i>
                                        <span class="fw-medium">{{ __('admin-dashboard.notifications') }}</span>
                                    </div>
                                    <span class="badge bg-{{ $user->notifications_enabled ? 'success' : 'secondary' }} px-3 py-2">
                                        <i class="fas fa-{{ $user->notifications_enabled ? 'check' : 'times' }}-circle me-1"></i>
                                        {{ $user->notifications_enabled ? __('admin-dashboard.enabled') : __('admin-dashboard.disabled') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Details -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                {{ __('admin-dashboard.contact_information') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted text-uppercase small mb-2 fw-semibold">
                                        <i class="fas fa-envelope me-1 text-primary"></i>
                                        {{ __('admin-dashboard.user_email') }}
                                    </label>
                                    <div class="d-flex align-items-center">
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none fw-medium">
                                            {{ $user->email }}
                                        </a>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success ms-2 px-2 py-1">
                                                <i class="fas fa-check-circle me-1"></i>
                                                {{ __('admin-dashboard.verified') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted text-uppercase small mb-2 fw-semibold">
                                        <i class="fas fa-phone me-1 text-primary"></i>
                                        {{ __('admin-dashboard.user_phone') }}
                                    </label>
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        @if($user->phone)
                                            <a href="tel:{{ $user->phone }}" class="text-decoration-none fw-medium">
                                                {{ $user->phone }}
                                            </a>
                                            <span class="badge bg-{{ $user->phone_verified_at ? 'success' : 'danger' }} px-2 py-1">
                                                <i class="fas fa-{{ $user->phone_verified_at ? 'check' : 'times' }}-circle me-1"></i>
                                                {{ __('admin-dashboard.' . ($user->phone_verified_at ? 'verified' : 'not_verified')) }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-phone-slash me-1"></i>
                                                {{ __('admin-dashboard.not_provided') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses Section -->
                    @if($user->addresses && $user->addresses->count())
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    {{ __('admin-dashboard.addresses') }}
                                    <span class="badge bg-primary ms-2 px-2 py-1">{{ $user->addresses->count() }}</span>
                                </h6>
                            </div>
                            <div class="card-body">

                                <div class="row g-3">
                                    @foreach($user->addresses as $address)
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm {{ $address->is_default ? 'border-primary border-2' : '' }}">
                                                <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <span class="badge bg-{{ $address->address_type === 'home' ? 'success' : ($address->address_type === 'work' ? 'primary' : 'secondary') }} px-2 py-1">
                                                            <i class="fas fa-{{ $address->address_type === 'home' ? 'home' : ($address->address_type === 'work' ? 'briefcase' : 'map-marker-alt') }} me-1"></i>
                                                            {{ ucfirst($address->address_type) }}
                                                        </span>
                                                        @if($address->is_default)
                                                            <span class="badge bg-warning text-dark ms-1 px-2 py-1">
                                                                <i class="fas fa-star me-1"></i>
                                                                {{ __('admin-dashboard.default') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $address->created_at->format('M d, Y') }}
                                                    </small>
                                                </div>

                                                <div class="address-details small">
                                                    @php
                                                        $lang = app()->getLocale();
                                                    @endphp
                                                    <div class="mb-1">
                                                        <i class="fas fa-road text-muted me-2"></i>
                                                        <strong>{{ $address->street_name }}</strong>
                                                    </div>

                                                    @if($address->zone)
                                                        <div class="mb-1">
                                                            <i class="fas fa-map-pin text-muted me-2"></i>
                                                            {{ optional($address->zone)->{"name_{$lang}"} ?? $address->zone->name_en }}
                                                        </div>
                                                    @endif

                                                    <div class="row">
                                                        @if($address->city)
                                                            <div class="col-6">
                                                                <small class="text-muted">{{ __('admin-dashboard.city') }}:</small>
                                                                <div>{{ optional($address->city)->{"name_{$lang}"} ?? $address->city->name_en }}</div>
                                                            </div>
                                                        @endif
                                                        @if($address->state)
                                                            <div class="col-6">
                                                                <small class="text-muted">{{ __('admin-dashboard.state') }}:</small>
                                                                <div>{{ optional($address->state)->{"name_{$lang}"} ?? $address->state->name_en }}</div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="row mt-2">
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('messages.building') }}:</small>
                                                            <div class="fw-medium">{{ $address->building }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('messages.floor') }}:</small>
                                                            <div class="fw-medium">{{ $address->floor }}</div>
                                                        </div>
                                                    </div>

                                                    @if($address->landmark)
                                                        <div class="mt-2">
                                                            <small class="text-muted">{{ __('messages.landmark') }}:</small>
                                                            <div>{{ $address->landmark }}</div>
                                                        </div>
                                                    @endif

                                                    @if($address->latitude && $address->longitude)
                                                        <div class="mt-3">
                                                            <a href="https://maps.google.com/?q={{ $address->latitude }},{{ $address->longitude }}"
                                                               target="_blank"
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-map-marked-alt me-1"></i>
                                                                {{ __('admin-dashboard.view_on_map') }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="card-footer bg-light border-top py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">
                                                            <i class="fas fa-hashtag me-1"></i>
                                                            ID: {{ $address->id }}
                                                        </small>
                                                        <small class="badge bg-secondary px-2 py-1">
                                                            {{ $address->is_village ? __('admin-dashboard.village') : __('admin-dashboard.city_area') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="text-center py-5">
                                    <i class="fas fa-map-marker-alt fa-4x text-muted mb-3"></i>
                                    <h6 class="text-muted mb-0">{{ __('admin-dashboard.no_addresses_found') }}</h6>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-top">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    {{ __('admin-dashboard.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12) !important;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
        padding: 1rem 1.25rem;
    }
    
    .address-details {
        line-height: 1.8;
    }
    
    .address-details .fa {
        width: 18px;
        text-align: center;
    }
    
    .list-group-item {
        border-color: #f0f0f0;
        transition: background-color 0.15s ease;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-weight: 500;
        font-size: 0.8125rem;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .card-header {
            padding: 0.75rem 1rem;
        }
    }
</style>
@endpush
