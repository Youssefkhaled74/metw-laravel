@extends('layouts.vendor')

@section('title', __('vendor-dashboard.profile'))
@section('page-title', __('vendor-dashboard.my_profile'))

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Profile Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('vendor-dashboard.profile_information') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('vendor-dashboard.name') }}</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $vendor->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('vendor-dashboard.email_address') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $vendor->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('vendor-dashboard.phone_number') }}</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', $vendor->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="logo" class="form-label">{{ __('vendor-dashboard.store_logo') }}</label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                        id="logo" name="logo" accept="image/*">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($vendor->logo)
                                        <div class="mt-2">
                                            <img src="{{ asset( $vendor->logo) }}" alt="Store Logo" class="img-thumbnail" style="max-height: 100px">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('vendor-dashboard.address') }}</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" rows="3" required>{{ old('address', $vendor->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('vendor-dashboard.update_profile') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('vendor-dashboard.change_password') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.change-password') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('vendor-dashboard.current_password') }}</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('vendor-dashboard.new_password') }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('vendor-dashboard.confirm_new_password') }}</label>
                            <input type="password" class="form-control"
                                id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-1"></i> {{ __('vendor-dashboard.change_password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Profile Summary -->
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($vendor->logo)
                            <img src="{{ asset($vendor->logo) }}" alt="Store Logo"
                                class="img-fluid rounded-circle mb-3" style="max-width: 150px">
                        @else
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 150px; height: 150px">
                                <i class="fas fa-store fa-4x text-muted"></i>
                            </div>
                        @endif
                        <h4 class="mb-0">{{ $vendor->name }}</h4>
                        <p class="text-muted">{{ __('vendor-dashboard.vendor') }}</p>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <small class="text-muted d-block">{{ __('vendor-dashboard.email_address') }}</small>
                            <div>{{ $vendor->email }}</div>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted d-block">{{ __('vendor-dashboard.phone_number') }}</small>
                            <div>{{ $vendor->phone }}</div>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted d-block">{{ __('vendor-dashboard.address') }}</small>
                            <div>{{ $vendor->address }}</div>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted d-block">{{ __('vendor-dashboard.member_since') }}</small>
                            <div>{{ $vendor->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
