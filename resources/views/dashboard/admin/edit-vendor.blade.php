@extends('layouts.admin')

@section('title', 'Edit Vendor - ' . $vendor->name)
@section('page-title', 'Edit Vendor: ' . $vendor->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.vendors') }}">Vendors</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.vendors.show', $vendor->id) }}">{{ $vendor->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Vendor
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Vendor Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <!-- Logo -->
                            <div class="col-12 mb-4">
                                <div class="text-center">
                                    @if($vendor->logo)
                                        <img src="{{ asset(  $vendor->logo) }}"
                                             alt="{{ $vendor->name }}"
                                             class="img-thumbnail rounded-circle mb-3"
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                                             style="width: 150px; height: 150px; margin: 0 auto;">
                                            <i class="fas fa-store fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Change Logo</label>
                                        <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                               id="logo" name="logo" accept="image/*">
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Maximum file size: 2MB. Supported formats: JPG, PNG</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Vendor Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $vendor->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $vendor->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $vendor->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location Information -->
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="2" required>{{ old('address', $vendor->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="country_code" class="form-label">Country Code</label>
                                <input type="text" class="form-control @error('country_code') is-invalid @enderror"
                                       id="country_code" name="country_code" value="{{ old('country_code', $vendor->country_code) }}" required>
                                @error('country_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude" value="{{ old('latitude', $vendor->latitude) }}">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                       id="longitude" name="longitude" value="{{ old('longitude', $vendor->longitude) }}">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Vendor</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Vendor Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <span class="badge bg-{{ $vendor->is_active ? 'success' : 'danger' }} p-2">
                                {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <form action="{{ route('admin.vendors.toggle-status', $vendor->id) }}" method="POST" class="ms-auto">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $vendor->is_active ? 'warning' : 'success' }} btn-sm"
                                    onclick="return confirm('Are you sure you want to {{ $vendor->is_active ? 'deactivate' : 'activate' }} this vendor?')">
                                {{ $vendor->is_active ? 'Deactivate' : 'Activate' }} Vendor
                            </button>
                        </form>
                    </div>
                    <p class="text-muted small mb-0">
                        Last updated: {{ $vendor->updated_at->format('F j, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="text-muted mb-1">Total Products</h6>
                            <h4 class="mb-0">{{ $vendor->products_count }}</h4>
                        </div>
                        <div class="icon-circle bg-light">
                            <i class="fas fa-boxes text-primary"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Orders</h6>
                            <h4 class="mb-0">{{ $vendor->ecommerce_order_items_count }}</h4>
                        </div>
                        <div class="icon-circle bg-light">
                            <i class="fas fa-shopping-cart text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
    </style>
    @endpush
@endsection
