@extends('layouts.admin')

@section('title', app()->getLocale() === 'ar' ? 'المدن' : 'Cities')
@section('page-title', app()->getLocale() === 'ar' ? 'إدارة المدن' : 'Cities Management')

@section('page-actions')
    <a href="{{ route('admin.settings.cities.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة مدينة' : 'Add City' }}
    </a>
@endsection

@section('content')
    <div class="card shadow-sm border-0 cities-card">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h5 class="mb-0">{{ app()->getLocale() === 'ar' ? 'جميع المدن' : 'All cities' }}</h5>
                <span class="badge rounded-pill text-bg-light border text-muted px-3 py-2">
                    <i class="fas fa-city me-2"></i>
                    {{ $cities->count() }} / {{ $cities->total() }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.settings.cities.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث باسم المدينة أو المحافظة...' : 'Search by city or governorate...' }}">
                </div>
                <div class="col-lg-3">
                    <select name="governorate_id" class="form-select">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'كل المحافظات' : 'All governorates' }}</option>
                        @foreach ($governorates as $governorate)
                            <option value="{{ $governorate->id }}" @selected(request('governorate_id', 'all') == (string) $governorate->id)>{{ $governorate->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <select name="status" class="form-select">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All statuses' }}</option>
                        <option value="active" @selected(request('status') === 'active')>{{ __('admin-dashboard.active') }}</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>{{ __('admin-dashboard.inactive') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">{{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}</button>
                    <a href="{{ route('admin.settings.cities.index') }}" class="btn btn-outline-secondary">{{ app()->getLocale() === 'ar' ? 'إعادة' : 'Reset' }}</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if ($cities->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'اسم المدينة' : 'City name' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'المحافظة' : 'Governorate' }}</th>
                                <th>{{ __('admin-dashboard.status') }}</th>
                                <th>{{ __('admin-dashboard.created_at') }}</th>
                                <th>{{ __('admin-dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cities as $city)
                                <tr>
                                    <td>{{ $city->id }}</td>
                                    <td class="fw-semibold text-primary">{{ $city->name_ar }}</td>
                                    <td>{{ $city->governorate?->name_ar ?? '--' }}</td>
                                    <td>
                                        <form action="{{ route('admin.settings.cities.toggle-status', $city->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $city->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $city->is_active ? __('admin-dashboard.active') : __('admin-dashboard.inactive') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>@include('admin.partials.date', ['date' => $city->created_at])</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.settings.cities.edit', $city->id) }}" class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.settings.cities.destroy', $city->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل تريد حذف المدينة؟' : 'Delete this city?' }}');">
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
                    {{ $cities->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <h5 class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا توجد مدن بعد' : 'No cities found yet' }}</h5>
                    <a href="{{ route('admin.settings.cities.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> {{ app()->getLocale() === 'ar' ? 'إضافة مدينة' : 'Add City' }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
