<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\OrderPaymentRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderPaymentController extends Controller
{
    public function addItemPayment(Request $request, $orderId, $itemId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $orderItem = EcommerceOrderItem::where('ecommerce_order_id', $orderId)
            ->where('id', $itemId)
            ->firstOrFail();

        // Check if item can accept payment
        // if (!$orderItem->canAcceptPayment()) {
        //     return back()->with('error', 'Cannot add payment. Item must be accepted by vendor and have assigned shipment company.');
        // }

        // Check if amount exceeds remaining amount
        $maxAmount = $orderItem->remaining_amount > 0 ? $orderItem->remaining_amount : $orderItem->final_price;
        if ($request->amount > $maxAmount) {
            return back()->with('error', 'Payment amount cannot exceed remaining amount.');
        }

        DB::transaction(function () use ($request, $orderItem, $orderId) {
            // Create payment record
            OrderPaymentRecord::create([
                'ecommerce_order_id' => $orderId,
                'ecommerce_order_item_id' => $orderItem->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'admin_id' => Auth::guard('employee')->id()
            ]);

            // Update item payment amounts
            $orderItem->updatePaymentAmounts();

            // Update order totals
            $order = $orderItem->order;
            $order->updateOrderPaymentTotals();
        });

        return back()->with('success', __('admin-dashboard.payment_added_successfully'));
    }

    public function addOrderPayment(Request $request, $orderId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $order = EcommerceOrder::findOrFail($orderId);

        // Check if order can accept payment
        if (!$order->canAcceptOrderPayment()) {
            return back()->with('error', 'Cannot add payment. At least one item must be ready for payment.');
        }

        // Check if amount exceeds remaining amount
        $maxAmount = $order->remaining_amount > 0 ? $order->remaining_amount : $order->total_amount;
        if ($request->amount > $maxAmount) {
            return back()->with('error', 'Payment amount cannot exceed remaining amount.');
        }

        DB::transaction(function () use ($request, $order) {
            // Distribute payment to items with remaining amounts
            $paymentAmount = $request->amount;
            $items = $order->items()->where('remaining_amount', '>', 0)->get();

            foreach ($items as $item) {
                if ($paymentAmount <= 0) break;

                $itemPayment = min($paymentAmount, $item->remaining_amount);

                if ($itemPayment > 0) {
                    OrderPaymentRecord::create([
                        'ecommerce_order_id' => $order->id,
                        'ecommerce_order_item_id' => $item->id,
                        'amount' => $itemPayment,
                        'payment_method' => $request->payment_method,
                        'reference_number' => $request->reference_number,
                        'notes' => $request->notes . " (Distributed from order payment)",
                        'admin_id' => Auth::guard('employee')->id()
                    ]);

                    $item->updatePaymentAmounts();
                    $paymentAmount -= $itemPayment;
                }
            }

            // Update order totals
            $order->updateOrderPaymentTotals();
        });

        return back()->with('success', __('admin-dashboard.payment_added_successfully'));
    }
}
