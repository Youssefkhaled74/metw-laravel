@extends('layouts.shipment')

@section('title', __('shipment-dashboard.edit_location'))
@section('page-title', __('shipment-dashboard.edit_location'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('shipment.dashboard') }}">@lang('shipment-dashboard.dashboard')</a></li>
    <li class="breadcrumb-item"><a href="{{ route('shipment.locations.index') }}">@lang('shipment-dashboard.locations')</a></li>
    <li class="breadcrumb-item active">@lang('shipment-dashboard.edit_location')</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('shipment-dashboard.edit_location_details')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('shipment.locations.update', $location->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label required">@lang('shipment-dashboard.location_name')</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $location->name) }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label required">@lang('shipment-dashboard.location_type')</label>
                            <select class="form-select @error('type') is-invalid @enderror"
                                    id="type"
                                    name="type"
                                    required>
                                @foreach(\App\Enum\LocationType::cases() as $case)
                                    <option value="{{ $case->value }}"
                                        {{ old('type', $location->type->value ?? $location->type) == $case->value ? 'selected' : '' }}>
                                        {{ ucfirst($case->value) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Parent -->
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">@lang('shipment-dashboard.parent_location_optional')</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror"
                                    id="parent_id"
                                    name="parent_id">
                                <option value="">@lang('shipment-dashboard.none')</option>
                                @foreach($locations as $loc)
                                    @if($loc->id !== $location->id) <!-- prevent selecting itself -->
                                        <option value="{{ $loc->id }}"
                                            {{ old('parent_id', $location->parent_id) == $loc->id ? 'selected' : '' }}>
                                            {{ $loc->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Path -->
                        <div class="mb-3">
                            <label for="path" class="form-label">@lang('shipment-dashboard.path_optional')</label>
                            <input type="text"
                                   class="form-control @error('path') is-invalid @enderror"
                                   id="path"
                                   name="path"
                                   value="{{ old('path', $location->path) }}">
                            @error('path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Active -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $location->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">@lang('shipment-dashboard.active')</label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('shipment.locations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> @lang('shipment-dashboard.back_to_list')
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> @lang('shipment-dashboard.update_location_btn')
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
