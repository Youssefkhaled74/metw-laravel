@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'تعديل محافظة' : 'Edit Governorate')
@section('page-title', app()->getLocale() === 'ar' ? 'تعديل محافظة' : 'Edit Governorate')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.states.update', $governorate->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'رقم المحافظة' : 'Governorate number' }}</label>
                            <input type="number" name="governorate_number" value="{{ old('governorate_number', $governorate->governorate_number) }}" class="form-control @error('governorate_number') is-invalid @enderror" required>
                            @error('governorate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم المحافظة' : 'Governorate name' }}</label>
                            <input type="text" name="name_ar" value="{{ old('name_ar', $governorate->name_ar) }}" class="form-control @error('name_ar') is-invalid @enderror" required>
                            @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'العاصمة' : 'Capital city' }}</label>
                            <select name="capital_city_id" class="form-select @error('capital_city_id') is-invalid @enderror">
                                <option value="">{{ app()->getLocale() === 'ar' ? 'اختر العاصمة' : 'Select capital city' }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" @selected(old('capital_city_id', $governorate->capital_city_id) == $city->id)>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('capital_city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $governorate->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">{{ __('admin-dashboard.active') }}</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.settings.states.index') }}" class="btn btn-outline-secondary">{{ __('admin-dashboard.back_to_list') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
