@extends('layouts.admin')

@section('title', __('admin-dashboard.create_shipment_company'))
@section('page-title', __('admin-dashboard.create_new_shipment_company'))

@section('page-actions')
    <a href="{{ route('admin.shipment-companies') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_companies') }}
    </a>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.company_information') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shipment-companies.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('admin-dashboard.company_name_label') }} <span
                                            class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('admin-dashboard.email_address_label') }} <span
                                            class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('admin-dashboard.phone_number_label') }} <span
                                            class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">{{ __('admin-dashboard.password_label') }} <span
                                            class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('admin-dashboard.address_label') }} <span
                                    class="text-danger">{{ __('admin-dashboard.required_field') }}</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('admin-dashboard.description_label') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="facebook" class="form-label">{{ __('admin-dashboard.facebook_url_label') }}</label>
                                    <input type="url" class="form-control @error('facebook') is-invalid @enderror"
                                        id="facebook" name="facebook" value="{{ old('facebook') }}"
                                        placeholder="{{ __('admin-dashboard.facebook_url_placeholder') }}">
                                    @error('facebook')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="whatsapp" class="form-label">{{ __('admin-dashboard.whatsapp_number_label') }}</label>
                                    <input type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                                        id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}"
                                        placeholder="{{ __('admin-dashboard.whatsapp_placeholder') }}">
                                    @error('whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.shipment-companies') }}" class="btn btn-secondary me-md-2">{{ __('admin-dashboard.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>{{ __('admin-dashboard.create_shipment_company') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
