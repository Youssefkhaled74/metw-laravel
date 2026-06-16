<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\ReturnReason;
use App\Enum\ReturnStatus;
use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\ShipmentCompany;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Auth;

class ReturnRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display all return requests
     */
    public function index(Request $request)
    {
        try {
            if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests')) {
                return view('dashboard.admin.no-permission');
            }

            $statuses = array_map(fn ($status) => $status->value, ReturnStatus::cases());

            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:100'],
                'status' => ['nullable', 'string'],
                'sort_by' => ['nullable', 'in:return_number,order_number,created_at'],
                'sort_dir' => ['nullable', 'in:asc,desc'],
            ]);

            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortDir = $validated['sort_dir'] ?? 'desc';

            $returnRequestsQuery = ReturnRequest::query()
                ->with(['order.user', 'order.items.product', 'order.shipmentCompany'])
                ->leftJoin('ecommerce_orders as orders', 'return_requests.ecommerce_order_id', '=', 'orders.id')
                ->select('return_requests.*');

            if (!empty($validated['search'])) {
                $search = trim($validated['search']);
                $returnRequestsQuery->where(function ($query) use ($search) {
                    $query->where('return_requests.return_number', 'like', "%{$search}%")
                        ->orWhere('return_requests.reason', 'like', "%{$search}%")
                        ->orWhereHas('order', function ($orderQuery) use ($search) {
                            $orderQuery->where('order_number', 'like', "%{$search}%")
                                ->orWhereHas('user', function ($userQuery) use ($search) {
                                    $userQuery->where('username', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%");
                                });
                        });
                });
            }

            if (!empty($validated['status']) && $validated['status'] !== 'all') {
                $returnRequestsQuery->where('status', $validated['status']);
            }

            if ($sortBy === 'order_number') {
                $returnRequestsQuery->orderBy('orders.order_number', $sortDir);
            } elseif ($sortBy === 'return_number') {
                $returnRequestsQuery->orderBy('return_requests.return_number', $sortDir);
            } else {
                $returnRequestsQuery->orderBy('return_requests.created_at', $sortDir);
            }

            $returnRequests = $returnRequestsQuery
                ->paginate(20)
                ->appends($request->query());

            return view('dashboard.admin.return-requests', compact('returnRequests', 'statuses'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع' : 'Unexpected error occurred');
        }
    }

    /**
     * Show details of a single return request
     */
    public function showOrder($id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests.show')) {
            return view('dashboard.admin.no-permission');
        }
        $returnRequest = ReturnRequest::with([
                'user',
                'cashBack',
                'order.user',
                'order.userAddress',
                'order.items.product.vendor',
                'order.shipmentCompany'
            ])
            ->findOrFail($id);

        $shipmentCompanies = ShipmentCompany::where('is_active', true)->get();

        return view('dashboard.admin.return-requests-details', compact('returnRequest', 'shipmentCompanies'));
    }

    /**
     * Update the status of a return request
     */
    public function updateOrderStatus(Request $request, $id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests.update-status')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'status' => ['required', new Enum(ReturnStatus::class)],
        ]);

        $returnRequest = ReturnRequest::findOrFail($id);

        $returnRequest->update([
            'status' => $request->input('status'),
        ]);

        return redirect()->back()->with('success', 'Return request status updated successfully.');
    }

    public function updateOrderReason(Request $request, $id)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests.update-reason')) {
            return view('dashboard.admin.no-permission');
        }
        $request->validate([
            'reason' => ['required', new Enum(ReturnReason::class)],
        ]);

        $returnRequest = ReturnRequest::find($id);

        $returnRequest->update([
            'reason' => $request->input('reason'),
        ]);

        return redirect()->back()->with('success', 'Return request reason updated successfully.');
    }
}
