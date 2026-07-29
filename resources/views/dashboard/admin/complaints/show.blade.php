@extends('layouts.admin')

@section('title', 'تفاصيل الشكوى')
@section('page-title', 'تفاصيل الشكوى')

@section('page-actions')
    <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-right me-1"></i> {{ $text('Back to List', 'العودة للقائمة') }}
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-bold">رقم الشكوى</td>
                            <td>{{ $complaint->complaint_number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">نوع الشكوى</td>
                            <td>
                                @php
                                    $typeValue = $complaint->complaint_type->value ?? $complaint->complaint_type;
                                    $typeLabel = match ($typeValue) {
                                        'purchase_cancellation' => 'إلغاء المشتريات',
                                        'shipping_cancellation' => 'إلغاء الشحن',
                                        'return' => 'المرتجعات',
                                        'user' => 'المستخدمين',
                                        'vendor' => 'الموردين',
                                        'warehouse' => 'المستودعات',
                                        'representative' => 'المناديب',
                                        default => $typeValue,
                                    };
                                @endphp
                                {{ $typeLabel }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">الحالة</td>
                            <td>
                                @php
                                    $statusValue = $complaint->status->value ?? $complaint->status;
                                    $statusLabel = match ($statusValue) {
                                        'pending' => 'قيد الانتظار',
                                        'under_review' => 'قيد المراجعة',
                                        'resolved' => 'مغلقة',
                                        'rejected' => 'مرفوضة',
                                        default => $statusValue,
                                    };
                                @endphp
                                {{ $statusLabel }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">صاحب الشكوى</td>
                            <td>{{ $complaint->user?->username ?? $complaint->user?->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">العنوان</td>
                            <td>{{ $complaint->subject ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">الوصف</td>
                            <td>{{ $complaint->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">السبب</td>
                            <td>{{ $complaint->reason ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">المرجع المرتبط</td>
                            <td>
                                @if(!empty($related['url']))
                                    <a href="{{ $related['url'] }}">{{ $related['label'] }}</a>
                                @else
                                    {{ $related['label'] ?? '-' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">تاريخ الإنشاء</td>
                            <td>{{ optional($complaint->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">ملاحظات الأدمن</td>
                            <td>{{ $complaint->admin_notes ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">تحديث الحالة</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.complaints.update-status', $complaint) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                @foreach (\App\Enum\ComplaintStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected(($complaint->status->value ?? $complaint->status) === $status->value)>
                                        {{ match ($status->value) {
                                            'pending' => 'قيد الانتظار',
                                            'under_review' => 'قيد المراجعة',
                                            'resolved' => 'مغلقة',
                                            'rejected' => 'مرفوضة',
                                            default => $status->value,
                                        } }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات الأدمن</label>
                            <textarea name="admin_notes" rows="5" class="form-control">{{ old('admin_notes', $complaint->admin_notes) }}</textarea>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">حفظ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
