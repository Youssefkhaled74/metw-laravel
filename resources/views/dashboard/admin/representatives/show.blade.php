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

    $statusTone = fn ($value) => match ($value) {
        'approved' => 'approved',
        'pending_review' => 'pending',
        'rejected' => 'rejected',
        'suspended' => 'suspended',
        'incomplete' => 'incomplete',
        default => 'empty',
    };

    $accountTypeLabel = fn ($value) => match ($value) {
        'free' => 'مندوب حر',
        'warehouse' => 'مندوب مستودع',
        default => '--',
    };

    $accountTone = fn ($value) => match ($value) {
        'free' => 'free',
        'warehouse' => 'warehouse',
        default => 'empty',
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

    $representativeName = trim(collect([
        $representative->first_name,
        $representative->father_name,
        $representative->last_name,
    ])->filter()->implode(' ')) ?: '--';

    $statusValue = $representative->status?->value ?? $representative->status;
    $accountTypeValue = $representative->account_type?->value ?? $representative->account_type;

    $workTypesText = $representative->workTypes->pluck('option.name')->filter()->implode('، ') ?: '--';
    $governoratesText = $representative->governorates->pluck('name')->filter()->implode('، ') ?: '--';
    $citiesText = $representative->cities->pluck('name')->filter()->implode('، ') ?: '--';

    $documents = $representative->mediaFiles->groupBy('document_type');
    $documentsCount = $representative->mediaFiles->count();

    $profileChecks = [
        filled($representative->first_name),
        filled($representative->last_name),
        filled($representative->phone),
        filled($representative->user?->email),
        filled($representative->address),
        filled($representative->birth_date),
        filled($accountTypeValue),
        $representative->workTypes->count() > 0,
        $representative->governorates->count() > 0,
        $representative->cities->count() > 0,
        (bool) $representative->vehicle,
        $documentsCount > 0,
    ];

    $profileScore = (int) round((collect($profileChecks)->filter()->count() / count($profileChecks)) * 100);

    $vehiclePlate = $representative->vehicle
        ? trim(($representative->vehicle->registration_plate_letters ?: '') . ' ' . ($representative->vehicle->registration_plate_numbers ?: ''))
        : '--';
@endphp

@section('page-actions')
    <a href="{{ route('admin.representatives.index') }}" class="btn reps-light-btn">
        <i class="fas fa-arrow-right ms-1"></i>
        الرجوع للقائمة
    </a>
@endsection

@section('content')
    <div class="reps-page" dir="rtl">
        <section class="reps-hero">
            <div class="reps-hero-main">
                <div class="reps-avatar">
                    {{ \Illuminate\Support\Str::substr($representativeName !== '--' ? $representativeName : 'م', 0, 1) }}
                </div>

                <div class="reps-hero-copy">
                    <span class="reps-chip">ملف المندوب</span>

                    <h3>{{ $representativeName }}</h3>

                    <p>
                        مراجعة بيانات الحساب والعمل والمركبة والمستندات، مع إمكانية اعتماد أو إيقاف أو رفض الحساب من نفس الشاشة.
                    </p>

                    <div class="reps-meta">
                        <span>
                            <i class="fas fa-hashtag"></i>
                            {{ $representative->account_number }}
                        </span>

                        <span class="status-{{ $statusTone($statusValue) }}">
                            <i class="fas fa-circle"></i>
                            {{ $statusLabel($statusValue) }}
                        </span>

                        <span class="account-{{ $accountTone($accountTypeValue) }}">
                            <i class="fas fa-user-tie"></i>
                            {{ $accountTypeLabel($accountTypeValue) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="reps-score-card">
                <span>اكتمال الملف</span>
                <strong>{{ $profileScore }}%</strong>
                <div class="reps-score-bar">
                    <em style="width: {{ $profileScore }}%"></em>
                </div>
                <small>{{ $documentsCount }} مستند مرفوع</small>
            </div>
        </section>

        <section class="reps-actions-card">
            <div>
                <h5>إجراءات المراجعة</h5>
                <p>استخدم الإجراءات التالية لتغيير حالة حساب المندوب حسب نتيجة المراجعة.</p>
            </div>

            <div class="reps-action-buttons">
                <form action="{{ route('admin.representatives.approve', $representative) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn reps-success-btn">
                        <i class="fas fa-circle-check ms-1"></i>
                        اعتماد
                    </button>
                </form>

                <form action="{{ route('admin.representatives.suspend', $representative) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn reps-warning-btn">
                        <i class="fas fa-pause ms-1"></i>
                        إيقاف
                    </button>
                </form>

                <form action="{{ route('admin.representatives.reactivate', $representative) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn reps-primary-btn">
                        <i class="fas fa-rotate-right ms-1"></i>
                        إعادة تفعيل
                    </button>
                </form>

                <button type="button" class="btn reps-danger-btn" data-bs-toggle="modal" data-bs-target="#rejectRepresentativeModal">
                    <i class="fas fa-xmark ms-1"></i>
                    رفض
                </button>
            </div>
        </section>

        <section class="reps-metrics">
            <div class="reps-metric">
                <i class="fas fa-id-card"></i>
                <div>
                    <span>رقم الحساب</span>
                    <strong>{{ $representative->account_number ?: '--' }}</strong>
                </div>
            </div>

            <div class="reps-metric">
                <i class="fas fa-phone"></i>
                <div>
                    <span>الموبايل الرئيسي</span>
                    <strong>{{ $representative->phone ?: '--' }}</strong>
                </div>
            </div>

            <div class="reps-metric">
                <i class="fas fa-briefcase"></i>
                <div>
                    <span>نوع العمل</span>
                    <strong>{{ $workTypesText }}</strong>
                </div>
            </div>

            <div class="reps-metric">
                <i class="fas fa-file"></i>
                <div>
                    <span>المستندات</span>
                    <strong>{{ number_format($documentsCount) }}</strong>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-12 col-xl-6">
                <section class="reps-card h-100">
                    <div class="reps-section-head">
                        <div>
                            <h5>بيانات الحساب</h5>
                            <p>البيانات الشخصية وبيانات التواصل وحالة الحساب.</p>
                        </div>
                    </div>

                    <div class="reps-info-grid">
                        <div class="reps-info-item">
                            <span>الاسم الأول</span>
                            <strong>{{ $representative->first_name ?: '--' }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>اسم الأب</span>
                            <strong>{{ $representative->father_name ?: '--' }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>اللقب</span>
                            <strong>{{ $representative->last_name ?: '--' }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>نوع الحساب</span>
                            <strong>{{ $accountTypeLabel($accountTypeValue) }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>البريد الإلكتروني</span>
                            <strong>{{ $representative->user?->email ?: '--' }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>الموبايل الرئيسي</span>
                            <strong>{{ $representative->phone ?: '--' }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>الموبايل الثاني</span>
                            <strong>{{ $representative->second_phone ?: '--' }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>تاريخ الميلاد</span>
                            <strong>{{ optional($representative->birth_date)->format('Y-m-d') ?: '--' }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>النوع</span>
                            <strong>{{ $representative->gender === 'male' ? 'ذكر' : ($representative->gender === 'female' ? 'أنثى' : '--') }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>تاريخ فتح الحساب</span>
                            <strong>{{ optional($representative->account_opened_at)->format('Y-m-d') ?: '--' }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>حالة التفعيل</span>
                            <strong>{{ $representative->is_active ? 'نشط' : 'غير نشط' }}</strong>
                        </div>

                        <div class="reps-info-item reps-wide">
                            <span>العنوان التفصيلي</span>
                            <strong>{{ $representative->address ?: '--' }}</strong>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-12 col-xl-6">
                <section class="reps-card h-100">
                    <div class="reps-section-head">
                        <div>
                            <h5>بيانات العمل والتغطية</h5>
                            <p>نوع العمل ونطاق التغطية والمستودع المرتبط.</p>
                        </div>
                    </div>

                    <div class="reps-info-grid">
                        <div class="reps-info-item reps-wide">
                            <span>نوع العمل</span>
                            <strong>{{ $workTypesText }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>خدمة القرى</span>
                            <strong>{{ $representative->village_service ? 'نعم' : 'لا' }}</strong>
                        </div>

                        <div class="reps-info-item">
                            <span>المستودع</span>
                            <strong>{{ $representative->warehouse ? $representative->warehouse->name : '--' }}</strong>
                        </div>

                        <div class="reps-info-item reps-wide">
                            <span>المحافظات</span>
                            <strong>{{ $governoratesText }}</strong>
                        </div>

                        <div class="reps-info-item reps-wide">
                            <span>المدن</span>
                            <strong>{{ $citiesText }}</strong>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-12">
                <section class="reps-card">
                    <div class="reps-section-head">
                        <div>
                            <h5>بيانات المركبة</h5>
                            <p>بيانات وسيلة النقل ولوحات المركبة والترخيص.</p>
                        </div>

                        @if($representative->vehicle)
                            <span class="reps-mini-badge success">متوفرة</span>
                        @else
                            <span class="reps-mini-badge muted">غير متوفرة</span>
                        @endif
                    </div>

                    @if($representative->vehicle)
                        <div class="reps-vehicle-card">
                            <div class="reps-vehicle-icon">
                                <i class="fas fa-truck"></i>
                            </div>

                            <div class="reps-vehicle-main">
                                <h5>{{ $representative->vehicle->transportType?->name_ar ?: 'نوع نقل غير محدد' }}</h5>
                                <p>لوحة المركبة: {{ $vehiclePlate ?: '--' }}</p>
                            </div>
                        </div>

                        <div class="reps-info-grid mt-3">
                            <div class="reps-info-item">
                                <span>نوع النقل</span>
                                <strong>{{ $representative->vehicle->transportType?->name_ar ?: '--' }}</strong>
                            </div>

                            <div class="reps-info-item">
                                <span>لوحات الحروف</span>
                                <strong>{{ $representative->vehicle->registration_plate_letters ?: '--' }}</strong>
                            </div>

                            <div class="reps-info-item">
                                <span>لوحات الأرقام</span>
                                <strong>{{ $representative->vehicle->registration_plate_numbers ?: '--' }}</strong>
                            </div>

                            <div class="reps-info-item">
                                <span>العلامة التجارية</span>
                                <strong>{{ $representative->vehicle->brand ?: '--' }}</strong>
                            </div>

                            <div class="reps-info-item">
                                <span>الموديل</span>
                                <strong>{{ $representative->vehicle->model ?: '--' }}</strong>
                            </div>

                            <div class="reps-info-item">
                                <span>رقم الترخيص</span>
                                <strong>{{ $representative->vehicle->license_number ?: '--' }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="reps-empty-inline">
                            <i class="fas fa-truck"></i>
                            <strong>لا توجد بيانات مركبة</strong>
                            <span>لم يقم المندوب بإضافة بيانات المركبة حتى الآن.</span>
                        </div>
                    @endif
                </section>
            </div>

            <div class="col-12">
                <section class="reps-card">
                    <div class="reps-section-head">
                        <div>
                            <h5>المستندات</h5>
                            <p>مراجعة وتحميل المستندات المرفوعة من المندوب.</p>
                        </div>

                        <span class="reps-count-pill">
                            {{ number_format($documentsCount) }} مستند
                        </span>
                    </div>

                    @if($documents->count() > 0)
                        <div class="reps-doc-grid">
                            @foreach($documents as $documentType => $files)
                                <article class="reps-doc-card">
                                    <div class="reps-doc-head">
                                        <div class="reps-doc-icon">
                                            <i class="fas fa-file-lines"></i>
                                        </div>

                                        <div>
                                            <h6>{{ $documentLabel($documentType) }}</h6>
                                            <span>{{ $files->count() }} ملف</span>
                                        </div>
                                    </div>

                                    <div class="reps-doc-files">
                                        @foreach($files as $file)
                                            <div class="reps-doc-file">
                                                <div>
                                                    <strong>{{ $file->title ?: $file->original_name }}</strong>
                                                    <small>{{ $file->original_name ?: '--' }}</small>
                                                </div>

                                                <div class="reps-doc-actions">
                                                    <a href="{{ $file->url }}" target="_blank" class="btn reps-doc-btn primary">
                                                        <i class="fas fa-eye"></i>
                                                        معاينة
                                                    </a>

                                                    <a href="{{ $file->url }}" download class="btn reps-doc-btn light">
                                                        <i class="fas fa-download"></i>
                                                        تحميل
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="reps-empty-inline">
                            <i class="fas fa-file-circle-xmark"></i>
                            <strong>لا توجد مستندات مرفوعة</strong>
                            <span>سيتم عرض المستندات هنا عند رفعها من المندوب.</span>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectRepresentativeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content reps-modal">
                <form action="{{ route('admin.representatives.reject', $representative) }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title">سبب الرفض</h5>
                            <p class="reps-modal-subtitle mb-0">اكتب سبب واضح للرفض ليظهر في سجل المراجعة.</p>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="reps-label">سبب الرفض</label>
                        <textarea
                            name="rejection_reason"
                            rows="5"
                            class="form-control reps-control"
                            placeholder="اكتب سبب الرفض هنا"
                            required
                        ></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn reps-light-btn" data-bs-dismiss="modal">
                            إلغاء
                        </button>

                        <button type="submit" class="btn reps-danger-btn">
                            حفظ الرفض
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style data-page-style>
        .reps-page {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .reps-hero,
        .reps-actions-card,
        .reps-metric,
        .reps-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
        }

        .reps-hero {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .reps-hero-main {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
        }

        .reps-avatar {
            width: 68px;
            height: 68px;
            border-radius: 24px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            font-weight: 950;
            flex-shrink: 0;
        }

        .reps-chip {
            display: inline-flex;
            width: fit-content;
            padding: .28rem .75rem;
            margin-bottom: .5rem;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            font-size: .78rem;
            font-weight: 900;
        }

        .reps-hero-copy h3 {
            margin: 0;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            letter-spacing: -.02em;
        }

        .reps-hero-copy p {
            max-width: 820px;
            margin: .45rem 0 0;
            color: #64748b;
            font-size: .93rem;
            line-height: 1.8;
        }

        .reps-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: .75rem;
        }

        .reps-meta span,
        .reps-mini-badge,
        .reps-count-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .38rem .75rem;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #475569;
            font-size: .76rem;
            font-weight: 950;
            white-space: nowrap;
        }

        .reps-meta .status-approved,
        .reps-mini-badge.success {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .reps-meta .status-pending {
            background: #fffbeb;
            border-color: #fde68a;
            color: #b45309;
        }

        .reps-meta .status-rejected,
        .reps-meta .status-suspended {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .reps-meta .status-incomplete,
        .reps-meta .status-empty,
        .reps-mini-badge.muted {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .reps-meta .account-free {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .reps-meta .account-warehouse {
            background: #f0f9ff;
            border-color: #bae6fd;
            color: #0369a1;
        }

        .reps-score-card {
            min-width: 240px;
            padding: .95rem;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .reps-score-card span {
            display: block;
            color: #64748b;
            font-size: .76rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .reps-score-card strong {
            display: block;
            color: #111827;
            font-size: 1.45rem;
            font-weight: 950;
            line-height: 1;
        }

        .reps-score-card small {
            display: block;
            color: #64748b;
            font-size: .78rem;
            margin-top: .5rem;
        }

        .reps-score-bar {
            height: 8px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
            margin-top: .65rem;
        }

        .reps-score-bar em {
            display: block;
            height: 100%;
            background: #2563eb;
            border-radius: inherit;
        }

        .reps-actions-card {
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .reps-actions-card h5,
        .reps-section-head h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .reps-actions-card p,
        .reps-section-head p {
            margin: .25rem 0 0;
            color: #64748b;
            font-size: .86rem;
            line-height: 1.7;
        }

        .reps-action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            justify-content: flex-end;
        }

        .reps-primary-btn,
        .reps-light-btn,
        .reps-success-btn,
        .reps-warning-btn,
        .reps-danger-btn {
            min-height: 42px;
            border-radius: 999px !important;
            font-weight: 950 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .reps-primary-btn {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
            box-shadow: 0 12px 22px rgba(37, 99, 235, .16);
        }

        .reps-success-btn {
            background: #047857 !important;
            border-color: #047857 !important;
            color: #fff !important;
        }

        .reps-warning-btn {
            background: #f59e0b !important;
            border-color: #f59e0b !important;
            color: #111827 !important;
        }

        .reps-danger-btn {
            background: #b91c1c !important;
            border-color: #b91c1c !important;
            color: #fff !important;
        }

        .reps-light-btn {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #475569 !important;
        }

        .reps-light-btn:hover {
            background: #f8fafc !important;
            color: #111827 !important;
        }

        .reps-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }

        .reps-metric {
            min-height: 96px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .reps-metric i {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .reps-metric span {
            display: block;
            color: #64748b;
            font-size: .76rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .reps-metric strong {
            display: block;
            color: #111827;
            font-size: .92rem;
            font-weight: 950;
            line-height: 1.5;
        }

        .reps-card {
            padding: 1.25rem;
        }

        .reps-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .reps-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }

        .reps-info-item {
            padding: .9rem;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .reps-wide {
            grid-column: 1 / -1;
        }

        .reps-info-item span {
            display: block;
            color: #64748b;
            font-size: .75rem;
            font-weight: 950;
            margin-bottom: .25rem;
        }

        .reps-info-item strong {
            display: block;
            color: #111827;
            font-size: .88rem;
            font-weight: 850;
            line-height: 1.7;
            word-break: break-word;
        }

        .reps-vehicle-card {
            padding: 1rem;
            border-radius: 18px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            display: flex;
            align-items: center;
            gap: .85rem;
        }

        .reps-vehicle-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: #fff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .reps-vehicle-main h5 {
            margin: 0;
            color: #111827;
            font-size: 1rem;
            font-weight: 950;
        }

        .reps-vehicle-main p {
            margin: .25rem 0 0;
            color: #475569;
            font-size: .84rem;
            font-weight: 850;
        }

        .reps-doc-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .85rem;
        }

        .reps-doc-card {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            background: #fff;
        }

        .reps-doc-head {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: .9rem;
        }

        .reps-doc-icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .reps-doc-head h6 {
            margin: 0;
            color: #111827;
            font-size: .9rem;
            font-weight: 950;
        }

        .reps-doc-head span {
            display: block;
            color: #64748b;
            font-size: .75rem;
            margin-top: .2rem;
        }

        .reps-doc-files {
            display: grid;
            gap: .65rem;
        }

        .reps-doc-file {
            padding: .75rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }

        .reps-doc-file strong {
            display: block;
            color: #111827;
            font-size: .82rem;
            font-weight: 950;
            line-height: 1.5;
            word-break: break-word;
        }

        .reps-doc-file small {
            display: block;
            color: #64748b;
            font-size: .72rem;
            margin-top: .2rem;
            word-break: break-word;
        }

        .reps-doc-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
            margin-top: .65rem;
        }

        .reps-doc-btn {
            min-height: 34px;
            border-radius: 999px !important;
            font-size: .76rem;
            font-weight: 950 !important;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .reps-doc-btn.primary {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
        }

        .reps-doc-btn.light {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #475569 !important;
        }

        .reps-empty-inline {
            min-height: 180px;
            border: 1px dashed #cbd5e1;
            border-radius: 20px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            text-align: center;
            padding: 1.5rem;
        }

        .reps-empty-inline i {
            width: 64px;
            height: 64px;
            border-radius: 22px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
        }

        .reps-empty-inline strong {
            color: #111827;
            font-weight: 950;
        }

        .reps-empty-inline span {
            color: #64748b;
            font-size: .84rem;
        }

        .reps-modal {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        }

        .reps-modal .modal-title {
            color: #111827;
            font-weight: 950;
        }

        .reps-modal-subtitle {
            color: #64748b;
            font-size: .82rem;
            margin-top: .2rem;
        }

        .reps-label {
            display: block;
            margin-bottom: .42rem;
            color: #475569;
            font-size: .8rem;
            font-weight: 950;
        }

        .reps-control {
            border-radius: 16px !important;
            border-color: #dbe3ea !important;
            box-shadow: none !important;
            font-weight: 700;
        }

        .reps-control:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .1) !important;
        }

        @media (max-width: 1199.98px) {
            .reps-hero,
            .reps-actions-card {
                flex-direction: column;
                align-items: stretch;
            }

            .reps-score-card {
                min-width: 0;
            }

            .reps-action-buttons {
                justify-content: flex-start;
            }

            .reps-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .reps-doc-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .reps-hero,
            .reps-actions-card,
            .reps-metric,
            .reps-card {
                border-radius: 18px;
            }

            .reps-hero,
            .reps-actions-card,
            .reps-card {
                padding: 1rem;
            }

            .reps-hero-main {
                flex-direction: column;
            }

            .reps-hero-copy h3 {
                font-size: 1.2rem;
            }

            .reps-metrics,
            .reps-info-grid,
            .reps-doc-grid {
                grid-template-columns: 1fr;
            }

            .reps-action-buttons .btn,
            .reps-action-buttons form {
                width: 100%;
            }

            .reps-action-buttons .btn {
                justify-content: center;
            }

            .reps-section-head {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
@endsection