@extends('layouts.vendor')

@section('title', 'المهام العاجلة')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">المهام العاجلة</h4>
        <p class="text-muted mb-0 small">جميع الطلبات والمهام التي تتطلب اهتمامك الفوري</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success-subtle text-success px-3 py-2 d-flex align-items-center gap-1" id="auto-refresh-indicator">
            <i class="fas fa-sync-alt fa-spin"></i> تحديث تلقائي
        </span>
    </div>
</div>

@include('dashboard.vendor.urgent-tasks.partials.stats-cards', ['stats' => $stats])

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                @include('dashboard.vendor.urgent-tasks.partials.filter-bar')
            </div>
            <div class="col-md-4">
                <input type="text"
                       class="form-control"
                       id="search-input"
                       placeholder="بحث برقم الطلب أو اسم العميل..."
                       value="{{ $currentSearch }}">
            </div>
        </div>
    </div>
</div>

<div id="tasks-container">
    @include('dashboard.vendor.urgent-tasks.partials.task-list', ['tasks' => $tasks])
</div>

@include('dashboard.vendor.urgent-tasks.partials.empty-state')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const baseUrl = '{{ url("vendor") }}';
    let currentType = '{{ request()->get("type", "all") }}';
    let currentSort = '{{ $currentSort }}';
    let searchTimeout = null;

    function fetchTasks() {
        const params = new URLSearchParams({
            type: currentType,
            sort: currentSort,
            search: document.getElementById('search-input').value,
        });

        fetch(`${baseUrl}/urgent-tasks/filter?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('tasks-container').innerHTML = data.html;
                updateStats(data.stats);
            }
        });
    }

    function updateStats(stats) {
        document.getElementById('stat-total').textContent = stats.total;
        document.getElementById('stat-critical').textContent = stats.critical;
        document.getElementById('stat-high').textContent = stats.high;
        document.getElementById('stat-purchases').textContent = stats.purchases;
        document.getElementById('stat-cancellations').textContent = stats.cancellations;
        document.getElementById('stat-returns').textContent = stats.returns;
        document.getElementById('stat-shipping').textContent = stats.shipping;
    }

    // Filter tabs
    document.querySelectorAll('[data-type]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('[data-type]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentType = this.dataset.type;
            fetchTasks();
        });
    });

    // Sort select
    document.getElementById('sort-select')?.addEventListener('change', function () {
        currentSort = this.value;
        fetchTasks();
    });

    // Search input
    document.getElementById('search-input')?.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(fetchTasks, 400);
    });

    // Process task
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.process-task-btn');
        if (!btn) return;

        e.preventDefault();
        const type = btn.dataset.taskType;
        const id = btn.dataset.taskId;

        if (!confirm('هل أنت متأكد من معالجة هذه المهمة؟')) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(`${baseUrl}/urgent-tasks/process`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ type, id }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                updateStats(data.stats);
                fetchTasks();
            } else {
                showToast(data.message || 'حدث خطأ', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i>';
            }
        })
        .catch(() => {
            showToast('حدث خطأ في الاتصال', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i>';
        });
    });

    // Auto-refresh every 30 seconds
    setInterval(fetchTasks, 30000);

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
        toast.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px; animation: fadeInDown 0.3s ease;';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
});
</script>
@endpush
