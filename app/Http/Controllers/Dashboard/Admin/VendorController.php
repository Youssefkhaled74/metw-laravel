<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\EcommerceOrder;
use App\Models\VendorCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exports\VendorReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        try {
            if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.vendors')) {
                return view('dashboard.admin.no-permission');
            }

            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'status' => ['nullable', 'in:all,active,inactive'],
                'sort_by' => ['nullable', 'in:id,created_at,orders,products'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDir = $validated['sort_dir'] ?? 'desc';

            $vendorsQuery = Vendor::withoutGlobalScope('active')
                ->withCount(['products', 'ecommerceOrderItems']);

            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $vendorsQuery->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            }

            if (!empty($validated['status']) && $validated['status'] !== 'all') {
                $vendorsQuery->where('is_active', $validated['status'] === 'active');
            }

            if ($sortBy === 'orders') {
                $vendorsQuery->orderBy('ecommerce_order_items_count', $sortDir);
            } elseif ($sortBy === 'products') {
                $vendorsQuery->orderBy('products_count', $sortDir);
            } else {
                $vendorsQuery->orderBy($sortBy, $sortDir);
            }

            $vendors = $vendorsQuery
                ->paginate(20)
                ->appends($request->query());

            return view('dashboard.admin.vendors', compact('vendors', 'sortBy', 'sortDir'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    public function show(Vendor $vendor)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.vendors.show')) {
            return view('dashboard.admin.no-permission');
        }

        // counts
        $vendor->loadCount(['products']);

        // 🔥 العدد الصحيح للطلبات
        $vendor->total_orders_count = EcommerceOrder::whereHas('items.product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })->distinct('ecommerce_orders.id')->count('ecommerce_orders.id');


        $recentProducts = Product::withoutGlobalScope('active')->with(['category', 'images'])
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->limit(5)
            ->get();

        $vendor_orders = EcommerceOrder::whereHas('items.product', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id);
        })
            ->with(['user', 'items.product.images'])
            ->latest()
            ->limit(10)
            ->get();

        $vendorCommission = VendorCommission::where('vendor_id', $vendor->id)->first();
        $publicCommission = VendorCommission::whereNull('vendor_id')->first();
        $isUsingPublic = !$vendorCommission && $publicCommission;

        return view(
            'dashboard.admin.vendor-details',
            compact('vendor','recentProducts','vendor_orders','vendorCommission','publicCommission','isUsingPublic')
        );
    }

    public function exportReport(Vendor $vendor)
    {
        Log::info('Export report started', [
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->name ?? null,
        ]);

        try {

            if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                Log::error('Excel facade class NOT found!');
            } else {
                Log::info('Excel facade class exists.');
            }

            Log::info('Creating VendorReportExport instance...');
            $export = new VendorReportExport($vendor);

            Log::info('Starting Excel download...');

            return Excel::download(
                $export,
                'vendor_'.$vendor->id.'_report.xlsx'
            );

        } catch (\Throwable $e) {

            Log::error('Export failed!', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'Export failed. Check logs.');
        }
    }

    public function create()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.vendors.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.create-vendor');
    }

    public function store(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.vendors.store')) {
            return view('dashboard.admin.no-permission');
        }
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

    public function edit(Vendor $vendor)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.vendors.edit')) {
            return view('dashboard.admin.no-permission');
        }

        return view('dashboard.admin.edit-vendor', compact('vendor'));
    }
    public function update(Request $request, Vendor $vendor)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.vendors.update')) {
            return view('dashboard.admin.no-permission');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendors,email,' . $vendor->id,
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
            ->route('admin.vendors.show', $vendor)
            ->with('success', 'Vendor updated successfully');
    }


    public function toggleStatus(Vendor $vendor)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.vendors.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }

        $vendor->is_active = !$vendor->is_active;
        $vendor->save();

        return back()->with('success', 'Vendor status updated successfully');
    }

    public function products(Vendor $vendor)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.vendors.products')) {
            return view('dashboard.admin.no-permission');
        }

        $products = Product::withoutGlobalScope('active')->with(['category', 'images'])
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.vendor-products', compact('vendor','products'));
    }
}
