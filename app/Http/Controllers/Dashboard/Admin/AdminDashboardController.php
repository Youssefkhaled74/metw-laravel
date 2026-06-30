<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\BusinessProfileStatus;
use App\Enum\PaymentStatus;
use App\Enum\ReturnStatus;
use App\Enum\ShipmentRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Product;
use App\Models\Representative;
use App\Models\ShipmentCompany;
use App\Models\Employee;
use App\Models\ShipmentRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorBusinessProfile;
use App\Models\WarehouseBusinessProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;


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
        // dd(Auth::guard('employee')->user()->can('admin.dashboard'));

        $safeCount = function ($model, $column = null, $value = null) {
            try {
                $table = (new $model)->getTable();
                if (!Schema::hasTable($table)) {
                    return 0;
                }
                $query = $model::query();
                if ($column && $value !== null) {
                    $query = $query->where($column, $value);
                }
                return $query->count();
            } catch (\Exception $e) {
                return 0;
            }
        };

        $stats = [
            'total_users' => User::count(),
            'total_vendors' => Vendor::count(),
            'total_shipment_companies' => ShipmentCompany::count(),
            'total_products' => Product::count(),
            'total_shipment_orders' => Order::count(),
            'total_ecommerce_orders' => EcommerceOrder::count(),
            'pending_shipment_orders' => Order::where('status', 'pending')->count(),
            'pending_ecommerce_orders' => EcommerceOrder::where('status', 'pending')->count(),

            // Phase 2 - Shipment Requests
            'total_shipment_requests' => $safeCount(ShipmentRequest::class),
            'pending_shipment_requests' => $safeCount(ShipmentRequest::class, 'status', ShipmentRequestStatus::SUBMITTED),
            // Assigned, completed, and cancelled statuses do not exist on ShipmentRequest yet.
            // ShipmentRequestStatus enum only has DRAFT and SUBMITTED.
            // 'assigned_shipment_requests' => ShipmentRequest::where(...)->count(),
            // 'completed_shipment_requests' => ShipmentRequest::where(...)->count(),
            // 'cancelled_shipment_requests' => ShipmentRequest::where(...)->count(),

            // Phase 2 - Vendor Business Profiles
            'pending_vendor_approvals' => $safeCount(VendorBusinessProfile::class, 'status', BusinessProfileStatus::PENDING_REVIEW),
            'approved_vendors' => $safeCount(VendorBusinessProfile::class, 'status', BusinessProfileStatus::APPROVED),

            // Phase 2 - Warehouse Business Profiles
            'pending_warehouse_approvals' => $safeCount(WarehouseBusinessProfile::class, 'status', BusinessProfileStatus::PENDING_REVIEW),
            'approved_warehouses' => $safeCount(WarehouseBusinessProfile::class, 'status', BusinessProfileStatus::APPROVED),

            // Phase 2 - Active Shipment Companies (without global scope to query raw)
            'active_shipment_companies' => ShipmentCompany::withoutGlobalScope('active')->where('is_active', true)->count(),

            // Phase 2 - Active Representatives
            'active_representatives' => $safeCount(Representative::class, 'is_active', true),
        ];

        $latestUnverifiedUser = User::whereNull('email_verified_at')->latest()->first();
        $latestVerifiedUser = User::whereNotNull('email_verified_at')->latest('email_verified_at')->first();
        $latestRejectedUser = User::onlyTrashed()->latest('deleted_at')->first();

        $latestPendingVendor = Vendor::withoutGlobalScope('active')->where('is_active', false)->latest()->first();
        $latestActiveVendor = Vendor::active()->latest()->first();
        $latestRejectedVendor = Vendor::onlyTrashed()->latest('deleted_at')->first();

        $latestPendingShipmentCompany = ShipmentCompany::withoutGlobalScope('active')->where('is_active', false)->latest()->first();
        $latestActiveShipmentCompany = ShipmentCompany::active()->latest()->first();
        $latestRejectedShipmentCompany = ShipmentCompany::onlyTrashed()->latest('deleted_at')->first();

        $latestPendingPaymentOrder = EcommerceOrder::where('payment_status', PaymentStatus::PENDING->value)
            ->latest()
            ->first();
        $latestCancelledEcommerceOrder = EcommerceOrder::where('status', 'cancelled')->latest()->first();
        $latestApprovedReturnRequest = ReturnRequest::where('status', ReturnStatus::APPROVED->value)->latest()->first();
        $latestWalletRefundRequest = ReturnRequest::where('refund_type', 'wallet')
            ->where('status', ReturnStatus::REFUNDED->value)
            ->latest()
            ->first();

        $latestPendingShipmentOrder = Order::where('status', 'pending')->latest()->first();
        $latestPendingEcommerceOrder = EcommerceOrder::where('status', 'pending')->latest()->first();
        $latestPendingReturnRequest = ReturnRequest::where('status', ReturnStatus::REQUESTED->value)->latest()->first();
        $latestPickupReturnRequest = ReturnRequest::where('status', ReturnStatus::PICKUP->value)->latest()->first();

        $cycleUiLabels = [
            'needs_approval' => __('admin-dashboard.needs_approval'),
            'latest_item' => __('admin-dashboard.latest_item'),
            'latest_trusted' => __('admin-dashboard.latest_trusted'),
            'latest_rejected' => __('admin-dashboard.latest_rejected'),
            'trust_level' => __('admin-dashboard.trust_level'),
            'trusted' => __('admin-dashboard.trusted'),
            'rejected' => __('admin-dashboard.rejected'),
            'view_all' => __('admin-dashboard.view_all'),
            'no_complaints_source' => __('admin-dashboard.no_complaints_source'),
        ];

        $dashboardCycles = [
            'approvals' => [
                'title' => __('admin-dashboard.accounts_need_approval'),
                'subtitle' => __('admin-dashboard.new_accounts_cycle'),
                'cards' => [
                    // [
                    //     'title' => __('admin-dashboard.user_accounts'),
                    //     'count' => User::whereNull('email_verified_at')->count(),
                    //     'latest_title' => $latestUnverifiedUser?->username ?? __('admin-dashboard.not_available'),
                    //     'latest_meta' => $latestUnverifiedUser?->email ?? '',
                    //     'latest_status' => __('admin-dashboard.needs_approval'),
                    //     'latest_url' => $latestUnverifiedUser ? route('admin.users.show', $latestUnverifiedUser->id) : null,
                    //     'view_all_url' => route('admin.users'),
                    // ],
                    [
                        'title' => __('admin-dashboard.vendor_accounts'),
                        'count' => Vendor::withoutGlobalScope('active')->where('is_active', false)->count(),
                        'latest_title' => $latestPendingVendor?->name ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestPendingVendor?->email ?? '',
                        'latest_status' => __('admin-dashboard.needs_approval'),
                        'latest_url' => $latestPendingVendor ? route('admin.vendors.show', $latestPendingVendor->id) : null,
                        'view_all_url' => route('admin.vendors'),
                    ],
                    [
                        'title' => __('admin-dashboard.shipment_company_accounts'),
                        'count' => ShipmentCompany::withoutGlobalScope('active')->where('is_active', false)->count(),
                        'latest_title' => $latestPendingShipmentCompany?->name ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestPendingShipmentCompany?->email ?? '',
                        'latest_status' => __('admin-dashboard.needs_approval'),
                        'latest_url' => $latestPendingShipmentCompany ? route('admin.shipment-companies.show', $latestPendingShipmentCompany->id) : null,
                        'view_all_url' => route('admin.shipment-companies'),
                    ],
                ],
            ],
            'trust' => [
                'title' => __('admin-dashboard.trusted_or_rejected_accounts'),
                'subtitle' => __('admin-dashboard.trust_cycle'),
                'cards' => [
                    [
                        'title' => __('admin-dashboard.users'),
                        'trusted_count' => User::whereNotNull('email_verified_at')->count(),
                        'rejected_count' => User::onlyTrashed()->count(),
                        'trusted_title' => $latestVerifiedUser?->username ?? __('admin-dashboard.not_available'),
                        'trusted_meta' => $latestVerifiedUser?->email ?? '',
                        'trusted_url' => $latestVerifiedUser ? route('admin.users.show', $latestVerifiedUser->id) : null,
                        'rejected_title' => $latestRejectedUser?->username ?? __('admin-dashboard.not_available'),
                        'rejected_meta' => $latestRejectedUser?->email ?? '',
                        'rejected_url' => $latestRejectedUser ? route('admin.users.show', $latestRejectedUser->id) : null,
                        'view_all_url' => route('admin.users'),
                    ],
                    [
                        'title' => __('admin-dashboard.vendors'),
                        'trusted_count' => Vendor::active()->count(),
                        'rejected_count' => Vendor::onlyTrashed()->count(),
                        'trusted_title' => $latestActiveVendor?->name ?? __('admin-dashboard.not_available'),
                        'trusted_meta' => $latestActiveVendor?->email ?? '',
                        'trusted_url' => $latestActiveVendor ? route('admin.vendors.show', $latestActiveVendor->id) : null,
                        'rejected_title' => $latestRejectedVendor?->name ?? __('admin-dashboard.not_available'),
                        'rejected_meta' => $latestRejectedVendor?->email ?? '',
                        'rejected_url' => $latestRejectedVendor ? route('admin.vendors.show', $latestRejectedVendor->id) : null,
                        'view_all_url' => route('admin.vendors'),
                    ],
                    [
                        'title' => __('admin-dashboard.shipment_companies'),
                        'trusted_count' => ShipmentCompany::active()->count(),
                        'rejected_count' => ShipmentCompany::onlyTrashed()->count(),
                        'trusted_title' => $latestActiveShipmentCompany?->name ?? __('admin-dashboard.not_available'),
                        'trusted_meta' => $latestActiveShipmentCompany?->email ?? '',
                        'trusted_url' => $latestActiveShipmentCompany ? route('admin.shipment-companies.show', $latestActiveShipmentCompany->id) : null,
                        'rejected_title' => $latestRejectedShipmentCompany?->name ?? __('admin-dashboard.not_available'),
                        'rejected_meta' => $latestRejectedShipmentCompany?->email ?? '',
                        'rejected_url' => $latestRejectedShipmentCompany ? route('admin.shipment-companies.show', $latestRejectedShipmentCompany->id) : null,
                        'view_all_url' => route('admin.shipment-companies'),
                    ],
                ],
            ],
            'adminApprovals' => [
                'title' => __('admin-dashboard.admin_approvals'),
                'subtitle' => __('admin-dashboard.admin_approval_cycle'),
                'cards' => [
                    [
                        'title' => __('admin-dashboard.pending_payments'),
                        'count' => EcommerceOrder::where('payment_status', PaymentStatus::PENDING->value)->count(),
                        'latest_title' => $latestPendingPaymentOrder?->order_number ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestPendingPaymentOrder ? ucfirst($latestPendingPaymentOrder->status) : '',
                        'latest_status' => __('admin-dashboard.needs_review'),
                        'latest_url' => $latestPendingPaymentOrder ? route('admin.ecommerce-orders.show', $latestPendingPaymentOrder->id) : null,
                        'view_all_url' => route('admin.ecommerce-orders'),
                    ],
                    [
                        'title' => __('admin-dashboard.approved_cancellations'),
                        'count' => EcommerceOrder::where('status', 'cancelled')->count(),
                        'latest_title' => $latestCancelledEcommerceOrder?->order_number ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestCancelledEcommerceOrder ? ucfirst($latestCancelledEcommerceOrder->status) : '',
                        'latest_status' => __('admin-dashboard.approved'),
                        'latest_url' => $latestCancelledEcommerceOrder ? route('admin.ecommerce-orders.show', $latestCancelledEcommerceOrder->id) : null,
                        'view_all_url' => route('admin.ecommerce-orders'),
                    ],
                    [
                        'title' => __('admin-dashboard.approved_returns'),
                        'count' => ReturnRequest::where('status', ReturnStatus::APPROVED->value)->count(),
                        'latest_title' => $latestApprovedReturnRequest?->return_number ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestApprovedReturnRequest?->order?->order_number ?? '',
                        'latest_status' => __('admin-dashboard.approved'),
                        'latest_url' => $latestApprovedReturnRequest ? route('admin.return-requests.show', $latestApprovedReturnRequest->id) : null,
                        'view_all_url' => route('admin.return-requests'),
                    ],
                    [
                        'title' => __('admin-dashboard.wallet_refund_requests'),
                        'count' => ReturnRequest::where('refund_type', 'wallet')->where('status', ReturnStatus::REFUNDED->value)->count(),
                        'latest_title' => $latestWalletRefundRequest?->return_number ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestWalletRefundRequest?->order?->order_number ?? '',
                        'latest_status' => __('admin-dashboard.refunded'),
                        'latest_url' => $latestWalletRefundRequest ? route('admin.return-requests.show', $latestWalletRefundRequest->id) : null,
                        'view_all_url' => route('admin.return-requests'),
                    ],
                ],
            ],
            'pending' => [
                'title' => __('admin-dashboard.pending_or_incomplete_orders'),
                'subtitle' => __('admin-dashboard.pending_cycle'),
                'cards' => [
                    [
                        'title' => __('admin-dashboard.pending_shipment_orders'),
                        'count' => Order::where('status', 'pending')->count(),
                        'latest_title' => $latestPendingShipmentOrder?->order_number ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestPendingShipmentOrder ? ucfirst($latestPendingShipmentOrder->status->name) : '',
                        'latest_status' => __('admin-dashboard.pending'),
                        'latest_url' => $latestPendingShipmentOrder ? route('admin.shipment-orders.show', $latestPendingShipmentOrder->id) : null,
                        'view_all_url' => route('admin.shipment-orders'),
                    ],
                    [
                        'title' => __('admin-dashboard.pending_ecommerce_orders'),
                        'count' => EcommerceOrder::where('status', 'pending')->count(),
                        'latest_title' => $latestPendingEcommerceOrder?->order_number ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestPendingEcommerceOrder ? ucfirst($latestPendingEcommerceOrder->status) : '',
                        'latest_status' => __('admin-dashboard.pending'),
                        'latest_url' => $latestPendingEcommerceOrder ? route('admin.ecommerce-orders.show', $latestPendingEcommerceOrder->id) : null,
                        'view_all_url' => route('admin.ecommerce-orders'),
                    ],
                    [
                        'title' => __('admin-dashboard.pending_return_requests'),
                        'count' => ReturnRequest::where('status', ReturnStatus::REQUESTED->value)->count(),
                        'latest_title' => $latestPendingReturnRequest?->return_number ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestPendingReturnRequest?->order?->order_number ?? '',
                        'latest_status' => __('admin-dashboard.pending'),
                        'latest_url' => $latestPendingReturnRequest ? route('admin.return-requests.show', $latestPendingReturnRequest->id) : null,
                        'view_all_url' => route('admin.return-requests'),
                    ],
                    [
                        'title' => __('admin-dashboard.processing_return_requests'),
                        'count' => ReturnRequest::where('status', ReturnStatus::PICKUP->value)->count(),
                        'latest_title' => $latestPickupReturnRequest?->return_number ?? __('admin-dashboard.not_available'),
                        'latest_meta' => $latestPickupReturnRequest?->order?->order_number ?? '',
                        'latest_status' => __('admin-dashboard.in_progress'),
                        'latest_url' => $latestPickupReturnRequest ? route('admin.return-requests.show', $latestPickupReturnRequest->id) : null,
                        'view_all_url' => route('admin.return-requests'),
                    ],
                ],
            ],
            'complaints' => [
                'title' => __('admin-dashboard.pending_or_incomplete_complaints'),
                'subtitle' => __('admin-dashboard.no_complaints_source'),
                'cards' => [],
            ],
        ];

        return view('dashboard.admin.dashboard.dashboard2', compact(
            'stats',
            'dashboardCycles',
            'cycleUiLabels'
        ));
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
