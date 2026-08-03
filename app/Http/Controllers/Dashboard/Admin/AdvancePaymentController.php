<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Enum\AdvancePaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Services\CourierSystem\AdvancePaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdvancePaymentController extends Controller
{
    public function __construct(
        protected AdvancePaymentService $advancePaymentService
    ) {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $query = AdvancePayment::query()
            ->with(['requestPath', 'submitter']);

        if (! empty($validated['status']) && in_array($validated['status'], AdvancePaymentStatus::values(), true)) {
            $query->where('status', $validated['status']);
        } else {
            $query->whereIn('status', [
                AdvancePaymentStatus::PAID->value,
                AdvancePaymentStatus::CONFIRMED->value,
            ]);
        }

        if (! empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function ($q) use ($search) {
                $q->where('id', (int) $search)
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        return view('dashboard.admin.advance-payments.index', compact('payments'));
    }

    public function confirm(Request $request, AdvancePayment $advancePayment)
    {
        try {
            $this->advancePaymentService->confirm($advancePayment, $this->currentAdminId());

            return redirect()->route('admin.advance-payments.index')
                ->with('success', 'Advance payment confirmed and request execution started.');
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }
    }

    public function reject(Request $request, AdvancePayment $advancePayment)
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->advancePaymentService->reject($advancePayment, $this->currentAdminId(), $validated['note'] ?? null);

            return redirect()->route('admin.advance-payments.index')
                ->with('success', 'Advance payment rejected.');
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }
    }

    protected function currentAdminId(): ?int
    {
        if (auth()->guard('admin')->check()) {
            return auth()->guard('admin')->id();
        }

        if (auth()->guard('employee')->check()) {
            return auth()->guard('employee')->id();
        }

        return null;
    }
}
