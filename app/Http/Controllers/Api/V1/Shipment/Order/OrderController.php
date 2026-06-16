<?php

namespace App\Http\Controllers\Api\V1\Shipment\Order;

use App\Enum\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\FullPackageResource;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PackageResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Jobs\SendFcmNotification;
use App\Services\FirebaseService;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $orders = Order::all();
            return responseJson(true, trans('messages.order.index_success'), $orders);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $order = Order::findOrFail($id);
            return responseJson(true, trans('messages.order.show_success'), $order);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function updateOrderItemStatus(UpdateOrderRequest $request, string $orderItemId)
    {
        try {
            $validatedData = $request->validated();
            $order = Order::where('user_id', auth()->id())->findOrFail($orderItemId);
            $order->update([
                'status' => $validatedData['status']
            ]);
            $order->orderItems()->update([
                    'status' => $validatedData['status']
            ]);
             return responseJson(true, trans('messages.update_success'), $order);
         } catch (\Throwable $th) {
             return responseJson(false, $th->getMessage(), null, 500);
         }
    }


    // public function updateOrderItemStatus(UpdateOrderRequest $request, string $orderItemId)
    // {
    //     try {
    //         $validatedData = $request->validated();
    //         $orderItem = OrderItem::findOrFail($orderItemId);

    //         if (
    //             ($validatedData['status'] == $orderItem->status) ||
    //             ($validatedData['status'] == OrderStatus::CANCELLED->value && $orderItem->status == OrderStatus::DELIVERED->value)
    //         ) {
    //             return responseJson(false, trans('messages.item_status_already', ['status' => $orderItem->status]), null, 422);
    //         }

    //         // تحديث الحالة
    //         $orderItem->update([
    //             'status' => $validatedData['status']
    //         ]);

    //         // إضافة Tracking جديد
    //         $orderItem->trackings()->create([
    //             'status' => $validatedData['status'],
    //             'order_item_id' => $orderItem->id,
    //             'package_id' => $orderItem->package_id,
    //             'location' => $orderItem->package->pickupAddress->address,
    //             'description' => trans('messages.item_status_updated'),
    //             'occurred_at' => now(),
    //         ]);

    //         if ($orderItem->order->user->notifications_enabled) {

    //             // ✅ Enum safe status
    //             $statusValue = $validatedData['status'] instanceof \App\Enum\OrderStatus
    //                 ? $validatedData['status']->value
    //                 : (string) $validatedData['status'];

    //             // ✅ map status → notification key
    //             $statusToKey = [
    //                 'pending'   => 'shipment_pending',
    //                 'accepted'  => 'shipment_accepted',
    //                 'pickup'    => 'shipment_pickup',
    //                 'on_way'    => 'shipment_on_way',
    //                 'delivered' => 'shipment_delivered',
    //                 'cancelled' => 'shipment_cancelled',
    //             ];

    //             $key = $statusToKey[$statusValue] ?? 'shipment_updated';

    //             $user = $orderItem->order->user;

    //             // ✅ force user language
    //             app()->setLocale($user->default_lang ?? 'en');

    //             // ✅ translated text (FCM only)
    //             $title = __("notifications.$key.title");
    //             $body  = __("notifications.$key.body", [
    //                 'order_item_id' => $orderItem->id
    //             ]);

    //             // ✅ DB stores only key
    //             $data = [
    //                 'key' => $key,
    //                 'order_item_id' => $orderItem->id,
    //                 'package_id' => $orderItem->package_id,
    //                 'status' => $statusValue,
    //                 'notification_type' => 'shipment',
    //                 'navigation_type' => 'order_tracking',
    //             ];

    //             $user->notify(
    //                 new \App\Notifications\OrderStatusUpdated(
    //                     title: $title,
    //                     body: $body,
    //                     data: $data,
    //                     type: 'shipment',
    //                     navigationType: 'order_tracking'
    //                 )
    //             );
    //         }


    //         return responseJson(true, trans('messages.update_success'), $orderItem);
    //     } catch (\Throwable $th) {
    //         return responseJson(false, $th->getMessage(), null, 500);
    //     }
    // }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            return responseJson(true, trans('messages.order.destroy_success'));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
    public function getOrders(Request $request)
    {
        try {
            $limit  = (int) $request->input('limit', 10);
            $page   = (int) $request->input('page', 1);
            $status = $request->input('status');

            $orders = Order::query()
                ->where('user_id', auth()->id())
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->with([
                    'shipmentCompany',
                    'orderItems' => function ($q) {
                        $q->whereNull('parent_id')
                        ->with([
                            'package.pickupAddress',
                            'package.dropoffAddress',
                            'package.packageDetails',
                            'shipmentCompany',
                        ]);
                    },
                ])
                ->latest();

            $payload = paginate($orders, OrderResource::class, $limit, $page);

            return responseJson(true, trans('messages.order.list'), $payload);
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.order.failed_list'), null, 500);
        }
    }

    public function getOrderItem($orderItemId , Request $request)
    {
        try {
            $limit  = (int) $request->input('limit', 10);
            $page   = (int) $request->input('page', 1);
            $orders = Order::query()->where('id', $orderItemId)
                ->where('user_id', auth()->id())
                ->with([
                    'shipmentCompany',
                    'orderItems' => function ($q) {
                        $q->whereNull('parent_id')
                        ->with([
                            'package.pickupAddress',
                            'package.dropoffAddress',
                            'package.packageDetails',
                            'shipmentCompany',
                        ]);
                    },
                ])
                ->latest();

            $payload = paginate($orders, OrderResource::class, $limit, $page);

            return responseJson(true, trans('messages.order.list'), $payload);
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.order.failed_list'), null, 500);
        }
    }

    public function reviewOrderItem(Request $request, $orderId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        $order = Order::with([
            'orderItems.route',
            'orderItems.shipmentCompany',
            'orderItems.parent',
        ])->findOrFail($orderId);

        if ($order->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $shipmentCompanyId = null;

        // 🔸 Split order → find drop-off leg
        $dropoffItem = $order->orderItems
            ->filter(fn ($item) => $item->route && $item->route->isDropoffLeg())
            ->first();

        if ($dropoffItem) {
            $shipmentCompanyId = $dropoffItem->shipment_company_id;
        } else {
            $directItem = $order->orderItems->first();

            if (!$directItem || !$directItem->shipment_company_id) {
                return response()->json([
                    'message' => 'Shipment company not found'
                ], 422);
            }

            $shipmentCompanyId = $directItem->shipment_company_id;
        }

        $alreadyReviewed = Review::where([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'shipment_company_id' => $shipmentCompanyId,
        ])->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'message' => 'You already reviewed this delivery company'
            ], 409);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'shipment_company_id' => $shipmentCompanyId,
            'rate' => $validated['rating'],
            'comment' => $validated['review'] ?? null,
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'data' => $review,
        ], 201);
    }

    // public function getOrderItem($orderItemId)
    // {
    //     try {
    //         $orderItem = OrderItem::with('order.shipmentCompany', 'package', 'trackings')->whereHas('order', fn($query) => $query->where('user_id', auth()->id()))->where('id', $orderItemId)->first();
    //         if (!$orderItem) {
    //             return responseJson(false, trans('messages.order.item_not_found'), null, 404);
    //         }
    //         return responseJson(true, trans('messages.order.item'), new OrderItemResource($orderItem));
    //     } catch (\Throwable $th) {
    //         return responseJson(false, trans('messages.order.failed_get_item'), $th->getMessage(), 500);
    //     }
    // }



    public function getTracking(Request $request)
    {

        $validated = $request->validate([
            'package_number' => ['required', 'string'],
        ]);

        $order = Order::with([
                    'shipmentCompany',
                    'orderItems' => function ($q) {
                        $q->whereNull('parent_id')
                        ->with([
                            'package.pickupAddress',
                            'package.dropoffAddress',
                            'package.packageDetails',
                            'shipmentCompany',
                        ]);
                    },
                ])->where('order_number', $validated['package_number'])->first();
        if (!$order) {
            return responseJson(false, trans('messages.package.not_found'), null, 404);
        }

        return responseJson(true, trans('messages.order.item'), new OrderResource($order));
    }

    public function getPackage($id)
    {
        try {
            $package = Package::with([
                'type',
                'size',
                'deliveryType',
                'consignmentType',
                'pickupAddress.city',
                'dropoffAddress.city',
                'packageDetails',
                'shipmentCompany.reviews',
                'shipmentCompany.shipmentLocations',
                'images',
                'trackings',
            ])->findOrFail($id);

            return responseJson(
                true,
                trans('messages.package.single'),
                new FullPackageResource($package)
            );
        } catch (\Throwable $th) {
            return responseJson(
                false,
                trans('messages.package.failed_single'),
                $th->getMessage(),
                500
            );
        }
    }


    public function cancelOrder($id)
    {
        try {
            $order = Order::where('user_id', auth()->id())->findOrFail($id);
            $order->update([
                'status' => OrderStatus::CANCELLED->value
            ]);
            $order->orderItems()->update([
                'status' => OrderStatus::CANCELLED->value
            ]);
            return responseJson(true, trans('messages.order.cancelled'));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.order.failed_cancel'), $th->getMessage(), 500);
        }
    }
}
