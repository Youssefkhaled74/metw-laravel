<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\BusinessProfileStatus;
use App\Enum\ComplaintStatus;
use App\Enum\ComplaintType;
use App\Enum\PaymentStatus;
use App\Enum\OrderStatus;
use App\Enum\ReturnStatus;
use App\Enum\ShipmentRequestStatus;
use App\Enum\RepresentativeStatus;
use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Product;
use App\Models\Representative;
use App\Models\ShipmentCompany;
use App\Models\Employee;
use App\Models\Complaint;
use App\Models\ShipmentRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBusinessProfile;
use App\Models\Warehouse;
use App\Models\WarehouseBusinessProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;


class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.dashboard')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.dashboard.dashboard2', [
            'stats' => $this->dashboardStats(),
            'recent_shipment_orders' => Order::with(['user', 'shipmentCompany'])->latest()->limit(5)->get(),
            'recent_ecommerce_orders' => EcommerceOrder::with(['user'])->latest()->limit(5)->get(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
        ]);
        $route = fn (string $name, array $parameters = []) => Route::has($name) ? route($name, $parameters) : null;
        $count = fn (string $modelClass, ?callable $callback = null): int => $this->countFor($modelClass, $callback);

        $summaryItems = [
            $count(User::class, fn ($query) => $query->whereNull('email_verified_at')),
            $count(VendorBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::PENDING_REVIEW->value)),
            $count(WarehouseBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::PENDING_REVIEW->value)),
            $count(ShipmentCompany::class, fn ($query) => $query->withoutGlobalScope('active')->where('is_active', false)),
            $count(Representative::class, fn ($query) => $query->where('status', RepresentativeStatus::PENDING_REVIEW->value)),
            $count(User::class, fn ($query) => $query->onlyTrashed()),
            $count(Vendor::class, fn ($query) => $query->withTrashed()->where(function ($subQuery) {
                $subQuery->where('is_active', false)->orWhereNotNull('deleted_at');
            })),
            $count(WarehouseBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::REJECTED->value)),
            $count(Representative::class, fn ($query) => $query->whereIn('status', [RepresentativeStatus::SUSPENDED->value, RepresentativeStatus::REJECTED->value])),
            $count(EcommerceOrder::class, fn ($query) => $query->where('payment_status', PaymentStatus::PENDING->value)),
            $count(EcommerceOrder::class, fn ($query) => $query->where('status', OrderStatus::CANCELLED->value)),
            $count(ReturnRequest::class, fn ($query) => $query->where('status', ReturnStatus::APPROVED->value)),
            $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::PURCHASE_CANCELLATION->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])),
            $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::SHIPPING_CANCELLATION->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])),
            $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::RETURN->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])),
            $count(ReturnRequest::class, fn ($query) => $query->where('refund_type', 'wallet')->where('status', ReturnStatus::REFUNDED->value)),
            $count(EcommerceOrder::class, fn ($query) => $query->where('status', OrderStatus::PENDING->value)),
            $count(ReturnRequest::class, fn ($query) => $query->cancellations()->where('status', ReturnStatus::REQUESTED->value)),
            $count(ReturnRequest::class, fn ($query) => $query->where('status', ReturnStatus::REQUESTED->value)),
            $count(ShipmentRequest::class, fn ($query) => $query->where('status', ShipmentRequestStatus::SUBMITTED->value)),
            $count(Order::class, fn ($query) => $query->where('status', OrderStatus::PENDING->value)),
            $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::USER->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])),
            $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::VENDOR->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])),
            $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::WAREHOUSE->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])),
            $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::REPRESENTATIVE->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])),
        ];

        $sections = [
            [
                'title' => 'حسابات جديدة تحتاج موافقة',
                'items' => [
                    [
                        'label' => 'حسابات المستخدمين غير الموثقة',
                        'count' => $summaryItems[0],
                        'note' => 'تحتاج مراجعة بيانات الدخول والتوثيق قبل الاعتماد.',
                        'url' => $route('admin.users', ['verification_status' => 'unverified']),
                    ],
                    [
                        'label' => 'حسابات الموردين قيد المراجعة',
                        'count' => $summaryItems[1],
                        'note' => 'ملفات النشاط التجاري بانتظار اعتماد الإدارة.',
                        'url' => $route('admin.vendors', ['profile_status' => 'pending_review']),
                    ],
                    [
                        'label' => 'حسابات المستودعات قيد المراجعة',
                        'count' => $summaryItems[2],
                        'note' => 'الملفات التجارية للمستودعات تحتاج موافقة.',
                        'url' => $route('admin.settings.warehouses.index', ['profile_status' => 'pending_review']),
                    ],
                    [
                        'label' => 'حسابات شركات الشحن غير المفعلة',
                        'count' => $summaryItems[3],
                        'note' => 'يمكن مراجعتها وتفعيلها من صفحة الشركات.',
                        'url' => $route('admin.shipment-companies', ['status' => 'inactive']),
                    ],
                    [
                        'label' => 'حسابات المناديب قيد المراجعة',
                        'count' => $summaryItems[4],
                        'note' => 'اختر المناديب بانتظار الاعتماد أو الرفض.',
                        'url' => $route('admin.representatives.index', ['status' => 'pending_review']),
                    ],
                ],
            ],
            [
                'title' => 'حسابات موقوفة أو ملغية',
                'items' => [
                    [
                        'label' => 'المستخدمون المحذوفون',
                        'count' => $summaryItems[5],
                        'note' => 'الحسابات الملغية أو المحذوفة من النظام.',
                        'url' => $route('admin.users'),
                    ],
                    [
                        'label' => 'الموردون الموقوفون أو المحذوفون',
                        'count' => $summaryItems[6],
                        'note' => 'يشمل الحسابات غير النشطة والمرفوعة من النظام.',
                        'url' => $route('admin.vendors', ['status' => 'inactive']),
                    ],
                    [
                        'label' => 'المستودعات الملغية',
                        'count' => $summaryItems[7],
                        'note' => 'تعتمد هذه القيمة على حالة ملف الاعتماد للمستودع.',
                        'url' => $route('admin.settings.warehouses.index', ['profile_status' => 'rejected']),
                    ],
                    [
                        'label' => 'المناديب الموقوفون أو المرفوضون',
                        'count' => $summaryItems[8],
                        'note' => 'يمكن مراجعة الحالة من صفحة المناديب.',
                        'url' => $route('admin.representatives.index'),
                    ],
                ],
            ],
            [
                'title' => 'موافقات الأدمن',
                'items' => [
                    [
                        'label' => 'طلبات الدفع المعلقة',
                        'count' => $summaryItems[9],
                        'note' => 'طلبات تحتاج اعتماد الدفع قبل المتابعة.',
                        'url' => $route('admin.ecommerce-orders', ['payment_status' => 'pending']),
                    ],
                    [
                        'label' => 'طلبات الإلغاء المعتمدة',
                        'count' => $summaryItems[10],
                        'note' => 'الطلبات التي تم إلغاؤها واعتماد الإلغاء لها.',
                        'url' => $route('admin.ecommerce-orders', ['status' => 'cancelled']),
                    ],
                    [
                        'label' => 'طلبات الإرجاع المعتمدة',
                        'count' => $summaryItems[11],
                        'note' => 'طلبات الإرجاع التي تمت الموافقة عليها.',
                        'url' => $route('admin.return-requests', ['status' => 'approved']),
                    ],
                    [
                        'label' => 'شكاوى إلغاء المشتريات',
                        'count' => $summaryItems[12],
                        'note' => 'الشكاوى الخاصة بإلغاء طلبات الشراء بحاجة لمتابعة.',
                        'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::PURCHASE_CANCELLATION->value, 'status' => ComplaintStatus::PENDING->value]),
                    ],
                    [
                        'label' => 'شكاوى إلغاء الشحن',
                        'count' => $summaryItems[13],
                        'note' => 'الشكاوى الخاصة بإلغاء طلبات الشحن بحاجة لمتابعة.',
                        'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::SHIPPING_CANCELLATION->value, 'status' => ComplaintStatus::PENDING->value]),
                    ],
                    [
                        'label' => 'شكاوى المرتجعات',
                        'count' => $summaryItems[14],
                        'note' => 'شكاوى المرتجعات المفتوحة أو قيد المراجعة.',
                        'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::RETURN->value, 'status' => ComplaintStatus::PENDING->value]),
                    ],
                    [
                        'label' => 'طلبات استرداد المحفظة',
                        'count' => $summaryItems[15],
                        'note' => 'طلبات استرداد Metwzon عبر المحفظة.',
                        'url' => $route('admin.return-requests', ['status' => 'refunded', 'refund_type' => 'wallet']),
                    ],
                ],
            ],
            [
                'title' => 'طلبات معلقة أو غير مكتملة',
                'items' => [
                    [
                        'label' => 'طلبات الشراء المعلقة',
                        'count' => $summaryItems[16],
                        'note' => 'الطلبات التي لم تُعتمد بعد داخل المتجر الإلكتروني.',
                        'url' => $route('admin.ecommerce-orders', ['status' => 'pending']),
                    ],
                    [
                        'label' => 'طلبات الإلغاء المعلقة',
                        'count' => $summaryItems[17],
                        'note' => 'طلبات الإلغاء الجديدة بانتظار أول إجراء.',
                        'url' => $route('admin.return-requests', ['request_type' => 'cancellation', 'status' => 'requested']),
                    ],
                    [
                        'label' => 'طلبات الإرجاع المعلقة',
                        'count' => $summaryItems[18],
                        'note' => 'طلبات الإرجاع الجديدة بانتظار أول إجراء.',
                        'url' => $route('admin.return-requests', ['status' => 'requested']),
                    ],
                    [
                        'label' => 'طلبات الشحن المعلقة',
                        'count' => $summaryItems[19],
                        'note' => 'طلبات الشحن التي تم إرسالها ولم تُعالج بعد.',
                        'url' => $route('admin.shipment-requests.index', ['status' => 'submitted']),
                    ],
                    [
                        'label' => 'طلبات التوصيل المعلقة',
                        'count' => $summaryItems[20],
                        'note' => 'طلبات التوصيل داخل مسار الشحن العادي.',
                        'url' => $route('admin.shipment-orders', ['status' => 'pending']),
                    ],
                ],
            ],
            [
                'title' => 'شكاوى معلقة أو غير مغلقة',
                'items' => [
                    [
                        'label' => 'شكاوى المستخدمين',
                        'count' => $summaryItems[21],
                        'note' => 'الشكاوى العامة للمستخدمين قيد المتابعة.',
                        'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::USER->value, 'status' => ComplaintStatus::PENDING->value]),
                    ],
                    [
                        'label' => 'شكاوى الموردين',
                        'count' => $summaryItems[22],
                        'note' => 'الشكاوى العامة للموردين قيد المتابعة.',
                        'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::VENDOR->value, 'status' => ComplaintStatus::PENDING->value]),
                    ],
                    [
                        'label' => 'شكاوى المستودعات',
                        'count' => $summaryItems[23],
                        'note' => 'الشكاوى العامة للمستودعات قيد المتابعة.',
                        'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::WAREHOUSE->value, 'status' => ComplaintStatus::PENDING->value]),
                    ],
                    [
                        'label' => 'شكاوى المناديب',
                        'count' => $summaryItems[24],
                        'note' => 'الشكاوى العامة للمناديب قيد المتابعة.',
                        'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::REPRESENTATIVE->value, 'status' => ComplaintStatus::PENDING->value]),
                    ],
                ],
            ],
        ];

        $totalUrgentItems = collect($sections)->pluck('items')->flatten(1)->sum('count');

        return view('dashboard.admin.dashboard.dashboard2', [
            'stats' => $this->dashboardStats(),
            'recent_shipment_orders' => Order::with(['user', 'shipmentCompany'])->latest()->limit(5)->get(),
            'recent_ecommerce_orders' => EcommerceOrder::with(['user'])->latest()->limit(5)->get(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
        ]);
    }

    private function countFor(string $modelClass, ?callable $callback = null): int
    {
        try {
            if (! class_exists($modelClass)) {
                return 0;
            }

            $model = new $modelClass();
            if (! Schema::hasTable($model->getTable())) {
                return 0;
            }

            $query = $modelClass::query();
            if ($callback) {
                $callback($query);
            }

            return (int) $query->count();
        } catch (\Throwable $throwable) {
            return 0;
        }
    }

    public function urgentTasks()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.dashboard')) {
            return view('dashboard.admin.no-permission');
        }

        $route = fn (string $name, array $parameters = []) => Route::has($name) ? route($name, $parameters) : null;
        $count = fn (string $modelClass, ?callable $callback = null): int => $this->countFor($modelClass, $callback);

        $sections = [
            [
                'title' => 'حسابات جديدة تحتاج موافقة',
                'items' => [
                    ['label' => 'حسابات المستخدمين غير الموثقة', 'count' => $count(User::class, fn ($query) => $query->whereNull('email_verified_at')), 'note' => 'تحتاج مراجعة بيانات الدخول والتوثيق قبل الاعتماد.', 'url' => $route('admin.users', ['verification_status' => 'unverified'])],
                    ['label' => 'حسابات الموردين قيد المراجعة', 'count' => $count(VendorBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::PENDING_REVIEW->value)), 'note' => 'الملفات التجارية بانتظار اعتماد الإدارة.', 'url' => $route('admin.vendors', ['profile_status' => 'pending_review'])],
                    ['label' => 'حسابات المستودعات قيد المراجعة', 'count' => $count(WarehouseBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::PENDING_REVIEW->value)), 'note' => 'الملفات التجارية للمستودعات تحتاج موافقة.', 'url' => $route('admin.settings.warehouses.index', ['profile_status' => 'pending_review'])],
                    ['label' => 'حسابات شركات الشحن غير المفعلة', 'count' => $count(ShipmentCompany::class, fn ($query) => $query->withoutGlobalScope('active')->where('is_active', false)), 'note' => 'يمكن مراجعتها وتفعيلها من صفحة الشركات.', 'url' => $route('admin.shipment-companies', ['status' => 'inactive'])],
                    ['label' => 'حسابات المناديب قيد المراجعة', 'count' => $count(Representative::class, fn ($query) => $query->where('status', RepresentativeStatus::PENDING_REVIEW->value)), 'note' => 'اختر المناديب بانتظار الاعتماد أو الرفض.', 'url' => $route('admin.representatives.index', ['status' => 'pending_review'])],
                ],
            ],
            [
                'title' => 'حسابات موقوفة أو ملغية',
                'items' => [
                    ['label' => 'المستخدمون المحذوفون', 'count' => $count(User::class, fn ($query) => $query->onlyTrashed()), 'note' => 'الحسابات الملغية أو المحذوفة من النظام.', 'url' => $route('admin.users')],
                    ['label' => 'الموردون الموقوفون أو المحذوفون', 'count' => $count(Vendor::class, fn ($query) => $query->withTrashed()->where(function ($subQuery) { $subQuery->where('is_active', false)->orWhereNotNull('deleted_at'); })), 'note' => 'يشمل الحسابات غير النشطة والمرفوعة من النظام.', 'url' => $route('admin.vendors', ['status' => 'inactive'])],
                    ['label' => 'المستودعات الملغية', 'count' => $count(WarehouseBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::REJECTED->value)), 'note' => 'تعتمد هذه القيمة على حالة ملف الاعتماد للمستودع.', 'url' => $route('admin.settings.warehouses.index', ['profile_status' => 'rejected'])],
                    ['label' => 'المناديب الموقوفون أو المرفوضون', 'count' => $count(Representative::class, fn ($query) => $query->whereIn('status', [RepresentativeStatus::SUSPENDED->value, RepresentativeStatus::REJECTED->value])), 'note' => 'يمكن مراجعة الحالة من صفحة المناديب.', 'url' => $route('admin.representatives.index')],
                ],
            ],
            [
                'title' => 'موافقات الأدمن',
                'items' => [
                    ['label' => 'طلبات الدفع المعلقة', 'count' => $count(EcommerceOrder::class, fn ($query) => $query->where('payment_status', PaymentStatus::PENDING->value)), 'note' => 'طلبات تحتاج اعتماد الدفع قبل المتابعة.', 'url' => $route('admin.ecommerce-orders', ['payment_status' => 'pending'])],
                    ['label' => 'طلبات الإلغاء المعتمدة', 'count' => $count(EcommerceOrder::class, fn ($query) => $query->where('status', OrderStatus::CANCELLED->value)), 'note' => 'الطلبات التي تم إلغاؤها واعتماد الإلغاء لها.', 'url' => $route('admin.ecommerce-orders', ['status' => 'cancelled'])],
                    ['label' => 'طلبات الإرجاع المعتمدة', 'count' => $count(ReturnRequest::class, fn ($query) => $query->where('status', ReturnStatus::APPROVED->value)), 'note' => 'طلبات الإرجاع التي تمت الموافقة عليها.', 'url' => $route('admin.return-requests', ['status' => 'approved'])],
                    ['label' => 'شكاوى إلغاء المشتريات', 'count' => $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::PURCHASE_CANCELLATION->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])), 'note' => 'الشكاوى الخاصة بإلغاء طلبات الشراء بحاجة لمتابعة.', 'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::PURCHASE_CANCELLATION->value, 'status' => ComplaintStatus::PENDING->value])],
                    ['label' => 'شكاوى إلغاء الشحن', 'count' => $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::SHIPPING_CANCELLATION->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])), 'note' => 'الشكاوى الخاصة بإلغاء طلبات الشحن بحاجة لمتابعة.', 'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::SHIPPING_CANCELLATION->value, 'status' => ComplaintStatus::PENDING->value])],
                    ['label' => 'شكاوى المرتجعات', 'count' => $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::RETURN->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])), 'note' => 'شكاوى المرتجعات المفتوحة أو قيد المراجعة.', 'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::RETURN->value, 'status' => ComplaintStatus::PENDING->value])],
                    ['label' => 'طلبات استرداد المحفظة', 'count' => $count(ReturnRequest::class, fn ($query) => $query->where('refund_type', 'wallet')->where('status', ReturnStatus::REFUNDED->value)), 'note' => 'طلبات استرداد Metwzon عبر المحفظة.', 'url' => $route('admin.return-requests', ['status' => 'refunded', 'refund_type' => 'wallet'])],
                ],
            ],
            [
                'title' => 'طلبات معلقة أو غير مكتملة',
                'items' => [
                    ['label' => 'طلبات الشراء المعلقة', 'count' => $count(Order::class, fn ($query) => $query->where('status', OrderStatus::PENDING->value)), 'note' => 'الطلبات التي لم تُعتمد بعد داخل المتجر الإلكتروني.', 'url' => $route('admin.ecommerce-orders', ['status' => 'pending'])],
                    ['label' => 'طلبات الإلغاء المعلقة', 'count' => $count(ReturnRequest::class, fn ($query) => $query->where('status', ReturnStatus::REQUESTED->value)), 'note' => 'طلبات الإلغاء الجديدة بانتظار أول إجراء.', 'url' => $route('admin.return-requests', ['request_type' => 'cancellation', 'status' => 'requested'])],
                    ['label' => 'طلبات الإرجاع المعلقة', 'count' => $count(ReturnRequest::class, fn ($query) => $query->where('status', ReturnStatus::REQUESTED->value)), 'note' => 'طلبات الإرجاع الجديدة بانتظار أول إجراء.', 'url' => $route('admin.return-requests', ['status' => 'requested'])],
                    ['label' => 'طلبات الشحن المعلقة', 'count' => $count(ShipmentRequest::class, fn ($query) => $query->where('status', ShipmentRequestStatus::SUBMITTED->value)), 'note' => 'طلبات الشحن التي تم إرسالها ولم تُعالج بعد.', 'url' => $route('admin.shipment-requests.index', ['status' => 'submitted'])],
                    ['label' => 'طلبات التوصيل المعلقة', 'count' => $count(EcommerceOrder::class, fn ($query) => $query->where('status', OrderStatus::PENDING->value)), 'note' => 'طلبات التوصيل داخل مسار الشحن العادي.', 'url' => $route('admin.shipment-orders', ['status' => 'pending'])],
                ],
            ],
            [
                'title' => 'شكاوى معلقة أو غير مغلقة',
                'items' => [
                    ['label' => 'شكاوى المستخدمين', 'count' => $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::USER->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])), 'note' => 'الشكاوى العامة للمستخدمين قيد المتابعة.', 'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::USER->value, 'status' => ComplaintStatus::PENDING->value])],
                    ['label' => 'شكاوى الموردين', 'count' => $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::VENDOR->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])), 'note' => 'الشكاوى العامة للموردين قيد المتابعة.', 'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::VENDOR->value, 'status' => ComplaintStatus::PENDING->value])],
                    ['label' => 'شكاوى المستودعات', 'count' => $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::WAREHOUSE->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])), 'note' => 'الشكاوى العامة للمستودعات قيد المتابعة.', 'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::WAREHOUSE->value, 'status' => ComplaintStatus::PENDING->value])],
                    ['label' => 'شكاوى المناديب', 'count' => $count(Complaint::class, fn ($query) => $query->where('complaint_type', ComplaintType::REPRESENTATIVE->value)->whereIn('status', [ComplaintStatus::PENDING->value, ComplaintStatus::UNDER_REVIEW->value])), 'note' => 'الشكاوى العامة للمناديب قيد المتابعة.', 'url' => $route('admin.complaints.index', ['complaint_type' => ComplaintType::REPRESENTATIVE->value, 'status' => ComplaintStatus::PENDING->value])],
                ],
            ],
        ];

        $totalUrgentItems = collect($sections)->pluck('items')->flatten(1)->sum('count');

        return view('dashboard.admin.urgent-tasks', compact('sections', 'totalUrgentItems'));
    }

    private function dashboardStats(): array
    {
        $count = fn (string $modelClass, ?callable $callback = null): int => $this->countFor($modelClass, $callback);

        return [
            'total_users' => $count(User::class),
            'total_vendors' => $count(Vendor::class),
            'total_shipment_companies' => $count(ShipmentCompany::class),
            'total_products' => $count(Product::class),
            'total_shipment_orders' => $count(Order::class),
            'total_ecommerce_orders' => $count(EcommerceOrder::class),
            'pending_shipment_orders' => $count(Order::class, fn ($query) => $query->where('status', OrderStatus::PENDING->value)),
            'pending_ecommerce_orders' => $count(EcommerceOrder::class, fn ($query) => $query->where('status', OrderStatus::PENDING->value)),
            'pending_vendor_approvals' => $count(VendorBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::PENDING_REVIEW->value)),
            'pending_warehouse_approvals' => $count(WarehouseBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::PENDING_REVIEW->value)),
            'pending_shipment_requests' => $count(ShipmentRequest::class, fn ($query) => $query->where('status', ShipmentRequestStatus::SUBMITTED->value)),
            'total_shipment_requests' => $count(ShipmentRequest::class),
            'assigned_shipment_requests' => $count(ShipmentRequest::class, fn ($query) => $query->where('status', 'assigned')),
            'completed_shipment_requests' => $count(ShipmentRequest::class, fn ($query) => $query->where('status', 'completed')),
            'cancelled_shipment_requests' => $count(ShipmentRequest::class, fn ($query) => $query->where('status', 'cancelled')),
            'approved_vendors' => $count(VendorBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::APPROVED->value)),
            'approved_warehouses' => $count(WarehouseBusinessProfile::class, fn ($query) => $query->where('status', BusinessProfileStatus::APPROVED->value)),
            'active_representatives' => $count(Representative::class, fn ($query) => $query->where('status', RepresentativeStatus::APPROVED->value)),
        ];
    }

    public function monthlyRevenue()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.dashboard')) {
            return view('dashboard.admin.no-permission');
        }

        $monthly_revenue = $this->getMonthlyRevenue();

        return view('dashboard.admin.dashboard.monthly-revenue', compact('monthly_revenue'));
    }

    // Shipment Orders Management
    public function shipmentOrders()
    {
        $orders = Order::with(['user', 'shipmentCompany', 'orderItems.package'])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.shipment-orders', compact('orders'));
    }

    public function shipmentOrderDetails($id)
    {
        $order = Order::with(['user', 'shipmentCompany', 'orderItems.package.packageDetails'])
            ->findOrFail($id);

        return view('dashboard.admin.shipment-order-details', compact('order'));
    }

    public function updateShipmentOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,in_transit,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    // Ecommerce Orders Management
    public function ecommerceOrders()
    {
        $orders = EcommerceOrder::with(['user', 'items.product'])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.ecommerce-orders', compact('orders'));
    }

    public function ecommerceOrderDetails($id)
    {
        $order = EcommerceOrder::with(['user', 'userAddress', 'items.product.vendor'])
            ->findOrFail($id);

        return view('dashboard.admin.ecommerce-order-details', compact('order'));
    }

    public function updateEcommerceOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,shipped,delivered,cancelled,returned'
        ]);

        $order = EcommerceOrder::findOrFail($id);
        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    // Vendors Management
    public function vendors()
    {
        $vendors = Vendor::withCount(['products', 'ecommerceOrderItems'])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.vendors', compact('vendors'));
    }

    public function vendorDetails($id)
    {
        $vendor = Vendor::withCount(['products', 'ecommerceOrderItems'])
            ->withTrashed()
            ->findOrFail($id);

        // recent products (last 5) with category + images
        $recentProducts = Product::with(['category', 'images'])
            ->where('vendor_id', $id)
            ->latest()
            ->limit(5)
            ->get();

        // recent orders for this vendor (last 10)
        $vendor_orders = EcommerceOrder::whereHas('items.product', function ($query) use ($id) {
            $query->where('vendor_id', $id);
        })
            ->with(['user', 'items.product.images'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.admin.vendor-details', compact('vendor', 'recentProducts', 'vendor_orders'));
    }

    public function vendorProducts($id)
    {
        $vendor = Vendor::findOrFail($id);
        $products = Product::with(['category', 'images'])
            ->where('vendor_id', $id)
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.vendor-products', compact('vendor', 'products'));
    }

    public function editVendor($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('dashboard.admin.edit-vendor', compact('vendor'));
    }

    public function updateVendor(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'country_code' => 'required|string|max:10',
            'logo' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('vendor-logos', 'public');
            $data['logo'] = $logoPath;
        }

        $vendor->update($data);

        return redirect()
            ->route('admin.vendors.show', $vendor->id)
            ->with('success', 'Vendor updated successfully');
    }


    public function createVendor()
    {
        return view('dashboard.admin.create-vendor');
    }

    public function storeVendor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'country_code' => 'required|string|max:10',
        ]);

        $vendor = Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'country_code' => $request->country_code,
            'is_active' => true,
        ]);

        return redirect()->route('admin.vendors')->with('success', 'Vendor created successfully.');
    }

    public function toggleVendorStatus($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->is_active = !$vendor->is_active;
        $vendor->save();

        return back()->with('success', 'Vendor status updated successfully');
    }

    // Shipment Companies Management
    public function shipmentCompanies()
    {
        $companies = ShipmentCompany::withCount(['packages', 'orders'])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.shipment-companies', compact('companies'));
    }

    public function createShipmentCompany()
    {
        return view('dashboard.admin.create-shipment-company');
    }

    public function storeShipmentCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:shipment_companies,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'facebook' => 'nullable|url',
            'whatsapp' => 'nullable|string',
        ]);

        $company = ShipmentCompany::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'description' => $request->description,
            'facebook' => $request->facebook,
            'whatsapp' => $request->whatsapp,
            'is_active' => true,
        ]);

        return redirect()->route('admin.shipment-companies')->with('success', 'Shipment company created successfully.');
    }

    public function shipmentCompanyDetails($id)
    {
        $company = ShipmentCompany::with(['packages', 'orders.user'])
            ->withCount(['packages', 'orders'])
            ->withTrashed()
            ->findOrFail($id);

        $company_orders = Order::where('shipment_company_id', $id)
            ->with(['user', 'orderItems.package'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.admin.shipment-company-details', compact('company', 'company_orders'));
    }

    public function toggleShipmentCompanyStatus($id)
    {
        $company = ShipmentCompany::findOrFail($id);
        $company->update(['is_active' => !$company->is_active]);

        $status = $company->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Shipment company {$status} successfully.");
    }

    // Users Management
    public function users()
    {
        $users = User::withCount(['orders', 'ecommerceOrders'])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.users', compact('users'));
    }

    public function userDetails($id)
    {
        $user = User::with(['addresses', 'orders.shipmentCompany', 'ecommerceOrders'])
            ->withCount(['orders', 'ecommerceOrders'])
            ->withTrashed()
            ->findOrFail($id);

        return view('dashboard.admin.user-details', compact('user'));
    }

    // Products Management
    public function products()
    {
        $products = Product::with(['vendor', 'category', 'media'])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.products', compact('products'));
    }

    public function showProduct($id)
    {
        $product = Product::with(['vendor', 'category', 'images'])
            ->findOrFail($id);

        $orders = EcommerceOrder::whereHas('items', function ($query) use ($id) {
            $query->where('product_id', $id);
        })
            ->with(['user', 'items' => function ($query) use ($id) {
                $query->where('product_id', $id);
            }])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.admin.product-details', compact('product', 'orders'));
    }

    public function toggleProductStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Product {$status} successfully.");
    }

    // Reports
    public function reports()
    {
        $sales_data = [
            'total_shipment_revenue' => Order::sum('final_price'),
            'total_ecommerce_revenue' => EcommerceOrder::sum('total_amount'),
            'top_vendors' => Vendor::withCount('ecommerceOrderItems')
                ->orderBy('ecommerce_order_items_count', 'desc')
                ->limit(5)
                ->get(),
            'top_shipment_companies' => ShipmentCompany::withCount('orders')
                ->orderBy('orders_count', 'desc')
                ->limit(5)
                ->get(),
        ];

        return view('dashboard.admin.reports', compact('sales_data'));
    }

    private function getMonthlyRevenue()
    {
        $shipment_revenue = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(final_price) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month', 'year')
            ->get();

        $ecommerce_revenue = EcommerceOrder::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month', 'year')
            ->get();

        return [
            'shipment' => $shipment_revenue,
            'ecommerce' => $ecommerce_revenue
        ];
    }
}
