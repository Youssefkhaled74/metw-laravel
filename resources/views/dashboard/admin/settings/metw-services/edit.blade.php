@extends('layouts.admin')

@section('title', 'تعديل خدمة')
@section('page_title', 'تعديل خدمة')

@section('content')
@component('dashboard.admin.settings.partials.module-shell', [
    'kicker' => 'إدارة محتوى الموقع',
    'title' => 'تعديل خدمة',
    'description' => 'عدّل البيانات الحالية مع الحفاظ على نفس شكل وتجربة بقية صفحات الإدارة.',
    'actions' => [
        ['label' => 'محتوى الموقع', 'url' => route('admin.settings.website-content'), 'icon' => 'fas fa-arrow-right', 'class' => 'btn-outline-secondary'],
        ['label' => 'قائمة الخدمات', 'url' => route('admin.settings.metw-services.index'), 'icon' => 'fas fa-list', 'class' => 'btn-primary'],
    ],
])
    <div class="settings-content-card">
        <div class="card-header py-3">
            <h3 class="card-title">تعديل بيانات الخدمة: {{ $metwService->title_ar }}</h3>
        </div>
        <form action="{{ route('admin.settings.metw-services.update', $metwService->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                    <div class="form-group">
                        <label>العنوان (عربي) <span class="text-danger">*</span></label>
                        <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror" value="{{ old('title_ar', $metwService->title_ar) }}" required>
                        @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>العنوان (إنجليزي)</label>
                        <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en', $metwService->title_en) }}">
                        @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>الوصف (عربي)</label>
                        <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" rows="4">{{ old('description_ar', $metwService->description_ar) }}</textarea>
                        @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>الوصف (إنجليزي)</label>
                        <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror" rows="4">{{ old('description_en', $metwService->description_en) }}</textarea>
                        @error('description_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>الأيقونة (اسم أو مسار)</label>
                        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $metwService->icon) }}">
                        @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ترتيب العرض</label>
                                <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $metwService->sort_order) }}">
                                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $metwService->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">مفعل</label>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="card-footer d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">تحديث</button>
                <a href="{{ route('admin.settings.metw-services.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
@endcomponent
@endsection
