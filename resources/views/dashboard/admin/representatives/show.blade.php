@extends('layouts.admin')

@section('title', 'تفاصيل المندوب')
@section('page-title', 'تفاصيل المندوب')

@php
    $statusLabel = fn ($value) => match ($value) {
        'incomplete' => 'غير مكتمل',
        'pending_review' => 'قيد المراجعة',
        'approved' => 'معتمد',
        'rejected' => 'مرفوض',
        'suspended' => 'موقوف',
        default => '--',
    };

    $accountTypeLabel = fn ($value) => match ($value) {
        'free' => 'مندوب حر',
        'warehouse' => 'مندوب مستودع',
        default => '--',
    };

    $documentLabel = fn ($value) => match ($value) {
        'personal_photo' => 'الصورة الشخصية',
        'national_id_front' => 'الوجه الأمامي للبطاقة',
        'national_id_back' => 'الوجه الخلفي للبطاقة',
        'vehicle_photo' => 'صورة المركبة',
        'driving_license_front' => 'رخصة القيادة - أمامي',
        'driving_license_back' => 'رخصة القيادة - خلفي',
        'vehicle_license_front' => 'رخصة المركبة - أمامي',
        'vehicle_license_back' => 'رخصة المركبة - خلفي',
        default => $value,
    };

    $documents = $representative->mediaFiles->groupBy('document_type');
@endphp

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="text-muted mb-1">رقم الحساب</div>
                        <h4 class="mb-1 fw-bold">{{ $representative->account_number }}</h4>
                        <div class="text-muted">{{ $statusLabel($representative->status?->value ?? $representative->status) }}</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('admin.representatives.approve', $representative) }}" method="POST">
                            @csrf
                            <button class="btn btn-success">اعتماد</button>
                        </form>
                        <form action="{{ route('admin.representatives.suspend', $representative) }}" method="POST">
                            @csrf
                            <button class="btn btn-warning">إيقاف</button>
                        </form>
                        <form action="{{ route('admin.representatives.reactivate', $representative) }}" method="POST">
                            @csrf
                            <button class="btn btn-primary">إعادة تفعيل</button>
                        </form>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectRepresentativeModal">رفض</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold">بيانات الحساب</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><div class="text-muted">الاسم الأول</div><div class="fw-bold">{{ $representative->first_name ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">اسم الأب</div><div class="fw-bold">{{ $representative->father_name ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">اللقب</div><div class="fw-bold">{{ $representative->last_name ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">نوع الحساب</div><div class="fw-bold">{{ $accountTypeLabel($representative->account_type?->value ?? $representative->account_type) }}</div></div>
                        <div class="col-md-6"><div class="text-muted">البريد الإلكتروني</div><div class="fw-bold">{{ $representative->user?->email ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">الموبايل الرئيسي</div><div class="fw-bold">{{ $representative->phone ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">الموبايل الثاني</div><div class="fw-bold">{{ $representative->second_phone ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">تاريخ الميلاد</div><div class="fw-bold">{{ optional($representative->birth_date)->format('Y-m-d') ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">النوع</div><div class="fw-bold">{{ $representative->gender === 'male' ? 'ذكر' : ($representative->gender === 'female' ? 'أنثى' : '--') }}</div></div>
                        <div class="col-12"><div class="text-muted">العنوان التفصيلي</div><div class="fw-bold">{{ $representative->address ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">تاريخ فتح الحساب</div><div class="fw-bold">{{ optional($representative->account_opened_at)->format('Y-m-d') ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">حالة التفعيل</div><div class="fw-bold">{{ $representative->is_active ? 'نشط' : 'غير نشط' }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold">بيانات العمل</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><div class="text-muted">نوع العمل</div><div class="fw-bold">{{ $representative->workTypes->pluck('option.name')->filter()->implode('، ') ?: '--' }}</div></div>
                        <div class="col-md-6"><div class="text-muted">خدمة القرى</div><div class="fw-bold">{{ $representative->village_service ? 'نعم' : 'لا' }}</div></div>
                        <div class="col-12"><div class="text-muted">المحافظات</div><div class="fw-bold">{{ $representative->governorates->pluck('name')->filter()->implode('، ') ?: '--' }}</div></div>
                        <div class="col-12"><div class="text-muted">المدن</div><div class="fw-bold">{{ $representative->cities->pluck('name')->filter()->implode('، ') ?: '--' }}</div></div>
                        <div class="col-12">
                            <div class="text-muted">المستودع</div>
                            <div class="fw-bold">
                                {{ $representative->warehouse ? $representative->warehouse->name : '--' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">بيانات المركبة</div>
                <div class="card-body">
                    @if($representative->vehicle)
                        <div class="row g-3">
                            <div class="col-md-4"><div class="text-muted">نوع النقل</div><div class="fw-bold">{{ $representative->vehicle->transportType?->name_ar ?: '--' }}</div></div>
                            <div class="col-md-4"><div class="text-muted">لوحات الحروف</div><div class="fw-bold">{{ $representative->vehicle->registration_plate_letters ?: '--' }}</div></div>
                            <div class="col-md-4"><div class="text-muted">لوحات الأرقام</div><div class="fw-bold">{{ $representative->vehicle->registration_plate_numbers ?: '--' }}</div></div>
                            <div class="col-md-4"><div class="text-muted">العلامة التجارية</div><div class="fw-bold">{{ $representative->vehicle->brand ?: '--' }}</div></div>
                            <div class="col-md-4"><div class="text-muted">الموديل</div><div class="fw-bold">{{ $representative->vehicle->model ?: '--' }}</div></div>
                            <div class="col-md-4"><div class="text-muted">رقم الترخيص</div><div class="fw-bold">{{ $representative->vehicle->license_number ?: '--' }}</div></div>
                        </div>
                    @else
                        <div class="text-muted">لا توجد بيانات مركبة.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">المستندات</div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($documents as $documentType => $files)
                            <div class="col-md-6 col-xl-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="fw-bold mb-2">{{ $documentLabel($documentType) }}</div>
                                    @foreach($files as $file)
                                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                            <div class="small text-muted">{{ $file->title ?: $file->original_name }}</div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ $file->url }}" target="_blank" class="btn btn-sm btn-outline-primary">معاينة</a>
                                                <a href="{{ $file->url }}" download class="btn btn-sm btn-outline-secondary">تحميل</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">لا توجد مستندات مرفوعة.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectRepresentativeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.representatives.reject', $representative) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">سبب الرفض</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="rejection_reason" rows="4" class="form-control" placeholder="اكتب سبب الرفض هنا" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">حفظ الرفض</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
