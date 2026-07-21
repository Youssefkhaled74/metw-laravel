<div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 {{ $currentSort === 'priority' ? 'active' : '' }}"
                data-sort="priority" onclick="changeSort('priority')">
            <i class="fas fa-flag me-1"></i> حسب الأولوية
        </button>
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 {{ $currentSort === 'newest' ? 'active' : '' }}"
                data-sort="newest" onclick="changeSort('newest')">
            <i class="fas fa-clock me-1"></i> الأحدث
        </button>
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 {{ $currentSort === 'oldest' ? 'active' : '' }}"
                data-sort="oldest" onclick="changeSort('oldest')">
            <i class="fas fa-history me-1"></i> الأقدم
        </button>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 {{ request()->get('type', 'all') === 'all' ? 'active' : '' }}"
                data-type="all">
            <i class="fas fa-layer-group me-1"></i> الكل
            <span class="badge bg-secondary ms-1" id="badge-all">{{ $stats['total'] }}</span>
        </button>
        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 {{ request()->get('type') === 'purchase' ? 'active' : '' }}"
                data-type="purchase">
            <i class="fas fa-shopping-cart me-1"></i> مشتريات
            <span class="badge bg-primary ms-1" id="badge-purchases">{{ $stats['purchases'] }}</span>
        </button>
        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 {{ request()->get('type') === 'cancellation' ? 'active' : '' }}"
                data-type="cancellation">
            <i class="fas fa-ban me-1"></i> إلغاء
            <span class="badge bg-danger ms-1" id="badge-cancellations">{{ $stats['cancellations'] }}</span>
        </button>
        <button class="btn btn-sm btn-outline-warning rounded-pill px-3 {{ request()->get('type') === 'return' ? 'active' : '' }}"
                data-type="return">
            <i class="fas fa-undo me-1"></i> إرجاع
            <span class="badge bg-warning ms-1" id="badge-returns">{{ $stats['returns'] }}</span>
        </button>
        <button class="btn btn-sm btn-outline-info rounded-pill px-3 {{ request()->get('type') === 'shipping' ? 'active' : '' }}"
                data-type="shipping">
            <i class="fas fa-truck me-1"></i> شحن
            <span class="badge bg-info ms-1" id="badge-shipping">{{ $stats['shipping'] }}</span>
        </button>
        <button class="btn btn-sm btn-outline-warning rounded-pill px-3 {{ request()->get('type') === 'notification' ? 'active' : '' }}"
                data-type="notification">
            <i class="fas fa-bell me-1"></i> إشعارات
            <span class="badge bg-warning ms-1" id="badge-notifications">{{ $stats['notifications'] ?? 0 }}</span>
        </button>
    </div>
</div>

@push('scripts')
<script>
function changeSort(sort) {
    currentSort = sort;
    document.querySelectorAll('[data-sort]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.sort === sort);
    });
    fetchTasks();
}
</script>
@endpush
