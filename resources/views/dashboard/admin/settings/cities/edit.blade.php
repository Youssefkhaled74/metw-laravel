@extends('layouts.admin')

@section('title', __('admin-dashboard.edit_city'))
@section('page-title', __('admin-dashboard.edit_city'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.cities.index') }}">{{ __('admin-dashboard.cities') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.edit_city') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('admin-dashboard.city_information') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.cities.update', $city->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="name_en" class="form-label required">{{ __('admin-dashboard.city_name_en') }}</label>
                        <input type="text"
                               class="form-control @error('name_en') is-invalid @enderror"
                               id="name_en"
                               name="name_en"
                               value="{{ old('name_en', $city->name_en) }}"
                               required>
                        @error('name_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name_ar" class="form-label required">{{ __('admin-dashboard.city_name_ar') }}</label>
                        <input type="text"
                               class="form-control @error('name_ar') is-invalid @enderror"
                               id="name_ar"
                               name="name_ar"
                               value="{{ old('name_ar', $city->name_ar) }}"
                               required>
                        @error('name_ar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="state_id" class="form-label required">{{ __('admin-dashboard.state') }}</label>
                        <select class="form-select @error('state_id') is-invalid @enderror"
                                id="state_id"
                                name="state_id"
                                required>
                            <option value="">{{ __('admin-dashboard.select_state') }}</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ old('state_id', $city->state_id) == $state->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar'
                                            ? ($state->name_ar ?? '-')
                                            : ($state->name_en ?? '-')}}
                                </option>
                            @endforeach
                        </select>
                        @error('state_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $city->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.settings.cities.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('admin-dashboard.update_city') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>.required:after { content:" *"; color:red; }</style>
@endpush
