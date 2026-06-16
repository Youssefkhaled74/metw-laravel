@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_config'))
@section('page-title', __('admin-dashboard.edit_config'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">
            {{ __('admin-dashboard.dashboard') }}
        </a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.configs.index') }}">
            {{ __('admin-dashboard.configs') }}
        </a>
    </li>
    <li class="breadcrumb-item active">
        {{ __('admin-dashboard.edit_config') }}
    </li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">{{ __('admin-dashboard.config_details') }}</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.configs.update', $config->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    {{-- Key --}}
                    <div class="mb-3">
                        <label for="key" class="form-label required">
                            {{ __('admin-dashboard.config_key') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('key') is-invalid @enderror"
                               id="key"
                               name="key"
                               value="{{ old('key', $config->key) }}"
                               disabled>

                        @error('key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Value --}}
                    <div class="mb-3">
                        <label for="value" class="form-label">
                            {{ __('admin-dashboard.config_value') }}
                        </label>
                        <textarea
                            class="form-control @error('value') is-invalid @enderror"
                            id="value"
                            name="value"
                            rows="3">{{ old('value', $config->value) }}</textarea>

                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Group --}}
                    {{-- <div class="mb-3">
                        <label for="group" class="form-label">
                            {{ __('admin-dashboard.config_group') }}
                        </label>
                        <input type="text"
                               class="form-control @error('group') is-invalid @enderror"
                               id="group"
                               name="group"
                               value="{{ old('group', $config->group) }}">

                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    {{-- Status --}}
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $config->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{ __('admin-dashboard.active') }}
                            </label>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="text-end">
                        <a href="{{ route('admin.configs.index') }}"
                           class="btn btn-light me-2">
                            {{ __('admin-dashboard.cancel') }}
                        </a>

                        <button type="submit" class="btn btn-primary">
                            {{ __('admin-dashboard.update_config') }}
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
