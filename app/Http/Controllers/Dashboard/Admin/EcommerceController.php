<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\OrderPaymentRecord;
use App\Models\ShipmentCompany;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class EcommerceController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function orders(Request $request)
    {
        try {
            if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.ecommerce-orders')) {
                return view('dashboard.admin.no-permission');
            }

            $statuses = ['pending', 'accepted', 'pickup', 'on_way', 'delivered', 'cancelled', 'returned'];

            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'status' => ['nullable', 'string'],
                'shipment_company_id' => ['nullable', 'string'],
                'sort_by' => ['nullable', 'in:created_at,order_number,total_amount'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDir = $validated['sort_dir'] ?? 'desc';

            $ordersQuery = EcommerceOrder::query()
                ->with([
                    'user',
                    'items' => function ($q) {
                        $q->with(['product', 'shipmentCompany']);
                    },
                    'shipmentCompany',
                ]);

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
                    $ordersQuery->where(function ($query) {
                        $query->whereNull('shipment_company_id')
                            ->whereDoesntHave('items', function ($itemQuery) {
                                $itemQuery->whereNotNull('shipment_company_id');
                            });
                    });
                } else {
                    $shipmentCompanyId = (int) $validated['shipment_company_id'];
                    $ordersQuery->where(function ($query) use ($shipmentCompanyId) {
                        $query->where('shipment_company_id', $shipmentCompanyId)
                            ->orWhereHas('items', function ($itemQuery) use ($shipmentCompanyId) {
                                $itemQuery->where('shipment_company_id', $shipmentCompanyId);
                            });
                    });
                }
            }

            $orders = $ordersQuery
                ->orderBy($sortBy, $sortDir)
                ->paginate(20)
                ->appends($request->query());

            $shipmentCompanies = ShipmentCompany::orderBy('name')->get(['id', 'name']);

            return view('dashboard.admin.ecommerce-orders', compact('orders', 'shipmentCompanies', 'statuses', 'sortBy', 'sortDir'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    public function showOrder($id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.ecommerce-orders.show')) {
            return view('dashboard.admin.no-permission');
        }

        $order = EcommerceOrder::with([
            'user',
            'userAddress',
            'items.product.vendor',
            'shipmentCompany',
            'warehouse',
            'items' => function($query) {
                $query->with(['product.vendor', 'shipmentCompany', 'pickupBranch']);
            }
        ])->findOrFail($id);

        $shipmentCompanies = \App\Models\ShipmentCompany::where('is_active', true)->get();
        $warehouses = Warehouse::orderByDesc('is_main')->orderBy('name')->get();

        return view('dashboard.admin.ecommerce-order-details', compact('order', 'shipmentCompanies', 'warehouses'));
    }

    public function sendWhatsapp(EcommerceOrder $order)
    {
        return redirect()->away($order->whatsapp_url);
    }

    // In your controller (likely EcommerceOrderController or similar)
    public function addOrderToWallet(Request $request, $id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.ecommerce-orders.show')) {
            return view('dashboard.admin.no-permission');
        }

        $data = $request->validate([
            'operation' => ['required', 'in:add,subtract'],
            'amount' => ['required', 'numeric', 'gt:0', 'lte:' . $request->input('max_amount')],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order = EcommerceOrder::with(['user'])->findOrFail($id);

        DB::transaction(function () use ($order, $data) {
            $user = $order->user;

            // Create wallet if doesn't exist
            $wallet = $user->wallet ?: $user->wallet()->create([
                'balance' => 0,
                'currency' => 'EGP',
                'is_active' => true,
            ]);

            $current = (float) $wallet->balance;
            $amount = (float) $data['amount'];

            $new = $data['operation'] === 'add'
                ? $current + $amount
                : $current - $amount;

            $wallet->update([
                'balance' => $new,
                'is_active' => true,
            ]);

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'reference_id' => 'ORDER-' . $order->order_number . '-' . Str::random(8),
                'amount' => $amount,
                'description' => 'Order #' . $order->order_number . ' value ' .
                            ($data['operation'] === 'add' ? 'added to' : 'deducted from') .
                            ' wallet by admin' .
                            ($data['notes'] ? ' (' . $data['notes'] . ')' : ''),
                'type' => $data['operation'] === 'add' ? 'increase' : 'decrease',
            ]);

            // Create payment record for the order
            OrderPaymentRecord::create([
                'ecommerce_order_id' => $order->id,
                'order_item_id' => null, // This is for order-level payment
                'amount' => $amount,
                'payment_method' => 'wallet_adjustment',
                'reference_number' => 'WALLET-' . Str::random(10),
                'notes' => 'Wallet ' . ($data['operation'] === 'add' ? 'credit' : 'debit') .
                        ($data['notes'] ? ': ' . $data['notes'] : ''),
                'admin_id' => Auth::guard('employee')->id(),
            ]);
        });

        return back()->with('success', __('admin-dashboard.wallet_balance_updated_from_order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        if (
            Auth::guard('employee')->check()
            && ! Auth::guard('employee')->user()->can('admin.ecommerce-orders.update-status')
        ) {
            return view('dashboard.admin.no-permission');
        }

        $request->validate([
            'status' => 'required|string|in:pending,accepted,pickup,on_way,delivered,cancelled,returned'
        ]);

        $order = EcommerceOrder::with('items.product')->findOrFail($id);

        // 🚫 Prevent going back to pending
        if (in_array($order->status, ['accepted', 'cancelled']) && $request->status === 'pending') {
            return redirect()
                ->back()
                ->with('error', __('admin-dashboard.cannot_return_to_pending'));
        }

        // 🚫 Prevent any change if cancelled
        if ($order->status === 'cancelled' && $request->status !== 'cancelled') {
            return redirect()
                ->back()
                ->with('error', __('admin-dashboard.cancelled_order_locked'));
        }

        // 🚫 Prevent any change if delivered (optional but recommended)
        if ($order->status === 'delivered') {
            return redirect()
                ->back()
                ->with('error', __('admin-dashboard.delivered_order_locked'));
        }

        if ($order->hasUnpaidDepositItems()) {
            return redirect()
                ->back()
                ->with('error', __('admin-dashboard.deposit_must_be_paid_first'));
        }

        $order->update([
            'status' => $request->status
        ]);

        $user = $order->user;
        $statusTitles = [
            'pending'   => __('notifications.order_pending_title'),
            'accepted'  => __('notifications.order_accepted_title'),
            'pickup'    => __('notifications.order_pickup_title'),
            'on_way'    => __('notifications.order_on_way_title'),
            'delivered' => __('notifications.order_delivered_title'),
            'cancelled' => __('notifications.order_cancelled_title'),
            'returned'  => __('notifications.order_returned_title'),
        ];

        $statusBodies = [
            'pending'   => __('notifications.order_pending_body', ['order' => $order->order_number]),
            'accepted'  => __('notifications.order_accepted_body', ['order' => $order->order_number]),
            'pickup'    => __('notifications.order_pickup_body', ['order' => $order->order_number]),
            'on_way'    => __('notifications.order_on_way_body', ['order' => $order->order_number]),
            'delivered' => __('notifications.order_delivered_body', ['order' => $order->order_number]),
            'cancelled' => __('notifications.order_cancelled_body', ['order' => $order->order_number]),
            'returned'  => __('notifications.order_returned_body', ['order' => $order->order_number]),
        ];

        if ($user && $user->notifications_enabled) {

            $user->notify(new \App\Notifications\OrderStatusUpdated(
                title: $statusTitles[$request->status],
                body: $statusBodies[$request->status],
                data: [
                        'key' => 'order_status_updated',
                        'order_number' => $order->order_number,
                        'id' => $order->id,
                        'notification_type' => 'ecommerce',
                        'navigation_type' => 'order_tracking',
                ],
                type: 'ecommerce',
                navigationType: 'order_tracking'
            ));
        }

        return redirect()
            ->back()
            ->with('success', __('admin-dashboard.order_status_updated'));
    }


    public function assignShippingCompany(Request $request, $id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.ecommerce-orders.assign-shipping')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'shipment_company_id' => 'required|exists:shipment_companies,id',
            'warehouse_id' => 'nullable|exists:warehouses,id'
        ]);

        $order = EcommerceOrder::with('userAddress')->findOrFail($id);

        // Select warehouse: chosen or default main
        $warehouse = $request->filled('warehouse_id')
            ? Warehouse::findOrFail($request->warehouse_id)
            : (Warehouse::where('is_main', 1)->first() ?? Warehouse::orderBy('id')->first());

        $update = [
            'shipment_company_id' => $request->shipment_company_id,
            'warehouse_id' => $warehouse?->id,
        ];

        // Compute shipping if we have coordinates and company has price_per_km
        if ($warehouse && $order->userAddress) {
            $company = \App\Models\ShipmentCompany::find($request->shipment_company_id);
            if ($company && $company->price_per_km && $order->userAddress->latitude && $order->userAddress->longitude && $warehouse->latitude && $warehouse->longitude) {
                $maps = app(GoogleMapsService::class);
                $distanceKm = $maps->distanceInKm((float)$warehouse->latitude, (float)$warehouse->longitude, (float)$order->userAddress->latitude, (float)$order->userAddress->longitude);
                $shippingPrice = round($distanceKm * (float)$company->price_per_km, 2);
                $discount = (float)($order->discount ?? 0);
                $newTotal = round(((float)$order->subtotal - $discount) + $shippingPrice, 2);
                $update['shipping_price'] = $shippingPrice;
                $update['total_amount'] = $newTotal;
                $update['remaining_amount'] = $newTotal;
                $update['final_price'] = $newTotal;
            }
        }

        $order->update($update);

        return redirect()->back()->with('success', __('admin-dashboard.order_updated'));
    }

    public function vendorOrders($vendorId)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.ecommerce-orders')) {
            return view('dashboard.admin.no-permission');
        }
        $vendor_id = $vendorId->id;

        $orders = EcommerceOrder::whereHas('items.product', function ($q) use ($vendor_id) {
                $q->where('vendor_id', $vendor_id);
            })
            ->with([
                'user',
                'shipmentCompany',
                'items' => function ($q) use ($vendor_id) {
                    $q->whereHas('product', fn($p) => $p->where('vendor_id', $vendor_id))
                    ->with('product');
                }
            ])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.ecommerce-orders', compact('orders', 'vendorId'));
    }
}
