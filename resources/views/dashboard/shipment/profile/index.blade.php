@extends('layouts.shipment')

@section('title', __('shipment-dashboard.profile'))
@section('page-title', __('shipment-dashboard.company_profile'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('shipment.dashboard') }}">@lang('shipment-dashboard.dashboard')</a></li>
    <li class="breadcrumb-item active">@lang('shipment-dashboard.profile')</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('shipment-dashboard.profile_information')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('shipment.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <!-- Logo Preview -->
                        <div class="text-center mb-3">
                            <img id="logo-preview"
                                src="{{ $company->logo ? asset($company->logo) : 'https://via.placeholder.com/120x120?text=Logo' }}"
                                alt="Logo"
                                class="img-thumbnail"
                                style="max-height: 120px;">
                        </div>

                        <!-- Logo -->
                        <div class="mb-3">
                            <label for="logo" class="form-label">@lang('shipment-dashboard.logo')</label>
                            <input type="file"
                                class="form-control @error('logo') is-invalid @enderror"
                                id="logo"
                                name="logo"
                                accept="image/*"
                                onchange="previewLogo(event)">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label required">@lang('shipment-dashboard.company_name')</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $company->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label required">@lang('shipment-dashboard.email')</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $company->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label required">@lang('shipment-dashboard.phone')</label>
                            <input type="text"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $company->phone) }}"
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label required">@lang('shipment-dashboard.address')</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address"
                                      name="address"
                                      rows="2"
                                      required>{{ old('address', $company->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">@lang('shipment-dashboard.description')</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3">{{ old('description', $company->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Facebook -->
                        <div class="mb-3">
                            <label for="facebook_url" class="form-label">@lang('shipment-dashboard.facebook_url')</label>
                            <input type="url"
                                   class="form-control @error('facebook_url') is-invalid @enderror"
                                   id="facebook_url"
                                   name="facebook_url"
                                   value="{{ old('facebook_url', $company->facebook_url) }}">
                            @error('facebook_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- WhatsApp -->
                        <div class="mb-3">
                            <label for="whatsapp_url" class="form-label">@lang('shipment-dashboard.whatsapp_url')</label>
                            <input type="text"
                                   class="form-control @error('whatsapp_url') is-invalid @enderror"
                                   id="whatsapp_url"
                                   name="whatsapp_url"
                                   value="{{ old('whatsapp_url', $company->whatsapp_url) }}">
                            @error('whatsapp_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('shipment.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> @lang('shipment-dashboard.back')
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> @lang('shipment-dashboard.update_profile')
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">@lang('shipment-dashboard.change_password')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('shipment.change-password') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="current_password" class="form-label required">@lang('shipment-dashboard.current_password')</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label required">@lang('shipment-dashboard.new_password')</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label required">@lang('shipment-dashboard.confirm_password')</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key"></i> @lang('shipment-dashboard.change_password')
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
<style>
    .required:after {
        content: " *";
        color: red;
    }
</style>
@endpush
@push('scripts')
<script>
    function previewLogo(event) {
        let reader = new FileReader();
        reader.onload = function(){
            let output = document.getElementById('logo-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush
