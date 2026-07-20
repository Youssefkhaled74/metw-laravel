<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\CancellationSellerStatus;
use App\Enum\ReturnStatus;
use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AdminCancellationController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string'],
        ]);

        $query = ReturnRequest::cancellations()
            ->with(['user', 'order.user', 'order.items.product.vendor', 'items.product'])
            ->leftJoin('ecommerce_orders as eo', 'return_requests.ecommerce_order_id', '=', 'eo.id')
            ->select('return_requests.*');

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function ($q) use ($search) {
                $q->where('return_requests.return_number', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('username', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        $returnRequests = $query->latest()->paginate(20)->appends($request->query());

        return view('dashboard.admin.cancellations.index', compact('returnRequests'));
    }

    public function complaints(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = ReturnRequest::cancellations()
            ->with(['user', 'order.user', 'order.items.product.vendor', 'items.product', 'complaints'])
            ->whereHas('complaints', function ($q) {
                $q->whereIn('status', ['pending', 'under_review']);
            })
            ->latest();

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function ($q) use ($search) {
                $q->where('return_requests.return_number', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('username', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $cancellations = $query->paginate(20)->appends($request->query());

        return view('dashboard.admin.cancellations.complaints', compact('cancellations'));
    }

    public function approved(Request $request)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = ReturnRequest::pendingAdminCompletion()
            ->with(['user', 'order.user', 'order.items.product.vendor', 'items.product']);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function ($q) use ($search) {
                $q->where('return_requests.return_number', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('username', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $cancellations = $query->latest()->paginate(20)->appends($request->query());

        return view('dashboard.admin.cancellations.approved', compact('cancellations'));
    }

    public function show(ReturnRequest $returnRequest)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests.show')) {
            return view('dashboard.admin.no-permission');
        }

        $returnRequest->load([
            'user',
            'order.user',
            'order.userAddress',
            'order.items.product.vendor',
            'order.shipmentCompany',
            'items.product',
            'items.orderItem',
            'complaints',
            'cashBack',
        ]);

        return view('dashboard.admin.cancellations.show', compact('returnRequest'));
    }

    public function reactivate(Request $request, ReturnRequest $returnRequest)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests.update-status')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $returnRequest->update([
            'status' => ReturnStatus::REQUESTED->value,
            'seller_status' => null,
            'seller_rejection_reason' => null,
            'admin_reactivation_reason' => $validated['reason'],
            'reactivated_at' => now(),
        ]);

        // Notify buyer
        $buyer = $returnRequest->user;
        if ($buyer) {
            $key = 'cancellation_reactivated';
            app()->setLocale($buyer->default_lang ?? 'ar');
            $buyer->notify(new \App\Notifications\OrderStatusUpdated(
                title: __("notifications.{$key}.title", ['return_number' => $returnRequest->return_number]),
                body: __("notifications.{$key}.body", ['return_number' => $returnRequest->return_number]),
                data: [
                    'key' => $key,
                    'id' => $returnRequest->id,
                    'notification_type' => 'ecommerce',
                    'navigation_type' => 'order_tracking_return',
                ],
                type: 'ecommerce',
                navigationType: 'order_tracking_return'
            ));
        }

        // Notify seller via order items
        $vendorIds = $returnRequest->items->pluck('vendor_id')->filter()->unique();
        foreach ($vendorIds as $vendorId) {
            $vendor = \App\Models\Vendor::find($vendorId);
            if ($vendor) {
                app()->setLocale($vendor->default_lang ?? 'ar');
                $vendor->notify(new \App\Notifications\OrderStatusUpdated(
                    title: 'تم إعادة تفعيل طلب إلغاء',
                    body: 'تم إعادة تفعيل طلب الإلغاء رقم ' . $returnRequest->return_number,
                    data: [
                        'key' => 'cancellation_reactivated',
                        'id' => $returnRequest->id,
                        'notification_type' => 'ecommerce',
                    ],
                    type: 'ecommerce',
                    navigationType: 'order_tracking_return'
                ));
            }
        }

        return redirect()->back()->with('success', 'تم إعادة تفعيل طلب الإلغاء بنجاح');
    }

    public function completeCancellation(Request $request, ReturnRequest $returnRequest)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests.update-status')) {
            return view('dashboard.admin.no-permission');
        }

        $validated = $request->validate([
            'admin_refund_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $refundAmount = (float) $validated['admin_refund_amount'];

            $returnRequest->update([
                'status' => ReturnStatus::COMPLETED->value,
                'admin_refund_amount' => $refundAmount,
                'refund_amount' => $refundAmount,
                'notes' => $validated['notes'] ?? $returnRequest->notes,
                'refunded_at' => now(),
            ]);

            // Credit buyer's wallet
            $buyer = $returnRequest->user;
            if ($buyer) {
                $wallet = $buyer->wallet;
                if (! $wallet) {
                    $wallet = $buyer->wallet()->create([
                        'balance' => 0,
                        'currency' => 'EGP',
                        'is_active' => true,
                    ]);
                }

                $wallet->increment('balance', $refundAmount);

                // Record transaction
                Transaction::create([
                    'user_id' => $buyer->id,
                    'wallet_id' => $wallet->id,
                    'reference_id' => 'REFUND-' . $returnRequest->return_number,
                    'amount' => $refundAmount,
                    'description' => ' refunded via Metwzon Wallet for cancellation request ' . $returnRequest->return_number,
                    'type' => 'increase',
                ]);

                $returnRequest->update(['wallet_credited_at' => now()]);

                // Notify buyer
                app()->setLocale($buyer->default_lang ?? 'ar');
                $buyer->notify(new \App\Notifications\OrderStatusUpdated(
                    title: 'تمت إضافة مبلغ إلى محفظتك',
                    body: 'تمت إضافة مبلغ ' . number_format($refundAmount, 2) . ' جنيه إلى محفظة ميتوزون الخاصة بك.',
                    data: [
                        'key' => 'wallet_refund_credited',
                        'id' => $returnRequest->id,
                        'amount' => $refundAmount,
                        'notification_type' => 'ecommerce',
                        'navigation_type' => 'order_tracking_return',
                    ],
                    type: 'ecommerce',
                    navigationType: 'order_tracking_return'
                ));
            }

            // Notify seller
            $vendorIds = $returnRequest->items->pluck('vendor_id')->filter()->unique();
            foreach ($vendorIds as $vendorId) {
                $vendor = \App\Models\Vendor::find($vendorId);
                if ($vendor) {
                    $vendor->notify(new \App\Notifications\OrderStatusUpdated(
                        title: 'اكتمل طلب الإلغاء',
                        body: 'تم اكتمال طلب الإلغاء رقم ' . $returnRequest->return_number . ' واسترداد المبلغ للعميل.',
                        data: [
                            'key' => 'cancellation_completed',
                            'id' => $returnRequest->id,
                            'notification_type' => 'ecommerce',
                        ],
                        type: 'ecommerce',
                        navigationType: 'order_tracking_return'
                    ));
                }
            }

            DB::commit();

            return redirect()->route('admin.cancellations.approved')
                ->with('success', 'تم اكتمال طلب الإلغاء وإضافة المبلغ إلى محفظة العميل بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة طلب الإلغاء: ' . $e->getMessage());
        }
    }

    public function showComplaints()
    {
        return $this->complaints(request());
    }
}
