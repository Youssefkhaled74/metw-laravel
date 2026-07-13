@extends('layouts.admin')
@section('title', 'تفاصيل طلب الإرجاع')
@section('page-title', 'تفاصيل طلب الإرجاع')
@section('content')
<div class="container-fluid">
    <a href="{{ route('admin.return-orders.pending') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> العودة للقائمة
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">معلومات طلب الإرجاع #{{ $return->id }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>رقم الطلب</th>
                            <td>{{ $return->order?->order_number }}</td>
                        </tr>
                        <tr>
                            <th>العميل</th>
                            <td>{{ $return->user->name ?? $return->user->username }} ({{ $return->user->phone }})</td>
                        </tr>
                        <tr>
                            <th>المندوب</th>
                            <td>{{ $return->representative?->first_name }} {{ $return->representative?->last_name }}</td>
                        </tr>
                        <tr>
                            <th>سبب الإرجاع</th>
                            <td>{{ $return->reason?->reason_text ?? $return->custom_reason_text }}</td>
                        </tr>
                        <tr>
                            <th>سبب الرفض</th>
                            <td>{{ $return->rejection_reason ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>الحالة</th>
                            <td>
                                <span class="badge bg-{{ $return->status->value === 'completed' ? 'success' : ($return->status->value === 'approved' ? 'primary' : ($return->status->value === 'rejected' || $return->status->value === 'rejected_return' ? 'danger' : 'warning')) }}">
                                    {{ $return->status->value }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>ملاحظات الإدارة</th>
                            <td>{{ $return->admin_notes ?? '—' }}</td>
                        </tr>
                        @if($return->refund_amount)
                        <tr>
                            <th>المبلغ المسترد</th>
                            <td>{{ number_format($return->net_refund, 2) }} ج.م (من أصل {{ number_format($return->refund_amount, 2) }} ج.م - رسوم الشحن: {{ number_format($return->shipping_fees, 2) }} ج.م - رسوم الإرجاع: {{ number_format($return->return_fees, 2) }} ج.م)</td>
                        </tr>
                        @endif
                        <tr>
                            <th>تاريخ الإنشاء</th>
                            <td>{{ $return->created_at?->toDateTimeString() }}</td>
                        </tr>
                        @if($return->completed_at)
                        <tr>
                            <th>تاريخ الإكمال</th>
                            <td>{{ $return->completed_at?->toDateTimeString() }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($return->complaints->isNotEmpty())
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">الشكاوى</h5>
                </div>
                <div class="card-body">
                    @foreach($return->complaints as $complaint)
                    <div class="border rounded p-3 mb-2">
                        <p class="mb-1"><strong>السبب:</strong> {{ $complaint->complaint_reason }}</p>
                        @if($complaint->admin_action)
                        <p class="mb-1"><strong>إجراء الإدارة:</strong> {{ $complaint->admin_action === 'reactivated' ? 'إعادة تفعيل' : 'رفض' }}</p>
                        <p class="mb-0"><strong>سبب الإجراء:</strong> {{ $complaint->action_reason }}</p>
                        @endif
                        <small class="text-muted">{{ $complaint->created_at?->toDateTimeString() }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($return->logs->isNotEmpty())
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">سجل التغييرات</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($return->logs as $log)
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <div>
                                <span class="badge bg-secondary">{{ $log->old_status ?? '—' }}</span>
                                <i class="fas fa-arrow-right mx-1"></i>
                                <span class="badge bg-info">{{ $log->new_status }}</span>
                                @if($log->notes)
                                    <p class="mb-0 mt-1 text-muted small">{{ $log->notes }}</p>
                                @endif
                            </div>
                            <small class="text-muted">{{ $log->created_at?->toDateTimeString() }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            @if(in_array($return->status->value, ['approved']))
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">إجراءات</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.return-orders.update-status', $return->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="ملاحظات..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('سيتم احتساب المبلغ المسترد وإيداعه في محفظة العميل. هل أنت متأكد؟')">
                            <i class="fas fa-check"></i> إكمال الطلب وصرف المبلغ
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if(in_array($return->status->value, ['rejected', 'rejected_return']) && $return->complaints->isNotEmpty())
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">إعادة تفعيل طلب الإرجاع</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.return-orders.reactivate', $return->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">سبب إعادة التفعيل <span class="text-danger">*</span></label>
                            <textarea name="action_reason" class="form-control" rows="2" required placeholder="أدخل سبب إعادة التفعيل..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100" onclick="return confirm('سيتم إعادة تفعيل طلب الإرجاع وإشعار العميل. هل أنت متأكد؟')">
                            <i class="fas fa-redo"></i> إعادة تفعيل طلب الإرجاع
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
