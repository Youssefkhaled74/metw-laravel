<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Enum\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Services\PackageService;
use App\Http\Requests\DirectCheckoutRequest;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;
use App\Services\PackageTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function preview(Request $request)
    {
        $cart = Cart::with('items')->where('user_id', auth()->id())->firstOrFail();
        $subtotal = $cart->item_total_price;
        $discount = 0.0; // apply promo later
        $total = max($subtotal - $discount, 0);

        return responseJson(true, 'Checkout preview', [
            'order_number' => Str::upper(Str::random(7)),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'items' => $cart->items,
        ]);
    }

    public function applyPromo(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);
        // Stub: no promo logic for now
        return responseJson(true, 'Promo applied', [
            'code' => $validated['code'],
            'discount' => 0,
        ]);
    }

    public function place(Request $request)
    {
        $cart = Cart::with(['items'])->where('user_id', auth()->id())->firstOrFail();
        if ($cart->items->isEmpty()) {
            return responseJson(false, 'Cart is empty', null, 422);
        }

        $subtotal = (float) $cart->item_total_price;
        $discount = 0.0; // inject promo
        $total = max($subtotal - $discount, 0);

        $order = Order::create([
            'user_id' => auth()->id(),
            'cart_id' => $cart->id,
            'order_number' => null,
            'total_price' => $total,
            'discount_price' => $discount,
            'shipping_price' => 0,
            'status' => OrderStatus::PENDING,
        ]);

        foreach ($cart->items as $item) {
            $OrderItem = OrderItem::create([
                'order_id' => $order->id,
                'item_number' => $item->package->package_number,
                'package_id' => $item->package_id,
                'shipment_company_id' => $item->shipment_company_id,
                'est_date' => $item->est_date,
                'est_price' => $item->est_price,
            ]);
            // initial tracking entry
            PackageTrackingService::createStatus(
                packageId: $item->package_id,
                orderItemId: $OrderItem->id,
                status: OrderStatus::PENDING,
                location: $item->package->pickupAddress->address ?? null,
                description: 'Order placed',
            );
        }

        // Optionally clear cart items after order
        $cart->items()->delete();

        return responseJson(true, 'Order placed', $order->load('orderItems'));
    }

    // // Checkout directly from a single package (no cart)
    // public function directPreview(DirectCheckoutRequest $request)
    // {
    //     $validated = $request->validated();

    //     $package = isset($validated['package_id'])
    //         ? Package::with('shipmentCompany')->findOrFail($validated['package_id'])
    //         : PackageService::createFromPayload($validated, $request);
    //     $subtotal = (float) $validated['est_price'];
    //     $discount = 0.0;
    //     $total = max($subtotal - $discount, 0);

    //     return responseJson(true, 'Direct checkout preview', [
    //         'order_number' => Str::upper(Str::random(7)),
    //         'subtotal' => $subtotal,
    //         'discount' => $discount,
    //         'total' => $total,
    //         'package' => $package,
    //     ]);
    // }

    public function directPlace(DirectCheckoutRequest $request)
    {
        $validated = $request->validated();

        // Ensure package
        $packageId = $validated['package_id'] ?? null;
        if (!$packageId) {
            $package = PackageService::createFromPayload($validated, $request);
            $packageId = $package->id;
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'cart_id' => null,
            'order_number' => null,
            'total_price' => (float) $validated['est_price'],
            'discount_price' => 0.0,
            'shipping_price' => 0.0,
            'status' => OrderStatus::PENDING,
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'item_number' => $package->package_number,
            'package_id' => (int) $packageId,
            'shipment_company_id' => (int) $validated['shipment_company_id'],
            'est_date' => $validated['est_date'] ?? null,
            'est_price' => (float) $validated['est_price'],
        ]);

        // initial tracking entry
        PackageTrackingService::createStatus(
            packageId: (int) $packageId,
            orderItemId: $orderItem->id,
            status: OrderStatus::PENDING,
            location: null,
            description: 'Order placed',
        );

        return responseJson(true, 'Order placed (direct)', $order->load('orderItems'));
    }

    public function getOrders()
    {
        try {
            $orders = Order::with('orderItems', 'orderItems.package')->where('user_id', auth()->id())->latest()->get();
            return responseJson(true, 'Orders', OrderResource::collection($orders));
        } catch (\Throwable $th) {
            return responseJson(false, 'Failed to get orders', null, 500);
        }
    }

    public function getOrderItem($itemNumber)
    {
        try {
            $orderItem = OrderItem::with('order', 'package', 'trackings')->whereHas('order', fn($query) => $query->where('user_id', auth()->id()))->where('item_number', $itemNumber)->first();
            if (!$orderItem) {
                return responseJson(false, 'Order item not found', null, 404);
            }
            return responseJson(true, 'Order item', new OrderItemResource($orderItem));
        } catch (\Throwable $th) {
            return responseJson(false, 'Failed to get order item', null, 500);
        }
    }
}
