@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_promo_code'))
@section('page-title', __('admin-dashboard.edit_promo_code'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.settings.promo_codes.index') }}">{{ __('admin-dashboard.promo_codes') }}</a>
    </li>
    <li class="breadcrumb-item active">
        {{ __('admin-dashboard.edit_promo_code') }}
    </li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('admin-dashboard.promo_code_details') }}</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.settings.promo_codes.update', $promo_code) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    {{-- Code --}}
                    <div class="mb-3">
                        <label for="code" class="form-label required">
                            {{ __('admin-dashboard.code') }}
                        </label>
                        <input type="text"
                               id="code"
                               name="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $promo_code->code) }}"
                               required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Discount Type --}}
                    <div class="mb-3">
                        <label for="discount_type" class="form-label required">
                            {{ __('admin-dashboard.discount_type') }}
                        </label>
                        <select id="discount_type"
                                name="discount_type"
                                class="form-select @error('discount_type') is-invalid @enderror"
                                required>
                            <option value="">{{ __('admin-dashboard.select_type') }}</option>

                            <option value="percentage"
                                {{ old('discount_type', optional($promo_code->discount_type)->value) === 'percentage' ? 'selected' : '' }}>
                                {{ __('admin-dashboard.percentage') }}
                            </option>

                            <option value="fixed"
                                {{ old('discount_type', optional($promo_code->discount_type)->value) === 'fixed' ? 'selected' : '' }}>
                                {{ __('admin-dashboard.fixed') }}
                            </option>
                        </select>
                        @error('discount_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Discount Value --}}
                    <div class="mb-3">
                        <label for="discount_value" class="form-label required">
                            {{ __('admin-dashboard.discount_value') }}
                        </label>
                        <input type="number"
                               step="0.01"
                               id="discount_value"
                               name="discount_value"
                               class="form-control @error('discount_value') is-invalid @enderror"
                               value="{{ old('discount_value', $promo_code->discount_value) }}"
                               required>
                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Valid From --}}
                    <div class="mb-3">
                        <label for="valid_from" class="form-label required">
                            {{ __('admin-dashboard.valid_from') }}
                        </label>
                        <input type="date"
                               id="valid_from"
                               name="valid_from"
                               class="form-control @error('valid_from') is-invalid @enderror"
                               value="{{ old('valid_from', optional($promo_code->valid_from)->format('Y-m-d')) }}"
                               required>
                        @error('valid_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Valid To --}}
                    <div class="mb-3">
                        <label for="valid_to" class="form-label required">
                            {{ __('admin-dashboard.valid_to') }}
                        </label>
                        <input type="date"
                               id="valid_to"
                               name="valid_to"
                               class="form-control @error('valid_to') is-invalid @enderror"
                               value="{{ old('valid_to', optional($promo_code->valid_to)->format('Y-m-d')) }}"
                               required>
                        @error('valid_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Max Uses --}}
                    <div class="mb-3">
                        <label for="max_uses" class="form-label">
                            {{ __('admin-dashboard.max_uses') }}
                        </label>
                        <input type="number"
                               id="max_uses"
                               name="max_uses"
                               class="form-control @error('max_uses') is-invalid @enderror"
                               value="{{ old('max_uses', $promo_code->max_uses) }}">
                        <small class="text-muted">
                            {{ __('admin-dashboard.leave_empty_for_unlimited_uses') }}
                        </small>
                        @error('max_uses')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- User Max Uses --}}
                    <div class="mb-3">
                        <label for="user_max_uses" class="form-label">
                            Max Uses per User
                        </label>
                        <input type="number"
                               id="user_max_uses"
                               name="user_max_uses"
                               class="form-control @error('user_max_uses') is-invalid @enderror"
                               value="{{ old('user_max_uses', $promo_code->user_max_uses) }}">
                        @error('user_max_uses')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Promo Code Type --}}
                    <div class="mb-3">
                        <label for="type" class="form-label required">
                            Promo Code Type
                        </label>
                        <select id="type"
                                name="type"
                                class="form-select @error('type') is-invalid @enderror"
                                required>
                            <option value="">-- Select Type --</option>

                            <option value="shipment"
                                {{ old('type', optional($promo_code->type)->value) === 'shipment' ? 'selected' : '' }}>
                                Shipment
                            </option>

                            <option value="ecommerce"
                                {{ old('type', optional($promo_code->type)->value) === 'ecommerce' ? 'selected' : '' }}>
                                E-commerce
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Active --}}
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   class="form-check-input"
                                   {{ old('is_active', $promo_code->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.settings.promo_codes.index') }}"
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
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
    .required::after {
        content: " *";
        color: red;
    }
</style>
@endpush
