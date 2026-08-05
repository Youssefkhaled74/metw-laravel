@extends('layouts.admin')

@section('title', 'إدارة خدمات ميتو')
@section('page_title', 'الخدمات')

@section('content')
@component('dashboard.admin.settings.partials.module-shell', [
    'kicker' => 'إدارة محتوى الموقع',
    'title' => 'خدمات ميتو',
    'description' => 'إضافة الخدمات الظاهرة للزوار وترتيبها وتفعيلها من مكان واحد.',
    'actions' => [
        ['label' => 'محتوى الموقع', 'url' => route('admin.settings.website-content'), 'icon' => 'fas fa-arrow-right', 'class' => 'btn-outline-secondary'],
        ['label' => 'إضافة خدمة جديدة', 'url' => route('admin.settings.metw-services.create'), 'icon' => 'fas fa-plus', 'class' => 'btn-primary'],
    ],
])
    <div class="settings-content-card">
        <div class="card-header py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h3 class="card-title">قائمة الخدمات</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الأيقونة</th>
                        <th>العنوان (عربي)</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($metwServices as $service)
                    <tr>
                        <td>{{ $service->id }}</td>
                        <td>{{ $service->icon }}</td>
                        <td>{{ $service->title_ar }}</td>
                        <td>{{ $service->sort_order }}</td>
                        <td>
                            <span class="badge badge-{{ $service->is_active ? 'success' : 'danger' }}">
                                {{ $service->is_active ? 'مفعل' : 'غير مفعل' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.settings.metw-services.edit', $service->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                            <form action="{{ route('admin.settings.metw-services.destroy', $service->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">لا توجد خدمات مضافة بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
@endsection
