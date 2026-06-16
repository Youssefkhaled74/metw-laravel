@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_cancel_reason'))
@section('page-title', __('admin-dashboard.edit_cancel_reason'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">
            {{ __('admin-dashboard.dashboard') }}
        </a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.settings.cancel-reasons.index') }}">
            {{ __('admin-dashboard.cancel_reasons') }}
        </a>
    </li>
    <li class="breadcrumb-item active">
        {{ __('admin-dashboard.edit_cancel_reason') }}
    </li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ __('admin-dashboard.cancel_reason_details') }}
                    </h5>
                </div>

                <div class="card-body">
                    <form
                        action="{{ route('admin.settings.cancel-reasons.update', $cancelReason->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PATCH')

                        {{-- Name (English) --}}
                        <div class="mb-3">
                            <label for="name_en" class="form-label required">
                                {{ __('admin-dashboard.name_en') }}
                            </label>

                            <input
                                type="text"
                                class="form-control @error('name_en') is-invalid @enderror"
                                id="name_en"
                                name="name_en"
                                value="{{ old('name_en', $cancelReason->name_en) }}"
                                required
                            >

                            @error('name_en')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Name (Arabic) --}}
                        <div class="mb-3">
                            <label for="name_ar" class="form-label required">
                                {{ __('admin-dashboard.name_ar') }}
                            </label>

                            <input
                                type="text"
                                class="form-control @error('name_ar') is-invalid @enderror"
                                id="name_ar"
                                name="name_ar"
                                value="{{ old('name_ar', $cancelReason->name_ar) }}"
                                required
                            >

                            @error('name_ar')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', $cancelReason->is_active) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="is_active">
                                    {{ __('admin-dashboard.active') }}
                                </label>
                            </div>

                            @error('is_active')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.settings.cancel-reasons.index') }}"
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                {{ __('admin-dashboard.back_to_list') }}
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                {{ __('admin-dashboard.update_cancel_reason') }}
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
