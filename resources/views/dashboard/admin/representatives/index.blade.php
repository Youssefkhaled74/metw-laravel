@extends('layouts.admin')

@section('title', 'المناديب')
@section('page-title', 'إدارة المناديب')

@php
    $sortBy = $sortBy ?? request('sort_by', 'account_number');
    $sortDir = $sortDir ?? request('sort_dir', 'asc');
    $nextSortDir = fn (string $column) => $sortBy === $column && $sortDir === 'asc' ? 'desc' : 'asc';
    $sortUrl = fn (string $column) => route('admin.representatives.index', array_merge(request()->except(['page', 'sort_by', 'sort_dir']), [
        'sort_by' => $column,
        'sort_dir' => $nextSortDir($column),
    ]));

    $accountTypeLabel = fn ($value) => match ($value) {
        'free' => 'مندوب حر',
        'warehouse' => 'مندوب مستودع',
        default => '--',
    };

    $statusLabel = fn ($value) => match ($value) {
        'incomplete' => 'غير مكتمل',
        'pending_review' => 'قيد المراجعة',
        'approved' => 'معتمد',
        'rejected' => 'مرفوض',
        'suspended' => 'موقوف',
        default => '--',
    };
@endphp

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h4 class="mb-1 fw-bold">المناديب</h4>
                            <div class="text-muted">عرض الحسابات والاعتماد والحالة وبيانات التغطية.</div>
                        </div>
                        <a href="{{ route('admin.representatives.index') }}" class="btn btn-outline-secondary">إعادة ضبط</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <form method="GET" action="{{ route('admin.representatives.index') }}" class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">رقم الحساب</label>
                            <input type="text" name="account_number" value="{{ request('account_number') }}" class="form-control" placeholder="بحث برقم الحساب">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الاسم</label>
                            <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="بحث بالاسم">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الموبايل</label>
                            <input type="text" name="mobile" value="{{ request('mobile') }}" class="form-control" placeholder="بحث بالموبايل">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">نوع الحساب</label>
                            <select name="account_type" class="form-select">
                                <option value="all">الكل</option>
                                <option value="free" @selected(request('account_type') === 'free')>مندوب حر</option>
                                <option value="warehouse" @selected(request('account_type') === 'warehouse')>مندوب مستودع</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">نوع العمل</label>
                            <select name="work_type" class="form-select">
                                <option value="all">الكل</option>
                                @foreach($workTypes as $workType)
                                    <option value="{{ $workType->code }}" @selected(request('work_type') === $workType->code)>{{ $workType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="all">الكل</option>
                                <option value="incomplete" @selected(request('status') === 'incomplete')>غير مكتمل</option>
                                <option value="pending_review" @selected(request('status') === 'pending_review')>قيد المراجعة</option>
                                <option value="approved" @selected(request('status') === 'approved')>معتمد</option>
                                <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
                                <option value="suspended" @selected(request('status') === 'suspended')>موقوف</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">المحافظة</label>
                            <select name="governorate_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}" @selected(request('governorate_id') == $governorate->id)>{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">المدينة</label>
                            <select name="city_id" class="form-select">
                                <option value="">الكل</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" @selected(request('city_id') == $city->id)>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">بحث</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <a href="{{ $sortUrl('account_number') }}" class="text-decoration-none text-dark">رقم الحساب</a>
                                </th>
                                <th>الاسم</th>
                                <th>الموبايل</th>
                                <th>نوع الحساب</th>
                                <th>نوع العمل</th>
                                <th>الحالة</th>
                                <th>المحافظات</th>
                                <th>المدن</th>
                                <th class="text-end">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($representatives as $representative)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.representatives.show', $representative) }}" class="fw-bold text-decoration-none">
                                            {{ $representative->account_number }}
                                        </a>
                                    </td>
                                    <td>{{ trim(collect([$representative->first_name, $representative->father_name, $representative->last_name])->filter()->implode(' ')) ?: '--' }}</td>
                                    <td>{{ $representative->phone ?: '--' }}</td>
                                    <td>{{ $accountTypeLabel($representative->account_type?->value ?? $representative->account_type) }}</td>
                                    <td>
                                        {{ $representative->workTypes->pluck('option.name')->filter()->implode('، ') ?: '--' }}
                                    </td>
                                    <td>{{ $statusLabel($representative->status?->value ?? $representative->status) }}</td>
                                    <td>{{ $representative->governorates->pluck('name')->filter()->implode('، ') ?: '--' }}</td>
                                    <td>{{ $representative->cities->pluck('name')->filter()->implode('، ') ?: '--' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.representatives.show', $representative) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">لا توجد نتائج مطابقة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    {{ $representatives->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
