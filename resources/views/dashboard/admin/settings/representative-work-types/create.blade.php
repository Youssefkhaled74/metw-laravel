@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'إضافة نوع عمل' : 'Add Work Type')
@section('page-title', app()->getLocale() === 'ar' ? 'إضافة نوع عمل' : 'Add Work Type')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.representative-work-types.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الكود' : 'Code' }}</label>
                                <input type="text" name="code" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم بالإنجليزية' : 'English name' }}</label>
                                <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control @error('name_en') is-invalid @enderror" required>
                                @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Arabic name' }}</label>
                                <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-control @error('name_ar') is-invalid @enderror">
                                @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label>
                                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ app()->getLocale() === 'ar' ? 'الترتيب' : 'Sort order' }}</label>
                                <input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control @error('sort_order') is-invalid @enderror">
                                @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-block">{{ app()->getLocale() === 'ar' ? 'منفصل' : 'Exclusive' }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" class="form-check-input" id="is_exclusive" name="is_exclusive" value="1" {{ old('is_exclusive') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_exclusive">{{ app()->getLocale() === 'ar' ? 'تفعيل' : 'Enable' }}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-block">{{ app()->getLocale() === 'ar' ? 'حالة التفعيل' : 'Active status' }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('admin-dashboard.active') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.settings.representative-work-types.index') }}" class="btn btn-outline-secondary">{{ __('admin-dashboard.back_to_list') }}</a>
                            <button type="submit" class="btn btn-primary">{{ app()->getLocale() === 'ar' ? 'حفظ' : 'Save' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
