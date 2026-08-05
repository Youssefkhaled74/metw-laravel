@extends('layouts.admin')

@section('title', __('admin-dashboard.admin_dashboard'))
@section('page-title', __('admin-dashboard.dashboard_overview'))

@section('content')
    @php
        $stats = $stats ?? [];
        $dashboardCycles = $dashboardCycles ?? [];
        $cycleUiLabels = $cycleUiLabels ?? [];
        $warehouseControl = $warehouseControl ?? [];
        $isArabic = app()->getLocale() === 'ar';

        $text = fn (string $english, string $arabic) => $isArabic ? $arabic : $english;

        $safeNumber = fn ($value) => number_format((int) ($value ?? 0));
        $label = fn ($key, $fallback) => __($key) === $key ? $fallback : __($key);
        $routeOrNull = fn ($name, $params = []) => Route::has($name) ? route($name, $params) : null;

        $actionCards = [
            [
                'label' => $text('Pending Shipments', 'طلبات الشحن المعلقة'),
                'value' => $stats['pending_shipment_requests'] ?? $stats['pending_shipment_orders'] ?? 0,
                'helper' => $text('Waiting for review, pricing, or assignment.', 'في انتظار المراجعة أو التسعير أو التعيين.'),
                'icon' => 'fas fa-clock',
                'tone' => 'warning',
                'badge' => $text('Needs action', 'يحتاج إجراء'),
                'url' => $routeOrNull('admin.shipment-orders'),
            ],
            [
                'label' => $text('Pending Vendors', 'الموردون المعلقون'),
                'value' => $stats['pending_vendor_approvals'] ?? 0,
                'helper' => $text('Business profiles waiting for approval.', 'الملفات التجارية في انتظار الموافقة.'),
                'icon' => 'fas fa-store-alt',
                'tone' => 'danger',
                'badge' => $text('Review', 'مراجعة'),
                'url' => $routeOrNull('admin.vendors'),
            ],
            [
                'label' => $text('Pending Warehouses', 'المستودعات المعلقة'),
                'value' => $stats['pending_warehouse_approvals'] ?? 0,
                'helper' => $text('Warehouses waiting for admin approval.', 'المستودعات في انتظار موافقة الإدارة.'),
                'icon' => 'fas fa-warehouse',
                'tone' => 'primary',
                'badge' => $text('Review', 'مراجعة'),
                'url' => $routeOrNull('admin.settings.warehouses.index'),
            ],
            [
                'label' => $text('Pending Orders', 'الطلبات المعلقة'),
                'value' => $stats['pending_ecommerce_orders'] ?? 0,
                'helper' => $text('Ecommerce orders that still need follow-up.', 'طلبات المتجر التي ما زالت تحتاج متابعة.'),
                'icon' => 'fas fa-shopping-bag',
                'tone' => 'info',
                'badge' => $text('Follow up', 'متابعة'),
                'url' => $routeOrNull('admin.orders'),
            ],
        ];

        $businessHealthCards = [
            [
                'label' => $label('admin-dashboard.total_users', 'Total Users'),
                'value' => $stats['total_users'] ?? 0,
                'helper' => $text('All registered customer accounts.', 'جميع حسابات العملاء المسجلة.'),
                'icon' => 'fas fa-users',
                'tone' => 'primary',
                'url' => $routeOrNull('admin.users'),
            ],
            [
                'label' => $label('admin-dashboard.total_vendors', 'Total Vendors'),
                'value' => $stats['total_vendors'] ?? 0,
                'helper' => $text('Vendor accounts inside the platform.', 'حسابات الموردين داخل المنصة.'),
                'icon' => 'fas fa-store',
                'tone' => 'success',
                'url' => $routeOrNull('admin.vendors'),
            ],
            [
                'label' => $label('admin-dashboard.shipment_companies', 'Shipment Companies'),
                'value' => $stats['total_shipment_companies'] ?? $stats['active_shipment_companies'] ?? 0,
                'helper' => $text('Companies connected to shipping operations.', 'الشركات المرتبطة بعمليات الشحن.'),
                'icon' => 'fas fa-truck-moving',
                'tone' => 'info',
                'url' => $routeOrNull('admin.shipment-companies'),
            ],
            [
                'label' => $label('admin-dashboard.total_products', 'Total Products'),
                'value' => $stats['total_products'] ?? 0,
                'helper' => $text('Products currently available in the system.', 'المنتجات المتوفرة حاليًا في النظام.'),
                'icon' => 'fas fa-box',
                'tone' => 'warning',
                'url' => $routeOrNull('admin.products'),
            ],
        ];

        $phase2Cards = [
            [
                'label' => $text('Shipment Requests', 'طلبات الشحن'),
                'value' => $stats['total_shipment_requests'] ?? $stats['total_shipment_orders'] ?? 0,
                'helper' => $text('New shipping request flow from Phase 2.', 'مسار طلبات الشحن الجديد في المرحلة الثانية.'),
                'icon' => 'fas fa-file-invoice',
                'tone' => 'primary',
                'url' => $routeOrNull('admin.shipment-orders'),
            ],
            [
                'label' => $text('Assigned Shipments', 'الطلبات المعينة'),
                'value' => $stats['assigned_shipment_requests'] ?? 0,
                'helper' => $text('Requests already assigned to a company or courier.', 'الطلبات التي تم تعيينها لشركة أو مندوب بالفعل.'),
                'icon' => 'fas fa-clipboard-check',
                'tone' => 'info',
                'url' => $routeOrNull('admin.shipment-orders'),
            ],
            [
                'label' => $text('Completed Shipments', 'الطلبات المكتملة'),
                'value' => $stats['completed_shipment_requests'] ?? $stats['completed_shipment_orders'] ?? 0,
                'helper' => $text('Delivered or completed shipment requests.', 'طلبات الشحن التي تم تسليمها أو إكمالها.'),
                'icon' => 'fas fa-check-circle',
                'tone' => 'success',
                'url' => $routeOrNull('admin.shipment-orders'),
            ],
            [
                'label' => $text('Cancelled Shipments', 'الطلبات الملغاة'),
                'value' => $stats['cancelled_shipment_requests'] ?? $stats['cancelled_shipment_orders'] ?? 0,
                'helper' => $text('Cancelled or rejected shipment requests.', 'طلبات الشحن الملغاة أو المرفوضة.'),
                'icon' => 'fas fa-ban',
                'tone' => 'danger',
                'url' => $routeOrNull('admin.shipment-orders'),
            ],
            [
                'label' => $text('Approved Vendors', 'الموردون المعتمدون'),
                'value' => $stats['approved_vendors'] ?? 0,
                'helper' => $text('Vendors approved to operate normally.', 'الموردون المعتمدون للعمل بشكل طبيعي.'),
                'icon' => 'fas fa-user-check',
                'tone' => 'success',
                'url' => $routeOrNull('admin.vendors'),
            ],
            [
                'label' => $text('Approved Warehouses', 'المستودعات المعتمدة'),
                'value' => $stats['approved_warehouses'] ?? 0,
                'helper' => $text('Warehouses approved for business use.', 'المستودعات المعتمدة للاستخدام التجاري.'),
                'icon' => 'fas fa-warehouse',
                'tone' => 'primary',
                'url' => $routeOrNull('admin.settings.warehouses.index'),
            ],
            [
                'label' => $text('Active Representatives', 'المندوبون النشطون'),
                'value' => $stats['active_representatives'] ?? 0,
                'helper' => $text('Representatives available for operations.', 'المندوبون المتاحون للعمليات.'),
                'icon' => 'fas fa-user-tie',
                'tone' => 'info',
                'url' => $routeOrNull('admin.representatives.index'),
            ],
            [
                'label' => $text('Ecommerce Orders', 'طلبات المتجر'),
                'value' => $stats['total_ecommerce_orders'] ?? 0,
                'helper' => $text('Marketplace order flow overview.', 'نظرة عامة على طلبات المتجر.'),
                'icon' => 'fas fa-shopping-cart',
                'tone' => 'warning',
                'url' => $routeOrNull('admin.orders'),
            ],
        ];

        $quickLinks = [
            [
                'label' => $text('Shipment Requests', 'طلبات الشحن'),
                'helper' => $text('Review sender, receiver, package, status, and assignment.', 'راجع بيانات المرسل والمستلم والطرد والحالة والتعيين.'),
                'icon' => 'fas fa-shipping-fast',
                'url' => $routeOrNull('admin.shipment-orders'),
            ],
            [
                'label' => $text('Vendors', 'الموردون'),
                'helper' => $text('Review vendor profile, branches, products, and approvals.', 'راجع ملف المورد والفروع والمنتجات والموافقات.'),
                'icon' => 'fas fa-store',
                'url' => $routeOrNull('admin.vendors'),
            ],
            [
                'label' => $text('Warehouses', 'المستودعات'),
                'helper' => $text('Manage warehouse approval and business data.', 'إدارة موافقات المستودعات وبياناتها التجارية.'),
                'icon' => 'fas fa-warehouse',
                'url' => $routeOrNull('admin.settings.warehouses.index'),
            ],
            [
                'label' => $text('Shipment Companies', 'شركات الشحن'),
                'helper' => $text('Manage shipping partners and company coverage.', 'إدارة شركاء الشحن وتغطية الشركات.'),
                'icon' => 'fas fa-truck',
                'url' => $routeOrNull('admin.shipment-companies'),
            ],
            [
                'label' => $text('Governorates', 'المحافظات'),
                'helper' => $text('Open the governorate CRUD and manage city grouping.', 'افتح CRUD المحافظات وأدر تجميع المدن.'),
                'icon' => 'fas fa-map-location-dot',
                'url' => $routeOrNull('admin.settings.governorates.index'),
            ],
            [
                'label' => $text('Cities', 'المدن'),
                'helper' => $text('Open the city CRUD and manage governorate links.', 'افتح CRUD المدن وأدر ربطها بالمحافظات.'),
                'icon' => 'fas fa-city',
                'url' => $routeOrNull('admin.settings.cities.index'),
            ],
        ];

        $monthlyRevenueRows = collect($monthly_revenue['shipment'] ?? [])
            ->merge($monthly_revenue['ecommerce'] ?? [])
            ->filter(fn ($item) => isset($item->year, $item->month))
            ->map(fn ($item) => ['y' => (int) $item->year, 'm' => (int) $item->month, 'total' => (float) ($item->total ?? 0)]);

        $revenueMonths = collect(range(11, 0))->map(fn ($i) => now()->subMonths($i))->values();

        $revenueShipment = $revenueMonths->map(function ($date) use ($monthlyRevenueRows) {
            return $monthlyRevenueRows->firstWhere(fn ($r) => $r['y'] === (int) $date->year && $r['m'] === (int) $date->month)['total'] ?? 0;
        });

        $revenueEcommerce = $revenueMonths->map(function ($date) use ($monthlyRevenueRows) {
            return $monthlyRevenueRows->firstWhere(fn ($r) => $r['y'] === (int) $date->year && $r['m'] === (int) $date->month)['total'] ?? 0;
        });

        $revenueLabels = $revenueMonths->map(fn ($date) => $date->translatedFormat('M y'));

        $charts = $charts ?? [];

        $chartSeries = fn (string $key, string $fallbackLabel) => collect($charts[$key] ?? [])
            ->map(fn ($item) => [
                'label' => $label('admin-dashboard.status_' . ($item['label'] ?? 'unknown'), $fallbackLabel . ': ' . ($item['label'] ?? 'unknown')),
                'value' => (int) ($item['value'] ?? 0),
            ])
            ->filter(fn ($item) => $item['value'] > 0)
            ->values()
            ->all();
    @endphp

    <div class="admin-dashboard-page">
        <div class="dashboard-hero mb-4">
            <div class="dashboard-hero-content">
                <div>
                    <span class="dashboard-eyebrow">{{ $text('Metw Admin Control Center', 'مركز تحكم ميتولوجيستيك') }}</span>
                    <h1 class="dashboard-title">{{ $label('admin-dashboard.dashboard_overview', $text('Dashboard Overview', 'المهام العاجلة')) }}</h1>
                    <p class="dashboard-subtitle mb-0">
                        {{ $text('Understand what needs action, what is moving, and what belongs to the new Phase 2 business flow.', 'اعرف ما يحتاج إلى إجراء، وما يتحرك، وما يخص مسار الأعمال الجديد في المرحلة الثانية.') }}
                    </p>
                </div>
                <div class="dashboard-hero-summary">
                    <span class="summary-label">{{ $text('Action items', 'العناصر المطلوبة') }}</span>
                    <strong>{{ $safeNumber(collect($actionCards)->sum('value')) }}</strong>
                    <small>{{ $text('items need attention', 'عنصر يحتاج إلى متابعة') }}</small>
                </div>
            </div>
        </div>

        <section class="dashboard-section mb-4">
            <div class="section-heading">
                <div>
                    <span class="section-kicker">{{ $text('Start here', 'ابدأ من هنا') }}</span>
                    <h2>{{ $text('Action Required', 'الإجراءات المطلوبة') }}</h2>
                    <p>{{ $text('Critical items that need admin review or operational follow-up.', 'عناصر مهمة تحتاج مراجعة من الإدارة أو متابعة تشغيلية.') }}</p>
                </div>
            </div>

            <div class="row g-3">
                @foreach ($actionCards as $card)
                    <div class="col-xl-3 col-md-6">
                        @if (!empty($card['url']))
                            <a href="{{ $card['url'] }}" class="dashboard-card-link">
                        @endif
                            <div class="metric-card metric-card-{{ $card['tone'] }} h-100">
                                <div class="metric-card-top">
                                    <div class="metric-icon"><i class="{{ $card['icon'] }}"></i></div>
                                    <span class="metric-badge">{{ $card['badge'] }}</span>
                                </div>
                                <div class="metric-label">{{ $card['label'] }}</div>
                                <div class="metric-value">{{ $safeNumber($card['value']) }}</div>
                                <p class="metric-helper">{{ $card['helper'] }}</p>
                                <div class="metric-action">
                                    <span>{{ !empty($card['url']) ? $text('Open details', 'عرض التفاصيل') : $text('Route not available', 'المسار غير متاح') }}</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        @if (!empty($card['url']))
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dashboard-section mb-4">
            <div class="section-heading section-heading-inline">
                <div>
                    <span class="section-kicker">{{ $text('Business health', 'صحة المنصة') }}</span>
                    <h2>{{ $text('Main Platform Numbers', 'الأرقام الرئيسية للمنصة') }}</h2>
                    <p>{{ $text('High-level numbers for users, vendors, products, and shipment partners.', 'أرقام عامة للمستخدمين والموردين والمنتجات وشركاء الشحن.') }}</p>
                </div>
            </div>

            <div class="row g-3">
                @foreach ($businessHealthCards as $card)
                    <div class="col-xl-3 col-md-6">
                        @if (!empty($card['url']))
                            <a href="{{ $card['url'] }}" class="dashboard-card-link">
                        @endif
                            <div class="compact-stat-card compact-stat-card-{{ $card['tone'] }} h-100">
                                <div class="compact-stat-icon"><i class="{{ $card['icon'] }}"></i></div>
                                <div class="compact-stat-content">
                                    <span>{{ $card['label'] }}</span>
                                    <strong>{{ $safeNumber($card['value']) }}</strong>
                                    <small>{{ $card['helper'] }}</small>
                                </div>
                            </div>
                        @if (!empty($card['url']))
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dashboard-section mb-4">
            <div class="section-heading">
                <div>
                    <span class="section-kicker">{{ $text('Phase 2 operations', 'عمليات المرحلة الثانية') }}</span>
                    <h2>{{ $text('New Flow Visibility', 'وضوح المسار الجديد') }}</h2>
                    <p>{{ $text('Shipment requests, approvals, warehouses, representatives, and order flow.', 'طلبات الشحن، الموافقات، المستودعات، المندوبون، ومسار الطلبات.') }}</p>
                </div>
            </div>

            <div class="row g-3">
                @foreach ($phase2Cards as $card)
                    <div class="col-xl-3 col-md-6">
                        @if (!empty($card['url']))
                            <a href="{{ $card['url'] }}" class="dashboard-card-link">
                        @endif
                            <div class="operation-card h-100">
                                <div class="operation-icon operation-icon-{{ $card['tone'] }}"><i class="{{ $card['icon'] }}"></i></div>
                                <div class="operation-body">
                                    <span class="operation-label">{{ $card['label'] }}</span>
                                    <strong>{{ $safeNumber($card['value']) }}</strong>
                                    <p>{{ $card['helper'] }}</p>
                                </div>
                            </div>
                        @if (!empty($card['url']))
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dashboard-section mb-4">
            <div class="section-heading section-heading-inline">
                <div>
                    <span class="section-kicker">{{ $text('Analytics', 'التحليلات') }}</span>
                    <h2>{{ $text('Charts & Insights', 'الرسوم البيانية والمؤشرات') }}</h2>
                    <p>{{ $text('Revenue over the last 12 months and the current distribution of orders and requests.', 'الإيرادات خلال آخر 12 شهرًا والتوزيع الحالي للطلبات وطلبات الشحن.') }}</p>
                </div>
            </div>

            <div class="dashboard-charts-grid">
                <div class="chart-panel chart-panel-lg">
                    <div class="chart-panel-head">
                        <div>
                            <span class="chart-panel-title">{{ $text('Monthly Revenue', 'الإيرادات الشهرية') }}</span>
                            <small>{{ $text('Shipment vs ecommerce revenue', 'إيرادات الشحن مقابل إيرادات المتجر') }}</small>
                        </div>
                    </div>
                    <div class="chart-panel-body">
                        <canvas id="adminRevenueChart"
                            data-shipment='@json($revenueShipment)'
                            data-ecommerce='@json($revenueEcommerce)'
                            data-labels='@json($revenueLabels)'
                            data-shipment-label="{{ $text('Shipment Revenue', 'إيرادات الشحن') }}"
                            data-ecommerce-label="{{ $text('Ecommerce Revenue', 'إيرادات المتجر') }}"></canvas>
                    </div>
                </div>

                <div class="chart-panel">
                    <div class="chart-panel-head">
                        <div>
                            <span class="chart-panel-title">{{ $text('Orders by Status', 'الطلبات حسب الحالة') }}</span>
                            <small>{{ $text('Shipping orders', 'طلبات الشحن') }}</small>
                        </div>
                    </div>
                    <div class="chart-panel-body chart-panel-doughnut">
                        <canvas id="adminOrdersStatusChart" data-series='@json($chartSeries('orders_by_status', 'Order'))'></canvas>
                    </div>
                </div>
            </div>

            <div class="dashboard-charts-grid">
                <div class="chart-panel">
                    <div class="chart-panel-head">
                        <div>
                            <span class="chart-panel-title">{{ $text('Ecommerce Orders', 'طلبات المتجر') }}</span>
                            <small>{{ $text('By status', 'حسب الحالة') }}</small>
                        </div>
                    </div>
                    <div class="chart-panel-body chart-panel-doughnut">
                        <canvas id="adminEcommerceStatusChart" data-series='@json($chartSeries('ecommerce_orders_by_status', 'Ecommerce'))'></canvas>
                    </div>
                </div>

                <div class="chart-panel">
                    <div class="chart-panel-head">
                        <div>
                            <span class="chart-panel-title">{{ $text('Shipment Requests', 'طلبات الشحن') }}</span>
                            <small>{{ $text('By status', 'حسب الحالة') }}</small>
                        </div>
                    </div>
                    <div class="chart-panel-body chart-panel-doughnut">
                        <canvas id="adminShipmentRequestsStatusChart" data-series='@json($chartSeries('shipment_requests_by_status', 'Shipment request'))'></canvas>
                    </div>
                </div>

                <div class="chart-panel">
                    <div class="chart-panel-head">
                        <div>
                            <span class="chart-panel-title">{{ $text('Representatives', 'المندوبون') }}</span>
                            <small>{{ $text('By status', 'حسب الحالة') }}</small>
                        </div>
                    </div>
                    <div class="chart-panel-body chart-panel-doughnut">
                        <canvas id="adminRepresentativesStatusChart" data-series='@json($chartSeries('representatives_by_status', 'Representative'))'></canvas>
                    </div>
                </div>
            </div>

            <div class="chart-panel chart-panel-lg">
                <div class="chart-panel-head">
                    <div>
                        <span class="chart-panel-title">{{ $text('Last 7 Days Activity', 'نشاط آخر 7 أيام') }}</span>
                        <small>{{ $text('New users, orders, ecommerce orders and shipment requests per day.', 'المستخدمون والطلبات وطلبات المتجر وطلبات الشحن الجديدة يوميًا.') }}</small>
                    </div>
                </div>
                <div class="chart-panel-body">
                    <canvas id="adminDailyChart"
                        data-users='@json($charts['daily_users']['values'] ?? [])'
                        data-orders='@json($charts['daily_orders']['values'] ?? [])'
                        data-ecommerce='@json($charts['daily_ecommerce_orders']['values'] ?? [])'
                        data-shipments='@json($charts['daily_shipment_requests']['values'] ?? [])'
                        data-labels='@json($charts['daily_users']['labels'] ?? [])'></canvas>
                </div>
            </div>
        </section>

        <section class="dashboard-section mb-4">
            <div class="section-heading">
                <div>
                    <span class="section-kicker">{{ $text('Navigation', 'التنقل') }}</span>
                    <h2>{{ $text('Quick Access', 'وصول سريع') }}</h2>
                    <p>{{ $text('Go directly to the operational pages related to Phase 2.', 'انتقل مباشرة إلى الصفحات التشغيلية الخاصة بالمرحلة الثانية.') }}</p>
                </div>
            </div>

            <div class="row g-3">
                @foreach ($quickLinks as $link)
                    <div class="col-xl-3 col-md-6">
                        @if (!empty($link['url']))
                            <a href="{{ $link['url'] }}" class="quick-link-card">
                        @else
                            <div class="quick-link-card quick-link-disabled">
                        @endif
                                <div class="quick-link-icon"><i class="{{ $link['icon'] }}"></i></div>
                                <div>
                                    <strong>{{ $link['label'] }}</strong>
                                    <p>{{ $link['helper'] }}</p>
                                </div>
                                <span class="quick-link-arrow"><i class="fas fa-arrow-right"></i></span>
                        @if (!empty($link['url']))
                            </a>
                        @else
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <section class="dashboard-section mb-4">
            <div class="section-heading section-heading-inline">
                <div>
                    <span class="section-kicker">{{ $text('Warehouse control', 'التحكم في المستودعات') }}</span>
                    <h2>{{ $text('Warehouse Command Center', 'مركز التحكم في المستودعات') }}</h2>
                    <p>{{ $text('Create warehouses, open the management page, and keep the main warehouse under control from one place.', 'أنشئ المستودعات، وافتح صفحة الإدارة، وتحكم في المستودع الرئيسي من مكان واحد.') }}</p>
                </div>
            </div>

            <div class="warehouse-control-grid">
                <div class="warehouse-control-panel">
                    <div class="warehouse-control-panel-top">
                        <div>
                            <span class="warehouse-control-eyebrow">{{ $text('Operations', 'العمليات') }}</span>
                            <h3 class="warehouse-control-title">{{ $text('Manage the active warehouse footprint', 'إدارة نطاق المستودعات النشطة') }}</h3>
                            <p class="warehouse-control-text">{{ $text('Review the current warehouse setup, promote the main warehouse, and jump straight to create or edit flows.', 'راجع معدل المستودعات، ورقّ للمستودع الرئيسي، وانتقل مباشرة إلى إنشاء أو تعديل مستودع.') }}</p>
                        </div>
                        <div class="warehouse-control-badge">
                            <i class="fas fa-warehouse"></i>
                            <span>{{ $text('Dashboard ready', 'جاهز في اللوحة') }}</span>
                        </div>
                    </div>

                    <div class="warehouse-control-actions">
                        @if (!empty($warehouseControl['manage_url']))
                            <a href="{{ $warehouseControl['manage_url'] }}" class="btn btn-dark warehouse-action-btn">
                                <i class="fas fa-sliders-h"></i>
                                <span>{{ $text('Manage warehouses', 'إدارة المستودعات') }}</span>
                            </a>
                        @endif

                        @if (!empty($warehouseControl['create_url']))
                            <a href="{{ $warehouseControl['create_url'] }}" class="btn btn-primary warehouse-action-btn">
                                <i class="fas fa-plus"></i>
                                <span>{{ $text('Add warehouse', 'إضافة مستودع') }}</span>
                            </a>
                        @endif

                        @if (!empty($warehouseControl['focus_url']))
                            <a href="{{ $warehouseControl['focus_url'] }}" class="btn btn-outline-primary warehouse-action-btn">
                                <i class="fas fa-pen"></i>
                                <span>{{ $text('Edit main warehouse', 'تعديل المستودع الرئيسي') }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="warehouse-control-stack">
                    <div class="warehouse-mini-card warehouse-mini-card-primary">
                        <span>{{ $text('Total warehouses', 'إجمالي المستودعات') }}</span>
                        <strong>{{ $safeNumber($warehouseControl['total'] ?? 0) }}</strong>
                    </div>
                    <div class="warehouse-mini-card warehouse-mini-card-success">
                        <span>{{ $text('Main warehouses', 'المستودعات الرئيسية') }}</span>
                        <strong>{{ $safeNumber($warehouseControl['main'] ?? 0) }}</strong>
                    </div>
                    <div class="warehouse-mini-card warehouse-mini-card-warning">
                        <span>{{ $text('Profiles pending review', 'الملفات في انتظار المراجعة') }}</span>
                        <strong>{{ $safeNumber($warehouseControl['pending_profiles'] ?? 0) }}</strong>
                    </div>
                    <div class="warehouse-mini-card warehouse-mini-card-info">
                        <span>{{ $text('Profiles approved', 'الملفات المعتمدة') }}</span>
                        <strong>{{ $safeNumber($warehouseControl['approved_profiles'] ?? 0) }}</strong>
                    </div>
                </div>

                <div class="warehouse-control-focus">
                    <div class="warehouse-control-focus-header">
                        <span class="warehouse-control-eyebrow">{{ $text('Current focus', 'التركيز الحالي') }}</span>
                        <span class="warehouse-control-status">{{ !empty($warehouseControl['focus_name']) ? $text('Main warehouse', 'المستودع الرئيسي') : $text('No main warehouse yet', 'لا يوجد مستودع رئيسي بعد') }}</span>
                    </div>
                    @if (!empty($warehouseControl['focus_name']))
                        <h3 class="warehouse-control-focus-title">{{ $warehouseControl['focus_name'] }}</h3>
                        <p class="warehouse-control-focus-text">{{ $warehouseControl['focus_location'] ?? $text('Location is not set yet.', 'لا توجد موقع محدد بعد.') }}</p>
                        @if (!empty($warehouseControl['focus_url']))
                            <a href="{{ $warehouseControl['focus_url'] }}" class="btn btn-light warehouse-focus-btn">
                                <i class="fas fa-location-arrow"></i>
                                <span>{{ $text('Open warehouse profile', 'افتح ملف المستودع') }}</span>
                            </a>
                        @endif
                    @else
                        <div class="empty-state-card warehouse-control-empty">
                            <i class="fas fa-warehouse"></i>
                            <strong>{{ $text('No main warehouse is configured.', 'لم يتم تعيين مستودع رئيسي بعد.') }}</strong>
                            <span>{{ $text('Create a warehouse and mark it as main to keep order and shipment flows centered.', 'أنشئ مستودعًا واجعله رئيسيًا لتنظيم مسار الطلبات والشحن.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if (!empty($dashboardCycles['approvals']['cards']))
            <section class="dashboard-cycle-section mb-4" id="approvalCycles">
                <div class="cycle-section-header mb-3">
                    <div>
                        <span class="section-kicker">{{ $text('Approval cycle', 'دورة الموافقات') }}</span>
                        <h5 class="mb-1 fw-bold">{{ $dashboardCycles['approvals']['title'] ?? $text('Approvals', 'الموافقات') }}</h5>
                        <p class="mb-0 text-muted small">{{ $dashboardCycles['approvals']['subtitle'] ?? $text('Items waiting for approval.', 'العناصر التي تنتظر الموافقة.') }}</p>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($dashboardCycles['approvals']['cards'] as $card)
                        <div class="col-xl-4 col-md-6">
                            <div class="cycle-card h-100">
                                <div class="cycle-card-header">
                                    <div class="cycle-card-heading">
                                        <div class="cycle-card-title">{{ $card['title'] ?? '-' }}</div>
                                        <div class="cycle-card-count">{{ $safeNumber($card['count'] ?? 0) }}</div>
                                    </div>
                                    <span class="cycle-badge cycle-badge-warning">{{ $cycleUiLabels['needs_approval'] ?? 'Needs approval' }}</span>
                                </div>
                                <div class="cycle-latest">
                                    <div class="cycle-latest-label">{{ $cycleUiLabels['latest_item'] ?? 'Latest item' }}</div>
                                    <div class="cycle-latest-title">
                                        @if (!empty($card['latest_url']))
                                            <a href="{{ $card['latest_url'] }}">{{ $card['latest_title'] ?? '-' }}</a>
                                        @else
                                            {{ $card['latest_title'] ?? '-' }}
                                        @endif
                                    </div>
                                    @if (!empty($card['latest_meta']))
                                        <div class="cycle-latest-meta">{{ $card['latest_meta'] }}</div>
                                    @endif
                                </div>
                                <div class="cycle-card-footer mt-3">
                                    <span class="text-muted small cycle-status-text">{{ $card['latest_status'] ?? '-' }}</span>
                                    @if (!empty($card['view_all_url']))
                                        <a href="{{ $card['view_all_url'] }}" class="btn btn-outline-primary btn-sm">{{ $cycleUiLabels['view_all'] ?? 'View all' }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($dashboardCycles['trust']['cards']))
            <section class="dashboard-cycle-section mb-4" id="trustCycles">
                <div class="cycle-section-header mb-3">
                    <div>
                        <span class="section-kicker">{{ $text('Trust cycle', 'دورة الثقة') }}</span>
                        <h5 class="mb-1 fw-bold">{{ $dashboardCycles['trust']['title'] ?? $text('Trust', 'الثقة') }}</h5>
                        <p class="mb-0 text-muted small">{{ $dashboardCycles['trust']['subtitle'] ?? $text('Trusted and rejected records.', 'السجلات الموثوقة والمرفوضة.') }}</p>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($dashboardCycles['trust']['cards'] as $card)
                        <div class="col-xl-4 col-md-6">
                            <div class="cycle-card h-100">
                                <div class="cycle-card-header">
                                    <div class="cycle-card-heading">
                                        <div class="cycle-card-title">{{ $card['title'] ?? '-' }}</div>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            <span class="cycle-mini-stat"><strong>{{ $safeNumber($card['trusted_count'] ?? 0) }}</strong> {{ $cycleUiLabels['trusted'] ?? 'Trusted' }}</span>
                                            <span class="cycle-mini-stat"><strong>{{ $safeNumber($card['rejected_count'] ?? 0) }}</strong> {{ $cycleUiLabels['rejected'] ?? 'Rejected' }}</span>
                                        </div>
                                    </div>
                                    <span class="cycle-badge cycle-badge-success">{{ $cycleUiLabels['trust_level'] ?? 'Trust level' }}</span>
                                </div>
                                <div class="cycle-latest mb-2">
                                    <div class="cycle-latest-label">{{ $cycleUiLabels['latest_trusted'] ?? 'Latest trusted' }}</div>
                                    <div class="cycle-latest-title">
                                        @if (!empty($card['trusted_url']))
                                            <a href="{{ $card['trusted_url'] }}">{{ $card['trusted_title'] ?? '-' }}</a>
                                        @else
                                            {{ $card['trusted_title'] ?? '-' }}
                                        @endif
                                    </div>
                                    @if (!empty($card['trusted_meta']))
                                        <div class="cycle-latest-meta">{{ $card['trusted_meta'] }}</div>
                                    @endif
                                </div>
                                <div class="cycle-latest">
                                    <div class="cycle-latest-label">{{ $cycleUiLabels['latest_rejected'] ?? 'Latest rejected' }}</div>
                                    <div class="cycle-latest-title">
                                        @if (!empty($card['rejected_url']))
                                            <a href="{{ $card['rejected_url'] }}">{{ $card['rejected_title'] ?? '-' }}</a>
                                        @else
                                            {{ $card['rejected_title'] ?? '-' }}
                                        @endif
                                    </div>
                                    @if (!empty($card['rejected_meta']))
                                        <div class="cycle-latest-meta">{{ $card['rejected_meta'] }}</div>
                                    @endif
                                </div>
                                <div class="cycle-card-footer cycle-card-footer-end mt-3">
                                    @if (!empty($card['view_all_url']))
                                        <a href="{{ $card['view_all_url'] }}" class="btn btn-outline-primary btn-sm">{{ $cycleUiLabels['view_all'] ?? 'View all' }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($dashboardCycles['adminApprovals']['cards']))
            <section class="dashboard-cycle-section mb-4" id="adminApprovalCycles">
                <div class="cycle-section-header mb-3">
                    <div>
                        <span class="section-kicker">{{ $text('Admin approvals', 'موافقات الإدارة') }}</span>
                        <h5 class="mb-1 fw-bold">{{ $dashboardCycles['adminApprovals']['title'] ?? $text('Admin Approvals', 'موافقات الإدارة') }}</h5>
                        <p class="mb-0 text-muted small">{{ $dashboardCycles['adminApprovals']['subtitle'] ?? $text('Admin-controlled approval items.', 'عناصر الموافقة التي تتحكم بها الإدارة.') }}</p>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($dashboardCycles['adminApprovals']['cards'] as $card)
                        <div class="col-xl-3 col-md-6">
                            <div class="cycle-card h-100">
                                <div class="cycle-card-header">
                                    <div class="cycle-card-heading">
                                        <div class="cycle-card-title">{{ $card['title'] ?? '-' }}</div>
                                        <div class="cycle-card-count">{{ $safeNumber($card['count'] ?? 0) }}</div>
                                    </div>
                                    <span class="cycle-badge cycle-badge-primary">{{ $card['latest_status'] ?? '-' }}</span>
                                </div>
                                <div class="cycle-latest">
                                    <div class="cycle-latest-label">{{ $cycleUiLabels['latest_item'] ?? 'Latest item' }}</div>
                                    <div class="cycle-latest-title">
                                        @if (!empty($card['latest_url']))
                                            <a href="{{ $card['latest_url'] }}">{{ $card['latest_title'] ?? '-' }}</a>
                                        @else
                                            {{ $card['latest_title'] ?? '-' }}
                                        @endif
                                    </div>
                                    @if (!empty($card['latest_meta']))
                                        <div class="cycle-latest-meta">{{ $card['latest_meta'] }}</div>
                                    @endif
                                </div>
                                <div class="cycle-card-footer cycle-card-footer-end mt-3">
                                    @if (!empty($card['view_all_url']))
                                        <a href="{{ $card['view_all_url'] }}" class="btn btn-outline-primary btn-sm">{{ $cycleUiLabels['view_all'] ?? 'View all' }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if (!empty($dashboardCycles['pending']['cards']))
            <section class="dashboard-cycle-section mb-4" id="pendingCycles">
                <div class="cycle-section-header mb-3">
                    <div>
                        <span class="section-kicker">{{ $text('Pending cycles', 'الدورات المعلقة') }}</span>
                        <h5 class="mb-1 fw-bold">{{ $dashboardCycles['pending']['title'] ?? $text('Pending', 'معلق') }}</h5>
                        <p class="mb-0 text-muted small">{{ $dashboardCycles['pending']['subtitle'] ?? $text('Pending operational cycles.', 'الدورات التشغيلية المعلقة.') }}</p>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($dashboardCycles['pending']['cards'] as $card)
                        <div class="col-xl-3 col-md-6">
                            <div class="cycle-card h-100">
                                <div class="cycle-card-header">
                                    <div class="cycle-card-heading">
                                        <div class="cycle-card-title">{{ $card['title'] ?? '-' }}</div>
                                        <div class="cycle-card-count">{{ $safeNumber($card['count'] ?? 0) }}</div>
                                    </div>
                                    <span class="cycle-badge cycle-badge-warning">{{ $card['latest_status'] ?? '-' }}</span>
                                </div>
                                <div class="cycle-latest">
                                    <div class="cycle-latest-label">{{ $cycleUiLabels['latest_item'] ?? 'Latest item' }}</div>
                                    <div class="cycle-latest-title">
                                        @if (!empty($card['latest_url']))
                                            <a href="{{ $card['latest_url'] }}">{{ $card['latest_title'] ?? '-' }}</a>
                                        @else
                                            {{ $card['latest_title'] ?? '-' }}
                                        @endif
                                    </div>
                                    @if (!empty($card['latest_meta']))
                                        <div class="cycle-latest-meta">{{ $card['latest_meta'] }}</div>
                                    @endif
                                </div>
                                <div class="cycle-card-footer cycle-card-footer-end mt-3">
                                    @if (!empty($card['view_all_url']))
                                        <a href="{{ $card['view_all_url'] }}" class="btn btn-outline-primary btn-sm">{{ $cycleUiLabels['view_all'] ?? 'View all' }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

            <section class="dashboard-cycle-section mb-4" id="complaintsCycles">
                <div class="cycle-section-header mb-3">
                    <div>
                        <span class="section-kicker">{{ $text('Support', 'الدعم') }}</span>
                        <h5 class="mb-1 fw-bold">{{ $dashboardCycles['complaints']['title'] ?? $text('Complaints', 'الشكاوى') }}</h5>
                        <p class="mb-0 text-muted small">{{ $dashboardCycles['complaints']['subtitle'] ?? $text('Complaints and customer support signals.', 'إشارات الشكاوى والدعم الفني للعملاء.') }}</p>
                    </div>
                </div>
                <div class="empty-state-card">
                    <i class="fas fa-comment-dots"></i>
                    <strong>{{ $cycleUiLabels['no_complaints_source'] ?? $text('No complaints source is connected yet.', 'لم يتم ربط مصدر للشكاوى بعد.') }}</strong>
                    <span>{{ $text('When complaint data is available, this area can show open complaints and urgent support issues.', 'عندما تتوفر بيانات الشكاوى، يمكن لهذه المساحة عرض الشكاوى المفتوحة ومشكلات الدعم العاجلة.') }}</span>
                </div>
            </section>
        </div>
@endsection

@section('scripts')
    <style data-page-style="admin-dashboard-enhanced">
        :root {
            --metw-bg: #f6f8fb;
            --metw-surface: #ffffff;
            --metw-border: #e5e7eb;
            --metw-text: #0f172a;
            --metw-muted: #64748b;
            --metw-primary: #2563eb;
            --metw-success: #16a34a;
            --metw-warning: #f59e0b;
            --metw-danger: #dc2626;
            --metw-info: #0891b2;
            --metw-shadow: 0 14px 32px rgba(15, 23, 42, 0.07);
            --metw-shadow-hover: 0 22px 44px rgba(15, 23, 42, 0.12);
        }

        .admin-dashboard-page {
            color: var(--metw-text);
        }

        .dashboard-hero {
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, 0.18), transparent 32%),
                linear-gradient(135deg, #0f172a 0%, #1e3a8a 52%, #0f766e 100%);
            border-radius: 28px;
            padding: 1.5rem;
            color: #ffffff;
            box-shadow: var(--metw-shadow);
            overflow: hidden;
            position: relative;
        }

        .dashboard-hero::after {
            content: '';
            position: absolute;
            width: 240px;
            height: 240px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.09);
            right: -80px;
            bottom: -110px;
        }

        .dashboard-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .dashboard-eyebrow,
        .section-kicker {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .dashboard-eyebrow {
            color: rgba(255, 255, 255, 0.72);
            margin-bottom: 0.55rem;
        }

        .dashboard-title {
            font-size: clamp(1.65rem, 3vw, 2.35rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 0.55rem;
        }

        .dashboard-subtitle {
            max-width: 680px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.98rem;
            line-height: 1.7;
        }

        .dashboard-hero-summary {
            min-width: 190px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            border-radius: 22px;
            padding: 1rem 1.15rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .summary-label {
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .dashboard-hero-summary strong {
            font-size: 2.15rem;
            font-weight: 900;
            line-height: 1.1;
        }

        .dashboard-hero-summary small {
            color: rgba(255, 255, 255, 0.76);
        }

        .dashboard-section,
        .dashboard-cycle-section {
            background: var(--metw-surface);
            border: 1px solid var(--metw-border);
            border-radius: 26px;
            padding: 1.25rem;
            box-shadow: var(--metw-shadow);
        }

        .section-heading,
        .cycle-section-header {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding-bottom: 0.95rem;
            border-bottom: 1px solid #eef2f7;
        }

        .section-kicker {
            color: var(--metw-primary);
            margin-bottom: 0.4rem;
        }

        .section-heading h2,
        .cycle-section-header h5 {
            font-size: 1.18rem;
            font-weight: 900 !important;
            line-height: 1.35;
            margin: 0 0 0.2rem;
            color: var(--metw-text);
        }

        .section-heading p,
        .cycle-section-header p {
            margin: 0;
            color: var(--metw-muted) !important;
            font-size: 0.9rem !important;
            line-height: 1.6;
        }

        .dashboard-card-link,
        .dashboard-card-link:hover {
            display: block;
            color: inherit;
            text-decoration: none;
            height: 100%;
        }

        .metric-card,
        .compact-stat-card,
        .operation-card,
        .quick-link-card,
        .cycle-card,
        .empty-state-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.055);
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }

        .metric-card:hover,
        .compact-stat-card:hover,
        .operation-card:hover,
        .quick-link-card:hover,
        .cycle-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--metw-shadow-hover);
            border-color: #c7d2fe;
        }

        .metric-card {
            padding: 1.2rem;
            min-height: 214px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: var(--card-tone, var(--metw-primary));
        }

        .metric-card-primary { --card-tone: var(--metw-primary); }
        .metric-card-success { --card-tone: var(--metw-success); }
        .metric-card-warning { --card-tone: var(--metw-warning); }
        .metric-card-danger { --card-tone: var(--metw-danger); }
        .metric-card-info { --card-tone: var(--metw-info); }

        .metric-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .metric-icon,
        .compact-stat-icon,
        .operation-icon,
        .quick-link-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .metric-icon {
            color: var(--card-tone, var(--metw-primary));
            background: color-mix(in srgb, var(--card-tone, var(--metw-primary)) 11%, white);
        }

        .metric-badge,
        .cycle-badge {
            border-radius: 999px;
            padding: 0.35rem 0.65rem;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .metric-badge {
            color: var(--card-tone, var(--metw-primary));
            background: color-mix(in srgb, var(--card-tone, var(--metw-primary)) 10%, white);
        }

        .metric-label,
        .operation-label {
            color: var(--metw-muted);
            font-size: 0.78rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }

        .metric-value {
            color: var(--metw-text);
            font-size: 2.25rem;
            font-weight: 950;
            line-height: 1;
        }

        .metric-helper {
            color: var(--metw-muted);
            line-height: 1.55;
            font-size: 0.88rem;
            margin: 0.85rem 0 1rem;
        }

        .metric-action {
            margin-top: auto;
            color: var(--card-tone, var(--metw-primary));
            font-size: 0.82rem;
            font-weight: 900;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .compact-stat-card {
            padding: 1.05rem;
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
        }

        .compact-stat-card-primary .compact-stat-icon,
        .operation-icon-primary { background: #eff6ff; color: var(--metw-primary); }
        .compact-stat-card-success .compact-stat-icon,
        .operation-icon-success { background: #ecfdf5; color: var(--metw-success); }
        .compact-stat-card-warning .compact-stat-icon,
        .operation-icon-warning { background: #fffbeb; color: var(--metw-warning); }
        .compact-stat-card-danger .compact-stat-icon,
        .operation-icon-danger { background: #fef2f2; color: var(--metw-danger); }
        .compact-stat-card-info .compact-stat-icon,
        .operation-icon-info { background: #ecfeff; color: var(--metw-info); }

        .compact-stat-content {
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .compact-stat-content span {
            color: var(--metw-muted);
            font-size: 0.78rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .compact-stat-content strong {
            color: var(--metw-text);
            font-size: 1.65rem;
            font-weight: 950;
            line-height: 1.15;
            margin: 0.2rem 0;
        }

        .compact-stat-content small {
            color: var(--metw-muted);
            line-height: 1.45;
        }

        .operation-card {
            padding: 1.05rem;
            display: flex;
            gap: 0.95rem;
            align-items: flex-start;
        }

        .operation-body strong {
            display: block;
            font-size: 1.7rem;
            font-weight: 950;
            line-height: 1.1;
            margin: 0.15rem 0 0.45rem;
        }

        .operation-body p {
            color: var(--metw-muted);
            font-size: 0.86rem;
            line-height: 1.5;
            margin: 0;
        }

        .quick-link-card {
            min-height: 128px;
            padding: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            position: relative;
            text-decoration: none;
            color: inherit;
        }

        .quick-link-card:hover {
            text-decoration: none;
            color: inherit;
        }

        .quick-link-disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .quick-link-icon {
            background: #f1f5f9;
            color: var(--metw-primary);
        }

        .quick-link-card strong {
            display: block;
            color: var(--metw-text);
            font-weight: 900;
            margin-bottom: 0.25rem;
        }

        .quick-link-card p {
            margin: 0;
            color: var(--metw-muted);
            font-size: 0.86rem;
            line-height: 1.5;
            padding-inline-end: 1rem;
        }

        .quick-link-arrow {
            position: absolute;
            right: 1rem;
            bottom: 1rem;
            color: var(--metw-primary);
        }

        .warehouse-control-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(250px, 0.85fr) minmax(0, 1.1fr);
            gap: 1rem;
        }

        .warehouse-control-panel,
        .warehouse-control-focus,
        .warehouse-mini-card {
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }

        .warehouse-control-panel {
            padding: 1.1rem;
        }

        .warehouse-control-panel-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .warehouse-control-eyebrow {
            display: inline-flex;
            align-items: center;
            font-size: 0.72rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--metw-primary);
            margin-bottom: 0.45rem;
        }

        .warehouse-control-title {
            margin: 0 0 0.45rem;
            font-size: 1.15rem;
            font-weight: 950;
            color: var(--metw-text);
        }

        .warehouse-control-text {
            margin: 0;
            color: var(--metw-muted);
            line-height: 1.6;
        }

        .warehouse-control-badge {
            min-width: 132px;
            border-radius: 18px;
            padding: 0.8rem 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 0.35rem;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(14, 165, 233, 0.12));
            color: var(--metw-primary);
            font-weight: 900;
            text-align: center;
        }

        .warehouse-control-badge i {
            font-size: 1.2rem;
        }

        .warehouse-control-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .warehouse-action-btn,
        .warehouse-focus-btn {
            min-height: 46px;
            border-radius: 16px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
        }

        .warehouse-action-btn span,
        .warehouse-focus-btn span {
            white-space: nowrap;
        }

        .warehouse-control-stack {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .warehouse-mini-card {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .warehouse-mini-card span {
            color: var(--metw-muted);
            font-size: 0.78rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .warehouse-mini-card strong {
            color: var(--metw-text);
            font-size: 1.8rem;
            font-weight: 950;
            line-height: 1;
        }

        .warehouse-mini-card-primary {
            background: linear-gradient(180deg, rgba(37, 99, 235, 0.08), #ffffff 70%);
        }

        .warehouse-mini-card-success {
            background: linear-gradient(180deg, rgba(22, 163, 74, 0.08), #ffffff 70%);
        }

        .warehouse-mini-card-warning {
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.09), #ffffff 70%);
        }

        .warehouse-mini-card-info {
            background: linear-gradient(180deg, rgba(8, 145, 178, 0.08), #ffffff 70%);
        }

        .warehouse-control-focus {
            padding: 1.1rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .warehouse-control-focus-header {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .warehouse-control-status {
            display: inline-flex;
            align-items: center;
            padding: 0.38rem 0.75rem;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: 0.74rem;
            font-weight: 900;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .warehouse-control-focus-title {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 950;
            color: var(--metw-text);
        }

        .warehouse-control-focus-text {
            margin: 0;
            color: var(--metw-muted);
            line-height: 1.65;
        }

        .warehouse-control-empty {
            margin-top: 0.1rem;
        }

        .dashboard-cycle-section {
            scroll-margin-top: 110px;
        }

        .cycle-card {
            padding: 1.15rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .cycle-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.9rem;
            margin-bottom: 0.9rem;
            flex-wrap: wrap;
        }

        .cycle-card-heading {
            flex: 1 1 180px;
            min-width: 0;
        }

        .cycle-card-title {
            color: var(--metw-muted);
            font-size: 0.76rem;
            font-weight: 900;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            line-height: 1.45;
            margin-bottom: 0.45rem;
        }

        .cycle-card-count {
            color: var(--metw-text);
            font-size: 2rem;
            font-weight: 950;
            line-height: 1;
        }

        .cycle-badge-warning { background: #fff7ed; color: #c2410c; }
        .cycle-badge-success { background: #ecfdf5; color: #047857; }
        .cycle-badge-primary { background: #eff6ff; color: #1d4ed8; }

        .cycle-latest {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 17px;
            padding: 0.9rem;
            margin-top: 0.75rem;
        }

        .cycle-latest-label {
            color: #94a3b8;
            font-size: 0.68rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-bottom: 0.28rem;
        }

        .cycle-latest-title,
        .cycle-latest-title a {
            color: var(--metw-text);
            font-weight: 900;
            line-height: 1.4;
            text-decoration: none;
            word-break: break-word;
        }

        .cycle-latest-title a:hover {
            color: var(--metw-primary);
        }

        .cycle-latest-meta {
            color: var(--metw-muted);
            font-size: 0.82rem;
            margin-top: 0.25rem;
        }

        .cycle-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: auto !important;
            padding-top: 1rem;
        }

        .cycle-card-footer-end {
            justify-content: flex-end;
        }

        .cycle-mini-stat {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: 0.34rem 0.7rem;
            color: #475569;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .cycle-mini-stat strong {
            color: var(--metw-text);
        }

        .btn-sm {
            border-radius: 999px;
            font-weight: 800;
            padding-inline: 0.9rem;
        }

        .empty-state-card {
            padding: 2rem 1rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            color: var(--metw-muted);
            background: #f8fafc;
        }

        .empty-state-card i {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: #ffffff;
            color: var(--metw-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 0.4rem;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        .empty-state-card strong {
            color: var(--metw-text);
            font-weight: 900;
        }

        .empty-state-card span {
            max-width: 560px;
            line-height: 1.6;
        }

        :dir(rtl) .dashboard-hero-content,
        :dir(rtl) .section-heading,
        :dir(rtl) .cycle-section-header,
        :dir(rtl) .metric-card,
        :dir(rtl) .compact-stat-card,
        :dir(rtl) .operation-card,
        :dir(rtl) .quick-link-card,
        :dir(rtl) .warehouse-control-panel,
        :dir(rtl) .warehouse-control-focus,
        :dir(rtl) .cycle-card,
        :dir(rtl) .cycle-latest {
            direction: rtl;
            text-align: right;
        }

        :dir(rtl) .metric-action i,
        :dir(rtl) .quick-link-arrow i {
            transform: rotate(180deg);
        }

        :dir(rtl) .quick-link-arrow {
            right: auto;
            left: 1rem;
        }

        :dir(rtl) .warehouse-control-badge,
        :dir(rtl) .warehouse-control-status {
            text-transform: none;
            letter-spacing: 0;
        }

        :dir(rtl) .cycle-card-footer-end {
            justify-content: flex-start;
        }

        :dir(rtl) .metric-label,
        :dir(rtl) .operation-label,
        :dir(rtl) .compact-stat-content span,
        :dir(rtl) .cycle-card-title,
        :dir(rtl) .cycle-latest-label,
        :dir(rtl) .dashboard-eyebrow,
        :dir(rtl) .section-kicker {
            text-transform: none;
            letter-spacing: 0;
        }

        @media (max-width: 768px) {
            .dashboard-hero,
            .dashboard-section,
            .dashboard-cycle-section {
                border-radius: 20px;
                padding: 1rem;
            }

            .dashboard-hero-content,
            .section-heading,
            .cycle-section-header {
                flex-direction: column;
            }

            .warehouse-control-grid,
            .warehouse-control-stack {
                grid-template-columns: 1fr;
            }

            .warehouse-control-panel-top {
                flex-direction: column;
            }

            .warehouse-control-badge {
                width: 100%;
            }

            .dashboard-hero-summary {
                width: 100%;
            }

            .metric-card {
                min-height: auto;
            }

            .metric-value {
                font-size: 1.85rem;
            }

            .compact-stat-content strong,
            .operation-body strong,
            .cycle-card-count {
                font-size: 1.45rem;
            }

            .metric-icon,
            .compact-stat-icon,
            .operation-icon,
            .quick-link-icon {
                width: 42px;
                height: 42px;
                border-radius: 14px;
            }

            .dashboard-charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .dashboard-charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .chart-panel {
            background: var(--metw-surface);
            border: 1px solid var(--metw-border);
            border-radius: 20px;
            box-shadow: var(--metw-shadow);
            padding: 1.15rem 1.25rem;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .chart-panel-lg {
            grid-column: span 2;
        }

        .chart-panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .chart-panel-title {
            display: block;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--metw-text);
        }

        .chart-panel-head small {
            display: block;
            color: var(--metw-muted);
            font-size: 0.78rem;
            margin-top: 0.15rem;
        }

        .chart-panel-body {
            position: relative;
            flex: 1;
            min-height: 240px;
            height: 100%;
        }

        .chart-panel-lg .chart-panel-body {
            min-height: 300px;
        }

        .chart-panel-doughnut {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chart-panel canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }

        .chart-panel-doughnut canvas {
            max-height: 200px;
        }

        @media (max-width: 768px) {
            .chart-panel-lg {
                grid-column: span 1;
            }
        }
    </style>

    <script data-page-script>
        (function () {
            if (typeof Chart === 'undefined') return;

            var isRtl = (document.documentElement.getAttribute('dir') === 'rtl');
            var baseColors = ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#0891b2', '#7c3aed', '#db2777', '#65a30d', '#ea580c', '#0d9488', '#475569'];

            function parseJson(value, fallback) {
                try {
                    var parsed = JSON.parse(value || '');
                    return Array.isArray(parsed) ? parsed : fallback;
                } catch (e) {
                    return fallback;
                }
            }

            function fillEmptyState(canvas) {
                var parent = canvas.parentElement;
                if (!parent || parent.querySelector('.chart-empty-state')) return;
                var note = document.createElement('div');
                note.className = 'chart-empty-state';
                note.style.cssText = 'text-align:center;color:var(--metw-muted, #64748b);font-size:.85rem;padding:2rem 0;';
                note.textContent = isRtl ? 'لا توجد بيانات كافية لعرض هذا الرسم.' : 'Not enough data to render this chart.';
                canvas.style.display = 'none';
                parent.appendChild(note);
            }

            function numberSeries(value, fallback) {
                var arr = parseJson(value, fallback || []);
                return arr.map(function (n) { return Number(n) || 0; });
            }

            function doughnutData(canvas) {
                var rows = parseJson(canvas.dataset.series, []);
                if (!rows.length) return null;
                return {
                    labels: rows.map(function (r) { return r.label || '—'; }),
                    datasets: [{
                        data: rows.map(function (r) { return Number(r.value) || 0; }),
                        backgroundColor: rows.map(function (_, i) { return baseColors[i % baseColors.length]; }),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                };
            }

            function doughnutOptions() {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    rtl: isRtl,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: isRtl,
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                padding: 12,
                                color: '#475569',
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            rtl: isRtl,
                            backgroundColor: '#0f172a',
                            titleColor: '#ffffff',
                            bodyColor: '#e2e8f0',
                            padding: 10,
                            cornerRadius: 8
                        }
                    }
                };
            }

            function buildRevenueChart() {
                var canvas = document.querySelector('#mainContent #adminRevenueChart');
                if (!canvas || canvas.dataset.chartBound === '1') return;

                var labels = parseJson(canvas.dataset.labels, []);
                var shipment = numberSeries(canvas.dataset.shipment);
                var ecommerce = numberSeries(canvas.dataset.ecommerce);
                if (!labels.length) { fillEmptyState(canvas); return; }

                new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: canvas.dataset.shipmentLabel || 'Shipment Revenue',
                                data: shipment,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#2563eb'
                            },
                            {
                                label: canvas.dataset.ecommerceLabel || 'Ecommerce Revenue',
                                data: ecommerce,
                                borderColor: '#16a34a',
                                backgroundColor: 'rgba(22, 163, 74, 0.10)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: 3,
                                pointHoverRadius: 5,
                                pointBackgroundColor: '#16a34a'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        rtl: isRtl,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                position: 'top',
                                rtl: isRtl,
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                    padding: 14,
                                    color: '#475569',
                                    font: { size: 11 }
                                }
                            },
                            tooltip: {
                                rtl: isRtl,
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#e2e8f0',
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function (ctx) {
                                        return ' ' + ctx.dataset.label + ': ' + Number(ctx.parsed.y || 0).toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 0, autoSkip: true }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(148, 163, 184, 0.15)' },
                                border: { display: false },
                                ticks: { color: '#94a3b8', font: { size: 10 }, callback: function (value) { return Number(value).toLocaleString(); } }
                            }
                        }
                    }
                });
                canvas.dataset.chartBound = '1';
            }

            function buildDoughnutCharts() {
                var ids = ['adminOrdersStatusChart', 'adminEcommerceStatusChart', 'adminShipmentRequestsStatusChart', 'adminRepresentativesStatusChart'];
                ids.forEach(function (id) {
                    var canvas = document.querySelector('#mainContent #' + id);
                    if (!canvas || canvas.dataset.chartBound === '1') return;
                    var data = doughnutData(canvas);
                    if (!data) { fillEmptyState(canvas); return; }
                    new Chart(canvas.getContext('2d'), {
                        type: 'doughnut',
                        data: data,
                        options: doughnutOptions()
                    });
                    canvas.dataset.chartBound = '1';
                });
            }

            function buildDailyChart() {
                var canvas = document.querySelector('#mainContent #adminDailyChart');
                if (!canvas || canvas.dataset.chartBound === '1') return;

                var labels = parseJson(canvas.dataset.labels, []);
                if (!labels.length) { fillEmptyState(canvas); return; }

                var series = [
                    { label: isRtl ? 'مستخدمون جدد' : 'New Users', data: numberSeries(canvas.dataset.users), color: '#2563eb' },
                    { label: isRtl ? 'طلبات الشحن' : 'Shipping Orders', data: numberSeries(canvas.dataset.orders), color: '#f59e0b' },
                    { label: isRtl ? 'طلبات المتجر' : 'Ecommerce Orders', data: numberSeries(canvas.dataset.ecommerce), color: '#16a34a' },
                    { label: isRtl ? 'طلبات شحن المرحلة 2' : 'Shipment Requests', data: numberSeries(canvas.dataset.shipments), color: '#0891b2' }
                ];

                new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: series.map(function (s) {
                            return {
                                label: s.label,
                                data: s.data,
                                borderColor: s.color,
                                backgroundColor: s.color,
                                tension: 0.35,
                                borderWidth: 2.5,
                                pointRadius: 3,
                                pointHoverRadius: 5
                            };
                        })
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        rtl: isRtl,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                position: 'top',
                                rtl: isRtl,
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 8,
                                    padding: 14,
                                    color: '#475569',
                                    font: { size: 11 }
                                }
                            },
                            tooltip: {
                                rtl: isRtl,
                                backgroundColor: '#0f172a',
                                titleColor: '#ffffff',
                                bodyColor: '#e2e8f0',
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 0, autoSkip: true }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(148, 163, 184, 0.15)' },
                                border: { display: false },
                                ticks: { color: '#94a3b8', font: { size: 10 }, precision: 0 }
                            }
                        }
                    }
                });
                canvas.dataset.chartBound = '1';
            }

            buildRevenueChart();
            buildDoughnutCharts();
            buildDailyChart();
        })();
    </script>
@endsection
