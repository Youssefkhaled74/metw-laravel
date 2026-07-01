<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\UserAddress;
use App\Models\Warehouse;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\PackageTracking;
use App\Models\Setting;
use App\Notifications\OrderStatusUpdated;
use App\Enum\OrderStatus;
use App\Notifications\OrderStatusUpdatedShipment;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\ShipmentCompanyCategoryPrice;
use App\Models\MainCategory;
use App\Models\ShipmentCompanySubCategorySizePrice;
use App\Models\Category;
use App\Models\Config;
use App\Models\ReturnRequest;
use Carbon\Carbon;

class ShipmentCompanyDashboardController extends Controller
{
    protected GoogleMapsService $maps;

    public function setGoogleMapsService(GoogleMapsService $maps): void
    {
        $this->maps = $maps;
    }
    public function __construct()
    {
        $this->middleware('shipment');
    }

    public function dashboard()
    {
        /** @var \App\Models\ShipmentCompany $company */
        $company = Auth::guard('shipment')->user();

        // Get price per km settings
        $pricePerKmMin = Setting::where('key', 'price_per_km_min')->first();
        $pricePerKmMax = Setting::where('key', 'price_per_km_max')->first();

        // Company-centric stats based on order items handled by this company
        $companyOrderQuery = Order::whereHas('orderItems', function ($q) use ($company) {
            $q->where('shipment_company_id', $company->id);
        });

        $ecommerceQuery = EcommerceOrder::where('shipment_company_id', $company->id);

        $stats = [
            // Shipping orders
            'total_orders' => (clone $companyOrderQuery)->count(),
            'pending_orders' => (clone $companyOrderQuery)->where('status', 'pending')->count(),
            'in_transit_orders' => (clone $companyOrderQuery)->where('status', 'in_transit')->count(),
            'delivered_orders' => (clone $companyOrderQuery)->where('status', 'delivered')->count(),

            // Ecommerce orders
            'ecommerce_total_orders' => (clone $ecommerceQuery)->count(),
            'ecommerce_pending_orders' => (clone $ecommerceQuery)->where('status', 'pending')->count(),
            'ecommerce_in_transit_orders' => (clone $ecommerceQuery)->where('status', 'on_way')->count(),
            'ecommerce_delivered_orders' => (clone $ecommerceQuery)->where('status', 'delivered')->count(),

            'total_packages' => Package::where('shipment_company_id', $company->id)->count(),

            // revenue shipping items
            'total_revenue' => (float) DB::table('order_items')
                ->where('shipment_company_id', $company->id)
                ->sum('est_price'),

            // ecommerce revenue
            'ecommerce_revenue' => (float) (clone $ecommerceQuery)->sum('final_price'),

            'current_price_per_km' => $company->price_per_km,
            'price_per_km_min' => $pricePerKmMin ? (float) $pricePerKmMin->value : 0,
            'price_per_km_max' => $pricePerKmMax ? (float) $pricePerKmMax->value : 100,
        ];

        // Today operations
        $todayStart = now()->startOfDay();
        $todayAssigned = OrderItem::where('shipment_company_id', $company->id)->whereDate('created_at', $todayStart)->count();
        $todayPickedUp = OrderItem::where('shipment_company_id', $company->id)->where('status', 'pickup')->whereDate('updated_at', $todayStart)->count();
        $todayDelivered = OrderItem::where('shipment_company_id', $company->id)->where('status', 'delivered')->whereDate('updated_at', $todayStart)->count();
        $todayCancelled = OrderItem::where('shipment_company_id', $company->id)->where('status', 'cancelled')->whereDate('updated_at', $todayStart)->count();

        // Coverage
        $activeLocations = $company->shipmentLocations()->where('is_active', true)->count();
        $inactiveLocations = $company->shipmentLocations()->where('is_active', false)->count();

        // Flow counts
        $flowCounts = [
            'pending' => OrderItem::where('shipment_company_id', $company->id)->where('status', 'pending')->count(),
            'accepted' => OrderItem::where('shipment_company_id', $company->id)->where('status', 'accepted')->count(),
            'pickup' => OrderItem::where('shipment_company_id', $company->id)->where('status', 'pickup')->count(),
            'on_way' => OrderItem::where('shipment_company_id', $company->id)->where('status', 'on_way')->count(),
            'delivered' => OrderItem::where('shipment_company_id', $company->id)->where('status', 'delivered')->count(),
            'cancelled' => OrderItem::where('shipment_company_id', $company->id)->where('status', 'cancelled')->count(),
            'returned' => OrderItem::where('shipment_company_id', $company->id)->where('status', 'returned')->count(),
        ];

        $totalFlowItems = collect($flowCounts)->sum();

        $shippingOrdersBase = Order::where(function ($query) use ($company) {
            $query->whereHas('orderItems.route', function ($q) use ($company) {
                $q->where(function ($q2) use ($company) {
                    $q2->where('pickup_company_id', $company->id)
                        ->orWhere('dropoff_company_id', $company->id);
                });
            })
            ->orWhereHas('orderItems', function ($q) use ($company) {
                $q->where('shipment_company_id', $company->id);
            });
        });

        $ecommerceOrdersBase = EcommerceOrder::whereHas('items', function ($q) use ($company) {
            $q->where('shipment_company_id', $company->id);
        });

        $metwExpressNewOrdersQuery = (clone $shippingOrdersBase)
            ->where('status', OrderStatus::PENDING->value);

        $metwExpressInProgressQuery = (clone $shippingOrdersBase)
            ->whereIn('status', [
                OrderStatus::ACCEPTED->value,
                OrderStatus::PICKUP->value,
                OrderStatus::ON_WAY->value,
            ]);

        $metwzonNewOrdersQuery = (clone $ecommerceOrdersBase)
            ->where('status', OrderStatus::PENDING->value);

        $metwzonCancelledDeliveryQuery = (clone $ecommerceOrdersBase)
            ->whereHas('items', function ($q) use ($company) {
                $q->where('shipment_company_id', $company->id)
                    ->where('status', OrderStatus::CANCELLED->value);
            });

        $metwzonInProgressQuery = (clone $ecommerceOrdersBase)
            ->whereIn('status', [
                OrderStatus::ACCEPTED->value,
                OrderStatus::PICKUP->value,
                OrderStatus::ON_WAY->value,
            ]);

        $metwzonReturnRequestsQuery = ReturnRequest::query()
            ->whereHas('items.orderItem', function ($q) use ($company) {
                $q->where('shipment_company_id', $company->id);
            })
            ->whereIn('status', [
                'requested',
                'approved',
                'pickup',
                'processing',
            ]);

        $mapOrderPreview = function ($orders, string $routeName, bool $isEcommerce = false) {
            return $orders->map(function ($order) use ($routeName, $isEcommerce) {
                return [
                    'title' => ($isEcommerce ? 'ECO-' : '') . ($order->order_number ?? ('#' . $order->id)),
                    'subtitle' => $order->user->username ?? __('shipment-dashboard.na'),
                    'meta' => optional($order->created_at)->diffForHumans(),
                    'url' => route($routeName, $order->id),
                ];
            })->values();
        };

        $urgent_tasks = [
            'metw_express' => [
                'label' => 'Metw Express',
                'cards' => [
                    [
                        'title' => __('shipment-dashboard.urgent_card_new_shipping_orders'),
                        'count' => (clone $metwExpressNewOrdersQuery)->count(),
                        'icon' => 'fa-truck-fast',
                        'accent' => 'danger',
                        'url' => route('shipment.orders', ['status' => 'pending']),
                        'items' => $mapOrderPreview(
                            (clone $metwExpressNewOrdersQuery)->with('user:id,username')->latest()->limit(5)->get(['id', 'order_number', 'user_id', 'created_at']),
                            'shipment.orders.show'
                        ),
                    ],
                    [
                        'title' => __('shipment-dashboard.urgent_card_pickup_courier_orders'),
                        'count' => 0,
                        'icon' => 'fa-user-check',
                        'accent' => 'secondary',
                        'url' => null,
                        'items' => collect(),
                    ],
                    [
                        'title' => __('shipment-dashboard.urgent_card_delivery_courier_orders'),
                        'count' => 0,
                        'icon' => 'fa-person-walking-arrow-right',
                        'accent' => 'secondary',
                        'url' => null,
                        'items' => collect(),
                    ],
                ],
            ],
            'metwzon' => [
                'label' => 'Metwzon',
                'cards' => [
                    [
                        'title' => __('shipment-dashboard.urgent_card_new_shipping_orders'),
                        'count' => (clone $metwzonNewOrdersQuery)->count(),
                        'icon' => 'fa-bag-shopping',
                        'accent' => 'warning',
                        'url' => route('shipment.ecommerce.orders', ['status' => 'pending']),
                        'items' => $mapOrderPreview(
                            (clone $metwzonNewOrdersQuery)->with('user:id,username')->latest()->limit(5)->get(['id', 'order_number', 'user_id', 'created_at']),
                            'shipment.ecommerce.orders.show',
                            true
                        ),
                    ],
                    [
                        'title' => __('shipment-dashboard.urgent_card_cancel_delivery_products'),
                        'count' => (clone $metwzonCancelledDeliveryQuery)->count(),
                        'icon' => 'fa-ban',
                        'accent' => 'danger',
                        'url' => route('shipment.ecommerce.orders', ['status' => 'cancelled']),
                        'items' => $mapOrderPreview(
                            (clone $metwzonCancelledDeliveryQuery)->with('user:id,username')->latest()->limit(5)->get(['id', 'order_number', 'user_id', 'created_at']),
                            'shipment.ecommerce.orders.show',
                            true
                        ),
                    ],
                    [
                        'title' => __('shipment-dashboard.urgent_card_return_products'),
                        'count' => (clone $metwzonReturnRequestsQuery)->count(),
                        'icon' => 'fa-rotate-left',
                        'accent' => 'info',
                        'url' => route('shipment.return-requests'),
                        'items' => (clone $metwzonReturnRequestsQuery)
                            ->with(['user:id,username'])
                            ->latest()
                            ->limit(5)
                            ->get(['id', 'return_number', 'user_id', 'created_at'])
                            ->map(function ($returnRequest) {
                                return [
                                    'title' => $returnRequest->return_number ?? ('RET-' . $returnRequest->id),
                                    'subtitle' => $returnRequest->user->username ?? __('shipment-dashboard.na'),
                                    'meta' => optional($returnRequest->created_at)->diffForHumans(),
                                    'url' => route('shipment.return-requests.show', $returnRequest->id),
                                ];
                            })
                            ->values(),
                    ],
                    [
                        'title' => __('shipment-dashboard.urgent_card_pickup_courier_orders'),
                        'count' => 0,
                        'icon' => 'fa-user-check',
                        'accent' => 'secondary',
                        'url' => null,
                        'items' => collect(),
                    ],
                    [
                        'title' => __('shipment-dashboard.urgent_card_delivery_courier_orders'),
                        'count' => 0,
                        'icon' => 'fa-person-walking-arrow-right',
                        'accent' => 'secondary',
                        'url' => null,
                        'items' => collect(),
                    ],
                ],
            ],
            'follow_up' => [
                'cards' => [
                    [
                        'title' => __('shipment-dashboard.urgent_card_follow_up_metwexpress'),
                        'count' => (clone $metwExpressInProgressQuery)->count(),
                        'icon' => 'fa-truck-ramp-box',
                        'accent' => 'primary',
                        'url' => route('shipment.orders', ['status' => 'on_way']),
                        'items' => $mapOrderPreview(
                            (clone $metwExpressInProgressQuery)->with('user:id,username')->latest()->limit(5)->get(['id', 'order_number', 'user_id', 'created_at']),
                            'shipment.orders.show'
                        ),
                    ],
                    [
                        'title' => __('shipment-dashboard.urgent_card_follow_up_metwzon'),
                        'count' => (clone $metwzonInProgressQuery)->count(),
                        'icon' => 'fa-box-open',
                        'accent' => 'primary',
                        'url' => route('shipment.ecommerce.orders', ['status' => 'on_way']),
                        'items' => $mapOrderPreview(
                            (clone $metwzonInProgressQuery)->with('user:id,username')->latest()->limit(5)->get(['id', 'order_number', 'user_id', 'created_at']),
                            'shipment.ecommerce.orders.show',
                            true
                        ),
                    ],
                    [
                        'title' => __('shipment-dashboard.urgent_card_new_notifications'),
                        'count' => $company->unreadNotifications()->count(),
                        'icon' => 'fa-bell',
                        'accent' => 'success',
                        'url' => route('shipment.notifications.index'),
                        'items' => $company->unreadNotifications()
                            ->latest()
                            ->limit(5)
                            ->get()
                            ->map(function ($notification) {
                                return [
                                    'title' => data_get($notification->data, 'title', data_get($notification->data, 'key', __('shipment-dashboard.notification'))),
                                    'subtitle' => data_get($notification->data, 'body', ''),
                                    'meta' => optional($notification->created_at)->diffForHumans(),
                                    'url' => route('shipment.notifications.index'),
                                ];
                            })
                            ->values(),
                    ],
                ],
            ],
        ];

        // Monthly revenue chart data
        $monthly_revenue = $this->getMonthlyRevenue($company->id);
        $ecommerceQuery = EcommerceOrder::where('shipment_company_id', $company->id);

        return view('dashboard.shipment.dashboard', compact(
            'stats',
            'urgent_tasks',
            'monthly_revenue',
            'todayAssigned',
            'todayPickedUp',
            'todayDelivered',
            'todayCancelled',
            'activeLocations',
            'inactiveLocations',
            'flowCounts',
            'totalFlowItems'
        ));
    }

    // Orders Management
    public function orders(Request $request)
    {
        /** @var \App\Models\ShipmentCompany $company */
        $company = Auth::guard('shipment')->user();

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:all,pending,accepted,pickup,on_way,delivered,cancelled,returned'],
            'sort_by' => ['nullable', 'in:order_number,customer,total_price,packages,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $ordersQuery = Order::where(function ($query) use ($company) {
            $query->whereHas('orderItems.route', function ($q) use ($company) {
                $q->where(function ($q2) use ($company) {
                    $q2->where('pickup_company_id', $company->id)
                        ->orWhere('dropoff_company_id', $company->id);
                });
            })
                ->orWhereHas('orderItems', function ($q) use ($company) {
                    $q->where('shipment_company_id', $company->id);
                });
        })
            ->with([
                'user',
                'orderItems' => function ($q) use ($company) {
                    $q->where(function ($itemsQuery) use ($company) {
                        $itemsQuery->whereHas('route', function ($q2) use ($company) {
                            $q2->where(function ($q3) use ($company) {
                                $q3->where('pickup_company_id', $company->id)
                                    ->orWhere('dropoff_company_id', $company->id);
                            });
                        })->orWhere('shipment_company_id', $company->id);
                    })
                        ->with([
                            'route',
                            'package.packageDetails',
                            'package.pickupAddress',
                            'package.dropoffAddress',
                        ]);
                },
            ])
            ->withCount([
                'orderItems as company_packages_count' => function ($q) use ($company) {
                    $q->where(function ($itemsQuery) use ($company) {
                        $itemsQuery->whereHas('route', function ($q2) use ($company) {
                            $q2->where(function ($q3) use ($company) {
                                $q3->where('pickup_company_id', $company->id)
                                    ->orWhere('dropoff_company_id', $company->id);
                            });
                        })->orWhere('shipment_company_id', $company->id);
                    });
                }
            ]);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $ordersQuery->where('status', $validated['status']);
        }

        if ($sortBy === 'customer') {
            $ordersQuery->leftJoin('users as u', 'orders.user_id', '=', 'u.id')
                ->select('orders.*')
                ->orderBy('u.username', $sortDir);
        } elseif ($sortBy === 'packages') {
            $ordersQuery->orderBy('company_packages_count', $sortDir);
        } elseif ($sortBy === 'total_price') {
            $ordersQuery->orderBy('orders.final_price', $sortDir);
        } else {
            $ordersQuery->orderBy("orders.{$sortBy}", $sortDir);
        }

        $orders = $ordersQuery
            ->paginate(20)
            ->appends($request->query());

        return view('dashboard.shipment.orders', compact('orders'));
    }


    public function orderDetails($id)
    {
        /** @var \App\Models\ShipmentCompany $company */
        $company = Auth::guard('shipment')->user();

        $order = Order::where(function ($query) use ($id, $company) {
            $query->where('id', $id)
                ->whereHas('orderItems.route', function ($q) use ($company) {
                    $q->where(function ($q2) use ($company) {
                        $q2->where('pickup_company_id', $company->id)
                        ->orWhere('dropoff_company_id', $company->id);
                    });
                })
                ->orWhere(function ($query) use ($id, $company) {
                    $query->where('id', $id)
                        ->whereHas('orderItems', function ($q) use ($company) {
                            $q->where('shipment_company_id', $company->id);
                        });
                });
        })
        ->with([
            'user',
            'orderItems' => function ($q) use ($company) {
                $q->whereHas('route', function ($q2) use ($company) {
                    $q2->where(function ($q3) use ($company) {
                        $q3->where('pickup_company_id', $company->id)
                        ->orWhere('dropoff_company_id', $company->id);
                    });
                })->with([
                    'shipmentCompany',
                    'route',
                    'trackings',
                    'package.type',
                    'package.size',
                    'package.deliveryType',
                    'package.consignmentType',
                    'package.packageDetails',
                    'package.pickupAddress',
                    'package.dropoffAddress',
                    'package.images',
                    'parent.pickupLeg.shipmentCompany',
                    'parent.dropoffLeg.shipmentCompany',
                ]);
            },
        ])
        ->firstOrFail();

        $company_items_total = (float) $order->orderItems
            ->sum(fn ($item) => $item->route?->cost ?? $item->est_price ?? 0);

        return view('dashboard.shipment.order-details', compact('order', 'company_items_total'));
    }

    public function updateEstimate(Request $request, OrderItem $item)
    {
        $company = Auth::guard('shipment')->user();

        // تأكد أن الشركة تملك هذا العنصر
        if ($item->shipment_company_id != $company->id) {
            abort(403);
        }

        $request->validate([
            'est_days' => 'required|integer|min:1|max:30',
        ]);

        // تحديث أيام التوصيل في الباكدج
        $item->package->update([
            'est_days' => $request->est_days
        ]);

        // حساب تاريخ التسليم المتوقع
        $item->update([
            'est_date' => now()->addDays($request->est_days)
        ]);

        return back()->with('success', 'Estimated delivery updated successfully');
    }



    public function updateOrderStatus(Request $request, $id)
    {
        /** @var \App\Models\ShipmentCompany $company */
        $company = Auth::guard('shipment')->user();

        $request->validate([
            'status' => 'required|string|in:pending,accepted,pickup,on_way,delivered,cancelled,returned',
            'items' => 'array',
            // Restrict item IDs to those belonging to this company
            'items.*.id' => ['exists:order_items,id'],
            'items.*.est_date' => 'nullable|date',
            'items.*.est_price' => 'nullable|numeric|min:0',
        ]);

        // Allow company to update the order only if it has at least one of its items
        $order = Order::where('id', $id)
            ->where(function ($query) use ($company) {
                $query
                    ->whereHas('orderItems.route', function ($q) use ($company) {
                        $q->where(function ($q2) use ($company) {
                            $q2->where('pickup_company_id', $company->id)
                            ->orWhere('dropoff_company_id', $company->id);
                        });
                    })
                    ->orWhereHas('orderItems', function ($q) use ($company) {
                        $q->where('shipment_company_id', $company->id);
                    });
            })
            ->firstOrFail();


        /** @var Order $order */

        $nextStatus = (string) $request->status;

        // Only the pickup-leg company can accept a pending order
        if ($nextStatus === OrderStatus::ACCEPTED->value) {
            if ((string) $order->status->value !== OrderStatus::PENDING->value) {
                return back()->withErrors(['status' => 'Order can only be accepted from pending status.']);
            }
            $isPickupLegCompany = OrderItem::where('order_id', $order->id)
                ->where('shipment_company_id', $company->id)
                ->whereHas('route', function ($q) {
                    $q->where('leg_type', 'pickup');
                })
                ->exists();

            // if (!$isPickupLegCompany) {
            //     return back()->withErrors(['status' => 'Only the pickup leg company can accept this order.']);
            // }
            // Immediately mark order as accepted to reveal item status updates in UI
            Order::where('id', $order->id)->update(['status' => OrderStatus::ACCEPTED->value]);
            // Also set all order items to accepted on acceptance
            OrderItem::where('order_id', $order->id)->update(['status' => OrderStatus::ACCEPTED->value]);
        }

        // Cancel is only allowed from pending → short-circuit after update
        if ($nextStatus === OrderStatus::CANCELLED->value) {
            if ((string) $order->status->value !== OrderStatus::PENDING->value) {
                return back()->withErrors(['status' => 'You can only cancel while the order is pending.']);
            }
            Order::where('id', $order->id)->update(['status' => OrderStatus::CANCELLED->value]);
            // Also set all order items to cancelled when cancelling order
            OrderItem::where('order_id', $order->id)->update(['status' => OrderStatus::CANCELLED->value]);
            return redirect()->back()->with('success', 'Order cancelled successfully.');
        }

        // If accepted → update order items est_date + est_price
        if ($nextStatus === OrderStatus::ACCEPTED->value && $request->has('items')) {
            foreach ($request->items as $itemData) {
                $orderItem = OrderItem::where('shipment_company_id', $company->id)
                    ->where('order_id', $order->id)
                    ->find($itemData['id']);
                if ($orderItem) {
                    $orderItem->update([
                        'est_date' => $itemData['est_date'] ?? null,
                        // Allow this company to update only its items' est_price
                        'est_price' => $itemData['est_price'] ?? $orderItem->est_price,
                    ]);
                }
            }
        }

        // Enforce forward-only transitions for this company's items
        $sequence = [
            OrderStatus::PENDING->value,
            OrderStatus::ACCEPTED->value,
            OrderStatus::PICKUP->value,
            OrderStatus::ON_WAY->value,
            OrderStatus::DELIVERED->value,
            OrderStatus::RETURNED->value,
        ];

        $companyItems = OrderItem::where('order_id', $order->id)
            ->where('shipment_company_id', $company->id)
            ->get();
        foreach ($companyItems as $item) {
            $current = (string) ($item->status ?? OrderStatus::PENDING->value);
            $curIdx = array_search($current, $sequence, true);
            $nextIdx = array_search($nextStatus, $sequence, true);
            if ($curIdx !== false && $nextIdx !== false && $nextIdx < $curIdx) {
                return back()->withErrors(['status' => 'Cannot move status backward for item #' . $item->id]);
            }
            $item->update(['status' => $nextStatus]);
        }

        // Sync order status if all items share same status
        $allStatuses = $order->orderItems()->pluck('status')->filter()->unique();
        if ($allStatuses->count() === 1) {
            Order::where('id', $order->id)->update(['status' => $allStatuses->first()]);
        }

        if ($order->user->enable_shipment_notifications) {

            // ✅ Enum safe status
            $statusValue = $nextStatus instanceof \App\Enum\OrderStatus
                ? $nextStatus->value
                : (string) $nextStatus;

            // ✅ map status → notification key
            $statusToKey = [
                'pending'   => 'order_pending',
                'accepted'  => 'order_accepted',
                'pickup'    => 'order_pickup',
                'on_way'    => 'order_on_way',
                'delivered' => 'order_delivered',
                'cancelled' => 'order_cancelled',
                'returned'  => 'order_returned',
            ];

            $key = $statusToKey[$statusValue] ?? 'order_updated';

            $user = $order->user;

            // ✅ force user language
            app()->setLocale($user->default_lang ?? 'en');

            // ✅ translated text (FCM only)
            $title = __("notifications.$key.title");
            $body  = __("notifications.$key.body", [
                'order_number' => $order->order_number ?? $order->id
            ]);

            // ✅ DB stores only key
            $data = [
                'key' => $key,
                'order_id' => $order->id,
                'order_number' => $order->order_number ?? null,
                'status' => $statusValue,
                'notification_type' => 'shipment',
                'navigation_type' => 'order_tracking_return',
            ];

            $user->notify(
                new OrderStatusUpdatedShipment(
                    title: $title,
                    body: $body,
                    data: $data,
                    type: 'shipment',
                    navigationType: 'order_tracking'
                )
            );
        }

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    // Ecommerce Orders for Shipment Company
    public function ecommerceOrders(Request $request)
    {
        /** @var \App\Models\ShipmentCompany $company */
        $company = Auth::guard('shipment')->user();

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:all,pending,accepted,pickup,on_way,delivered,cancelled,returned'],
            'sort_by' => ['nullable', 'in:order,user,items,total,shipping,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $ordersQuery = EcommerceOrder::whereHas('items', function ($query) use ($company) {
                $query->where('shipment_company_id', $company->id);
            })
            ->with([
                'user',
                'userAddress',
                'items',
                'items.product' => function ($q) {
                    $q->select('id', 'requires_delivery_otp');
                }
            ])
            ->withCount([
                'items as company_items_count' => function ($query) use ($company) {
                    $query->where('shipment_company_id', $company->id);
                }
            ]);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $ordersQuery->where('status', $validated['status']);
        }

        if ($sortBy === 'user') {
            $ordersQuery->leftJoin('users as u', 'ecommerce_orders.user_id', '=', 'u.id')
                ->select('ecommerce_orders.*')
                ->orderBy('u.username', $sortDir);
        } elseif ($sortBy === 'items') {
            $ordersQuery->orderBy('company_items_count', $sortDir);
        } elseif ($sortBy === 'total') {
            $ordersQuery->orderBy('ecommerce_orders.final_price', $sortDir);
        } elseif ($sortBy === 'shipping') {
            $ordersQuery->orderBy('ecommerce_orders.shipping_price', $sortDir);
        } elseif ($sortBy === 'order') {
            $ordersQuery->orderBy('ecommerce_orders.order_number', $sortDir);
        } else {
            $ordersQuery->orderBy('ecommerce_orders.created_at', $sortDir);
        }

        $orders = $ordersQuery
            ->paginate(20)
            ->appends($request->query());

        foreach ($orders as $order) {

            $totals = $order->getShipmentCompanyTotals($company->id);

            // totals
            $order->company_subtotal = $totals['subtotal'];
            $order->company_shipping = $totals['shipping'];
            $order->company_discount = $totals['discount'];
            $order->company_total = $totals['total'];
            $order->company_paid = $totals['paid'];
            $order->company_remaining = $totals['remaining'];
            $order->company_items_count = $totals['items_count'];
            $order->company_net_total = $totals['net_total'];
            $order->company_total_returned = $totals['total_returned'];
            $order->company_distance = $totals['distance'];

            // 🔥 THE IMPORTANT FLAG
            $order->requires_delivery_otp = $order->items
                ->where('shipment_company_id', $company->id)
                ->contains(function ($item) {
                    return optional($item->product)->requires_delivery_otp === true;
                });
        }

        return view('dashboard.shipment.ecommerce.orders', compact('orders'));
    }



    public function sendDeliveryOtp($id)
    {
        $company = Auth::guard('shipment')->user();

        $order = EcommerceOrder::where('id', $id)
            ->whereHas('items', function ($q) use ($company) {
                $q->where('shipment_company_id', $company->id);
            })->firstOrFail();


        // لو الطلب مش محتاج OTP
        if (!$order->items()->whereHas('product', fn($q) => $q->where('requires_delivery_otp', true))->exists()) {
            return response()->json(['message' => 'OTP not required'], 400);
        }

        // $otp = rand(100000, 999999);
        $otp = 1111;

        $order->update([
            'delivery_otp' => $otp,
            'otp_verified' => false,
        ]);


        return response()->json([
            'message' => 'OTP sent successfully'
        ]);
    }

    public function directDelivery($orderId)
    {
        $order = EcommerceOrder::with('items')->findOrFail($orderId);

        foreach ($order->items as $item) {
            if ($item->shipment_company_id !== auth('shipment')->id()) {
                return response()->json([
                    'message' => __('shipment-dashboard.unauthorized_action')
                ], 403);
            }

            $item->status = 'delivered';
            $item->delivered_at = now();
            $item->save();
        }

        // تحديث حالة الأوردر
        $order->status = 'delivered';
        $order->delivered_at = now();
        $order->save();

        return response()->json([
            'message' => __('shipment-dashboard.item_status_updated_successfully')
        ]);
    }


    public function confirmDeliveryOtp(Request $request, $id)
    {
        $request->validate([
            'otp' => 'required|string'
        ]);

        $company = Auth::guard('shipment')->user();

        $order = EcommerceOrder::where('id', $id)
            ->whereHas('items', fn($q) => $q->where('shipment_company_id', $company->id))
            ->firstOrFail();

        if ($order->delivery_otp !== $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP. This is not the correct client.'
            ], 403);
        }

        DB::transaction(function () use ($order, $company) {
            $order->update([
                'status' => 'delivered',
                'otp_verified' => true,
                'delivered_at' => now(),
            ]);

            $order->items()
                ->where('shipment_company_id', $company->id)
                ->update([
                    'status' => \App\Enum\OrderStatus::DELIVERED,
                    'delivered_at' => now(),
                ]);
        });

        return response()->json([
            'message' => 'Order delivered successfully'
        ]);
    }




    public function ecommerceOrderDetails($id)
    {
        /** @var \App\Models\ShipmentCompany $company */
        $company = Auth::guard('shipment')->user();

        // Ensure the order has items assigned to this shipment company
        $order = EcommerceOrder::whereHas('items', function($query) use ($company) {
                $query->where('shipment_company_id', $company->id);
            })
            ->with(['user', 'userAddress', 'userAddress.city', 'warehouse'])
            ->findOrFail($id);

        // Get current locale for translations
        $currentLocale = app()->getLocale();

        // Get only items assigned to this shipment company with product translations
        $companyItems = $order->items()
            ->where('shipment_company_id', $company->id)
            ->with([
                'product.media',
                'product.vendor',
                'product.translations' => function($query) use ($currentLocale) {
                    $query->where('locale', $currentLocale);
                },
                'variant',
                'pickupBranch',
                'shipmentCompany'
            ])
            ->get();

        // Use helper method for company-specific totals
        $companyTotals = $order->getShipmentCompanyTotals($company->id);

        // Calculate per-item details including returns
        foreach ($companyItems as $item) {
            // Calculate net values after returns
            $item->net_quantity = $item->quantity - ($item->returned_quantity ?? 0);
            $item->net_amount = $item->final_price - ($item->returned_amount ?? 0);
            $item->net_paid = $item->paid_amount - ($item->returned_amount ?? 0);
            $item->net_remaining = $item->remaining_amount;

            // Pickup/Dropoff info
            $item->pickup_location = $item->pickupBranch ?: optional($item->product)->branch;
            $item->dropoff_location = $order->userAddress;

            // FIX: Use the correct shipping price for the shipment company
            // shipment_price_company might be empty, so fall back to shipment_price
            $item_shipping_price = $item->shipment_price_company > 0
                ? $item->shipment_price_company
                : $item->shipment_price;

            // Calculate item breakdown
            $item->breakdown = [
                'product_price' => $item->total_price,
                'shipping_price' => $item_shipping_price,
                'product_discount' => $item->product_discount ?? 0,
                'discount' => $item->discount_price ?? 0,
                'final_price' => $item->final_price,
                'paid' => $item->paid_amount,
                'remaining' => $item->remaining_amount,
                'distance_km' => $item->distance,
                'returned_quantity' => $item->returned_quantity ?? 0,
                'returned_amount' => $item->returned_amount ?? 0,
            ];

            // Add display shipping price to item for easy access in blade
            $item->display_shipping_price = $item_shipping_price;
        }

        // Calculate additional summary data
        $summaryData = [
            'total_items' => $companyItems->count(),
            'total_quantity' => $companyItems->sum('quantity'),
            'net_quantity' => $companyItems->sum('net_quantity'),
            'total_weight' => $companyItems->sum(function($item) {
                return ($item->product->weight ?? 0) * $item->quantity;
            }),
            'total_returns' => $companyItems->sum('returned_quantity'),
            'total_return_amount' => $companyItems->sum('returned_amount'),
            'average_distance' => $companyItems->avg('distance') ?? 0,
        ];

        // Also fix the company totals if needed
        if ($companyTotals['shipping'] == 0) {
            // Recalculate shipping from items
            $companyTotals['shipping'] = $companyItems->sum(function($item) {
                return $item->shipment_price_company > 0
                    ? $item->shipment_price_company
                    : $item->shipment_price;
            });
        }

        $warehouses = Warehouse::orderByDesc('is_main')->orderBy('name')->get();

        return view('dashboard.shipment.ecommerce.order-details', compact(
            'order',
            'companyItems',
            'companyTotals',
            'warehouses',
            'summaryData'
        ));
    }

    public function acceptEcommerceOrder(Request $request, $id)
    {
        $request->validate([
            'estimated_days' => 'required|integer|min:1|max:30',
        ]);

        $company = Auth::guard('shipment')->user();

        $order = EcommerceOrder::whereHas('items', function ($q) use ($company) {
                $q->where('shipment_company_id', $company->id);
            })
            ->findOrFail($id);

        $days = (int) $request->estimated_days;

        $order->update([
            'status' => 'pickup',
            'shipment_company_id' => $company->id,
            'estimated_delivery_from' => Carbon::today(),
            'estimated_delivery_to' => Carbon::today()->addDays($days),
        ]);

        // تحديث حالة items الخاصة بالشركة
        $order->items()
            ->where('shipment_company_id', $company->id)
            ->update([
                'status' => 'pickup',
                'is_shipment_accepted' => 1,
            ]);

        return redirect()
            ->back()
            ->with('success', __('shipment-dashboard.order_accepted_successfully'));
    }


    public function cancelEcommerceOrder(Request $request, $id)
    {
        $company = Auth::guard('shipment')->user();

        // Ensure the order has items assigned to this shipment company
        $order = EcommerceOrder::whereHas('items', function($query) use ($company) {
                $query->where('shipment_company_id', $company->id);
            })
            ->findOrFail($id);
        /** @var EcommerceOrder $order */

        // Allow cancel only while pending; block otherwise
        // if ($order->status !== 'pending') {
        //     return redirect()->route('shipment.ecommerce.orders.show', $order->id)
        //         ->withErrors(['status' => 'You can only cancel while the order is pending.']);
        // }

        // Unassign items from this shipment company and reset shipment related fields
        $order->items()
            ->where('shipment_company_id', $company->id)
            ->update([
            'shipment_company_id' => null,
                'shipment_price_company' => 0,
                // 'distance' => null,
                'status' => 'pending',
        ]);

        return redirect()->route('shipment.ecommerce.orders')->with('success', __('shipment-dashboard.order_cancelled_successfully'));
    }

    public function updateEcommerceOrderStatus(Request $request, $id)
    {
        $company = Auth::guard('shipment')->user();

        $validated = $request->validate([
            'status'  => 'required|string|in:pending,accepted,pickup,on_way,delivered,cancelled',
            'item_id' => 'required|integer',
        ]);

        $statusToKey = [
            'accepted'  => 'order_accepted',
            'pickup'    => 'order_pickup',
            'on_way'    => 'order_on_way',
            'delivered' => 'order_delivered',
            'cancelled' => 'order_cancelled',
        ];

        // Get order belonging to same company
        $order = \App\Models\EcommerceOrder::whereHas('items', function($q) use ($company) {
            $q->where('shipment_company_id', $company->id);
        })->findOrFail($id);

        $item = $order->items()
            ->where('shipment_company_id', $company->id)
            ->where('id', $validated['item_id'])
            ->firstOrFail();

        // Validate order status direction
        $sequence = ['pending','accepted','pickup','on_way','delivered'];
        $cur = (string)($item->status->value ?? 'pending');
        $next = $validated['status'];

        if (array_search($next, $sequence) < array_search($cur, $sequence)) {
            return back()->withErrors(['status' => __('shipment-dashboard.invalid_status_transition')]);
        }

        if ($next === 'cancelled' && !in_array($cur, ['pending','accepted'])) {
            return back()->withErrors(['status' => __('shipment-dashboard.cannot_cancel_after_shipment_started')]);
        }

        // Update item
        $item->update(['status' => $next]);

        // Sync parent order status
        $allStatuses = $order->items()
            ->where('shipment_company_id', $company->id)
            ->pluck('status')
            ->filter()
            ->unique();

        if ($allStatuses->count() === 1) {
            $order->update(['status' => $allStatuses->first()]);
        }

        // Prepare notification
        $user = $order->user;
        $key  = $statusToKey[$next];

        if ($user->notifications_enabled) {

            // Force translation based on user saved language
            app()->setLocale($user->default_lang ?? 'en');

            // FCM readable text
            $title = __("notifications.$key.title");
            $body  = __("notifications.$key.body", [
                'order_number' => $order->order_number
            ]);

            // Send: database stores keys ONLY, FCM gets real translated text
            $user->notify(new OrderStatusUpdated(
                title: $title,
                body: $body,
                data: [
                    'key' => $key,
                    'order_number' => $order->order_number,
                    'id' => $order->id,
                    'notification_type' => 'ecommerce',
                    'navigation_type' => 'order_tracking'
                ],
                type: 'ecommerce',
                navigationType: 'order_tracking'
            ));
        }

        return back()->with('success', __('shipment-dashboard.item_status_updated_successfully'));
    }



    public function updateEcommerceOrderItemStatus(Request $request, $orderId)
    {
        $request->validate([
            'item_id' => 'required|exists:ecommerce_order_items,id',
            'status'  => 'required|string|in:pending,accepted,pickup,on_way,delivered,cancelled',
        ]);

        $order = EcommerceOrder::findOrFail($orderId);
        $item  = $order->items()->where('id', $request->item_id)->firstOrFail();

        // أمان: تأكد إن المستخدم ليه صلاحية تعديل العنصر
        if ($item->shipment_company_id !== auth('shipment')->user()->id) {
            return back()->withErrors(['status' => __('shipment-dashboard.unauthorized_action')]);
        }

        // تحديث حالة العنصر
        $item->status = $request->status;
        if ($request->status === 'delivered') {
            $item->delivered_at = now();
        }
        $item->save();

        // 🔹 التحقق من أن كل العناصر في الأوردر وصلوا لنفس الحالة
        $allItemStatuses = $order->items()->pluck('status')->unique();

        if ($allItemStatuses->count() === 1) {
            // لو كل العناصر بنفس الحالة — عدل حالة الأوردر
            $newStatus = $allItemStatuses->first();
            $order->status = $newStatus;
            if ($order->status === OrderStatus::DELIVERED) {
                $order->delivered_at = now();
            }
            $order->save();

        if ($order->user && $order->user->notifications_enabled) {
            $userLang = $order->user->default_lang ?? 'en';

            // ✅ ترجمات الحالة
            $statusTranslations = [
                'pending'   => ['en' => 'Pending',     'ar' => 'قيد الانتظار'],
                'accepted'  => ['en' => 'Accepted',    'ar' => 'تم القبول'],
                'pickup'    => ['en' => 'Picked up',   'ar' => 'تم الاستلام من المتجر'],
                'on_way'    => ['en' => 'On the way',  'ar' => 'في الطريق'],
                'delivered' => ['en' => 'Delivered',   'ar' => 'تم التسليم'],
                'cancelled' => ['en' => 'Cancelled',   'ar' => 'تم الإلغاء'],
            ];

            // ✅ عنوان ونص الإشعار
            $title = $userLang === 'ar'
                ? 'تم تحديث حالة الطلب'
                : 'Order Status Updated';

            $body = $userLang === 'ar'
                ? 'تم تحديث حالة طلبك إلى ' . ($statusTranslations[$newStatus->value]['ar'] ?? $newStatus)
                : 'Your order status has been updated to ' . ($statusTranslations[$newStatus->value]['en'] ?? ucfirst($newStatus->value));

            // البيانات المرسلة مع الإشعار
            $data = [
                'id' => $order->id,
                'image' => null,
                'notification_type' => 'ecommerce',
                'navigation_type' => 'order_tracking',
            ];

            // ✉️ إرسال الإشعار للمستخدم
            $order->user->notify(
                new OrderStatusUpdated($title, $body, $data, 'ecommerce', 'order_tracking')
            );
        }
        }

        return back()->with('success', __('shipment-dashboard.item_status_updated_successfully'));
    }


    // Package Tracking
    public function addTracking(Request $request, $orderId, $orderItemId)
    {
        /** @var \App\Models\ShipmentCompany $company */
        $company = Auth::guard('shipment')->user();

        $request->validate([
            'status' => 'required|string|in:pending,accepted,pickup,on_way,delivered,cancelled,returned',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_delivery' => 'nullable|date',
        ]);

        // Authorize only if this order contains an item handled by this company
        $order = Order::whereHas('orderItems', function ($q) use ($company) {
            $q->where('shipment_company_id', $company->id);
        })
            ->findOrFail($orderId);
        // Ensure the item belongs to this company
        $orderItem = OrderItem::where('order_id', $orderId)
            ->where('shipment_company_id', $company->id)
            ->findOrFail($orderItemId);

        PackageTracking::create([
            'order_item_id' => $orderItemId,
            'package_id' => $orderItem->package_id,
            'status' => $request->status,
            'location' => $request->location,
            'description' => $request->description,
            'estimated_delivery' => $request->estimated_delivery,
            'occurred_at' => now(),
        ]);

        // Forward-only status update on item
        $sequence = [
            OrderStatus::PENDING->value,
            OrderStatus::ACCEPTED->value,
            OrderStatus::PICKUP->value,
            OrderStatus::ON_WAY->value,
            OrderStatus::DELIVERED->value,
            OrderStatus::RETURNED->value,
        ];
        $current = (string) ($orderItem->status ?? OrderStatus::PENDING->value);
        $next = (string) $request->status;
        $curIdx = array_search($current, $sequence, true);
        $nextIdx = array_search($next, $sequence, true);
        if ($curIdx !== false && $nextIdx !== false && $nextIdx < $curIdx) {
            return redirect()->back()->withErrors(['status' => __('shipment-dashboard.invalid_status_transition')]);
        }
        $orderItem->update(['status' => $next]);

        // Sync order status if all items share same status
        $order->refresh();
        $allStatuses = $order->orderItems()->pluck('status')->filter()->unique();
        if ($allStatuses->count() === 1) {
            Order::where('id', $order->id)->update(['status' => $allStatuses->first()]);
        }

        return redirect()->back()->with('success', __('shipment-dashboard.tracking_information_added_successfully'));
    }

    // Packages Management
    public function packages()
    {
        $company = Auth::guard('shipment')->user();

        $packages = Package::where('shipment_company_id', $company->id)
            ->with(['type', 'size', 'pickupAddress', 'dropoffAddress', 'packageDetails'])
            ->latest()
            ->paginate(20);

        return view('dashboard.shipment.packages.index', compact('packages'));
    }

    public function packageDetails($id)
    {
        $company = Auth::guard('shipment')->user();

        $package = Package::where('shipment_company_id', $company->id)
            ->with(['type', 'size', 'pickupAddress', 'dropoffAddress', 'packageDetails', 'images', 'trackings'])
            ->findOrFail($id);

        return view('dashboard.shipment.package-details', compact('package'));
    }

    // Profile Management
    public function profile()
    {
        $company = Auth::guard('shipment')->user();
        return view('dashboard.shipment.profile.index', compact('company'));
    }

    public function updateProfile(Request $request)
    {
        $company = Auth::guard('shipment')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:shipment_companies,email,' . $company->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'description' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'whatsapp_url' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('logo', 'facebook', 'whatsapp');

        if ($request->hasFile('logo')) {
            $imagePath = uploadImage($request, 'logo', 'storage/shipment-companies/logos');
            $data['logo'] = $imagePath;
        }

        \App\Models\ShipmentCompany::where('id', $company->id)->update($data);

        return redirect()->back()->with('success', __('shipment-dashboard.profile_updated_successfully'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $company = Auth::guard('shipment')->user();

        if (!Hash::check($request->current_password, $company->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        \App\Models\ShipmentCompany::where('id', $company->id)->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', __('shipment-dashboard.password_changed_successfully'));
    }

    // Price Per KM Management
    public function pricing()
    {
        $company = Auth::guard('shipment')->user();

        $pricePerKmMin = Setting::where('key', 'price_per_km_min')->first();
        $pricePerKmMax = Setting::where('key', 'price_per_km_max')->first();

        // Categories already used by this company
        $selectedCategoryIds = ShipmentCompanyCategoryPrice::where('shipment_company_id', $company->id)
            ->pluck('category_id')
            ->toArray();

        // Available categories (NOT used yet)
        $categories = Category::active()
            ->whereNotIn('id', $selectedCategoryIds)
            ->with('translations')
            ->get();

        // Get category prices with category info
        $categoryPrices = ShipmentCompanyCategoryPrice::where('shipment_company_id', $company->id)
            ->with(['category.translations'])
            ->get();

        // Calculate stats
        $stats = [
            'total_orders' => Order::where('shipment_company_id', $company->id)->count(),
            'total_revenue' => Order::where('shipment_company_id', $company->id)->sum('final_price'),
            'price_per_km_min' => $pricePerKmMin ? (float) $pricePerKmMin->value : 0,
            'price_per_km_max' => $pricePerKmMax ? (float) $pricePerKmMax->value : 100,
            'piece_categories' => $categoryPrices->where('category.type', 'piece')->count(),
            'weight_categories' => $categoryPrices->where('category.type', 'weight')->count(),
            'weight_size_categories' => $categoryPrices->where('category.type', 'weight_size')->count(),
        ];
        $company = Auth::guard('shipment')->user();

        $distanceRules = Config::where('key', "shipment.distance_factors")
            ->where('shipment_company_id' , $company->id)
            ->value('value');

        $villageFactor = Config::where('key', "shipment.village.one")
            ->where('shipment_company_id' , $company->id)
            ->value('value');

        $distanceRules = $distanceRules ? json_decode($distanceRules, true) : [];
        $villageFactor = $villageFactor ?: 1.0;

        $perPage = Config::where('key', "shipment.per_piece")
            ->where('shipment_company_id', $company->id)
            ->value('value');

        $perPage = $perPage ? json_decode($perPage, true) : ['small' => 1, 'medium' => 2, 'large' => 3, 'xlarge' => 4];


        return view('dashboard.shipment.pricing', compact(
            'stats',
            'categories',
            'categoryPrices',
            'distanceRules',
            'villageFactor',
            'perPage'
        ));
    }

    public function updateDistanceFactors(Request $request)
    {
        $company = Auth::guard('shipment')->user();

        $data = $request->validate([
            'rules' => 'required|array',
            'rules.*.max' => 'required|numeric|min:1',
            'rules.*.factor' => 'required|numeric|min:0',

            'village_factor' => 'required|numeric|min:0.1',

            // New perPage rules
            'perPage' => 'required|array',
            'perPage.small' => 'required|numeric|min:0',
            'perPage.medium' => 'required|numeric|min:0',
            'perPage.large' => 'required|numeric|min:0',
            'perPage.xlarge' => 'required|numeric|min:0',
        ]);

        // Save distance rules
        Config::updateOrCreate(
            ['key' => "shipment.distance_factors", 'shipment_company_id' => $company->id],
            ['value' => json_encode($data['rules'])]
        );

        // Save village factor
        Config::updateOrCreate(
            ['key' => "shipment.village.one", 'shipment_company_id' => $company->id],
            ['value' => $data['village_factor']]
        );

        // Save perPage sizes
        Config::updateOrCreate(
            ['key' => "shipment.per_piece", 'shipment_company_id' => $company->id],
            ['value' => json_encode($data['perPage'])]
        );

        return back()->with('success', __('shipment-dashboard.distance_village_per_page_rules_updated_successfully'));
    }



    public function updatePricePerKm(Request $request)
    {
        $company = Auth::guard('shipment')->user();

        $data = $request->validate([
            'prices' => 'nullable|array',
            'new_prices' => 'nullable|array',

            'prices.*.id' => 'required|exists:shipment_company_category_prices,id',
            'prices.*.category_id' => 'required|exists:categories,id',
            'prices.*.deleted' => 'nullable|boolean',

            'new_prices.*.category_id' => 'required|exists:categories,id',

            // Pricing fields
            'prices.*.price_per_size' => 'nullable|numeric|min:0',
            'prices.*.price_per_kg' => 'nullable|numeric|min:0',
            'prices.*.per_piece'   => 'nullable|numeric|min:0',

            'new_prices.*.price_per_size' => 'nullable|numeric|min:0',
            'new_prices.*.price_per_kg' => 'nullable|numeric|min:0',
            'new_prices.*.per_piece'    => 'nullable|numeric|min:0',
        ]);

        // Handle updates to existing prices
        if (isset($data['prices'])) {
            foreach ($data['prices'] as $priceData) {
                // Handle deletion
                if (isset($priceData['deleted']) && $priceData['deleted']) {
                    ShipmentCompanyCategoryPrice::where('id', $priceData['id'])
                        ->where('shipment_company_id', $company->id)
                        ->delete();
                    continue;
                }

                $category = Category::find($priceData['category_id']);

                $payload = [
                    'price_per_size' => null,
                    'price_per_kg' => null,
                    'per_piece'    => null,
                ];

                // Set prices based on category type
                if ($category->type === 'piece') {
                    $payload['per_piece'] = $priceData['per_piece'] ?? 0;
                } elseif ($category->type === 'weight') {
                    $payload['price_per_kg'] = $priceData['price_per_kg'] ?? 0;
                } elseif ($category->type === 'weight_size') {
                    $payload['price_per_size'] = $priceData['price_per_size'] ?? 0;
                    $payload['price_per_kg'] = $priceData['price_per_kg'] ?? 0;
                }

                ShipmentCompanyCategoryPrice::where('id', $priceData['id'])
                    ->where('shipment_company_id', $company->id)
                    ->update($payload);
            }
        }

        // Handle new prices
        if (isset($data['new_prices'])) {
            foreach ($data['new_prices'] as $priceData) {
                $category = Category::find($priceData['category_id']);

                $payload = [
                    'shipment_company_id' => $company->id,
                    'category_id' => $category->id,
                    'price_per_size' => null,
                    'price_per_kg' => null,
                    'per_piece'    => null,
                ];

                // Set prices based on category type
                if ($category->type === 'piece') {
                    $payload['per_piece'] = $priceData['per_piece'] ?? 0;
                } elseif ($category->type === 'weight') {
                    $payload['price_per_kg'] = $priceData['price_per_kg'] ?? 0;
                } elseif ($category->type === 'weight_size') {
                    $payload['price_per_size'] = $priceData['price_per_size'] ?? 0;
                    $payload['price_per_kg'] = $priceData['price_per_kg'] ?? 0;
                }

                ShipmentCompanyCategoryPrice::create($payload);
            }
        }

        return redirect()->back()
            ->with('success', __('shipment-dashboard.category_pricing_updated_successfully'));
    }

    public function getSubCategories($categoryId)
    {
        $subCategories = Category::active()->where('main_category_id', $categoryId)->get();
        return response()->json($subCategories);
    }

    public function storeSubCategoryPrice(Request $request)
    {
        $data = $request->validate([
            'shipment_company_category_price_id' => 'required|exists:shipment_company_category_prices,id',
            'category_id' => 'required|exists:categories,id',
            'price_small' => 'required|numeric|min:0',
            'price_medium' => 'required|numeric|min:0',
            'price_large' => 'required|numeric|min:0',
        ]);

        ShipmentCompanySubCategorySizePrice::updateOrCreate(
            [
                'shipment_company_category_price_id' => $data['shipment_company_category_price_id'],
                'category_id' => $data['category_id'],
            ],
            [
                'price_small' => $data['price_small'],
                'price_medium' => $data['price_medium'],
                'price_large' => $data['price_large'],
            ]
        );

        return response()->json(['success' => true]);
    }

    public function viewSubCategoryPrices($categoryPriceId)
    {
        $categoryPrice = ShipmentCompanyCategoryPrice::findOrFail($categoryPriceId);

        $subPrices = ShipmentCompanySubCategorySizePrice::with('category')
            ->where('shipment_company_category_price_id', $categoryPriceId)
            ->paginate(20);

        return view('dashboard.shipment.subcategories.index', compact('categoryPrice', 'subPrices'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'price_small' => 'nullable|numeric|min:0',
            'price_medium' => 'nullable|numeric|min:0',
            'price_large' => 'nullable|numeric|min:0',
        ]);

        $sub = ShipmentCompanySubCategorySizePrice::findOrFail($id);

        // prevent duplicate category + parent ID
        $exists = ShipmentCompanySubCategorySizePrice::where('shipment_company_category_price_id', $sub->shipment_company_category_price_id)
            ->where('category_id', $data['category_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json(['error' => __('shipment-dashboard.subcategory_already_exists')], 422);
        }

        $sub->update($data);

        return response()->json(['success' => true]);
    }

    // Delete
    public function destroy($id)
    {
        ShipmentCompanySubCategorySizePrice::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // Reports
    public function reports()
    {
        $company = Auth::guard('shipment')->user();

        $reports = [
            'monthly_orders' => $this->getMonthlyOrders($company->id),
            'order_status_distribution' => $this->getOrderStatusDistribution($company->id),
            'top_package_types' => $this->getTopPackageTypes($company->id),
            'revenue_by_month' => $this->getRevenueByMonth($company->id),
        ];

        return view('dashboard.shipment.reports', compact('reports'));
    }

    private function getMonthlyRevenue($companyId)
    {
        // Sum this company's item est_price grouped by order month
        return DB::table('order_items AS oi')
            ->join('orders AS o', 'o.id', '=', 'oi.order_id')
            ->where('oi.shipment_company_id', $companyId)
            ->where('o.created_at', '>=', now()->subMonths(12))
            ->selectRaw('MONTH(o.created_at) as month, YEAR(o.created_at) as year, SUM(oi.est_price) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    private function getMonthlyOrders($companyId)
    {
        // Count distinct orders per month that include this company's items
        return DB::table('orders AS o')
            ->join('order_items AS oi', 'o.id', '=', 'oi.order_id')
            ->where('oi.shipment_company_id', $companyId)
            ->where('o.created_at', '>=', now()->subMonths(12))
            ->selectRaw('MONTH(o.created_at) as month, YEAR(o.created_at) as year, COUNT(DISTINCT o.id) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    private function getOrderStatusDistribution($companyId)
    {
        // Distribution of statuses across orders that include this company's items
        return DB::table('orders AS o')
            ->join('order_items AS oi', 'o.id', '=', 'oi.order_id')
            ->where('oi.shipment_company_id', $companyId)
            ->selectRaw('o.status, COUNT(DISTINCT o.id) as count')
            ->groupBy('o.status')
            ->get();
    }

    private function getTopPackageTypes($companyId)
    {
        // Top package types from this company's items
        return DB::table('order_items AS oi')
            ->join('packages AS p', 'p.id', '=', 'oi.package_id')
            ->join('package_types AS pt', 'p.type_id', '=', 'pt.id')
            ->where('oi.shipment_company_id', $companyId)
            ->selectRaw('pt.name, COUNT(*) as count')
            ->groupBy('pt.id', 'pt.name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    private function getRevenueByMonth($companyId)
    {
        // Alias of getMonthlyRevenue for clarity in reports
        return $this->getMonthlyRevenue($companyId);
    }
}
