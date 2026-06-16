<?php

namespace App\Http\Controllers\Dashboard\ShipmentCompany;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Enum\ReturnStatus;
use App\Models\ReturnRequestItem;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $company = Auth::guard('shipment')->user();
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
            ->whereHas('items.orderItem', function ($query) use ($company) {
                $query->where('shipment_company_id', $company->id);
            })
            ->with([
                'user',
                'order',
                'items' => function ($query) use ($company) {
                    $query->whereHas('orderItem', function ($itemQuery) use ($company) {
                        $itemQuery->where('shipment_company_id', $company->id);
                    })->with(['product', 'orderItem']);
                }
            ])
            ->withCount([
                'items as company_items_count' => function ($query) use ($company) {
                    $query->whereHas('orderItem', function ($itemQuery) use ($company) {
                        $itemQuery->where('shipment_company_id', $company->id);
                    });
                }
            ])
            ->withSum([
                'items as company_refund_amount_sum' => function ($query) use ($company) {
                    $query->whereHas('orderItem', function ($itemQuery) use ($company) {
                        $itemQuery->where('shipment_company_id', $company->id);
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
            $returnRequestsQuery->orderBy('company_items_count', $sortDir);
        } elseif ($sortBy === 'refund_amount') {
            $returnRequestsQuery->orderBy('company_refund_amount_sum', $sortDir);
        } else {
            $returnRequestsQuery->orderBy("return_requests.{$sortBy}", $sortDir);
        }

        $returnRequests = $returnRequestsQuery
            ->paginate($perPage)
            ->appends($request->query());

        return view('dashboard.shipment.return-requests.index', compact('returnRequests'));
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

        return view('dashboard.shipment.return-requests.show', compact('returnRequest'));
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
            'action' => 'required|string|in:requested,pickup,processing,refunded,cancelled',
        ]);

        $item = ReturnRequestItem::with(['returnRequest.user', 'orderItem.product'])
            ->findOrFail($id);

        $newStatus = $validated['action'];
        $currentStatus = $item->status instanceof \App\Enum\ReturnStatus
            ? $item->status->value
            : (string) $item->status;

        // ✅ Prevent backward transitions
        $sequence = ['requested', 'pickup', 'processing', 'refunded'];

        if ($newStatus !== 'cancelled') {
            if (array_search($newStatus, $sequence) < array_search($currentStatus, $sequence)) {
                return back()->withErrors(['action' => __('vendor-dashboard.status_backward_not_allowed')]);
            }
        }

        // ✅ Prevent cancelling after refund
        if ($newStatus === 'cancelled' && $currentStatus === 'refunded') {
            return back()->withErrors(['action' => __('vendor-dashboard.cannot_cancel_after_refund')]);
        }

        // ✅ Update item
        $item->update(['status' => $newStatus]);

        $returnRequest = $item->returnRequest;
        $user = $returnRequest?->user;
        $productName = $item->orderItem?->product?->name ?? __('vendor-dashboard.product');

        // ✅ Prepare notification keys
        $statusToKey = [
            'requested' => 'return_requested',
            'pickup'    => 'return_pickup',
            'processing'=> 'return_processing',
            'refunded'  => 'return_refunded',
            'cancelled' => 'return_cancelled',
        ];

        $key = $statusToKey[$newStatus] ?? 'return_updated';

        // ✅ Send Notification
        if ($user && $user->notifications_enabled) {

            // Force user language
            app()->setLocale($user->default_lang ?? 'en');

            $title = __("notifications.$key.title");
            $body  = __("notifications.$key.body", [
                'product' => $productName,
            ]);

            $user->notify(new OrderStatusUpdated(
                title: $title,
                body: $body,
                data: [
                    'key' => $key,
                    'id'  => $item->id,
                    'product' => $productName,
                    'notification_type' => 'ecommerce',
                    'navigation_type' => 'order_tracking_return',
                ],
                type: 'ecommerce',
                navigationType: 'order_tracking_return'
            ));
        }

        // ✅ Update parent request status
        $allStatuses = $returnRequest->items()->pluck('status')->filter()->unique();

        if ($allStatuses->count() === 1) {
            $returnRequest->update(['status' => $allStatuses->first()]);
        }

        // ✅ Mark completed if all refunded
        $allRefunded = $returnRequest->items()
            ->where('status', '!=', \App\Enum\ReturnStatus::REFUNDED)
            ->doesntExist();
        if ($allRefunded && !$returnRequest->refunded_at) {

            if ($returnRequest->refund_type === 'wallet') {

                $wallet = $user?->wallet;

                if ($wallet) {
                    $wallet->increment('balance', $returnRequest->refund_amount);
                }
            }

            $returnRequest->update([
                'status' => \App\Enum\ReturnStatus::COMPLETED,
                'refunded_at' => now(),
            ]);
        }
        // if ($allRefunded) {
        //     $returnRequest->update([
        //         'status' => \App\Enum\ReturnStatus::COMPLETED,
        //         'refunded_at' => now(),
        //     ]);
        // }

        return back()->with('success', __('vendor-dashboard.status_updated_successfully'));
    }




}
