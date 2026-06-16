<?php

namespace App\Http\Controllers\Dashboard\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Enum\ReturnStatus;
use App\Models\ReturnRequestItem;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = auth('vendor')->id();
        $perPage = (int) $request->get('per_page', 10);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string', 'in:all,requested,approved,pickup,processing,refunded,rejected,cancelled,completed'],
            'sort_by' => ['nullable', 'in:return_number,customer,order_number,items,refund_amount,pickup_date,created_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);

        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        $returnRequestsQuery = ReturnRequest::query()
            ->whereHas('items.orderItem.product', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->with([
                'user',
                'order',
                'items' => function ($query) use ($vendorId) {
                    $query->whereHas('orderItem.product', function ($productQuery) use ($vendorId) {
                        $productQuery->where('vendor_id', $vendorId);
                    })->with('product');
                }
            ])
            ->withCount([
                'items as vendor_items_count' => function ($query) use ($vendorId) {
                    $query->whereHas('orderItem.product', function ($productQuery) use ($vendorId) {
                        $productQuery->where('vendor_id', $vendorId);
                    });
                }
            ])
            ->withSum([
                'items as vendor_refund_amount_sum' => function ($query) use ($vendorId) {
                    $query->whereHas('orderItem.product', function ($productQuery) use ($vendorId) {
                        $productQuery->where('vendor_id', $vendorId);
                    });
                }
            ], 'return_price');

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $returnRequestsQuery->where(function ($query) use ($search) {
                $query->where('return_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('order_number', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $returnRequestsQuery->where('status', $validated['status']);
        }

        if ($sortBy === 'customer') {
            $returnRequestsQuery->leftJoin('users as u', 'return_requests.user_id', '=', 'u.id')
                ->select('return_requests.*')
                ->orderBy('u.username', $sortDir);
        } elseif ($sortBy === 'order_number') {
            $returnRequestsQuery->leftJoin('ecommerce_orders as eo', 'return_requests.ecommerce_order_id', '=', 'eo.id')
                ->select('return_requests.*')
                ->orderBy('eo.order_number', $sortDir);
        } elseif ($sortBy === 'items') {
            $returnRequestsQuery->orderBy('vendor_items_count', $sortDir);
        } elseif ($sortBy === 'refund_amount') {
            $returnRequestsQuery->orderBy('vendor_refund_amount_sum', $sortDir);
        } else {
            $returnRequestsQuery->orderBy("return_requests.{$sortBy}", $sortDir);
        }

        $returnRequests = $returnRequestsQuery
            ->paginate($perPage)
            ->appends($request->query());

        return view('dashboard.vendor.return-requests.index', compact('returnRequests'));
    }

    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load([
            'user',
            'order',
            'items.product',
            'items.orderItem.shipmentCompany',
            'pickupaddress'
        ]);

        return view('dashboard.vendor.return-requests.show', compact('returnRequest'));
    }

    public function toggleStatus(Request $request, ReturnRequest $returnRequest)
    {
        $action = $request->get('action');

        if ($action === 'approve') {
            $returnRequest->update(['status' => ReturnStatus::APPROVED]);
            $message = __('vendor-dashboard.request_approved');
        } elseif ($action === 'reject') {
            $returnRequest->update(['status' => ReturnStatus::REJECTED]);
            $message = __('vendor-dashboard.request_rejected');
        } else {
            $message = __('vendor-dashboard.invalid_action');
        }

        return redirect()->back()->with('success', $message);
    }

    public function toggleItemStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:approve,reject',
        ]);

        $item = ReturnRequestItem::with(['returnRequest.user', 'orderItem.product'])
            ->findOrFail($id);

        $action = $validated['action'];

        // ✅ map action → status
        $newStatus = $action === 'approve' ? 'approved' : 'rejected';

        $item->update(['status' => $newStatus]);

        $returnRequest = $item->returnRequest;
        $user = $returnRequest?->user;
        $productName = $item->orderItem?->product?->name ?? __('vendor-dashboard.product');

        // ✅ status → notification key
        $statusToKey = [
            'approved' => 'return_approved',
            'rejected' => 'return_rejected',
        ];

        $key = $statusToKey[$newStatus] ?? 'return_updated';

        if ($user && $user->notifications_enabled) {

            // ✅ force user language
            app()->setLocale($user->default_lang ?? 'en');

            // ✅ translated text (FCM only)
            $title = __("notifications.$key.title");
            $body  = __("notifications.$key.body", [
                'product' => $productName,
            ]);

            // ✅ DB stores only key
            $data = [
                'key' => $key,
                'id' => $item->id,
                'product' => $productName,
                'notification_type' => 'ecommerce',
                'navigation_type' => 'order_tracking_return',
            ];

            $user->notify(
                new OrderStatusUpdated(
                    title: $title,
                    body: $body,
                    data: $data,
                    type: 'ecommerce',
                    navigationType: 'order_tracking_return'
                )
            );
        }

        return back()->with('success', __('vendor-dashboard.status_updated_successfully'));
    }


}
