@extends('layouts.admin')

@section('title', 'تعديل فيديو الموقع')
@section('page-title', 'تعديل فيديو الموقع')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل فيديو الموقع</h1>
        <a href="{{ route('admin.settings.website-videos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة للخلف
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.settings.website-videos.update', $websiteVideo) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="title" value="{{ old('title', $websiteVideo->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">العنوان بالعربية</label>
                        <input type="text" name="title_ar" value="{{ old('title_ar', $websiteVideo->title_ar) }}" class="form-control @error('title_ar') is-invalid @enderror">
                        @error('title_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $websiteVideo->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">الوصف بالعربية</label>
                        <textarea name="description_ar" rows="3" class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $websiteVideo->description_ar) }}</textarea>
                        @error('description_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ملف الفيديو</label>
                        <input type="file" name="video" accept="video/*" class="form-control @error('video') is-invalid @enderror">
                        @if($websiteVideo->video_path)
                            <small class="text-muted d-block mt-2">الفيديو الحالي: {{ $websiteVideo->video_path }}</small>
                        @endif
                        @error('video')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">صورة الغلاف</label>
                        <input type="file" name="thumbnail" accept="image/*" class="form-control @error('thumbnail') is-invalid @enderror">
                        @if($websiteVideo->thumbnail)
                            <small class="text-muted d-block mt-2">الصورة الحالية: {{ $websiteVideo->thumbnail }}</small>
                        @endif
                        @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">الترتيب</label>
                        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $websiteVideo->sort_order) }}" class="form-control @error('sort_order') is-invalid @enderror">
                        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $websiteVideo->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.settings.website-videos.index') }}" class="btn btn-outline-secondary">عدم الحفظ</a>
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
