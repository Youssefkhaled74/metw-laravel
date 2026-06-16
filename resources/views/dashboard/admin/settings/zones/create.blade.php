@extends('layouts.admin')

@section('title', __('admin-dashboard.create_zone'))
@section('page-title', __('admin-dashboard.create_zone'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin-dashboard.dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.zones.index') }}">{{ __('admin-dashboard.zones') }}</a></li>
    <li class="breadcrumb-item active">{{ __('admin-dashboard.create_zone') }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">{{ __('admin-dashboard.zone_details') }}</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.settings.zones.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name_en" class="form-label required">{{ __('admin-dashboard.zone_name_en') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name_en') is-invalid @enderror" id="name_en" name="name_en" value="{{ old('name_en') }}" required>
                        @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="name_ar" class="form-label required">{{ __('admin-dashboard.zone_name_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name_ar') is-invalid @enderror" id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required>
                        @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="city_id" class="form-label required">{{ __('admin-dashboard.zone_city') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required>
                            <option value="">{{ __('admin-dashboard.select_city') }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar'
                                            ? ($city->name_ar ?? '-')
                                            : ($city->name_en ?? '-') }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.settings.zones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('admin-dashboard.create_zone') }}
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
