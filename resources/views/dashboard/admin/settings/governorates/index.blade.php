@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'المحافظات' : 'Governorates')
@section('page-title', app()->getLocale() === 'ar' ? 'إدارة المحافظات' : 'Governorates Management')

@section('page-actions')
    <a href="{{ route('admin.settings.governorates.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة محافظة' : 'Add Governorate' }}
    </a>
@endsection

@section('content')
    <div class="card shadow-sm border-0 locations-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ app()->getLocale() === 'ar' ? 'جميع المحافظات' : 'All governorates' }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                    <i class="fas fa-map me-2"></i>
                    {{ $governorates->count() }} / {{ $governorates->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.governorates.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث برقم المحافظة أو اسمها...' : 'Search by governorate number or name...' }}">
                </div>
                <div class="col-lg-3">
                    <select name="status" class="form-select">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="active" @selected(request('status') === 'active')>{{ __('admin-dashboard.active') }}</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>{{ __('admin-dashboard.inactive') }}</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">{{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}</button>
                </div>
                <div class="col-lg-2">
                    <a href="{{ route('admin.settings.governorates.index') }}" class="btn btn-outline-secondary w-100">{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if ($governorates->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'رقم المحافظة' : 'Governorate no.' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الاسم' : 'Name' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'العاصمة' : 'Capital city' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'المدن' : 'Cities' }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th>{{ __('admin-dashboard.created_at') }}</th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($governorates as $governorate)
                                <tr>
                                    <td>{{ $governorate->id }}</td>
                                    <td class="fw-semibold text-primary">{{ $governorate->governorate_number }}</td>
                                    <td>{{ $governorate->name_ar }}</td>
                                    <td>{{ $governorate->capitalCity?->name ?? '--' }}</td>
                                    <td>{{ $governorate->cities_count }}</td>
                                    <td>
                                        <form action="{{ route('admin.settings.governorates.toggle-status', $governorate->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $governorate->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $governorate->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $governorate->created_at])</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.settings.governorates.edit', $governorate->id) }}" class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.settings.governorates.destroy', $governorate->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل تريد حذف المحافظة؟' : 'Delete this governorate?' }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger text-white">
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
                    {{ $governorates->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <h5 class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا توجد محافظات بعد' : 'No governorates found yet' }}</h5>
                    <a href="{{ route('admin.settings.governorates.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة محافظة' : 'Add Governorate' }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
