@extends('layouts.admin')

@section('title', __('admin-dashboard.create_coverage'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.create_coverage') }}</h1>
        <a href="{{ route('admin.settings.company-coverages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.back_to_list') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.settings.company-coverages.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="city_id">{{ __('admin-dashboard.city') }}</label>
                    <select name="city_id" id="city_id" class="form-control @error('city_id') is-invalid @enderror" required>
                        <option value="">{{ __('admin-dashboard.select_city') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price">{{ __('admin-dashboard.price') }}</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                           id="price" name="price" value="{{ old('price') }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="delivery_time">{{ __('admin-dashboard.delivery_time') }}</label>
                    <input type="text" class="form-control @error('delivery_time') is-invalid @enderror"
                           id="delivery_time" name="delivery_time" value="{{ old('delivery_time') }}" required>
                    @error('delivery_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.create_coverage_button') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
