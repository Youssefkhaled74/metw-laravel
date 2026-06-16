<?php

namespace App\Http\Controllers\Api\V1\Shipment\Order;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutSelectedItemsRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\PackageService;
use App\Services\PromoCodeService;
use App\Http\Requests\DirectCheckoutRequest;
use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PreviewOrderResource;
use App\Models\CartItem;
use App\Models\CartItemRoute;
use App\Models\OrderItemRoute;
use App\Models\Package;
use App\Models\Payment;
use App\Models\ShipmentCompany;
use App\Models\ShipmentLocation;
use App\Services\CoverageService;
use App\Services\GoogleMapsService;
use App\Services\PackageTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected CoverageService $coverageService;

    public function __construct()
    {
        $this->coverageService = new CoverageService(new GoogleMapsService());
    }

    // public function preview(Request $request, $cartid)
    // {
    //     $cart = Cart::with('items')
    //         ->where('id', $cartid)
    //         ->where('user_id', auth()->id())
    //         ->first();

    //     if (!$cart) {
    //         return responseJson(false, 'Cart not found or does not belong to you', null, 404);
    //     }

    //     $subtotal = $cart->item_total_price;
    //     $discount = 0.0;
    //     $total = max($subtotal - $discount, 0);

    //     return responseJson(true, 'Checkout preview', [
    //         'country_code' => $cart->user->country_code,
    //         'phone' => $cart->user->phone,
    //         'subtotal' => $subtotal,
    //         'discount' => $discount,
    //         'total' => $total,
    //         'items' => CartItemResource::collection($cart->items),
    //     ]);
    // }

    public function preview(Request $request, $orderId)
    {
        // Load the order with all necessary relationships
        $order = Order::with([
            'orderItems.package.type',
            'orderItems.package.size',
            'orderItems.package.deliveryType',
            'orderItems.package.consignmentType',
            'orderItems.package.category',
            'orderItems.package.subCategory',
            'orderItems.package.pickupAddress',
            'orderItems.package.dropoffAddress',
            'orderItems.package.packageDetails',
            'orderItems.package.images', // <-- add this
        ])->findOrFail($orderId);



        // Return as JSON using OrderResource
        return new PreviewOrderResource($order);
    }



    // public function place(PlaceOrderRequest $request)
    // {
    //     try {
    //         $validated = $request->validated();

    //         $cart = Cart::with('items')->where('user_id', auth()->id())->where('id', $validated['cart_id'])->firstOrFail();

    //         if ($cart->items->isEmpty()) {
    //             return responseJson(false, 'Cart is empty', null, 422);
    //         }

    //         $subtotal = (float) $cart->item_total_price;
    //         $discount = 0.0;
    //         $promoCodeId = null;

    //         // Apply promo code if provided during order placement
    //         if (isset($validated['promo_code']) && !empty($validated['promo_code'])) {
    //             $promoCodeService = new PromoCodeService();
    //             $result = $promoCodeService->validateAndApply($validated['promo_code'], $subtotal, auth()->id());

    //             if ($result['valid']) {
    //                 $discount = $result['discount'];
    //                 $promoCodeId = $result['promo_code']->id;
    //             } else {
    //                 return responseJson(false, $result['message'], null, 422);
    //             }
    //         }


    //         $total = max($subtotal - $discount, 0);

    //         // Coverage check for each cart item before order creation
    //         $coverageService = new \App\Services\CoverageService(new \App\Services\GoogleMapsService());
    //         $company = $cart->shipmentCompany ?? null;
    //         if ($company) {
    //             foreach ($cart->items as $item) {
    //                 $pickup = [
    //                     'latitude' => (float) ($item->package->pickupAddress->latitude ?? 0),
    //                     'longitude' => (float) ($item->package->pickupAddress->longitude ?? 0),
    //                     'country_id' => $item->package->pickupAddress->country_id ?? null,
    //                     'state_id' => $item->package->pickupAddress->state_id ?? null,
    //                     'city_id' => $item->package->pickupAddress->city_id ?? null,
    //                     'zone_id' => $item->package->pickupAddress->zone_id ?? null,
    //                 ];
    //                 $dropoff = [
    //                     'latitude' => (float) ($item->package->dropoffAddress->latitude ?? 0),
    //                     'longitude' => (float) ($item->package->dropoffAddress->longitude ?? 0),
    //                     'country_id' => $item->package->dropoffAddress->country_id ?? null,
    //                     'state_id' => $item->package->dropoffAddress->state_id ?? null,
    //                     'city_id' => $item->package->dropoffAddress->city_id ?? null,
    //                     'zone_id' => $item->package->dropoffAddress->zone_id ?? null,
    //                 ];
    //                 $coverageAnalysis = $coverageService->analyzeCoverage($company, $pickup, $dropoff);
    //                 if ($coverageAnalysis['coverage_type'] === 'split_via_shared_location') {
    //                     return responseJson(false, $coverageAnalysis['message'], [
    //                         'split_suggestions' => $coverageAnalysis['split_suggestions'],
    //                         'best_suggestion' => $coverageAnalysis['best_suggestion'],
    //                         'item_id' => $item->id,
    //                     ], 422);
    //                 }
    //                 if ($coverageAnalysis['coverage_type'] === 'none') {
    //                     return responseJson(false, $coverageAnalysis['message'], ['item_id' => $item->id], 422);
    //                 }
    //             }
    //         }

    //         $paidAmount = $validated['payment_method'] === 'partial'
    //             ? ($validated['partial_amount'] ?? 0)
    //             : $total;

    //         $remainingAmount = $total - $paidAmount;

    //         $order = Order::create([
    //             'user_id' => auth()->id(),
    //             // 'cart_id' => $cart->id,
    //             'shipment_company_id' => $cart->shipment_company_id,
    //             'order_number' => Str::upper(Str::random(7)),
    //             'total_price' => $subtotal,
    //             'paid_amount' => $paidAmount,
    //             'remaining_amount' => $remainingAmount,
    //             'discount_price' => $discount,
    //             'final_price' => $total,
    //             'status' => OrderStatus::PENDING,
    //         ]);

    //         foreach ($cart->items as $item) {
    //             $OrderItem = OrderItem::create([
    //                 'order_id' => $order->id,
    //                 'item_number' => Str::upper(Str::random(7)),
    //                 'package_id' => $item->package_id,
    //                 'shipment_company_id' => $item->shipment_company_id,
    //                 'est_date' => $item->est_date,
    //                 'est_price' => $item->est_price,
    //             ]);
    //             // initial tracking entry
    //             PackageTrackingService::createStatus(
    //                 packageId: $item->package_id,
    //                 orderItemId: $OrderItem->id,
    //                 status: OrderStatus::PENDING,
    //                 location: $item->package->pickupAddress->address ?? null,
    //                 description: 'Order placed',
    //             );
    //         }

    //         Payment::create([
    //             'order_id' => $order->id,
    //             'transaction_id' => Str::upper(Str::random(7)),
    //             'payment_method' => $validated['payment_method'],
    //             'total_amount' => $subtotal,
    //             'paid_amount' => $paidAmount,
    //             'remaining_amount' => $remainingAmount,
    //             'payment_status' => PaymentStatus::PENDING,
    //             'promo_code_id' => $promoCodeId,
    //             'discount_price' => $discount,
    //             'final_price' => $total,
    //         ]);

    //         // Record promo code usage
    //         if ($promoCodeId) {
    //             $promoCodeService = new PromoCodeService();
    //             $promoCode = $promoCodeService->getByCode($validated['promo_code']);
    //             if ($promoCode) {
    //                 $promoCodeService->recordUsage($promoCode);
    //             }
    //         }

    //         // Optionally clear cart items after order
    //         $cart->items()->delete();
    //         $cart->delete();

    //         return responseJson(true, 'Order placed', $order->load('orderItems'));
    //     } catch (\Exception $e) {
    //         return responseJson(false, 'Failed to place order', $e->getMessage(), 500);
    //     }
    // }
    // 1️⃣ فانكشن إنشاء الأوردر فقط (بدون دفع) - من الكارت
    public function create(PlaceOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $cart = Cart::with([
                'items.route',
                'items.package.pickupAddress',
                'items.package.dropoffAddress',
                'items.package.packageDetails'
            ])
                ->where('user_id', auth()->id())
                ->where('id', $validated['cart_id'])
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                return responseJson(false, 'Cart is empty', null, 422);
            }

            $subtotal = (float) $cart->item_total_price;
            $discount = 0.0;
            $promoCodeId = null;

            // Apply promo code if provided
            if (isset($validated['promo_code']) && !empty($validated['promo_code'])) {
                $promoCodeService = new PromoCodeService();
                $result = $promoCodeService->validateAndApply($validated['promo_code'], $subtotal, auth()->id());

                if ($result['valid']) {
                    $discount = $result['discount'];
                    $promoCodeId = $result['promo_code']->id;
                } else {
                    return responseJson(false, $result['message'], null, 422);
                }
            }

            $total = max($subtotal - $discount, 0);

            // Create main order
            $order = Order::create([
                'user_id' => auth()->id(),
                'shipment_company_id' => $cart->shipment_company_id,
                'promo_code_id' => $promoCodeId,
                'order_number' => null,
                'total_price' => $subtotal,
                'paid_amount' => 0,
                'remaining_amount' => $total,
                'discount_price' => $discount,
                'final_price' => $total,
                'status' => OrderStatus::PENDING,
            ]);

            // Process each cart item
            foreach ($cart->items as $item) {
                // Check if item has split route
                if ($item->route && $item->route->is_split) {
                    $this->createSplitOrderItems($order, $item);
                } else {
                    $this->createRegularOrderItem($order, $item);
                }
            }

            // Clear cart and related data
            CartItemRoute::whereIn('cart_item_id', $cart->items->pluck('id'))->delete();
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return responseJson(true, 'Order created successfully', $order->load([
                'orderItems.childItems',
                'orderItems.package.pickupAddress',
                'orderItems.package.dropoffAddress',
                'orderItems.package.packageDetails'
            ]));
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::error('Order creation failed: ' . $e->getMessage(), [
            //     'trace' => $e->getTraceAsString(),
            //     'user_id' => auth()->id(),
            //     'cart_id' => $validated['cart_id'] ?? null
            // ]);
            return responseJson(false, 'Failed to place order', $e->getMessage(), 500);
        }
    }

    public function checkoutSelectedItems(CheckoutSelectedItemsRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();
        $cartId = $validated['cart_id'];

        // Get cart & verify ownership
        $cart = Cart::with('items')->findOrFail($cartId);

        if ($cart->user_id !== $user->id) {
            return responseJson(false, "Unauthorized access to cart", null, 403);
        }

        // Get all cart items from cart
        $cartItems = CartItem::where('cart_id', $cart->id)
            ->with(['cart', 'package', 'route', 'shipmentCompany'])
            ->get();

        if ($cartItems->isEmpty()) {
            return responseJson(false, "Cart is empty", null, 422);
        }

        DB::beginTransaction();

        try {
            $orders = [];
            $createdOrderItems = [];

            // Calculate totals
            $totalPrice = $cartItems->sum('est_price');

            $orderNumber = 'ORD-' . strtoupper(Str::random(8));

            $order = Order::create([
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'order_number' => null,
                'total_price' => $totalPrice,
                'discount_price' => 0,
                'paid_amount' => 0,
                'remaining_amount' => $totalPrice,
                'shipment_company_id' => null,
                'status' => OrderStatus::PENDING,
                'final_price' => $totalPrice,
                'payment_status' => 'pending',
            ]);

            $orders[] = $order;

            foreach ($cartItems as $index => $cartItem) {
                $itemNumber = $orderNumber . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'package_id' => $cartItem->package_id,
                    'shipment_company_id' => $cartItem->shipment_company_id,
                    'est_date' => $cartItem->est_date,
                    'est_price' => $cartItem->est_price,
                    'item_number' => $itemNumber,
                    'status' => OrderStatus::PENDING,
                    'parent_id' => null,
                    'is_split' => $cartItem->requires_split,
                ]);

                $createdOrderItems[] = $orderItem;

                if ($cartItem->route) {
                    $this->copyCartRouteToOrderRoute($cartItem, $orderItem);
                }

                if ($cartItem->package && !$cartItem->package->shipment_company_id) {
                    $cartItem->package->update([
                        'shipment_company_id' => $cartItem->shipment_company_id
                    ]);
                }

                $cartItem->delete();
            }

            $this->updateCartTotalsAfterCheckout($cart, $cartItems);

            DB::commit();

            $order->load([
                'orderItems.package.packageDetails',
                'orderItems.package.pickupAddress',
                'orderItems.package.dropoffAddress',
                'orderItems.route',
                'user'
            ]);

            return responseJson(true, "Order created successfully", [
                'order' => $order,
                'created_order_items' => $createdOrderItems,
                'message' => "Successfully checked out {$cartItems->count()} item(s)"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return responseJson(false, "Failed to create order: " . $e->getMessage(), null, 500);
        }
    }

    /**
     * Copy cart item route to order item route
     */
    private function copyCartRouteToOrderRoute(CartItem $cartItem, OrderItem $orderItem): void
    {
        $cartRoute = $cartItem->route;

        if (!$cartRoute) {
            // If no route exists, create a basic one from package addresses
            $this->createBasicOrderRoute($cartItem, $orderItem);
            return;
        }

        // Extract cost from cart route
        $cost = $this->extractCostFromCartRoute($cartRoute, $cartItem);

        if ($cartRoute->is_split) {
            // For split shipments, create two legs
            $legs = $cartRoute->legs ?? [];

            if (count($legs) >= 2) {
                // Extract cost for each leg
                $legACost = $this->extractLegCost($legs[0]);
                $legBCost = $this->extractLegCost($legs[1]);

                // First leg (pickup to handoff)
                OrderItemRoute::create([
                    'order_item_id' => $orderItem->id,
                    'from_address' => $cartRoute->pickup_address,
                    'from_latitude' => $cartRoute->pickup_address['latitude'] ?? null,
                    'from_longitude' => $cartRoute->pickup_address['longitude'] ?? null,
                    'from_city_id' => $cartRoute->pickup_address['city_id'] ?? null,
                    'from_state_id' => $cartRoute->pickup_address['state_id'] ?? null,
                    'from_zone_id' => $cartRoute->pickup_address['zone_id'] ?? null,
                    'to_address' => $cartRoute->handoff_point ?? $cartRoute->pickup_address,
                    'to_latitude' => $cartRoute->handoff_point['latitude'] ?? ($cartRoute->pickup_address['latitude'] ?? null),
                    'to_longitude' => $cartRoute->handoff_point['longitude'] ?? ($cartRoute->pickup_address['longitude'] ?? null),
                    'to_city_id' => $cartRoute->handoff_point['city_id'] ?? ($cartRoute->pickup_address['city_id'] ?? null),
                    'to_state_id' => $cartRoute->handoff_point['state_id'] ?? ($cartRoute->pickup_address['state_id'] ?? null),
                    'to_zone_id' => $cartRoute->handoff_point['zone_id'] ?? ($cartRoute->pickup_address['zone_id'] ?? null),
                    'leg_type' => 'pickup',
                    'leg_order' => 1,
                    'distance' => $legs[0]['distance_km'] ?? $legs[0]['price']['distance_km'] ?? null,
                    'cost' => $legACost,
                    'pickup_company_id' => $cartRoute->pickup_company_id ?? null,
                    'dropoff_company_id' => $cartRoute->dropoff_company_id ?? null,
                ]);

                // Second leg (handoff to dropoff)
                OrderItemRoute::create([
                    'order_item_id' => $orderItem->id,
                    'from_address' => $cartRoute->handoff_point ?? $cartRoute->dropoff_address,
                    'from_latitude' => $cartRoute->handoff_point['latitude'] ?? ($cartRoute->dropoff_address['latitude'] ?? null),
                    'from_longitude' => $cartRoute->handoff_point['longitude'] ?? ($cartRoute->dropoff_address['longitude'] ?? null),
                    'from_city_id' => $cartRoute->handoff_point['city_id'] ?? ($cartRoute->dropoff_address['city_id'] ?? null),
                    'from_state_id' => $cartRoute->handoff_point['state_id'] ?? ($cartRoute->dropoff_address['state_id'] ?? null),
                    'from_zone_id' => $cartRoute->handoff_point['zone_id'] ?? ($cartRoute->dropoff_address['zone_id'] ?? null),
                    'to_address' => $cartRoute->dropoff_address,
                    'to_latitude' => $cartRoute->dropoff_address['latitude'] ?? null,
                    'to_longitude' => $cartRoute->dropoff_address['longitude'] ?? null,
                    'to_city_id' => $cartRoute->dropoff_address['city_id'] ?? null,
                    'to_state_id' => $cartRoute->dropoff_address['state_id'] ?? null,
                    'to_zone_id' => $cartRoute->dropoff_address['zone_id'] ?? null,
                    'leg_type' => 'dropoff',
                    'leg_order' => 2,
                    'distance' => $legs[1]['distance_km'] ?? $legs[1]['price']['distance_km'] ?? null,
                    'cost' => $legBCost,
                    'pickup_company_id' => $cartRoute->pickup_company_id ?? null,
                    'dropoff_company_id' => $cartRoute->dropoff_company_id ?? null,
                ]);
            } else {
                // Fallback for split without proper legs
                $this->createBasicOrderRoute($cartItem, $orderItem, 'split');
            }
        } else {
            // For direct shipment
            OrderItemRoute::create([
                'order_item_id' => $orderItem->id,
                'from_address' => $cartRoute->pickup_address,
                'from_latitude' => $cartRoute->pickup_address['latitude'] ?? null,
                'from_longitude' => $cartRoute->pickup_address['longitude'] ?? null,
                'from_city_id' => $cartRoute->pickup_address['city_id'] ?? null,
                'from_state_id' => $cartRoute->pickup_address['state_id'] ?? null,
                'from_zone_id' => $cartRoute->pickup_address['zone_id'] ?? null,
                'to_address' => $cartRoute->dropoff_address,
                'to_latitude' => $cartRoute->dropoff_address['latitude'] ?? null,
                'to_longitude' => $cartRoute->dropoff_address['longitude'] ?? null,
                'to_city_id' => $cartRoute->dropoff_address['city_id'] ?? null,
                'to_state_id' => $cartRoute->dropoff_address['state_id'] ?? null,
                'to_zone_id' => $cartRoute->dropoff_address['zone_id'] ?? null,
                'leg_type' => 'direct',
                'leg_order' => 1,
                'distance' => $cartRoute->legs[0]['distance_km'] ?? $cartRoute->legs[0]['price']['distance_km'] ?? null,
                'cost' => $cost,
                'pickup_company_id' => $cartRoute->pickup_company_id ?? null,
                'dropoff_company_id' => $cartRoute->dropoff_company_id ?? null,
            ]);
        }
    }
    private function extractCostFromCartRoute($cartRoute, $cartItem)
    {
        // Try multiple ways to get cost
        if (isset($cartRoute->total_cost) && $cartRoute->total_cost !== null) {
            return (float) $cartRoute->total_cost;
        }

        if (isset($cartRoute->legs) && is_array($cartRoute->legs) && count($cartRoute->legs) > 0) {
            $leg = $cartRoute->legs[0];
            if (isset($leg['client_total'])) {
                return (float) $leg['client_total'];
            }
            if (isset($leg['price']['client_total'])) {
                return (float) $leg['price']['client_total'];
            }
            if (isset($leg['total'])) {
                return (float) $leg['total'];
            }
        }

        // Fallback to cart item est_price
        return (float) $cartItem->est_price;
    }

    /**
     * Extract cost from leg
     */
    private function extractLegCost($leg)
    {
        if (isset($leg['client_total'])) {
            return (float) $leg['client_total'];
        }
        if (isset($leg['price']['client_total'])) {
            return (float) $leg['price']['client_total'];
        }
        if (isset($leg['total'])) {
            return (float) $leg['total'];
        }
        return 0.00;
    }

    /**
     * Create basic order route from package addresses
     */
    private function createBasicOrderRoute(CartItem $cartItem, OrderItem $orderItem, $type = 'direct'): void
    {
        $package = $cartItem->package;

        if (!$package) {
            return;
        }

        $pickupAddress = $package->pickupAddress;
        $dropoffAddress = $package->dropoffAddress;

        if (!$pickupAddress || !$dropoffAddress) {
            return;
        }

        OrderItemRoute::create([
            'order_item_id' => $orderItem->id,
            'from_address' => [
                'address' => $pickupAddress->address,
                'phone' => $pickupAddress->phone,
                'city_id' => $pickupAddress->city_id,
                'state_id' => $pickupAddress->state_id,
                'zone_id' => $pickupAddress->zone_id,
                'latitude' => $pickupAddress->latitude,
                'longitude' => $pickupAddress->longitude,
            ],
            'from_latitude' => $pickupAddress->latitude,
            'from_longitude' => $pickupAddress->longitude,
            'from_city_id' => $pickupAddress->city_id,
            'from_state_id' => $pickupAddress->state_id,
            'from_zone_id' => $pickupAddress->zone_id,
            'to_address' => [
                'address' => $dropoffAddress->address,
                'phone' => $dropoffAddress->phone,
                'city_id' => $dropoffAddress->city_id,
                'state_id' => $dropoffAddress->state_id,
                'zone_id' => $dropoffAddress->zone_id,
                'latitude' => $dropoffAddress->latitude,
                'longitude' => $dropoffAddress->longitude,
            ],
            'to_latitude' => $dropoffAddress->latitude,
            'to_longitude' => $dropoffAddress->longitude,
            'to_city_id' => $dropoffAddress->city_id,
            'to_state_id' => $dropoffAddress->state_id,
            'to_zone_id' => $dropoffAddress->zone_id,
            'leg_type' => $type === 'split' ? 'pickup' : 'direct',
            'leg_order' => 1,
            'distance' => null,
            'cost' => (float) $cartItem->est_price,
        ]);
    }
    /**
     * Update cart totals after removing checked out items
     */
    private function updateCartTotalsAfterCheckout(Cart $cart, $removedItems): void
    {
        // Get remaining cart items
        $remainingItems = CartItem::where('cart_id', $cart->id)->get();

        $cart->update([
            'items_count' => $remainingItems->count(),
            'item_total_price' => $remainingItems->sum('est_price')
        ]);

        // If cart is empty after checkout, delete it
        if ($remainingItems->isEmpty()) {
            $cart->delete();
        }
    }

    /**
     * Update cart totals (existing method)
     */
    private function updateCartTotals(Cart $cart): void
    {
        $totalItems = $cart->items()->count();
        $totalPrice = $cart->items()->sum('est_price');

        $cart->update([
            'items_count' => $totalItems,
            'item_total_price' => $totalPrice
        ]);
    }

/**
 * Generate unique order number
 */
private function generateOrderNumber(): string
{
    return 'ORD' . date('Ymd') . strtoupper(Str::random(5));
}

/**
 * Create order item for regular (non-split) shipment
 */
private function createRegularOrderItem(Order $order, CartItem $cartItem): void
{
    $package = $cartItem->package;

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'package_id' => $package->id,
        'shipment_company_id' => $cartItem->shipment_company_id,
        'est_date' => $cartItem->est_date,
        'est_price' => $cartItem->est_price,
        'status' => 'pending',
        'requires_split' => false,
    ]);

    // Create route for the order item
    if ($cartItem->route) {
        OrderItemRoute::create([
            'order_item_id' => $orderItem->id,
            'pickup_company_id' => $cartItem->route->pickup_company_id,
            'dropoff_company_id' => $cartItem->route->dropoff_company_id,
            'pickup_address' => $cartItem->route->pickup_address,
            'dropoff_address' => $cartItem->route->dropoff_address,
            'is_split' => false,
            'legs' => $cartItem->route->legs,
            'total_cost' => $cartItem->route->total_cost,
            'handoff_point' => $cartItem->route->handoff_point,
        ]);
    }
}

/**
 * Create order items for split shipment
 */
private function createSplitOrderItems(Order $order, CartItem $cartItem): void
{
    $package = $cartItem->package;
    $route = $cartItem->route;

    if (!$route || !$route->is_split || empty($route->legs) || count($route->legs) < 2) {
        throw new \Exception('Invalid split shipment data for cart item: ' . $cartItem->id);
    }

    // Create main order item (parent)
    $parentOrderItem = OrderItem::create([
        'order_id' => $order->id,
        'package_id' => $package->id,
        'shipment_company_id' => null, // No single company for split shipments
        'est_date' => $cartItem->est_date,
        'est_price' => $cartItem->est_price,
        'status' => 'pending',
        'requires_split' => true,
    ]);

    // Create route for the parent item
    OrderItemRoute::create([
        'order_item_id' => $parentOrderItem->id,
        'pickup_company_id' => $route->pickup_company_id,
        'dropoff_company_id' => $route->dropoff_company_id,
        'pickup_address' => $route->pickup_address,
        'dropoff_address' => $route->dropoff_address,
        'is_split' => true,
        'legs' => $route->legs,
        'total_cost' => $route->total_cost,
        'handoff_point' => $route->handoff_point,
    ]);

    // Create child order items for each leg
    $legs = $route->legs;

    // First leg (pickup to handoff)
    $firstLegItem = OrderItem::create([
        'order_id' => $order->id,
        'package_id' => $package->id,
        'shipment_company_id' => $route->pickup_company_id,
        'parent_id' => $parentOrderItem->id,
        'est_date' => $cartItem->est_date,
        'est_price' => $legs[0]['total'] ?? 0,
        'status' => 'pending',
        'requires_split' => false,
        'is_split_leg' => true,
        'leg_index' => 0,
    ]);

    // Second leg (handoff to dropoff)
    $secondLegItem = OrderItem::create([
        'order_id' => $order->id,
        'package_id' => $package->id,
        'shipment_company_id' => $route->dropoff_company_id,
        'parent_id' => $parentOrderItem->id,
        'est_date' => $cartItem->est_date,
        'est_price' => $legs[1]['total'] ?? 0,
        'status' => 'pending',
        'requires_split' => false,
        'is_split_leg' => true,
        'leg_index' => 1,
    ]);

    // Create routes for child items if needed
    $this->createChildItemRoute($firstLegItem, $route, 0);
    $this->createChildItemRoute($secondLegItem, $route, 1);
}

/**
 * Create route for child order item in split shipment
 */
private function createChildItemRoute(OrderItem $orderItem, CartItemRoute $route, int $legIndex): void
{
    $legs = $route->legs;

    if (!isset($legs[$legIndex])) {
        return;
    }

    $leg = $legs[$legIndex];

    // Determine addresses for this leg
    if ($legIndex === 0) {
        // First leg: pickup to handoff
        $pickupAddress = $route->pickup_address;
        $dropoffAddress = $route->handoff_point ?? $route->pickup_address;
        $pickupCompanyId = $route->pickup_company_id;
        $dropoffCompanyId = $route->pickup_company_id; // Same company for this leg
    } else {
        // Second leg: handoff to dropoff
        $pickupAddress = $route->handoff_point ?? $route->dropoff_address;
        $dropoffAddress = $route->dropoff_address;
        $pickupCompanyId = $route->dropoff_company_id;
        $dropoffCompanyId = $route->dropoff_company_id; // Same company for this leg
    }

    OrderItemRoute::create([
        'order_item_id' => $orderItem->id,
        'pickup_company_id' => $pickupCompanyId,
        'dropoff_company_id' => $dropoffCompanyId,
        'pickup_address' => $pickupAddress,
        'dropoff_address' => $dropoffAddress,
        'is_split' => false,
        'legs' => [$leg], // Single leg for child item
        'total_cost' => $leg['total'] ?? 0,
        'handoff_point' => null, // No handoff for individual legs
    ]);
}

    /**
     * Create split order items (parent-child structure)
     */
    // protected function createSplitOrderItems(Order $order, $cartItem)
    // {
    //     $route = $cartItem->route;
    //     $legs = $route->legs ?? [];

    //     if (count($legs) < 2) {
    //         throw new \Exception("Split delivery requires at least 2 legs");
    //     }

    //     $pickupLeg = $legs[0];
    //     $dropoffLeg = $legs[1];
    //     $handoffPoint = $route->handoff_point ?? null;
    //     // dd($handoffPoint);

    //     // Create parent order item (for tracking purposes)
    //     $parentOrderItem = OrderItem::create([
    //         'order_id' => $order->id,
    //         'item_number' => Str::upper(Str::random(7)),
    //         'package_id' => $cartItem->package_id,
    //         'shipment_company_id' => null, // Parent has no single company
    //         'est_date' => $cartItem->est_date,
    //         'est_price' => $cartItem->est_price,
    //         'is_split' => true,
    //         'parent_id' => null,
    //     ]);

    //     // Create first leg (pickup to handoff)
    //     $pickupOrderItem = OrderItem::create([
    //         'order_id' => $order->id,
    //         'item_number' => Str::upper(Str::random(7)),
    //         'package_id' => $cartItem->package_id,
    //         'shipment_company_id' => $pickupLeg['company_id'],
    //         'est_date' => $cartItem->est_date,
    //         'est_price' => $pickupLeg['cost'] ?? 0,
    //         'is_split' => true,
    //         'parent_id' => $parentOrderItem->id,
    //     ]);

    //     // Create order item route for pickup leg
    //     OrderItemRoute::create([
    //         'order_item_id' => $pickupOrderItem->id,
    //         'from_address' => $route->pickup_address,
    //         'to_address' => $handoffPoint['handoff'] ?? null,
    //         'from_latitude' => $route->pickup_address['latitude'] ?? null,
    //         'from_longitude' => $route->pickup_address['longitude'] ?? null,
    //         'to_latitude' => $handoffPoint['handoff']['lat'] ?? null,
    //         'to_longitude' => $handoffPoint['handoff']['lng'] ?? null,
    //         'from_city_id' => $route->pickup_address['city_id'] ?? null,
    //         'from_state_id' => $route->pickup_address['state_id'] ?? null,
    //         'from_zone_id' => $route->pickup_address['zone_id'] ?? null,
    //         'to_city_id' => $handoffPoint['handoff']['city']['id'] ?? null,
    //         'to_state_id' => $handoffPoint['handoff']['state']['id'] ?? null,
    //         'to_zone_id' => $handoffPoint['handoff']['zone']['id'] ?? null,
    //         'leg_type' => 'pickup',
    //         'leg_order' => 1,
    //         'distance' => $pickupLeg['distance_km'] ?? null,
    //         'cost' => $pickupLeg['cost'] ?? 0,
    //     ]);

    //     // Initial tracking for pickup leg
    //     PackageTrackingService::createStatus(
    //         packageId: $cartItem->package_id,
    //         orderItemId: $pickupOrderItem->id,
    //         status: OrderStatus::PENDING,
    //         location: $route->pickup_address['address'] ?? null,
    //         description: 'Order placed - Pickup leg',
    //     );

    //     // Create second leg (handoff to dropoff)
    //     $dropoffOrderItem = OrderItem::create([
    //         'order_id' => $order->id,
    //         'item_number' => Str::upper(Str::random(7)),
    //         'package_id' => $cartItem->package_id,
    //         'shipment_company_id' => $dropoffLeg['company_id'],
    //         'est_date' => $cartItem->est_date,
    //         'est_price' => $dropoffLeg['cost'] ?? 0,
    //         'is_split' => true,
    //         'parent_id' => $parentOrderItem->id,
    //     ]);

    //     // Create order item route for dropoff leg
    //     OrderItemRoute::create([
    //         'order_item_id' => $dropoffOrderItem->id,
    //         'from_address' => $handoffPoint['handoff'] ?? null,
    //         'to_address' => $route->dropoff_address,
    //         'from_latitude' => $handoffPoint['handoff']['lat'] ?? null,
    //         'from_longitude' => $handoffPoint['handoff']['lng'] ?? null,
    //         'to_latitude' => $route->dropoff_address['latitude'] ?? null,
    //         'to_longitude' => $route->dropoff_address['longitude'] ?? null,
    //         'from_city_id' => $handoffPoint['handoff']['city']['id'] ?? null,
    //         'from_state_id' => $handoffPoint['handoff']['state']['id'] ?? null,
    //         'from_zone_id' => $handoffPoint['handoff']['zone']['id'] ?? null,
    //         'to_city_id' => $route->dropoff_address['city_id'] ?? null,
    //         'to_state_id' => $route->dropoff_address['state_id'] ?? null,
    //         'to_zone_id' => $route->dropoff_address['zone_id'] ?? null,
    //         'leg_type' => 'dropoff',
    //         'leg_order' => 2,
    //         'distance' => $dropoffLeg['distance_km'] ?? null,
    //         'cost' => $dropoffLeg['cost'] ?? 0,
    //     ]);

    //     // Initial tracking for dropoff leg (pending until pickup is complete)
    //     PackageTrackingService::createStatus(
    //         packageId: $cartItem->package_id,
    //         orderItemId: $dropoffOrderItem->id,
    //         status: OrderStatus::PENDING,
    //         location: $handoffPoint['handoff']['address'] ?? 'Handoff location',
    //         description: 'Awaiting pickup completion',
    //     );
    // }

    // /**
    //  * Create regular order item (no split)
    //  */
    // protected function createRegularOrderItem(Order $order, $cartItem)
    // {
    //     $orderItem = OrderItem::create([
    //         'order_id' => $order->id,
    //         'item_number' => Str::upper(Str::random(7)),
    //         'package_id' => $cartItem->package_id,
    //         'shipment_company_id' => $cartItem->shipment_company_id,
    //         'est_date' => $cartItem->est_date,
    //         'est_price' => $cartItem->est_price,
    //         'is_split' => false,
    //         'parent_id' => null,
    //     ]);

    //     // Create route if exists
    //     if ($cartItem->route) {
    //         $route = $cartItem->route;
    //         OrderItemRoute::create([
    //             'order_item_id' => $orderItem->id,
    //             'from_address' => $route->pickup_address,
    //             'to_address' => $route->dropoff_address,
    //             'from_latitude' => $route->pickup_address['latitude'] ?? null,
    //             'from_longitude' => $route->pickup_address['longitude'] ?? null,
    //             'to_latitude' => $route->dropoff_address['latitude'] ?? null,
    //             'to_longitude' => $route->dropoff_address['longitude'] ?? null,
    //             'from_city_id' => $route->pickup_address['city_id'] ?? null,
    //             'from_state_id' => $route->pickup_address['state_id'] ?? null,
    //             'from_zone_id' => $route->pickup_address['zone_id'] ?? null,
    //             'to_city_id' => $route->dropoff_address['city_id'] ?? null,
    //             'to_state_id' => $route->dropoff_address['state_id'] ?? null,
    //             'to_zone_id' => $route->dropoff_address['zone_id'] ?? null,
    //             'leg_type' => 'direct',
    //             'leg_order' => 1,
    //             'distance' => $route->legs[0]['distance_km'] ?? null,
    //             'cost' => $cartItem->est_price,
    //         ]);
    //     }

    //     // Initial tracking
    //     PackageTrackingService::createStatus(
    //         packageId: $cartItem->package_id,
    //         orderItemId: $orderItem->id,
    //         status: OrderStatus::PENDING,
    //         location: $cartItem->package->pickupAddress->address ?? null,
    //         description: 'Order placed',
    //     );
    // }

    // // Checkout directly from a single package (no cart)
    public function directPreview(DirectCheckoutRequest $request)
    {
        $validated = $request->validated();

        $package = isset($validated['package_id'])
            ? Package::with('shipmentCompany')->findOrFail($validated['package_id'])
            : PackageService::createFromPayload($validated, $request);
        $subtotal = (float) $validated['est_price'];
        $discount = 0.0;
        $promoCode = null;

        // Handle promo code if provided
        if (isset($validated['promo_code']) && !empty($validated['promo_code'])) {
            $promoCodeService = new PromoCodeService();
            $result = $promoCodeService->validateAndApply($validated['promo_code'], $subtotal, auth()->id());

            if ($result['valid']) {
                $discount = $result['discount'];
                $promoCode = $result['promo_code']->code;
            } else {
                return responseJson(false, $result['message'], null, 422);
            }
        }

        $total = max($subtotal - $discount, 0);

        return responseJson(true, 'Direct checkout preview', [
            'order_number' => Str::upper(Str::random(7)),
            'country_code' => auth()->user()->country_code,
            'phone' => auth()->user()->phone,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            // 'promo_code' => $promoCode,
            'package' => $package,
        ]);
    }
    // 2️⃣ فانكشن إنشاء الأوردر مباشرة (بدون دفع) - بدون كارت
    public function createDirect(DirectCheckoutRequest $request)
    {
        try {
            $validated = $request->validated();

            // 1️⃣ Create package first
            $package = PackageService::createFromPayload($validated, $request);

            if (empty($validated['shipment_company_id'])) {
                return $this->createOrderWithoutCompany($validated, $package);
            }


            // 2️⃣ Prepare pickup/dropoff data
            $pickup = [
                'latitude' => (float)$validated['pickup_address']['latitude'],
                'longitude' => (float)$validated['pickup_address']['longitude'],
                'country_id' => $validated['pickup_address']['country_id'] ?? null,
                'state_id' => $validated['pickup_address']['state_id'],
                'city_id' => $validated['pickup_address']['city_id'],
                'zone_id' => $validated['pickup_address']['zone_id'],
                'location' => $validated['pickup_address']['location'] ?? null,
                'address' => $validated['pickup_address']['address'],
            ];

            $dropoff = [
                'latitude' => (float)$validated['dropoff_address']['latitude'],
                'longitude' => (float)$validated['dropoff_address']['longitude'],
                'country_id' => $validated['dropoff_address']['country_id'] ?? null,
                'state_id' => $validated['dropoff_address']['state_id'],
                'city_id' => $validated['dropoff_address']['city_id'],
                'zone_id' => $validated['dropoff_address']['zone_id'],
                'location' => $validated['dropoff_address']['location'] ?? null,
                'address' => $validated['dropoff_address']['address'],
            ];

            $companyId = $validated['shipment_company_id'];

            // 3️⃣ Check coverage using the service
            $coverageAnalysis = $this->coverageService->checkCompanyCoverage($companyId, $pickup, $dropoff);

            // 4️⃣ If service returned error
            if (!$coverageAnalysis['success']) {
                return responseJson(false, $coverageAnalysis['message'], [
                    'coverage_status' => $coverageAnalysis['data']['coverage_status'] ?? 'error',
                ], 422);
            }

            $coverageStatus = $coverageAnalysis['data']['coverage_status'];

            // 5️⃣ Handle full_coverage → Create single order directly
            if ($coverageStatus === 'full_coverage') {
                return $this->createFullCoverageOrder($validated, $package, $coverageAnalysis);
            }

            // 6️⃣ Handle split_via_shared_location
            if ($coverageStatus === 'split_via_shared_location') {
                $suggestions = $coverageAnalysis['data']['suggestions'] ?? [];

                // Check if user sent split data
                $splitData = $validated['split'] ?? null;

                // If no split data → return error with suggestions
                if (!$splitData || empty($splitData['accept'])) {
                    return responseJson(false, 'This delivery requires split. Please select a suggestion and accept.', [
                        'coverage_status' => 'split_required',
                        'requires_split' => true,
                        'suggestions' => $suggestions,
                    ], 200);
                }

                // If split declined
                if ($splitData['accept'] != 1 && $splitData['accept'] !== true) {
                    return responseJson(false, 'Split delivery is required but was declined', null, 422);
                }

                // Get selected suggestion
                $selectedIndex = $splitData['selected_suggestion_index'] ?? 0;

                if (!isset($suggestions[$selectedIndex])) {
                    return responseJson(false, 'Invalid suggestion index', [
                        'total_suggestions' => count($suggestions),
                        'selected_index' => $selectedIndex,
                    ], 422);
                }

                $selectedSuggestion = $suggestions[$selectedIndex];

                // Create split order
                return $this->createSplitCoverageOrder($validated, $package, $selectedSuggestion, $pickup, $dropoff);
            }

            // 7️⃣ Handle partial or no coverage
            return responseJson(false, 'Selected company cannot deliver to this location', [
                'coverage_status' => $coverageStatus,
            ], 422);
        } catch (\Exception $e) {
            return responseJson(false, 'Failed to place order', $e->getMessage(), 500);
        }
    }

    /**
     * Create order with full coverage (no split)
     */
    protected function createFullCoverageOrder(array $validated, $package, array $coverageAnalysis)
    {
        $leg = $coverageAnalysis['data']['suggestions'][0]['pickup_leg'];
        $company = ShipmentCompany::findOrFail($validated['shipment_company_id']);

        // Calculate prices
        $subtotal = $leg['cost'];
        $discount = 0.0;
        $promoCodeId = null;

        // Apply promo code if provided
        if (isset($validated['promo_code']) && !empty($validated['promo_code'])) {
            $promoCodeService = new PromoCodeService();
            $result = $promoCodeService->validateAndApply($validated['promo_code'], $subtotal, auth()->id());

            if ($result['valid']) {
                $discount = $result['discount'];
                $promoCodeId = $result['promo_code']->id;
            } else {
                return responseJson(false, $result['message'], null, 422);
            }
        }

        $total = max($subtotal - $discount, 0);

        // Create order (بدون دفع)
        $order = Order::create([
            'user_id' => auth()->id(),
            'shipment_company_id' => $company->id,
            'order_number' =>null,
            'total_price' => $subtotal,
            'paid_amount' => 0,
            'remaining_amount' => $total,
            'discount_price' => $discount,
            'final_price' => $total,
            'status' => OrderStatus::PENDING,
        ]);

        // Create order item
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'item_number' => Str::upper(Str::random(7)),
            'package_id' => $package->id,
            'shipment_company_id' => $company->id,
            'est_date' => $validated['est_date'] ?? null,
            'est_price' => $leg['cost'],
            'is_split' => false,
            'parent_id' => null,
        ]);

        // Create route
        OrderItemRoute::create([
            'order_item_id' => $orderItem->id,
            'from_address' => $validated['pickup_address'],
            'to_address' => $validated['dropoff_address'],
            'from_latitude' => $validated['pickup_address']['latitude'] ?? null,
            'from_longitude' => $validated['pickup_address']['longitude'] ?? null,
            'to_latitude' => $validated['dropoff_address']['latitude'] ?? null,
            'to_longitude' => $validated['dropoff_address']['longitude'] ?? null,
            'from_city_id' => $validated['pickup_address']['city_id'] ?? null,
            'from_state_id' => $validated['pickup_address']['state_id'] ?? null,
            'from_zone_id' => $validated['pickup_address']['zone_id'] ?? null,
            'to_city_id' => $validated['dropoff_address']['city_id'] ?? null,
            'to_state_id' => $validated['dropoff_address']['state_id'] ?? null,
            'to_zone_id' => $validated['dropoff_address']['zone_id'] ?? null,
            'leg_type' => 'direct',
            'leg_order' => 1,
            'distance' => $leg['distance_km'] ?? null,
            'cost' => $leg['cost'],
        ]);

        // Initial tracking
        PackageTrackingService::createStatus(
            packageId: $package->id,
            orderItemId: $orderItem->id,
            status: OrderStatus::PENDING,
            location: $validated['pickup_address']['address'] ?? null,
            description: 'Order created',
        );

        return responseJson(true, 'Order created successfully', [
            'order' => $order->load('orderItems'),
            'coverage_type' => 'full_coverage',
        ]);
    }

    /**
     * Create order with split coverage
     */
    protected function createSplitCoverageOrder(array $validated, $package, array $selectedSuggestion, array $pickup, array $dropoff)
    {
        // Calculate prices
        $subtotal = $selectedSuggestion['total_cost'];
        $discount = 0.0;
        $promoCodeId = null;

        // Apply promo code if provided
        if (isset($validated['promo_code']) && !empty($validated['promo_code'])) {
            $promoCodeService = new PromoCodeService();
            $result = $promoCodeService->validateAndApply($validated['promo_code'], $subtotal, auth()->id());

            if ($result['valid']) {
                $discount = $result['discount'];
                $promoCodeId = $result['promo_code']->id;
            } else {
                return responseJson(false, $result['message'], null, 422);
            }
        }

        $total = max($subtotal - $discount, 0);

        // Create main order (بدون دفع)
        $order = Order::create([
            'user_id' => auth()->id(),
            'shipment_company_id' => null, // No single company for split
            'order_number' => null,
            'total_price' => $subtotal,
            'paid_amount' => 0,
            'remaining_amount' => $total,
            'discount_price' => $discount,
            'final_price' => $total,
            'status' => OrderStatus::PENDING,
        ]);

        // Create parent order item (for tracking)
        $parentOrderItem = OrderItem::create([
            'order_id' => $order->id,
            'item_number' => Str::upper(Str::random(7)),
            'package_id' => $package->id,
            'shipment_company_id' => null,
            'est_date' => $validated['est_date'] ?? null,
            'est_price' => $selectedSuggestion['total_cost'],
            'is_split' => true,
            'parent_id' => null,
        ]);

        $pickupLeg = $selectedSuggestion['pickup_leg'];
        $dropoffLeg = $selectedSuggestion['dropoff_leg'];
        $handoffPoint = $selectedSuggestion['handoff'] ?? null;

        // Create first leg (pickup to handoff)
        $pickupOrderItem = OrderItem::create([
            'order_id' => $order->id,
            'item_number' => Str::upper(Str::random(7)),
            'package_id' => $package->id,
            'shipment_company_id' => $pickupLeg['company_id'],
            'est_date' => $validated['est_date'] ?? null,
            'est_price' => $pickupLeg['cost'] ?? 0,
            'is_split' => true,
            'parent_id' => $parentOrderItem->id,
        ]);

        // Create route for pickup leg
        OrderItemRoute::create([
            'order_item_id' => $pickupOrderItem->id,
            'from_address' => $validated['pickup_address'],
            'to_address' => $handoffPoint,
            'from_latitude' => $pickup['latitude'] ?? null,
            'from_longitude' => $pickup['longitude'] ?? null,
            'to_latitude' => $handoffPoint['lat'] ?? null,
            'to_longitude' => $handoffPoint['lng'] ?? null,
            'from_city_id' => $pickup['city_id'] ?? null,
            'from_state_id' => $pickup['state_id'] ?? null,
            'from_zone_id' => $pickup['zone_id'] ?? null,
            'to_city_id' => $handoffPoint['city']['id'] ?? null,
            'to_state_id' => $handoffPoint['state']['id'] ?? null,
            'to_zone_id' => $handoffPoint['zone']['id'] ?? null,
            'leg_type' => 'pickup',
            'leg_order' => 1,
            'distance' => $pickupLeg['distance_km'] ?? null,
            'cost' => $pickupLeg['cost'] ?? 0,
        ]);

        // Initial tracking for pickup leg
        PackageTrackingService::createStatus(
            packageId: $package->id,
            orderItemId: $pickupOrderItem->id,
            status: OrderStatus::PENDING,
            location: $validated['pickup_address']['address'] ?? null,
            description: 'Order placed - Pickup leg',
        );

        // Create second leg (handoff to dropoff)
        $dropoffOrderItem = OrderItem::create([
            'order_id' => $order->id,
            'item_number' => Str::upper(Str::random(7)),
            'package_id' => $package->id,
            'shipment_company_id' => $dropoffLeg['company_id'],
            'est_date' => $validated['est_date'] ?? null,
            'est_price' => $dropoffLeg['cost'] ?? 0,
            'is_split' => true,
            'parent_id' => $parentOrderItem->id,
        ]);

        // Create route for dropoff leg
        OrderItemRoute::create([
            'order_item_id' => $dropoffOrderItem->id,
            'from_address' => $handoffPoint,
            'to_address' => $validated['dropoff_address'],
            'from_latitude' => $handoffPoint['lat'] ?? null,
            'from_longitude' => $handoffPoint['lng'] ?? null,
            'to_latitude' => $dropoff['latitude'] ?? null,
            'to_longitude' => $dropoff['longitude'] ?? null,
            'from_city_id' => $handoffPoint['city']['id'] ?? null,
            'from_state_id' => $handoffPoint['state']['id'] ?? null,
            'from_zone_id' => $handoffPoint['zone']['id'] ?? null,
            'to_city_id' => $dropoff['city_id'] ?? null,
            'to_state_id' => $dropoff['state_id'] ?? null,
            'to_zone_id' => $dropoff['zone_id'] ?? null,
            'leg_type' => 'dropoff',
            'leg_order' => 2,
            'distance' => $dropoffLeg['distance_km'] ?? null,
            'cost' => $dropoffLeg['cost'] ?? 0,
        ]);

        // Initial tracking for dropoff leg
        PackageTrackingService::createStatus(
            packageId: $package->id,
            orderItemId: $dropoffOrderItem->id,
            status: OrderStatus::PENDING,
            location: $handoffPoint['address'] ?? 'Handoff location',
            description: 'Awaiting pickup completion',
        );

        return responseJson(true, 'Order created successfully with split delivery', [
            'order' => $order->load('orderItems.childItems'),
            'coverage_type' => 'split_via_shared_location',
        ]);
    }
    protected function createOrderWithoutCompany(array $validated, $package)
    {
        // Create order بدون شركة شحن
        $order = Order::create([
            'user_id' => auth()->id(),
            'shipment_company_id' => null,
            'order_number' => null,
            'total_price' => 0,
            'paid_amount' => 0,
            'remaining_amount' => 0,
            'discount_price' => 0,
            'final_price' => 0,
            'status' => OrderStatus::PENDING,
        ]);

        // Create item بسيط مربوط بالبكج
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'item_number' => Str::upper(Str::random(7)),
            'package_id' => $package->id,
            'shipment_company_id' => null,
            'is_split' => false,
            'parent_id' => null,
        ]);

        // إنشاء تتبع مبدئي
        PackageTrackingService::createStatus(
            packageId: $package->id,
            orderItemId: $orderItem->id,
            status: OrderStatus::PENDING,
            location: $validated['pickup_address']['address'] ?? null,
            description: 'Order created without shipment company',
        );

        return responseJson(true, 'Order created successfully without shipment company', [
            'order' => $order->load('orderItems'),
            'coverage_type' => 'no_company',
        ]);
    }


    // 3️⃣ فانكشن الـ Checkout (للدفع وتطبيق البرومو كود)
    public function checkout(Request $request, $orderId)
    {
        try {
            $request->validate([
                'payment_method' => 'required|in:full,partial',
                'paid_amount'    => 'required_if:payment_method,partial|numeric|min:0',
                'promo_code'     => 'nullable|string',
            ]);

            $user = auth()->user();
            $order = Order::findOrFail($orderId);

            // تأكد أن الأوردر يخص اليوزر
            if ($order->user_id !== $user->id) {
                return responseJson(false, 'Unauthorized', null, 403);
            }
            if ($order->status !== OrderStatus::ACCEPTED) {
                return responseJson(
                    false,
                    trans('messages.order_must_be_accepted_before_payment'),
                    null,
                    422
                );
            }

            // تأكد أن الأوردر لم يتم الدفع له من قبل
            if ($order->payment_status == PaymentStatus::PAID->value) {
                return responseJson(false, 'Order already Paid', null, 422);
            }

            $subtotal = $order->total_price;
            $discount = $order->discount_price ?? 0;
            $promoCode = null;

            // 1. Handle Promo Code
            if ($request->filled('promo_code')) {
                try {
                    $promoCodeService = new PromoCodeService();
                    $result = $promoCodeService->validateAndApply($request->promo_code, $subtotal, $user->id);

                    if ($result['valid']) {
                        $discount = $result['discount'];
                        $promoCode = $result['promo_code'];
                    } else {
                        return responseJson(false, $result['message'], null, 422);
                    }
                } catch (\Exception $e) {
                    return responseJson(false, $e->getMessage(), null, 422);
                }
            }

            $total = max($subtotal - $discount, 0);

            // 2. Calculate Payment
            if ($request->payment_method === 'full') {
                $paid = $total;
            } else { // partial
                $paid = $request->paid_amount;
            }

            $remaining = $total - $paid;

            // 3. Update Order
            $order->update([
                'discount_price'    => $discount,
                'final_price'       => $total,
                'paid_amount'       => $paid,
                'remaining_amount'  => $remaining,
                'payment_status'    => PaymentStatus::PAID->value
            ]);

            // 4. Create Payment Record
            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => Str::upper(Str::random(7)),
                'payment_method' => $request->payment_method,
                'total_amount' => $subtotal,
                'paid_amount' => $paid,
                'remaining_amount' => $remaining,
                'payment_status' => PaymentStatus::PAID,
                'promo_code_id' => $promoCode?->id ?? null,
                'discount_price' => $discount,
                'final_price' => $total,
            ]);

            // 5. Increase promo code usage
            if ($promoCode) {
                $promoCodeService = new PromoCodeService();
                $promoCodeService->recordUsage($promoCode);
            }

            return responseJson(true, 'Payment processed successfully', $order->fresh(['orderItems.childItems']));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
    // public function directPlace(DirectCheckoutRequest $request)
    // {
    //     $validated = $request->validated();

    //     // Initialize services
    //     $company = \App\Models\ShipmentCompany::findOrFail((int) $validated['shipment_company_id']);
    //     $coverageService = new \App\Services\CoverageService(new \App\Services\GoogleMapsService());

    //     $pickup = $validated['pickup_address'];
    //     $dropoff = $validated['dropoff_address'];

    //     // تحليل شامل للتغطية
    //     $coverageAnalysis = $coverageService->analyzeCoverage($company, $pickup, $dropoff);

    //     // السيناريو 1: الشركة تغطي كل شيء - استمر بشكل طبيعي
    //     if ($coverageAnalysis['can_deliver'] && $coverageAnalysis['coverage_type'] === 'full') {
    //         return $this->createSingleOrder($validated, $request);
    //     }

    //     // السيناريو 2: لا توجد تغطية نهائياً
    //     if (!$coverageAnalysis['can_deliver'] && $coverageAnalysis['coverage_type'] === 'none') {
    //         return responseJson(false, $coverageAnalysis['message'], null, 422);
    //     }

    //     // السيناريو 3: لا توجد تغطية للـ dropoff
    //     if ($coverageAnalysis['coverage_type'] === 'no_dropoff_coverage') {
    //         return responseJson(false, $coverageAnalysis['message'], [
    //             'available_for_pickup' => $coverageAnalysis['available_for_pickup'],
    //         ], 422);
    //     }

    //     // السيناريو 4: لا توجد تغطية للـ dropoff
    //     if ($coverageAnalysis['coverage_type'] === 'no_dropoff_coverage') {
    //         return responseJson(false, $coverageAnalysis['message'], [
    //             'available_for_pickup' => $coverageAnalysis['available_for_pickup'],
    //         ], 422);
    //     }

    //     // السيناريو 5: يحتاج تقسيم
    //     if ($coverageAnalysis['coverage_type'] === 'partial_split_required') {
    //         // إذا المستخدم قبل التقسيم
    //         if (($validated['split']['accept'] ?? false) &&
    //             isset($validated['split']['pickup_company_id'], $validated['split']['dropoff_company_id'])
    //         ) {

    //             return $this->createSplitOrders($validated, $request, $coverageAnalysis);
    //         }

    //         // إرجاع كل الاقتراحات
    //         return responseJson(false, $coverageAnalysis['message'], [
    //             'pickup_covered' => $coverageAnalysis['pickup_covered'],
    //             'dropoff_covered' => $coverageAnalysis['dropoff_covered'],
    //             'available_pickup_companies' => $coverageAnalysis['available_pickup_companies'],
    //             'available_dropoff_companies' => $coverageAnalysis['available_dropoff_companies'],
    //             'suggestions' => $coverageAnalysis['suggestions'],
    //             'best_suggestion' => $coverageAnalysis['suggestions'][0] ?? null,
    //         ], 422);
    //     }

    //     return responseJson(false, 'Unable to process order', null, 422);
    // }

    // /**
    //  * إنشاء طلب واحد عادي (الشركة تغطي كل شيء)
    //  */
    // protected function createSingleOrder(array $validated, $request)
    // {
    //     // Ensure package
    //     $packageId = $validated['package_id'] ?? null;
    //     if (!$packageId) {
    //         $package = PackageService::createFromPayload($validated, $request);
    //         $packageId = $package->id;
    //     }

    //     // Get company and calculate distance-based price
    //     $company = ShipmentCompany::findOrFail((int) $validated['shipment_company_id']);
    //     $mapsService = new \App\Services\GoogleMapsService();

    //     $pickup = $validated['pickup_address'];
    //     $dropoff = $validated['dropoff_address'];

    //     // Calculate distance
    //     $distanceKm = $mapsService->distanceInKm(
    //         (float) $pickup['latitude'],
    //         (float) $pickup['longitude'],
    //         (float) $dropoff['latitude'],
    //         (float) $dropoff['longitude']
    //     );

    //     // Calculate price based on distance
    //     $calculatedPrice = round($distanceKm * (float) ($company->price_per_km ?? 0), 2);

    //     // Use calculated price or provided est_price (whichever is higher for safety)
    //     $subtotal = max($calculatedPrice, (float) ($validated['est_price'] ?? 0));

    //     $discount = 0.0;
    //     $promoCodeId = null;

    //     // Handle promo code
    //     if (isset($validated['promo_code']) && !empty($validated['promo_code'])) {
    //         $promoCodeService = new PromoCodeService();
    //         $result = $promoCodeService->validateAndApply($validated['promo_code'], $subtotal, auth()->id());

    //         if ($result['valid']) {
    //             $discount = $result['discount'];
    //             $promoCodeId = $result['promo_code']->id;
    //         } else {
    //             return responseJson(false, $result['message'], null, 422);
    //         }
    //     }

    //     $total = max($subtotal - $discount, 0);
    //     $paidAmount = $validated['payment_method'] === 'partial'
    //         ? ($validated['partial_amount'] ?? 0)
    //         : $total;

    //     $remainingAmount = $total - $paidAmount;

    //     $order = Order::create([
    //         'user_id' => auth()->id(),
    //         'shipment_company_id' => $validated['shipment_company_id'],
    //         'order_number' => Str::upper(Str::random(7)),
    //         'total_price' => $subtotal,
    //         'paid_amount' => $paidAmount,
    //         'remaining_amount' => $remainingAmount,
    //         'discount_price' => $discount,
    //         'final_price' => $total,
    //         'status' => OrderStatus::PENDING,
    //     ]);

    //     $orderItem = OrderItem::create([
    //         'order_id' => $order->id,
    //         'item_number' => $package->package_number ?? Str::upper(Str::random(7)),
    //         'package_id' => (int) $packageId,
    //         'shipment_company_id' => (int) $validated['shipment_company_id'],
    //         'est_date' => $validated['est_date'] ?? null,
    //         'est_price' => $subtotal,
    //         'distance_km' => round($distanceKm, 2),
    //     ]);

    //     PackageTrackingService::createStatus(
    //         packageId: (int) $packageId,
    //         orderItemId: $orderItem->id,
    //         status: OrderStatus::PENDING,
    //         location: null,
    //         description: 'Order placed',
    //     );

    //     Payment::create([
    //         'order_id' => $order->id,
    //         'transaction_id' => Str::upper(Str::random(7)),
    //         'payment_method' => $validated['payment_method'],
    //         'total_amount' => $subtotal,
    //         'paid_amount' => $paidAmount,
    //         'remaining_amount' => $remainingAmount,
    //         'payment_status' => PaymentStatus::PENDING,
    //         'promo_code_id' => $promoCodeId,
    //         'discount_price' => $discount,
    //         'final_price' => $total,
    //     ]);

    //     if ($promoCodeId) {
    //         $promoCodeService = new PromoCodeService();
    //         $promoCode = $promoCodeService->getByCode($validated['promo_code']);
    //         if ($promoCode) {
    //             $promoCodeService->recordUsage($promoCode);
    //         }
    //     }

    //     return responseJson(true, 'Order created successfully', [
    //         'order' => $order->load('orderItems'),
    //         'pricing_details' => [
    //             'distance_km' => round($distanceKm, 2),
    //             'price_per_km' => (float) $company->price_per_km,
    //             'calculated_price' => $calculatedPrice,
    //             'final_price' => $total,
    //         ],
    //     ]);
    // }

    // /**
    //  * إنشاء طلبات مقسمة (parent + 2 children)
    //  */
    // protected function createSplitOrders(array $validated, $request, array $coverageAnalysis)
    // {
    //     $coverageService = new \App\Services\CoverageService(new \App\Services\GoogleMapsService());

    //     // البحث عن الاقتراح المطابق
    //     $selectedSuggestion = null;
    //     foreach ($coverageAnalysis['suggestions'] as $suggestion) {
    //         if (
    //             $suggestion['pickup_leg']['company_id'] == $validated['split']['pickup_company_id'] &&
    //             $suggestion['dropoff_leg']['company_id'] == $validated['split']['dropoff_company_id']
    //         ) {
    //             $selectedSuggestion = $suggestion;
    //             break;
    //         }
    //     }

    //     if (!$selectedSuggestion) {
    //         return responseJson(false, 'Invalid split companies selected', null, 422);
    //     }

    //     // Ensure package
    //     $packageId = $validated['package_id'] ?? null;
    //     if (!$packageId) {
    //         $package = PackageService::createFromPayload($validated, $request);
    //         $packageId = $package->id;
    //     }

    //     $handoff = $validated['split']['handoff'] ?? $selectedSuggestion['handoff_point'];
    //     $subtotal = $selectedSuggestion['total_cost'];
    //     $discount = 0.0;

    //     $total = max($subtotal - $discount, 0);
    //     $paidAmount = $validated['payment_method'] === 'partial'
    //         ? ($validated['partial_amount'] ?? 0)
    //         : $total;

    //     // Create parent order
    //     $parentOrder = Order::create([
    //         'user_id' => auth()->id(),
    //         'shipment_company_id' => null,
    //         'order_number' => Str::upper(Str::random(7)),
    //         'total_price' => $subtotal,
    //         'paid_amount' => $paidAmount,
    //         'remaining_amount' => $total - $paidAmount,
    //         'discount_price' => $discount,
    //         'final_price' => $total,
    //         'status' => OrderStatus::PENDING,
    //     ]);

    //     // Child 1: pickup to handoff
    //     $order1 = Order::create([
    //         'user_id' => auth()->id(),
    //         'parent_id' => $parentOrder->id,
    //         'shipment_company_id' => $selectedSuggestion['pickup_leg']['company_id'],
    //         'order_number' => Str::upper(Str::random(7)),
    //         'total_price' => $selectedSuggestion['pickup_leg']['cost'],
    //         'paid_amount' => 0,
    //         'remaining_amount' => $selectedSuggestion['pickup_leg']['cost'],
    //         'discount_price' => 0,
    //         'final_price' => $selectedSuggestion['pickup_leg']['cost'],
    //         'status' => OrderStatus::PENDING,
    //     ]);

    //     // Child 2: handoff to dropoff
    //     $order2 = Order::create([
    //         'user_id' => auth()->id(),
    //         'parent_id' => $parentOrder->id,
    //         'shipment_company_id' => $selectedSuggestion['dropoff_leg']['company_id'],
    //         'order_number' => Str::upper(Str::random(7)),
    //         'total_price' => $selectedSuggestion['dropoff_leg']['cost'],
    //         'paid_amount' => 0,
    //         'remaining_amount' => $selectedSuggestion['dropoff_leg']['cost'],
    //         'discount_price' => 0,
    //         'final_price' => $selectedSuggestion['dropoff_leg']['cost'],
    //         'status' => OrderStatus::PENDING,
    //     ]);

    //     // Create order items for each leg
    //     $orderItem1 = OrderItem::create([
    //         'order_id' => $order1->id,
    //         'item_number' => Str::upper(Str::random(7)),
    //         'package_id' => (int) $packageId,
    //         'shipment_company_id' => $selectedSuggestion['pickup_leg']['company_id'],
    //         'est_date' => $validated['est_date'] ?? null,
    //         'est_price' => $selectedSuggestion['pickup_leg']['cost'],
    //     ]);

    //     $orderItem2 = OrderItem::create([
    //         'order_id' => $order2->id,
    //         'item_number' => Str::upper(Str::random(7)),
    //         'package_id' => (int) $packageId,
    //         'shipment_company_id' => $selectedSuggestion['dropoff_leg']['company_id'],
    //         'est_date' => $validated['est_date'] ?? null,
    //         'est_price' => $selectedSuggestion['dropoff_leg']['cost'],
    //     ]);

    //     // Tracking entries
    //     PackageTrackingService::createStatus(
    //         packageId: (int) $packageId,
    //         orderItemId: $orderItem1->id,
    //         status: OrderStatus::PENDING,
    //         location: null,
    //         description: 'Split order created - Pickup leg',
    //     );

    //     Payment::create([
    //         'order_id' => $parentOrder->id,
    //         'transaction_id' => Str::upper(Str::random(7)),
    //         'payment_method' => $validated['payment_method'],
    //         'total_amount' => $subtotal,
    //         'paid_amount' => $paidAmount,
    //         'remaining_amount' => $total - $paidAmount,
    //         'payment_status' => PaymentStatus::PENDING,
    //         'discount_price' => $discount,
    //         'final_price' => $total,
    //     ]);

    //     return responseJson(true, 'Split orders created successfully', [
    //         'parent_order' => $parentOrder,
    //         'pickup_leg_order' => $order1->load('orderItems'),
    //         'dropoff_leg_order' => $order2->load('orderItems'),
    //         'handoff_point' => $handoff,
    //         'selected_suggestion' => $selectedSuggestion,
    //     ]);
    // }
    // public function directPlace(DirectCheckoutRequest $request)
    // {
    //     $validated = $request->validated();

    //     // Coverage check and recommendation for handoff if needed
    //     $company = \App\Models\ShipmentCompany::findOrFail((int) $validated['shipment_company_id']);
    //     $coverageService = new \App\Services\CoverageService(new \App\Services\GoogleMapsService());

    //     $pickup = $validated['pickup_address'];
    //     $dropoff = $validated['dropoff_address'];

    //     $pickupCovered = $coverageService->companyCoversIds(
    //         $company,
    //         $pickup['country_id'] ?? null,
    //         $pickup['state_id'] ?? null,
    //         $pickup['city_id'] ?? null,
    //         $pickup['zone_id'] ?? null,
    //         true,
    //     );

    //     $dropoffCovered = $coverageService->companyCoversIds(
    //         $company,
    //         $dropoff['country_id'] ?? null,
    //         $dropoff['state_id'] ?? null,
    //         $dropoff['city_id'] ?? null,
    //         $dropoff['zone_id'] ?? null,
    //         false,
    //     );
    //     // removed debug dump
    //     if (!($pickupCovered && $dropoffCovered)) {
    //         $split = $coverageService->suggestSplitByMidpoint($pickup, $dropoff);

    //         // If client accepts split and passed companies, create parent+two child orders
    //         if (($validated['split']['accept'] ?? false) && isset($validated['split']['pickup_company_id'], $validated['split']['dropoff_company_id'])) {
    //             $handoff = $validated['split']['handoff'] ?? $split['handoff_point'];

    //             $subtotal = (float) ($validated['est_price'] ?? 0);
    //             $discount = 0.0;
    //             $total = max($subtotal - $discount, 0);

    //             // Create parent order (no company assigned, represents the user-level order)
    //             $parentOrder = Order::create([
    //                 'user_id' => auth()->id(),
    //                 'shipment_company_id' => null,
    //                 'order_number' => Str::upper(Str::random(7)),
    //                 'total_price' => $subtotal,
    //                 'paid_amount' => $validated['partial_amount'] ?? $total,
    //                 'remaining_amount' => isset($validated['partial_amount']) ? $total - $validated['partial_amount'] : 0,
    //                 'discount_price' => $discount,
    //                 'final_price' => $total,
    //                 'status' => OrderStatus::PENDING,
    //             ]);

    //             // Child 1: pickup to handoff
    //             $pickupCompanyId = (int) $validated['split']['pickup_company_id'];
    //             $order1 = Order::create([
    //                 'user_id' => auth()->id(),
    //                 'parent_id' => $parentOrder->id,
    //                 'shipment_company_id' => $pickupCompanyId,
    //                 'order_number' => Str::upper(Str::random(7)),
    //                 'total_price' => (float) ($split['pickup_leg']['cost'] ?? 0),
    //                 'paid_amount' => 0,
    //                 'remaining_amount' => (float) ($split['pickup_leg']['cost'] ?? 0),
    //                 'discount_price' => 0,
    //                 'final_price' => (float) ($split['pickup_leg']['cost'] ?? 0),
    //                 'status' => OrderStatus::PENDING,
    //             ]);

    //             // Child 2: handoff to dropoff
    //             $dropoffCompanyId = (int) $validated['split']['dropoff_company_id'];
    //             $order2 = Order::create([
    //                 'user_id' => auth()->id(),
    //                 'parent_id' => $parentOrder->id,
    //                 'shipment_company_id' => $dropoffCompanyId,
    //                 'order_number' => Str::upper(Str::random(7)),
    //                 'total_price' => (float) ($split['dropoff_leg']['cost'] ?? 0),
    //                 'paid_amount' => 0,
    //                 'remaining_amount' => (float) ($split['dropoff_leg']['cost'] ?? 0),
    //                 'discount_price' => 0,
    //                 'final_price' => (float) ($split['dropoff_leg']['cost'] ?? 0),
    //                 'status' => OrderStatus::PENDING,
    //             ]);

    //             return responseJson(true, 'Split orders created', [
    //                 'parent' => $parentOrder,
    //                 'pickup_leg_order' => $order1,
    //                 'dropoff_leg_order' => $order2,
    //                 'handoff_point' => $handoff,
    //             ]);
    //         }

    //         return responseJson(false, 'Selected company does not fully cover. Suggested split with companies and costs.', $split, 422);
    //     }

    //     // Ensure package
    //     $packageId = $validated['package_id'] ?? null;
    //     if (!$packageId) {
    //         $package = PackageService::createFromPayload($validated, $request);
    //         $packageId = $package->id;
    //     }

    //     $subtotal = (float) ($validated['est_price'] ?? 0);
    //     $discount = 0.0;
    //     $promoCodeId = null;

    //     // Handle promo code if provided
    //     if (isset($validated['promo_code']) && !empty($validated['promo_code'])) {
    //         $promoCodeService = new PromoCodeService();
    //         $result = $promoCodeService->validateAndApply($validated['promo_code'], $subtotal, auth()->id());

    //         if ($result['valid']) {
    //             $discount = $result['discount'];
    //             $promoCodeId = $result['promo_code']->id;
    //         } else {
    //             return responseJson(false, $result['message'], null, 422);
    //         }
    //     }

    //     $total = max($subtotal - $discount, 0);
    //     $paidAmount = $validated['payment_method'] === 'partial'
    //         ? ($validated['partial_amount'] ?? 0)
    //         : $total;

    //     $remainingAmount = $total - $paidAmount;

    //     $order = Order::create([
    //         'user_id' => auth()->id(),
    //         // 'cart_id' => null,
    //         'shipment_company_id' => $validated['shipment_company_id'],
    //         'order_number' => Str::upper(Str::random(7)),
    //         'total_price' => $subtotal,
    //         'paid_amount' => $validated['partial_amount'] ?? $total,
    //         'remaining_amount' => isset($validated['partial_amount']) ? $total - $validated['partial_amount'] : 0,
    //         'discount_price' => $discount,
    //         'final_price' => $total,
    //         'status' => OrderStatus::PENDING,
    //     ]);

    //     $orderItem = OrderItem::create([
    //         'order_id' => $order->id,
    //         'item_number' => $package->package_number,
    //         'package_id' => (int) $packageId,
    //         'shipment_company_id' => (int) $validated['shipment_company_id'],
    //         'est_date' => $validated['est_date'] ?? null,
    //         'est_price' => (float) ($validated['est_price'] ?? 0),
    //     ]);

    //     // initial tracking entry
    //     PackageTrackingService::createStatus(
    //         packageId: (int) $packageId,
    //         orderItemId: $orderItem->id,
    //         status: OrderStatus::PENDING,
    //         location: null,
    //         description: 'Order placed',
    //     );
    //     Payment::create([
    //         'order_id' => $order->id,
    //         'transaction_id' => Str::upper(Str::random(7)),
    //         'payment_method' => $validated['payment_method'],
    //         'total_amount' => $subtotal,
    //         'paid_amount' => $paidAmount,
    //         'remaining_amount' => $remainingAmount,
    //         'payment_status' => PaymentStatus::PENDING,
    //         'promo_code_id' => $promoCodeId,
    //         'discount_price' => $discount,
    //         'final_price' => $total,
    //     ]);

    //     // Record promo code usage
    //     if ($promoCodeId) {
    //         $promoCodeService = new PromoCodeService();
    //         $promoCode = $promoCodeService->getByCode($validated['promo_code']);
    //         if ($promoCode) {
    //             $promoCodeService->recordUsage($promoCode);
    //         }
    //     }

    //     return responseJson(true, 'Order created', $order->load('orderItems'));
    // }
    public function applyPromo(Request $request)
    {
        $validated = $request->validate([
            'order_id'    => ['required', 'integer', 'exists:orders,id'],
            'code'        => ['required', 'string'],
            'total_price' => ['nullable', 'numeric'], // optional override
        ]);

        $user = auth()->user();

        // 🔹 Fetch the order
        $order = Order::with('payment')->where('id', $validated['order_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        // 🔹 Determine subtotal
        $subtotal = $validated['total_price'] ?? (float) $order->total_price;

        if ($subtotal <= 0) {
            return responseJson(false, 'Invalid total price for applying promo code', null, 422);
        }

        // 🔹 Validate & calculate discount
        $promoCodeService = new PromoCodeService();
        $result = $promoCodeService->validateAndApply($validated['code'], $subtotal, $user->id);

        if (!$result['valid']) {
            return responseJson(false, $result['message'], null, 422);
        }

        // 🔹 Store promo code and discount on order
        $order->update([
            'discount_price' => $result['discount'],
            'final_price'    => max($subtotal - $result['discount'], 0),
        ]);

        // Optional: record usage
        $promoCodeService->recordUsage($result['promo_code']);

        return responseJson(true, $result['message'], [
            'order_id' => $order->id,
            'code'     => $result['promo_code']->code,
            'discount' => $result['discount'],
            'subtotal' => $subtotal,
            'total'    => $order->final_price,
        ]);
    }

}
