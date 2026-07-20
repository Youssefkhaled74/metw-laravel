<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #dc3545, #fd7e14);">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1">مهام حرجة</h6>
                    <h2 class="text-white fw-bold mb-0" id="stat-critical">{{ $stats['critical'] }}</h2>
                </div>
                <div class="stat-icon text-white-50">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fd7e14, #ffc107);">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1">مهام عالية</h6>
                    <h2 class="text-white fw-bold mb-0" id="stat-high">{{ $stats['high'] }}</h2>
                </div>
                <div class="stat-icon text-white-50">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1">إجمالي المهام</h6>
                    <h2 class="text-white fw-bold mb-0" id="stat-total">{{ $stats['total'] }}</h2>
                </div>
                <div class="stat-icon text-white-50">
                    <i class="fas fa-list-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0dcaf0, #6610f2);">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1">شحن نشط</h6>
                    <h2 class="text-white fw-bold mb-0" id="stat-shipping">{{ $stats['shipping'] }}</h2>
                </div>
                <div class="stat-icon text-white-50">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
    </div>
</div>
