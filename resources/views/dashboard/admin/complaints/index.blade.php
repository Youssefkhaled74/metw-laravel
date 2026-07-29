@extends('layouts.admin')

@section('title', 'الشكاوى')
@section('page-title', 'الشكاوى')

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="رقم الشكوى أو الوصف">
                </div>
                <div class="col-md-3">
                    <label class="form-label">نوع الشكوى</label>
                    <select name="complaint_type" class="form-select">
                        <option value="">الكل</option>
                        @foreach (\App\Enum\ComplaintType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(request('complaint_type') === $type->value)>
                                {{ match ($type->value) {
                                    'purchase_cancellation' => 'إلغاء المشتريات',
                                    'shipping_cancellation' => 'إلغاء الشحن',
                                    'return' => 'المرتجعات',
                                    'user' => 'المستخدمين',
                                    'vendor' => 'الموردين',
                                    'warehouse' => 'المستودعات',
                                    'representative' => 'المناديب',
                                    default => $type->value,
                                } }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        @foreach (\App\Enum\ComplaintStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
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
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">تصفية</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>رقم الشكوى</th>
                        <th>النوع</th>
                        <th>الحالة</th>
                        <th>صاحب الشكوى</th>
                        <th>المرجع</th>
                        <th>التاريخ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->complaint_number }}</td>
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
                            <td>{{ $complaint->user?->username ?? $complaint->user?->email ?? '-' }}</td>
                            <td>{{ class_basename($complaint->complaintable_type) }} #{{ $complaint->complaintable_id }}</td>
                            <td>{{ optional($complaint->created_at)->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">لا توجد شكاوى.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            {{ $complaints->links() }}
        </div>
    </div>
@endsection
