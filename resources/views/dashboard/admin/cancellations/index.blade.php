@extends('layouts.admin')

@section('title', 'طلبات الإلغاء')
@section('page-title', 'إدارة طلبات الإلغاء')

@section('page-actions')
    <a href="{{ route('admin.cancellations.complaints') }}" class="btn btn-warning text-white">
        <i class="fas fa-exclamation-triangle me-1"></i> شكاوى الإلغاء
    </a>
    <a href="{{ route('admin.cancellations.approved') }}" class="btn btn-success text-white">
        <i class="fas fa-check-circle me-1"></i> الإلغاءات الموافق عليها
    </a>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> العودة للوحة التحكم
    </a>
@endsection

@section('content')
    <div class="cancel-card">
        <div class="cancel-card-header">
            <h5><i class="fas fa-times-circle me-2"></i> جميع طلبات الإلغاء</h5>
            <span class="badge bg-secondary rounded-pill">{{ $returnRequests->total() }} طلب</span>
        </div>

        <div class="cancel-card-body">
            <form method="GET" action="{{ route('admin.cancellations.index') }}" class="cancel-filter-form">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-5">
                        <div class="cancel-search">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="ابحث برقم الإلغاء، رقم الطلب، اسم العميل..." class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <select name="status" class="form-select">
                            <option value="all">كل الحالات</option>
                            <option value="requested" {{ request('status') === 'requested' ? 'selected' : '' }}>بانتظار المراجعة</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>موافق عليه</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                    <div class="col-lg-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i> تطبيق
                        </button>
                        <a href="{{ route('admin.cancellations.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>

            @if ($returnRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>رقم الإلغاء</th>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>البائع</th>
                                <th>حالة الطلب</th>
                                <th>حالة البائع</th>
                                <th>السبب</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($returnRequests as $returnRequest)
                                @php
                                    $seller = $returnRequest->items->first()?->orderItem?->product?->vendor;
                                    $statusValue = is_object($returnRequest->status) ? $returnRequest->status->value : (string) $returnRequest->status;
                                    $statusColor = match($statusValue) {
                                        'completed', 'approved' => 'success',
                                        'requested', 'pickup', 'processing' => 'warning',
                                        'rejected', 'cancelled' => 'danger',
                                        default => 'info',
                                    };
                                    $sellerStatusValue = $returnRequest->seller_status 
                                        ? (is_object($returnRequest->seller_status) ? $returnRequest->seller_status->value : $returnRequest->seller_status)
                                        : null;
                                    $sellerStatusCss = $sellerStatusValue 
                                        ? match($sellerStatusValue) {
                                            'approved', 'return_accepted' => 'success',
                                            'rejected', 'return_rejected' => 'danger',
                                            'under_inspection' => 'warning',
                                            default => 'secondary',
                                        }
                                        : 'secondary';
                                    $sellerStatusLabel = $sellerStatusValue
                                        ? match($sellerStatusValue) {
                                            'approved' => 'طلب إلغاء موافق عليه',
                                            'rejected' => 'شكوى طلب إلغاء مرفوض',
                                            'under_inspection' => 'تم استلام المنتج وجاري الفحص',
                                            'return_accepted' => 'مرتجع مقبول',
                                            'return_rejected' => 'طلب إلغاء شكوى مرتجع مرفوض',
                                            default => $sellerStatusValue,
                                        }
                                        : 'بانتظار رد البائع';
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $returnRequest->return_number }}</span>
                                    </td>
                                    <td>{{ $returnRequest->order->order_number ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="cancel-avatar bg-primary">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $returnRequest->user->username ?? '-' }}</div>
                                                <small class="text-muted">{{ $returnRequest->user->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($seller)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="cancel-avatar bg-purple">
                                                    <i class="fas fa-store"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $seller->name ?? '-' }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="cancel-status-badge badge bg-{{ $statusColor }}">
                                            {{ $statusValue }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="cancel-status-badge badge bg-{{ $sellerStatusCss }}">
                                            {{ $sellerStatusLabel }}
                                        </span>
                                    </td>
                                    <td>{{ $returnRequest->reason ?? '-' }}</td>
                                    <td>{{ $returnRequest->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.cancellations.show', $returnRequest->id) }}" 
                                           class="btn btn-sm btn-primary" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="cancel-empty">
                                            <i class="fas fa-check-circle"></i>
                                            <p>لا توجد طلبات إلغاء حالياً</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $returnRequests->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="cancel-empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h5>لا توجد طلبات إلغاء</h5>
                    <p>لم يتم العثور على طلبات إلغاء مطابقة لمعايير البحث.</p>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .cancel-card { background: #fff; border-radius: 22px; border: 1px solid #ECE6F2; box-shadow: 0 12px 30px rgba(15,23,42,.05); overflow: hidden; }
        .cancel-card-header { padding: 1.25rem; border-bottom: 1px solid #ECE6F2; display: flex; justify-content: space-between; align-items: center; }
        .cancel-card-header h5 { margin: 0; font-weight: 800; color: #181022; }
        .cancel-card-body { padding: 1.25rem; }
        .cancel-search { position: relative; }
        .cancel-search i { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #7C7285; }
        .cancel-search input { border-radius: 12px; padding-right: 2.5rem; min-height: 44px; border-color: #ECE6F2; }
        .cancel-search input:focus { border-color: #7B00A8; box-shadow: 0 0 0 .2rem rgba(123,0,168,.1); }
        .cancel-avatar { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .85rem; flex-shrink: 0; }
        .cancel-avatar.bg-purple { background: linear-gradient(135deg, #7B00A8, #4C0078); }
        .cancel-status-badge { border-radius: 999px; padding: .35rem .85rem; font-weight: 600; font-size: .78rem; }
        .cancel-empty { padding: 2rem; color: #7C7285; }
        .cancel-empty i { font-size: 2rem; color: #35D36F; margin-bottom: .5rem; display: block; }
        .cancel-empty-state { text-align: center; padding: 3rem; }
        .cancel-empty-state i { font-size: 3rem; color: #35D36F; margin-bottom: 1rem; }
        .cancel-empty-state h5 { color: #181022; font-weight: 800; }
        .cancel-empty-state p { color: #7C7285; }
        .table th { background: #F7F5FA; color: #7C7285; font-weight: 700; font-size: .82rem; border-color: #ECE6F2; }
        .table td { border-color: #F0ECF5; vertical-align: middle; }
    </style>
    @endpush
@endsection
