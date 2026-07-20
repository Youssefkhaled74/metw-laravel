@extends('layouts.admin')

@section('title', 'تفاصيل طلب الإلغاء')
@section('page-title', 'تفاصيل طلب الإلغاء #' . $returnRequest->return_number)

@section('page-actions')
    <a href="{{ route('admin.cancellations.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> العودة
    </a>
@endsection

@section('content')
    @php
        $seller = $returnRequest->items->first()?->orderItem?->product?->vendor;
        $statusValue = is_object($returnRequest->status) ? $returnRequest->status->value : (string) $returnRequest->status;
        $statusColor = match($statusValue) {
            'completed', 'approved' => 'success',
            'requested', 'pickup', 'processing' => 'warning',
            'rejected', 'cancelled' => 'danger',
            default => 'info',
        };
        $statusLabel = match($statusValue) {
            'requested' => 'بانتظار المراجعة',
            'approved' => 'موافق عليه',
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'rejected' => 'مرفوض',
            'cancelled' => 'ملغي',
            default => $statusValue,
        };
        $sellerStatus = $returnRequest->seller_status 
            ? (is_object($returnRequest->seller_status) ? $returnRequest->seller_status->value : $returnRequest->seller_status)
            : null;
        $sellerStatusLabel = $returnRequest->sellerStatusLabel();
        $sellerStatusCss = $returnRequest->sellerStatusCss();
    @endphp

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-lg-8">
            <!-- Cancellation Request Details -->
            <div class="cancel-detail-card mb-4">
                <div class="cancel-detail-header">
                    <h5><i class="fas fa-times-circle me-2"></i> تفاصيل طلب الإلغاء</h5>
                    <span class="cancel-status-badge badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                </div>
                <div class="cancel-detail-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">رقم طلب الإلغاء</span>
                                <span class="info-value fw-bold text-primary">{{ $returnRequest->return_number }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">رقم الطلب</span>
                                <span class="info-value">{{ $returnRequest->order->order_number ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">تاريخ الإنشاء</span>
                                <span class="info-value">{{ $returnRequest->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">نوع الطلب</span>
                                <span class="info-value">
                                    <span class="badge bg-light text-dark border">إلغاء</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">سبب الإلغاء</span>
                                <span class="info-value">{{ $returnRequest->reason ?? '-' }}</span>
                            </div>
                        </div>
                        @if ($returnRequest->other_reason)
                            <div class="col-md-12">
                                <div class="info-item">
                                    <span class="info-label">سبب آخر</span>
                                    <span class="info-value">{{ $returnRequest->other_reason }}</span>
                                </div>
                            </div>
                        @endif
                        @if ($returnRequest->notes)
                            <div class="col-md-12">
                                <div class="info-item">
                                    <span class="info-label">ملاحظات</span>
                                    <span class="info-value">{{ $returnRequest->notes }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Seller Status -->
            <div class="cancel-detail-card mb-4">
                <div class="cancel-detail-header">
                    <h5><i class="fas fa-store me-2 text-purple"></i> حالة البائع</h5>
                    <span class="cancel-status-badge badge bg-{{ $sellerStatusCss }}">{{ $sellerStatusLabel }}</span>
                </div>
                <div class="cancel-detail-body">
                    @if ($returnRequest->seller_rejection_reason)
                        <div class="alert alert-danger mb-3">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <strong>سبب رفض البائع:</strong> {{ $returnRequest->seller_rejection_reason }}
                        </div>
                    @endif
                    @if ($returnRequest->return_rejection_reason)
                        <div class="alert alert-danger mb-3">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            <strong>سبب رفض المرتجع:</strong> {{ $returnRequest->return_rejection_reason }}
                        </div>
                    @endif
                    @if (! $sellerStatus)
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-clock me-1"></i> لم يتخذ البائع أي إجراء بعد.
                        </div>
                    @endif
                    @if ($returnRequest->inspected_at)
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-search me-1"></i>
                            تم استلام المنتج للفحص بتاريخ: {{ $returnRequest->inspected_at->format('Y-m-d H:i') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Admin Actions -->
            @if ($returnRequest->admin_reactivation_reason)
                <div class="cancel-detail-card mb-4">
                    <div class="cancel-detail-header">
                        <h5><i class="fas fa-redo me-2 text-warning"></i> إعادة التفعيل</h5>
                    </div>
                    <div class="cancel-detail-body">
                        <div class="alert alert-warning mb-2">
                            <strong>سبب إعادة التفعيل:</strong> {{ $returnRequest->admin_reactivation_reason }}
                        </div>
                        @if ($returnRequest->reactivated_at)
                            <small class="text-muted">تم في: {{ $returnRequest->reactivated_at->format('Y-m-d H:i') }}</small>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Refund Info -->
            @if ($returnRequest->admin_refund_amount)
                <div class="cancel-detail-card mb-4">
                    <div class="cancel-detail-header">
                        <h5><i class="fas fa-wallet me-2 text-success"></i> معلومات الاسترداد</h5>
                    </div>
                    <div class="cancel-detail-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="info-label">مبلغ الاسترداد</span>
                                    <span class="info-value fw-bold text-success">{{ number_format($returnRequest->admin_refund_amount, 2) }} جنيه</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <span class="info-label">تاريخ الشحن للمحفظة</span>
                                    <span class="info-value">{{ $returnRequest->wallet_credited_at ? $returnRequest->wallet_credited_at->format('Y-m-d H:i') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Products -->
            <div class="cancel-detail-card mb-4">
                <div class="cancel-detail-header">
                    <h5><i class="fas fa-box me-2"></i> المنتجات المطلوب إلغاؤها</h5>
                </div>
                <div class="cancel-detail-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($returnRequest->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($item->product?->images?->first())
                                                    <img src="{{ asset('storage/' . $item->product->images->first()->file_path) }}" 
                                                         alt="" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                                @else
                                                    <div class="cancel-avatar bg-secondary"><i class="fas fa-box"></i></div>
                                                @endif
                                                <span>{{ $item->product?->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $item->return_quantity ?? '-' }}</td>
                                        <td>{{ number_format($item->return_price ?? 0, 2) }} جنيه</td>
                                        <td class="fw-bold">{{ number_format($item->return_price ?? 0, 2) }} جنيه</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">لا توجد منتجات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Complaints -->
            @if ($returnRequest->complaints->count() > 0)
                <div class="cancel-detail-card mb-4">
                    <div class="cancel-detail-header">
                        <h5><i class="fas fa-exclamation-triangle me-2 text-warning"></i> الشكاوى ({{ $returnRequest->complaints->count() }})</h5>
                    </div>
                    <div class="cancel-detail-body">
                        @foreach ($returnRequest->complaints as $complaint)
                            <div class="complaint-box mb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $complaint->title ?? 'شكوى' }}</strong>
                                        <p class="mb-0 mt-1 text-muted">{{ $complaint->description }}</p>
                                    </div>
                                    <span class="badge bg-{{ $complaint->status === 'pending' ? 'warning' : ($complaint->status === 'resolved' ? 'success' : 'secondary') }}">
                                        {{ $complaint->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Info -->
            <div class="cancel-detail-card mb-4">
                <div class="cancel-detail-header">
                    <h5><i class="fas fa-user me-2 text-primary"></i> بيانات العميل</h5>
                </div>
                <div class="cancel-detail-body">
                    <div class="info-item mb-2">
                        <span class="info-label">اسم العميل</span>
                        <span class="info-value fw-semibold">{{ $returnRequest->user->username ?? '-' }}</span>
                    </div>
                    <div class="info-item mb-2">
                        <span class="info-label">البريد الإلكتروني</span>
                        <span class="info-value">{{ $returnRequest->user->email ?? '-' }}</span>
                    </div>
                    <div class="info-item mb-2">
                        <span class="info-label">رقم الهاتف</span>
                        <span class="info-value">{{ $returnRequest->user->phone ?? '-' }}</span>
                    </div>
                    <div class="info-item mb-2">
                        <span class="info-label">رقم العميل</span>
                        <span class="info-value">USR-{{ str_pad($returnRequest->user_id, 8, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="info-item mb-0">
                        <span class="info-label">المحفظة</span>
                        <span class="info-value">
                            @if ($returnRequest->user->wallet)
                                {{ number_format($returnRequest->user->wallet->balance, 2) }} جنيه
                            @else
                                <span class="text-warning">لا توجد محفظة</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Seller Info -->
            @if ($seller)
                <div class="cancel-detail-card mb-4">
                    <div class="cancel-detail-header">
                        <h5><i class="fas fa-store me-2 text-purple"></i> بيانات البائع</h5>
                    </div>
                    <div class="cancel-detail-body">
                        <div class="info-item mb-2">
                            <span class="info-label">اسم البائع</span>
                            <span class="info-value fw-semibold">{{ $seller->name ?? '-' }}</span>
                        </div>
                        <div class="info-item mb-2">
                            <span class="info-label">البريد الإلكتروني</span>
                            <span class="info-value">{{ $seller->email ?? '-' }}</span>
                        </div>
                        <div class="info-item mb-0">
                            <span class="info-label">رقم البائع</span>
                            <span class="info-value">VDR-{{ str_pad($seller->id, 8, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="cancel-detail-card">
                <div class="cancel-detail-header">
                    <h5><i class="fas fa-cogs me-2"></i> الإجراءات</h5>
                </div>
                <div class="cancel-detail-body">
                    @if ($statusValue === 'requested')
                        <button type="button" class="btn btn-warning text-white w-100 mb-2"
                                data-bs-toggle="modal" data-bs-target="#reactivateModal">
                            <i class="fas fa-redo me-1"></i> إعادة تفعيل طلب الإلغاء
                        </button>
                    @endif

                    @if ($statusValue === 'approved' && $returnRequest->seller_status && is_object($returnRequest->seller_status) && $returnRequest->seller_status->value === 'approved')
                        <button type="button" class="btn btn-success text-white w-100 mb-2"
                                data-bs-toggle="modal" data-bs-target="#completeModal">
                            <i class="fas fa-wallet me-1"></i> إكمال واسترداد للمحفظة
                        </button>
                    @endif

                    <a href="{{ route('admin.cancellations.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-list me-1"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reactivate Modal -->
    <div class="modal fade" id="reactivateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.cancellations.reactivate', $returnRequest->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إعادة تفعيل طلب الإلغاء</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            سيتم إعادة تفعيل طلب الإلغاء رقم <strong>{{ $returnRequest->return_number }}</strong>
                            وسيتم إرسال إشعار للبائع والعميل.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">سبب إعادة التفعيل <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required 
                                      placeholder="أدخل سبب إعادة تفعيل طلب الإلغاء..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="fas fa-redo me-1"></i> إعادة تفعيل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Complete Modal -->
    <div class="modal fade" id="completeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.cancellations.complete', $returnRequest->id) }}">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-wallet me-1"></i> إكمال طلب الإلغاء واسترداد المبلغ
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="fas fa-info-circle me-1"></i>
                            سيتم إضافة المبلغ إلى محفظة ميتوزون الخاصة بالعميل وإرسال إشعار بالعملية.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                مبلغ الاسترداد (بعد خصم الرسوم) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="admin_refund_amount" class="form-control form-control-lg" 
                                       step="0.01" min="0" required 
                                       placeholder="أدخل مبلغ الاسترداد"
                                       value="{{ $returnRequest->refund_amount ?? '' }}">
                                <span class="input-group-text">جنيه</span>
                            </div>
                            <small class="text-muted">أدخل المبلغ النهائي بعد خصم أي رسوم</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ملاحظات الإدارة</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="أدخل أي ملاحظات إضافية...">{{ $returnRequest->notes }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success text-white">
                            <i class="fas fa-check-circle me-1"></i> إكمال طلب الإلغاء وإضافة المبلغ للمحفظة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .cancel-detail-card { background: #fff; border-radius: 22px; border: 1px solid #ECE6F2; box-shadow: 0 12px 30px rgba(15,23,42,.05); overflow: hidden; }
        .cancel-detail-header { padding: 1.1rem 1.25rem; border-bottom: 1px solid #ECE6F2; display: flex; justify-content: space-between; align-items: center; }
        .cancel-detail-header h5 { margin: 0; font-weight: 800; color: #181022; font-size: .95rem; }
        .cancel-detail-body { padding: 1.25rem; }
        .info-item { padding: .65rem; background: #F7F5FA; border-radius: 12px; }
        .info-label { display: block; color: #7C7285; font-size: .78rem; font-weight: 700; margin-bottom: .2rem; }
        .info-value { color: #181022; font-size: .9rem; }
        .cancel-avatar { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .85rem; flex-shrink: 0; }
        .cancel-avatar.bg-secondary { background: #94a3b8; }
        .cancel-status-badge { border-radius: 999px; padding: .35rem .85rem; font-weight: 600; font-size: .78rem; }
        .complaint-box { padding: .85rem; background: #FFF8E1; border-radius: 12px; border: 1px solid #FDE68A; }
        .table th { background: #F7F5FA; color: #7C7285; font-weight: 700; font-size: .82rem; border-color: #ECE6F2; }
        .table td { border-color: #F0ECF5; vertical-align: middle; }
        .text-purple { color: #7B00A8 !important; }
    </style>
    @endpush
@endsection
