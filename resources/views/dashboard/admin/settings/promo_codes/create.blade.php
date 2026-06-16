@extends('layouts.admin')

@section('title', __('admin-dashboard.create_promo_code'))
@section('page-title', __('admin-dashboard.create_promo_code'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.promo_codes.index') }}">{{ __('admin-dashboard.promo_codes') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.create_promo_code') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admin-dashboard.promo_code_details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.promo_codes.store') }}" method="POST">
                        @csrf

                        {{-- Code --}}
                        <div class="mb-3">
                            <label for="code" class="form-label required">{{ __('admin-dashboard.code') }} <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('code') is-invalid @enderror"
                                   id="code"
                                   name="code"
                                   value="{{ old('code') }}"
                                   required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Discount Type --}}
                        <div class="mb-3">
                            <label for="discount_type" class="form-label required">{{ __('admin-dashboard.discount_type') }} <span class="text-danger">*</span></label>
                            <select id="discount_type"
                                    name="discount_type"
                                    class="form-select @error('discount_type') is-invalid @enderror"
                                    required>
                                <option value="">{{ __('admin-dashboard.select_type') }}</option>
                                <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>{{ __('admin-dashboard.percentage') }}</option>
                                <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>{{ __('admin-dashboard.fixed') }}</option>
                            </select>
                            @error('discount_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Discount Value --}}
                        <div class="mb-3">
                            <label for="discount_value" class="form-label required">{{ __('admin-dashboard.discount_value') }} <span class="text-danger">*</span></label>
                            <input type="number"
                                   step="0.01"
                                   class="form-control @error('discount_value') is-invalid @enderror"
                                   id="discount_value"
                                   name="discount_value"
                                   value="{{ old('discount_value') }}"
                                   required>
                            @error('discount_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Valid From --}}
                        <div class="mb-3">
                            <label for="valid_from" class="form-label required">{{ __('admin-dashboard.valid_from') }} <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('valid_from') is-invalid @enderror"
                                   id="valid_from"
                                   name="valid_from"
                                   value="{{ old('valid_from') }}"
                                   required>
                            @error('valid_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Valid To --}}
                        <div class="mb-3">
                            <label for="valid_to" class="form-label required">{{ __('admin-dashboard.valid_to') }} <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control @error('valid_to') is-invalid @enderror"
                                   id="valid_to"
                                   name="valid_to"
                                   value="{{ old('valid_to') }}"
                                   required>
                            @error('valid_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Max Uses --}}
                        <div class="mb-3">
                            <label for="max_uses" class="form-label">{{ __('admin-dashboard.max_uses_help') }}</label>
                            <input type="number"
                                   class="form-control @error('max_uses') is-invalid @enderror"
                                   id="max_uses"
                                   name="max_uses"
                                   value="{{ old('max_uses') }}">
                            @error('max_uses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- User Max Uses --}}
                        <div class="mb-3">
                            <label for="user_max_uses" class="form-label">{{ __('admin-dashboard.user_max_uses_help') }}</label>
                            <input type="number"
                                   class="form-control @error('user_max_uses') is-invalid @enderror"
                                   id="user_max_uses"
                                   name="user_max_uses"
                                   value="{{ old('user_max_uses') }}">
                            @error('user_max_uses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div class="mb-3">
                            <label for="type" class="form-label required">{{ __('admin-dashboard.type') }} <span class="text-danger">*</span></label>
                            <select id="type"
                                    name="type"
                                    class="form-select @error('type') is-invalid @enderror"
                                    required>
                                <option value="">{{ __('admin-dashboard.select_type') }}</option>
                                <option value="shipment" {{ old('type') === 'shipment' ? 'selected' : '' }}>{{ __('admin-dashboard.shipment') }}</option>
                                <option value="ecommerce" {{ old('type') === 'ecommerce' ? 'selected' : '' }}>{{ __('admin-dashboard.ecommerce') }}</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Active --}}
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.settings.promo_codes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('admin-dashboard.save_promo_code') }}
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
