@extends('layouts.admin')

@section('title', 'فيديوهات الموقع')
@section('page-title', 'فيديوهات الموقع')

@section('content')
@component('dashboard.admin.settings.partials.module-shell', [
    'kicker' => 'إدارة محتوى الموقع',
    'title' => 'فيديوهات الموقع',
    'description' => 'إدارة الفيديوهات التي تظهر في الصفحة العامة بأسلوب موحد وواضح.',
    'actions' => [
        ['label' => 'محتوى الموقع', 'url' => route('admin.settings.website-content'), 'icon' => 'fas fa-arrow-right', 'class' => 'btn-outline-secondary'],
        ['label' => 'إضافة فيديو جديد', 'url' => route('admin.settings.website-videos.create'), 'icon' => 'fas fa-plus', 'class' => 'btn-primary'],
    ],
])
    <div class="settings-content-card">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">فيديوهات الموقع</h5>
            <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                {{ $videos->count() }} / {{ $videos->total() }}
            </span>
        </div>

        <div class="card-body p-0 table-wrap">
            @if($videos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 data-table">
                        <thead>
                        <tr>
                            <th class="text-nowrap">الفيديو</th>
                            <th class="text-nowrap">العنوان</th>
                            <th class="text-nowrap mobile-hide">الترتيب</th>
                            <th class="text-nowrap">الحالة</th>
                            <th class="text-nowrap">الإجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($videos as $video)
                            <tr>
                                <td style="width: 220px">
                                    @if($video->thumbnail)
                                        <img src="{{ asset($video->thumbnail) }}" alt="{{ $video->title }}" class="rounded" style="width: 200px; max-width: 100%; height: 120px; object-fit: cover;">
                                    @elseif($video->video_path)
                                        <video controls preload="metadata" style="width: 200px; max-width: 100%; height: 120px; object-fit: cover;" class="rounded">
                                            <source src="{{ asset($video->video_path) }}">
                                        </video>
                                    @else
                                        <div class="entity-avatar"><i class="fas fa-video"></i></div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $video->title_ar ?: $video->title }}</div>
                                    @if($video->description_ar || $video->description)
                                        <small class="text-muted d-block mt-1">{{ \Illuminate\Support\Str::limit($video->description_ar ?: $video->description, 120) }}</small>
                                    @endif
                                </td>
                                <td class="mobile-hide">{{ $video->sort_order }}</td>
                                <td>
                                    <span class="status-pill {{ $video->is_active ? 'status-active' : 'status-inactive' }}">
                                        <span class="status-dot {{ $video->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></span>
                                        {{ $video->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-group">
                                        <form action="{{ route('admin.settings.website-videos.toggle-status', $video) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-{{ $video->is_active ? 'warning' : 'success' }} text-white action-icon-btn" title="{{ $video->is_active ? 'إيقاف' : 'تفعيل' }}">
                                                <i class="fas fa-{{ $video->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.settings.website-videos.edit', $video) }}" class="btn btn-sm btn-primary text-white action-icon-btn" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.settings.website-videos.destroy', $video) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('هل تريد حذف هذا الفيديو؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger text-white action-icon-btn" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4 mb-3">
                    {{ $videos->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state d-inline-block px-4 py-5">
                        <i class="fas fa-video empty-icon mb-3"></i>
                        <h5 class="text-muted">لا توجد فيديوهات حتى الآن.</h5>
                        <p class="text-muted mb-0">يمكن إضافة فيديو تعريفي جديد من هذه الصفحة.</p>
                        <div class="mt-3">
                            <a href="{{ route('admin.settings.website-videos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إضافة أول فيديو
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endcomponent
@endsection
