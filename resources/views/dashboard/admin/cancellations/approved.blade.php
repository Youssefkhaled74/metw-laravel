@extends('layouts.admin')

@section('title', 'طلبات إلغاء موافق عليها')
@section('page-title', 'قسم طلبات إلغاء موافق عليها')

@section('page-actions')
    <a href="{{ route('admin.cancellations.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> العودة لطلبات الإلغاء
    </a>
@endsection

@section('content')
    <div class="cancel-card">
        <div class="cancel-card-header">
            <div>
                <h5><i class="fas fa-check-circle me-2 text-success"></i> طلبات الإلغاء الموافق عليها</h5>
                <p class="text-muted mb-0 mt-1" style="font-size:.85rem;">الطلبات التيوافق البائع عليها وتحتاج إكمال من الإدارة مع استرداد المبلغ للمحفظة</p>
            </div>
            <span class="badge bg-success rounded-pill">{{ $cancellations->total() }} طلب</span>
        </div>

        <div class="cancel-card-body">
            <form method="GET" action="{{ route('admin.cancellations.approved') }}" class="cancel-filter-form">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-6">
                        <div class="cancel-search">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="ابحث برقم الإلغاء، رقم الطلب، اسم العميل..." class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-filter me-1"></i> تطبيق
                        </button>
                        <a href="{{ route('admin.cancellations.approved') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>

            @if ($cancellations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>رقم الإلغاء</th>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>البائع</th>
                                <th>المنتجات</th>
                                <th>السبب</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cancellations as $returnRequest)
                                @php
                                    $seller = $returnRequest->items->first()?->orderItem?->product?->vendor;
                                    $products = $returnRequest->items->pluck('product.name')->filter()->implode('، ');
                                    if (! $products) {
                                        $products = '-';
                                    }
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
                                        <small>{{ Str::limit($products, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $returnRequest->reason ?? '-' }}</span>
                                    </td>
                                    <td>{{ $returnRequest->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.cancellations.show', $returnRequest->id) }}" 
                                               class="btn btn-sm btn-primary" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success text-white"
                                                    data-bs-toggle="modal" data-bs-target="#completeModal{{ $returnRequest->id }}"
                                                    title="إكمال طلب الإلغاء واسترداد المبلغ">
                                                <i class="fas fa-wallet"></i>
                                            </button>
                                        </div>

                                        <!-- Complete Cancellation Modal -->
                                        <div class="modal fade" id="completeModal{{ $returnRequest->id }}" tabindex="-1">
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
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <div class="info-box">
                                                                        <label>رقم طلب الإلغاء</label>
                                                                        <span class="fw-bold text-primary">{{ $returnRequest->return_number }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="info-box">
                                                                        <label>رقم الطلب</label>
                                                                        <span class="fw-bold">{{ $returnRequest->order->order_number ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <div class="info-box">
                                                                        <label>العميل</label>
                                                                        <span class="fw-semibold">{{ $returnRequest->user->username ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="info-box">
                                                                        <label>البريد الإلكتروني</label>
                                                                        <span>{{ $returnRequest->user->email ?? '-' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <div class="info-box">
                                                                        <label>رقم المحفظة</label>
                                                                        <span class="fw-bold">
                                                                            @if ($returnRequest->user->wallet)
                                                                                {{ $returnRequest->user->wallet->id }}
                                                                            @else
                                                                                <span class="text-warning">سيتم إنشاء محفظة جديدة</span>
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="info-box">
                                                                        <label>الرصيد الحالي</label>
                                                                        <span class="fw-bold">
                                                                            {{ number_format($returnRequest->user->wallet->balance ?? 0, 2) }} جنيه
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <hr>

                                                            <div class="alert alert-success">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                سيتم إضافة المبلغ المحدد إلى محفظة ميتوزون الخاصة بالعميل
                                                                وإرسال إشعار بالعملية.
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="cancel-empty">
                                            <i class="fas fa-check-circle"></i>
                                            <p>لا توجد طلبات إلغاء موافق عليها حالياً</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $cancellations->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="cancel-empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h5>لا توجد طلبات إلغاء موافق عليها</h5>
                    <p>جميع طلبات الإلغاء التيوافق عليها البائع تم معالجتها.</p>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .cancel-card { background: #fff; border-radius: 22px; border: 1px solid #ECE6F2; box-shadow: 0 12px 30px rgba(15,23,42,.05); overflow: hidden; }
        .cancel-card-header { padding: 1.25rem; border-bottom: 1px solid #ECE6F2; display: flex; justify-content: space-between; align-items: flex-start; }
        .cancel-card-header h5 { margin: 0; font-weight: 800; color: #181022; }
        .cancel-card-body { padding: 1.25rem; }
        .cancel-search { position: relative; }
        .cancel-search i { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #7C7285; }
        .cancel-search input { border-radius: 12px; padding-right: 2.5rem; min-height: 44px; border-color: #ECE6F2; }
        .cancel-search input:focus { border-color: #7B00A8; box-shadow: 0 0 0 .2rem rgba(123,0,168,.1); }
        .cancel-avatar { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .85rem; flex-shrink: 0; }
        .cancel-avatar.bg-purple { background: linear-gradient(135deg, #7B00A8, #4C0078); }
        .cancel-avatar.bg-primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .cancel-status-badge { border-radius: 999px; padding: .35rem .85rem; font-weight: 600; font-size: .78rem; }
        .cancel-empty { padding: 2rem; color: #7C7285; }
        .cancel-empty i { font-size: 2rem; color: #35D36F; margin-bottom: .5rem; display: block; }
        .cancel-empty-state { text-align: center; padding: 3rem; }
        .cancel-empty-state i { font-size: 3rem; color: #35D36F; margin-bottom: 1rem; }
        .cancel-empty-state h5 { color: #181022; font-weight: 800; }
        .cancel-empty-state p { color: #7C7285; }
        .table th { background: #F7F5FA; color: #7C7285; font-weight: 700; font-size: .82rem; border-color: #ECE6F2; }
        .table td { border-color: #F0ECF5; vertical-align: middle; }
        .info-box { padding: .75rem; background: #F7F5FA; border-radius: 12px; margin-bottom: .5rem; }
        .info-box label { display: block; color: #7C7285; font-size: .78rem; font-weight: 700; margin-bottom: .25rem; }
        .info-box span { color: #181022; font-size: .9rem; }
    </style>
    @endpush
@endsection
