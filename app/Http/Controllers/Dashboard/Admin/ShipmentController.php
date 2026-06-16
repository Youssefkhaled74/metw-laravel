<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Models\ShipmentCompany;
use App\Notifications\OrderAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Services\ShippingService;
use App\Models\ShipmentCommission;
use App\Exports\ShipmentCompanyReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function orders(Request $request)
    {
        try {
            if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-orders')) {
                return view('dashboard.admin.no-permission');
            }

            $statuses = array_map(fn ($status) => $status->value, OrderStatus::cases());

            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'status' => ['nullable', 'string'],
                'shipment_company_id' => ['nullable', 'string'],
                'sort_by' => ['nullable', 'in:created_at,order_number,final_price'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDir = $validated['sort_dir'] ?? 'desc';

            $ordersQuery = Order::query()->with(['user', 'shipmentCompany', 'orderItems.package']);

            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $ordersQuery->where(function ($query) use ($search) {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            if (!empty($validated['status']) && $validated['status'] !== 'all') {
                $ordersQuery->where('status', $validated['status']);
            }

            if (!empty($validated['shipment_company_id']) && $validated['shipment_company_id'] !== 'all') {
                if ($validated['shipment_company_id'] === 'none') {
                    $ordersQuery->whereNull('shipment_company_id');
                } else {
                    $ordersQuery->where('shipment_company_id', (int) $validated['shipment_company_id']);
                }
            }

            $orders = $ordersQuery
                ->orderBy($sortBy, $sortDir)
                ->paginate(20)
                ->appends($request->query());

            $shipmentCompanies = ShipmentCompany::withoutGlobalScope('active')->orderBy('name')->get(['id', 'name']);

            return view('dashboard.admin.shipment-orders', compact('orders', 'shipmentCompanies', 'statuses'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    public function exportReport(ShipmentCompany $shipmentCompany)
    {
        $company = ShipmentCompany::withoutGlobalScope('active')->findOrFail($shipmentCompany->id);

        return Excel::download(
            new ShipmentCompanyReportExport($shipmentCompany),
            'shipment_company_'.$shipmentCompany->id.'_report.xlsx'
        );
    }

    public function showOrder($id)
    {
        if (Auth::guard('employee')->check() &&
            !Auth::guard('employee')->user()->can('admin.shipment-orders.show')) {
            return view('dashboard.admin.no-permission');
        }

        $order = Order::with([
            'user',
            'orderItems.shipmentCompany',
            'orderItems.package.packageDetails',
            'orderItems.package.pickupAddress.city',
            'orderItems.package.pickupAddress.state',
            'orderItems.package.pickupAddress.zone',
            'orderItems.package.dropoffAddress.city',
            'orderItems.package.dropoffAddress.state',
            'orderItems.package.dropoffAddress.zone',
            'orderItems.route',
        ])->findOrFail($id);

        return view('dashboard.admin.shipment-order-details', compact('order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-orders.update-status')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'status' => ['required', Rule::in(OrderStatus::cases())]
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function assignCompany(Request $request, $id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-orders.assign-company')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'shipment_company_id' => 'required|exists:shipment_companies,id'
        ]);

        $order = Order::findOrFail($id);

        // Only check if order already has a company assigned
        if ($order->shipment_company_id !== null) {
            return redirect()->back()->with('error', 'Order is already assigned to a shipment company.');
        }

        // Update company and set status to confirmed if it's still pending
        $updateData = ['shipment_company_id' => $request->shipment_company_id];
        if ($order->status === 'pending') {
            $updateData['status'] = 'confirmed';
        }

        $shipmentCompany = ShipmentCompany::findOrFail($request->shipment_company_id);

        // Ensure coverage for all items
        if (!ShippingService::companyCoversOrder($shipmentCompany, $order)) {
            return redirect()->back()->with('error', 'Selected shipment company does not cover all package locations for this order.');
        }

        // Compute and assign shipping cost per item, and set shipment company on items
        $order->loadMissing(['orderItems.package.pickupAddress', 'orderItems.package.dropoffAddress']);
        $shippingSum = 0.0;
        foreach ($order->orderItems as $item) {
            $price = ShippingService::estimateItemPrice($shipmentCompany, $item);
            $price = $price === null ? 0.0 : (float) $price;
            $item->update([
                'shipment_company_id' => $shipmentCompany->id,
                'est_price' => $price,
            ]);
            $shippingSum += $price;
        }

        // Update order's shipment company and final price
        $base = (float) ($order->total_price ?? 0);
        if (!is_null($order->discount_price)) {
            $base -= (float) $order->discount_price;
        }
        $updateData['final_price'] = round($base + $shippingSum, 2);

        $order->update($updateData);

        // Notify shipment company after successful assignment
        $shipmentCompany->notify(new OrderAssigned($order));

        return redirect()->back()->with('success', 'Order assigned to shipment company successfully.');
    }

    public function companies(Request $request)
    {
        try {
            if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-companies')) {
                return view('dashboard.admin.no-permission');
            }

            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'status' => ['nullable', 'in:all,active,inactive'],
                'sort_by' => ['nullable', 'in:company_number,name,created_at,orders,packages'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDir = $validated['sort_dir'] ?? 'desc';

            $companiesQuery = ShipmentCompany::withoutGlobalScope('active')
                ->withCount(['packages', 'orders']);

            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $companiesQuery->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if (!empty($validated['status']) && $validated['status'] !== 'all') {
                $companiesQuery->where('is_active', $validated['status'] === 'active');
            }

            if ($sortBy === 'orders') {
                $companiesQuery->orderBy('orders_count', $sortDir);
            } elseif ($sortBy === 'packages') {
                $companiesQuery->orderBy('packages_count', $sortDir);
            } else {
                $companiesQuery->orderBy($sortBy, $sortDir);
            }

            $companies = $companiesQuery
                ->paginate(20)
                ->appends($request->query());

            return view('dashboard.admin.shipment-companies', compact('companies', 'sortBy', 'sortDir'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    public function createCompany()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-companies.create')) {
            return view('dashboard.admin.no-permission');
        }
        return view('dashboard.admin.create-shipment-company');
    }

    public function storeCompany(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-companies.store')) {
            return view('dashboard.admin.no-permission');
        }
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
            'facebook_url' => $request->facebook,
            'whatsapp_url' => $request->whatsapp,
            'is_active' => true,
        ]);

        return redirect()->route('admin.shipment-companies')->with('success', 'Shipment company created successfully.');
    }

    public function showCompany(ShipmentCompany $shipmentCompany)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-companies.show')) {
            return view('dashboard.admin.no-permission');
        }

        $company_orders = Order::where('shipment_company_id', $shipmentCompany->id)
            ->with(['user', 'orderItems.package'])
            ->latest()
            ->limit(10)
            ->get();

        $company = $shipmentCompany;

        $shipmentCommission = ShipmentCommission::where('shipment_company_id', $shipmentCompany->id)->first();
        $publicCommission   = ShipmentCommission::whereNull('shipment_company_id')->first();
        $isUsingPublic = !$shipmentCommission && $publicCommission;

        return view(
            'dashboard.admin.shipment-company-details',
            compact('company', 'company_orders', 'shipmentCommission', 'publicCommission', 'isUsingPublic')
        );
    }

    public function toggleCompanyStatus(ShipmentCompany $shipmentCompany)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-companies.toggle-status')) {
            return view('dashboard.admin.no-permission');
        }

        $shipmentCompany->update(['is_active' => !$shipmentCompany->is_active]);
        $status = $shipmentCompany->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Shipment company {$status} successfully.");
    }

    public function updateItemStatus(Request $request, $orderId, $itemId)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-orders.update-item-status')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'status' => ['required', Rule::in(OrderStatus::cases())],
            'location' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $order = Order::findOrFail($orderId);
        $orderItem = $order->orderItems()->findOrFail($itemId);

        // Create a new tracking entry
        $orderItem->trackings()->create([
            'package_id' => $orderItem->package_id,
            'status' => $request->status,
            'location' => $request->location,
            'description' => $request->description,
            'occurred_at' => now(),
        ]);

        // Update the order item status
        $orderItem->update(['status' => $request->status]);

        // Check if all items have the same status and update order status accordingly
        $allItemsStatus = $order->orderItems->pluck('status')->unique();
        if ($allItemsStatus->count() === 1) {
            $order->update(['status' => $allItemsStatus->first()]);
        }

        return redirect()->back()->with('success', 'Order item status updated successfully.');
    }

    public function updateCompanyPricePerKm(Request $request, ShipmentCompany $shipmentCompany)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.shipment-companies.update-price-per-km')) {
            return view('dashboard.admin.no-permission');
        }
        $company = $shipmentCompany->findOrFail($shipmentCompany->id);

        // Get min/max settings
        $pricePerKmMin = Setting::where('key', 'price_per_km_min')->first();
        $pricePerKmMax = Setting::where('key', 'price_per_km_max')->first();

        $minValue = $pricePerKmMin ? (float) $pricePerKmMin->value : 0;
        $maxValue = $pricePerKmMax ? (float) $pricePerKmMax->value : 100;

        $request->validate([
            'price_per_km' => "required|numeric|min:{$minValue}|max:{$maxValue}",
        ], [
            'price_per_km.min' => "The price per km must be at least {$minValue}.",
            'price_per_km.max' => "The price per km must not be greater than {$maxValue}.",
        ]);

        $company->update(['price_per_km' => $request->price_per_km]);

        return redirect()->back()->with('success', 'Shipment company price per km updated successfully.');
    }
}
