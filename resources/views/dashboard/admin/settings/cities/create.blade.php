@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'إضافة مدينة' : 'Add City')
@section('page-title', app()->getLocale() === 'ar' ? 'إضافة مدينة' : 'Add City')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.cities.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم المدينة بالإنجليزية' : 'City name in English' }}</label>
                            <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control @error('name_en') is-invalid @enderror" required>
                            @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'اسم المدينة بالعربية' : 'City name in Arabic' }}</label>
                            <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-control @error('name_ar') is-invalid @enderror" required>
                            @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ app()->getLocale() === 'ar' ? 'المحافظة' : 'Governorate' }}</label>
                            <select name="governorate_id" class="form-select @error('governorate_id') is-invalid @enderror" required>
                                <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المحافظة' : 'Select governorate' }}</option>
                                @foreach ($governorates as $governorate)
                                    <option value="{{ $governorate->id }}" @selected(old('governorate_id') == $governorate->id)>{{ $governorate->name_ar }}</option>
                                @endforeach
                            </select>
                            @error('governorate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">{{ __('admin-dashboard.active') }}</label>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.settings.cities.index') }}" class="btn btn-outline-secondary">{{ __('admin-dashboard.back_to_list') }}</a>
                            <button type="submit" class="btn btn-primary">{{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
