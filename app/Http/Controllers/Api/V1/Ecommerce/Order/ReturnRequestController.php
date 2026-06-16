<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Order;

use App\Models\ReturnRequest;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Enum\ReturnStatus;
use App\Enum\ReturnReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReturnRequestRequest;
use App\Http\Resources\ReturnRequestResource;
use App\Models\ReturnCashBack;
use App\Models\ShipmentCommission;
use App\Models\VendorCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnRequestController extends Controller
{
    public function index()
    {
        $returnRequests = ReturnRequest::where('user_id', Auth::id())
            ->with(['order', 'items.orderItem.product', 'items.orderItem.variant'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return responseJson(true, __('messages.return_requests_retrieved_successfully'), $returnRequests);
    }

    public function getOrderForReturn($orderId)
    {
        $order = EcommerceOrder::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'items.variant', 'userAddress', 'returnRequests'])
            ->first();

        if (!$order) {
            return responseJson(false, __('messages.order_not_found'), null, 404);
        }

        if ($order->status !== 'delivered') {
            return responseJson(false, __('messages.order_must_be_delivered_before_return'), null, 400);
        }

        $returnableItems = $order->items->filter(function ($item) {
            $totalReturnedQuantity = $item->returnRequestItems->sum('return_quantity');
            return $totalReturnedQuantity < $item->quantity;
        });

        return responseJson(true, __('messages.order_details_retrieved_successfully'), [
            'order' => $order,
            'returnable_items' => $returnableItems,
            'return_reasons' => ReturnReason::cases()
        ]);
    }

    public function store(StoreReturnRequestRequest $request)
    {
        $validatedData = $request->validated();

        $order = EcommerceOrder::where('id', $validatedData['order_id'])
            ->where('user_id', Auth::id())
            ->with(['items.product', 'items.returnRequestItems'])
            ->first();

        if (!$order) {
            return responseJson(false, __('messages.order_not_found'), null, 404);
        }

        if ($order->status !== 'delivered') {
            return responseJson(false, __('messages.order_must_be_delivered_before_return'), null, 400);
        }

        // التحقق من كل عنصر قبل الإرجاع
        foreach ($validatedData['items'] as $item) {
            $orderItem = $order->items->firstWhere('id', $item['order_item_id']);

            if (!$orderItem) {
                return responseJson(false, __('messages.order_item_not_found'), null, 404);
            }

            $alreadyRequested = $orderItem->returnRequestItems()
                ->whereHas('returnRequest', function ($q) {
                    $q->whereNotIn('status', [ReturnStatus::REJECTED, ReturnStatus::CANCELLED]);
                })
                ->exists();

            if ($alreadyRequested) {
                return responseJson(false, __('messages.item_already_requested_for_return', ['item' => $orderItem->product->name]), null, 400);
            }

            $totalReturnedQuantity = $orderItem->returnRequestItems->sum('return_quantity');
            if ($totalReturnedQuantity + $item['quantity'] > $orderItem->quantity) {
                return responseJson(false, __('messages.cannot_return_more_than_quantity', ['quantity' => $orderItem->quantity, 'item' => $orderItem->product->name]), null, 400);
            }
        }

        DB::beginTransaction();
        try {
            $returnRequest = ReturnRequest::create([
                'user_id' => Auth::id(),
                'ecommerce_order_id' => $order->id,
                'return_number' => (new ReturnRequest)->generateReturnNumber(),
                'status' => ReturnStatus::REQUESTED,
                'reason' => $validatedData['reason'] ?? '',
                'cancel_reason_ids' => $validatedData['cancel_reason_ids'],
                'other_reason' => $validatedData['other_reason'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'pickup_address_id' => $order->user_address_id,
                'pickup_phone' => $order->phone ?? null,
                'pickup_date' => $validatedData['pickup_date'] ?? null,
                'refund_type' => $validatedData['refund_type'] ?? null,
            ]);

            $totalRefundAmount = 0;

            foreach ($validatedData['items'] as $item) {

                $orderItem = $order->items->firstWhere('id', $item['order_item_id']);
                $quantity = $item['quantity'];

                $itemSubtotal = $orderItem->unit_price * $quantity;

                // =============================
                // 1️⃣ Vendor Commission
                // =============================
                $vendorId = $orderItem->product->vendor_id;
                $vendorCommission = VendorCommission::getForVendor($vendorId);

                $vendorRefundPercent = $vendorCommission->refund_fee_percent ?? 0;
                $vendorRefundMin = $vendorCommission->refund_fee_min ?? 0;

                $vendorRefundCommission = max(
                    $itemSubtotal * ($vendorRefundPercent / 100),
                    $vendorRefundMin
                );

                // =============================
                // 2️⃣ Shipment Commission
                // =============================
                $shipmentCompanyId = $orderItem->shipment_company_id;

                $shipmentCommission = ShipmentCommission::where('shipment_company_id', $shipmentCompanyId)
                    ->first();

                if (!$shipmentCommission) {
                    $shipmentCommission = ShipmentCommission::whereNull('shipment_company_id')->first();
                }

                $shipmentPercent = $shipmentCommission->shipment_commission_percent ?? 0;
                $shipmentMin = $shipmentCommission->shipment_commission_min ?? 0;

                $returnShipmentCost = $orderItem->shipment_price_company > 0
                    ? $orderItem->shipment_price_company
                    : $orderItem->shipment_price;

                $returnShipmentCost *= $quantity;

                $shipmentCommissionValue = max(
                    $returnShipmentCost * ($shipmentPercent / 100),
                    $shipmentMin
                );

                // =============================
                // 3️⃣ Final Refund Calculation
                // =============================
                $isFreeReturn = false; // عدل حسب نظامك

                if ($isFreeReturn) {
                    $finalRefund = $itemSubtotal - $vendorRefundCommission;
                } else {
                    $finalRefund = $itemSubtotal
                        - $vendorRefundCommission
                        - $returnShipmentCost;
                }

                $totalRefundAmount += $finalRefund;

                $shipmentNet = $returnShipmentCost - $shipmentCommissionValue;

                $returnRequest->items()->create([
                    'ecommerce_order_item_id' => $orderItem->id,
                    'return_quantity' => $quantity,

                    'item_subtotal' => $itemSubtotal,

                    'vendor_id' => $vendorId,
                    'vendor_refund_commission' => $vendorRefundCommission,

                    'shipment_company_id' => $shipmentCompanyId,
                    'return_shipping_cost' => $returnShipmentCost,
                    'shipment_commission' => $shipmentCommissionValue,
                    'shipment_net' => $shipmentNet,

                    'customer_refund_amount' => $finalRefund,
                    'return_price' => $finalRefund,
                ]);
            }

            $returnRequest->update([
                'refund_amount' => $totalRefundAmount,
                'vendor_refund_commission_total' => $returnRequest->items->sum('vendor_refund_commission'),
                'return_shipping_total' => $returnRequest->items->sum('return_shipping_cost'),
                'shipment_commission_total' => $returnRequest->items->sum('shipment_commission'),
                'shipment_net_total' => $returnRequest->items->sum('shipment_net'),
                'vendor_deduction_total' =>
                    $returnRequest->items->sum('vendor_refund_commission')
            ]);
            DB::commit();

            return responseJson(true, __('messages.return_request_created_successfully'), $returnRequest->load(['order', 'items.orderItem.product']), 201);
        } catch (\Exception $e) {
            DB::rollback();
            return responseJson(false, __('messages.failed_to_create_return_request'), ['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $returnRequest = ReturnRequest::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['pickupaddress','order.userAddress', 'items.orderItem.product', 'items.product.media'])
            ->first();

        if (!$returnRequest) {
            return responseJson(false, __('messages.return_request_not_found'), null, 404);
        }

        return responseJson(true, __('messages.return_request_retrieved_successfully'), new ReturnRequestResource($returnRequest));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::enum(ReturnStatus::class)],
            'notes' => 'nullable|string|max:1000',
        ]);

        $returnRequest = ReturnRequest::findOrFail($id);

        if (in_array($returnRequest->status, [ReturnStatus::APPROVED, ReturnStatus::REJECTED, ReturnStatus::CANCELLED])) {
            return responseJson(false, __('messages.return_request_already_finalized', ['status' => $returnRequest->status->value]), null, 400);
        }

        if ($request->status === $returnRequest->status) {
            return responseJson(false, __('messages.return_request_already_has_status', ['status' => $returnRequest->status->value]), null, 400);
        }

        $returnRequest->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'refunded_at' => $request->status === ReturnStatus::REFUNDED ? now() : null,
        ]);

        if ($returnRequest->order->user->notifications_enabled) {

            $orderItem = EcommerceOrderItem::find(
                $returnRequest->items->first()->ecommerce_order_item_id
            );

            // ✅ map status → notification key
            $statusToKey = [
                'approved'  => 'return_approved',
                'rejected'  => 'return_rejected',
                'cancelled' => 'return_cancelled',
                'refunded'  => 'return_refunded',
            ];

            $statusValue = $request->status instanceof \App\Enum\ReturnStatus
                ? $request->status->value
                : (string) $request->status;

            $key = $statusToKey[$statusValue] ?? 'return_updated';

            // ✅ force user language
            $user = $orderItem->order->user;
            app()->setLocale($user->default_lang ?? 'en');

            // ✅ translated text (FCM only)
            $title = __("notifications.$key.title");
            $body  = __("notifications.$key.body");

            // ✅ DB stores only key
            $data = [
                'key' => $key,
                'return_request_id' => $returnRequest->id,
                'order_item_id' => $orderItem->id,
                'status' => $statusValue,
                'notification_type' => 'ecommerce',
                'navigation_type' => 'order_tracking_return',
            ];

            $user->notify(
                new \App\Notifications\OrderStatusUpdated(
                    title: $title,
                    body: $body,
                    data: $data,
                    type: 'ecommerce',
                    navigationType: 'order_tracking_return'
                )
            );
        }


        return responseJson(true, __('messages.return_request_updated_successfully'), $returnRequest);
    }

    public function cancel($id)
    {
        $returnRequest = ReturnRequest::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$returnRequest) {
            return responseJson(false, __('messages.return_request_not_found'), null, 404);
        }

        if (!$returnRequest->canBeReturned()) {
            return responseJson(false, __('messages.return_request_cannot_be_cancelled'), null, 400);
        }

        $returnRequest->update(['status' => ReturnStatus::REJECTED]);

        return responseJson(true, __('messages.return_request_cancelled_successfully'));
    }

    public function cashBack(Request $request)
    {
        $request->validate([
            'return_id' => 'required|exists:return_requests,id',
            'cash_back_method' => 'required|in:lasco_wallet,insta_pay,mobile_wallet',
            'value' => 'nullable',
        ]);

        $returnRequest = ReturnRequest::where('id', $request->return_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$returnRequest) {
            return responseJson(false, __('messages.return_request_not_found'), null, 404);
        }

        if (!in_array($returnRequest->status, [
            ReturnStatus::APPROVED,
            ReturnStatus::REFUNDED,
            ReturnStatus::COMPLETED
        ])) {
            return responseJson(false, 'Return request not eligible for cashback', null, 400);
        }

        if ($returnRequest->cashBack) {
            return responseJson(false, 'Cashback already submitted for this return', null, 400);
        }

        $cashback = ReturnCashBack::create([
            'return_id' => $request->return_id,
            'cash_back_method' => $request->cash_back_method,
            'value' => $request->value,
        ]);

        return responseJson(true, 'Cashback submitted successfully', $cashback);
    }

}
