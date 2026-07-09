@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_rejection_reason'))
@section('page-title', __('admin-dashboard.edit_rejection_reason'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">
            {{ __('admin-dashboard.dashboard') }}
        </a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.settings.rejection-reasons.index') }}">
            {{ __('admin-dashboard.rejection_reasons') }}
        </a>
    </li>
    <li class="breadcrumb-item active">
        {{ __('admin-dashboard.edit_rejection_reason') }}
    </li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ __('admin-dashboard.rejection_reason_details') }}
                    </h5>
                </div>

                <div class="card-body">
                    <form
                        action="{{ route('admin.settings.rejection-reasons.update', $rejectionReason->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="reason_text" class="form-label required">
                                {{ __('admin-dashboard.reason_text') }}
                            </label>

                            <input
                                type="text"
                                class="form-control @error('reason_text') is-invalid @enderror"
                                id="reason_text"
                                name="reason_text"
                                value="{{ old('reason_text', $rejectionReason->reason_text) }}"
                                required
                            >

                            @error('reason_text')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', $rejectionReason->is_active) ? 'checked' : '' }}
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

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.settings.rejection-reasons.index') }}"
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                {{ __('admin-dashboard.back_to_list') }}
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                {{ __('admin-dashboard.update_rejection_reason') }}
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
