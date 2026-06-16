<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShipmentCompany;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_vendors' => Vendor::count(),
            'total_shipment_companies' => ShipmentCompany::count(),
            'total_products' => Product::count(),
            'total_shipment_orders' => Order::count(),
            'total_ecommerce_orders' => EcommerceOrder::count(),
            'pending_shipment_orders' => Order::where('status', 'pending')->count(),
            'pending_ecommerce_orders' => EcommerceOrder::where('status', 'pending')->count(),
        ];

        // Recent orders
        $recent_shipment_orders = Order::with(['user', 'shipmentCompany'])
            ->latest()
            ->limit(5)
            ->get();

        $recent_ecommerce_orders = EcommerceOrder::with(['user'])
            ->latest()
            ->limit(5)
            ->get();

        // Monthly revenue chart data
        $monthly_revenue = $this->getMonthlyRevenue();

        return view('dashboard.admin.dashboard', compact(
            'stats',
            'recent_shipment_orders',
            'recent_ecommerce_orders',
            'monthly_revenue'
        ));
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
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.reports')) {
            return view('dashboard.admin.no-permission');
        }
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
