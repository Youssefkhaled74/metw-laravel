<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\ReturnRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\OrderReturnRequest;
use App\Services\MetwGo\ReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderReturnController extends Controller
{
    public function __construct(
        protected ReturnService $returnService,
    ) {}

    public function pendingReturns()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests')) {
            return view('dashboard.admin.no-permission');
        }

        $returns = $this->returnService->pendingReturnsQuery()->paginate(15);

        return view('dashboard.admin.return-orders.pending', compact('returns'));
    }

    public function complaints()
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests')) {
            return view('dashboard.admin.no-permission');
        }

        $returns = $this->returnService->complaintReturnsQuery()->paginate(15);

        return view('dashboard.admin.return-orders.complaints', compact('returns'));
    }

    public function show(OrderReturnRequest $orderReturnRequest)
    {
        if (Auth::guard('employee')->check() && !Auth::guard('employee')->user()->can('admin.return-requests')) {
            return view('dashboard.admin.no-permission');
        }

        $orderReturnRequest->load([
            'order.orderItems',
            'representative.user',
            'user',
            'reason',
            'complaints.user',
            'logs',
        ]);

        return view('dashboard.admin.return-orders.show', [
            'return' => $orderReturnRequest,
        ]);
    }

    public function updateStatus(Request $request, OrderReturnRequest $orderReturnRequest)
    {
        try {
            $validated = $request->validate([
                'status' => ['required', 'in:completed,approved'],
                'admin_notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $employee = Auth::guard('employee')->user();

            if ($validated['status'] === 'completed') {
                $refund = $this->returnService->calculateRefund($orderReturnRequest->order);

                $orderReturnRequest->update([
                    'refund_amount' => $refund['refund_amount'],
                    'shipping_fees' => $refund['shipping_fees'],
                    'return_fees' => $refund['return_fees'],
                    'net_refund' => $refund['net_refund'],
                ]);

                $this->returnService->creditRefundToWallet($orderReturnRequest);
            }

            $newStatus = ReturnRequestStatus::tryFrom($validated['status']);
            $this->returnService->updateStatus(
                $orderReturnRequest,
                $newStatus,
                $validated['admin_notes'] ?? null,
                get_class($employee),
                $employee?->id
            );

            $message = $validated['status'] === 'completed'
                ? 'تم إكمال طلب الإرجاع وإيداع المبلغ في المحفظة بنجاح.'
                : 'تم تحديث حالة طلب الإرجاع بنجاح.';

            return redirect()->back()->with('success', $message);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function reactivate(Request $request, OrderReturnRequest $orderReturnRequest)
    {
        try {
            $validated = $request->validate([
                'action_reason' => ['required', 'string', 'max:1000'],
            ]);

            $employee = Auth::guard('employee')->user();

            $lastComplaint = $orderReturnRequest->complaints()->latest()->first();

            if ($lastComplaint) {
                $lastComplaint->update([
                    'admin_action' => 'reactivated',
                    'action_reason' => $validated['action_reason'],
                ]);
            }

            $this->returnService->updateStatus(
                $orderReturnRequest,
                ReturnRequestStatus::APPROVED,
                'إعادة تفعيل: ' . $validated['action_reason'],
                get_class($employee),
                $employee?->id
            );

            return redirect()->back()->with('success', 'تم إعادة تفعيل طلب الإرجاع وإشعار العميل بنجاح.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
