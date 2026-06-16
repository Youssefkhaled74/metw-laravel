<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Order;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Enum\PromoCodeType;
use App\Enum\ReturnStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyPromoCodeRequest;
use App\Http\Requests\EcommerceOrderPreview;
use App\Http\Requests\StoreEcommerceOrderRequest;
use App\Http\Requests\UpdateEcommerceOrderRequest;
use App\Http\Resources\EcommerceOrderItemResource;
use App\Http\Resources\EcommerceOrderResource;
use App\Http\Resources\ReturnRequestResource;
use App\Models\Admin;
use App\Models\EcommerceCart;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\PromoCode;
use App\Models\ReturnRequest;
use App\Models\ShipmentCompany;
use App\Models\ShipmentCompanyCategoryPrice;
use App\Models\ShipmentCompanySubCategorySizePrice;
use App\Models\UserAddress;
use App\Notifications\NewEcommerceOrder;
use App\Notifications\OrderStatusUpdated;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class EcommerceOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit  = (int) $request->input('limit', 10);
            $page   = (int) $request->input('page', 1);
            $status = $request->input('status');
            $paymentStatus = $request->input('payment_status');

            // لو الحالة returned، جيب من جدول ReturnRequest
            if ($status === OrderStatus::RETURNED->value) {
                $returns = ReturnRequest::with([
                    'user',
                    'order.userAddress',
                    'order.shipmentCompany',
                    'items.product.media'
                ])
                ->whereHas('order', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                // ->whereIn('status', [
                //     ReturnStatus::APPROVED->value,
                //     ReturnStatus::PICKUP->value,
                //     ReturnStatus::PROCESSING->value,
                //     ReturnStatus::REFUNDED->value
                // ])
                ->latest();

                $payload = paginate($returns, ReturnRequestResource::class, $limit, $page);
                return responseJson(true, 'Orders retrieved successfully', $payload);
            }

            // غير كده جيب الأوردرات العادية
            $orders = EcommerceOrder::with('user', 'cart', 'items.product.media', 'userAddress')
                ->where('user_id', auth()->id())
                ->when($status, function ($q) use ($status) {
                    if ($status === OrderStatus::PENDING->value) {
                        $q->whereIn('status', [
                            OrderStatus::PENDING->value,
                            OrderStatus::ACCEPTED->value,
                            OrderStatus::PICKUP->value,
                            OrderStatus::ON_WAY->value,
                        ]);
                    } else {
                        $q->where('status', $status);
                    }
                })
                ->when($paymentStatus, function ($q) use ($paymentStatus) {
                    $q->where('payment_status', $paymentStatus);
                })
                ->latest();

            $payload = paginate($orders, EcommerceOrderResource::class, $limit, $page);
            return responseJson(true, 'Orders retrieved successfully', $payload);

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function showItems($orderId, Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            // ✅ Load the order first
            $order = EcommerceOrder::with('items.product.media')->findOrFail($orderId);

            // ✅ Check if the authenticated user owns the order
            if ($order->user_id !== auth()->id()) {
                return responseJson(false, 'Unauthorized', null, 403);
            }

            // ✅ Use relationship for cleaner data access
            $orderItems = $order->items()
                ->with(['product.media', 'variant']);


            // ✅ Paginate the query
            $payload = paginate($orderItems, EcommerceOrderItemResource::class, $limit, $page);

            return responseJson(true, 'Order items retrieved successfully', $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    // 1️⃣ فانكشن إنشاء الأوردر فقط (بدون دفع)
    public function store(StoreEcommerceOrderRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            // ===========================
            // 1. Handle Address
            // ===========================
            $userAddressId = $validatedData['user_address_id'] ?? null;

            // 2️⃣ If not provided, try default
            if (!$userAddressId) {
                $userAddressId = UserAddress::where('user_id', $user->id)
                    ->where('is_default', true)
                    ->value('id');
            }

            if (!$userAddressId && !$request->has('address')) {
                return responseJson(false, 'Either user_address_id or address is required', null, 422);
            }

            if ($request->has('address')) {
                $userAddressId = UserAddress::create(array_merge(
                    $request->address,
                    ['user_id' => $user->id]
                ))->id;
            }

            $userAddress = UserAddress::findOrFail($userAddressId);

            if (!$userAddress->latitude || !$userAddress->longitude) {
                return responseJson(false, 'User address must have valid coordinates', null, 422);
            }

            // Phone
            $validatedData['phone'] = $request->mobile ?? $user->phone;

            // ===========================
            // 2. Get Cart + Products
            // ===========================
            $cart = EcommerceCart::with(['items.product.branch'])->findOrFail($request->cart_id);
            $selectedItems = $cart->items()->whereIn('id', $request->items)->get();

            if ($selectedItems->isEmpty()) {
                return responseJson(false, 'No items selected from cart', null, 422);
            }

            // Load service
            $shipmentService = app(\App\Services\ShipmentSuggestionService::class);
            $mapsService     = app(\App\Services\GoogleMapsService::class);

            $itemsWithShipment = [];
            $totalShipmentPrice = 0;

            // Get all companies
            $companies = ShipmentCompany::active()->get();

            // ===========================
            // 3. Loop Through Each Item
            // ===========================
            foreach ($selectedItems as $item) {

                $product = $item->product;
                if (!$product) {
                    return responseJson(false, 'Product not found', null, 422);
                }

                $isFreeShipping = false;

                if ($product->free_shipping != '0') {

                    if ($product->free_shipping == 'available') {
                        $isFreeShipping = true;

                    } elseif ($product->free_shipping == 'price') {
                        if ($product->price >= $product->free_shipping_price) {
                            $isFreeShipping = true;
                        }
                    }
                }

                if ($isFreeShipping) {
                    $itemsWithShipment[] = [
                        'cart_item'      => $item,
                        'product'        => $product,
                        'branch'         => $product->branch,
                        'shipment_price' => 0,
                        'distance_km'    => 0,
                    ];

                    continue;
                }

                if (!$product->branch) {
                    return responseJson(false, "Product {$product->name} has no branch assigned", 422);
                }

                $branch = $product->branch;

                if (!$branch->latitude || !$branch->longitude) {
                    return responseJson(false, "Branch for {$product->name} has invalid coordinates", 422);
                }

                // ===========================
                // Build the package object like ShipmentSuggestionService
                // ===========================
                $package = [
                    'id'               => $item->id,
                    'category_id'      => $product->category_id,
                    'sub_category_id'  => $product->sub_category_id,
                    'weight'           => $product->package_weight ?? 1,
                    'size'             => $product->package_height* $product->package_length* $product->package_width ?? 1,
                    'piece'            => $item->quantity ?? 1,
                    'piece_type'       =>$product->piece_type ?? 'small',
                    'pieces_per_package' => $product->pieces_per_package ?? 1,
                ];

                // Pickup + Dropoff structure
                $pickup = [
                    'latitude' => $branch->latitude,
                    'longitude' => $branch->longitude,
                    'city_id' => $branch->city_id,
                    'state_id' => $branch->state_id,
                    'zone_id' => $branch->zone_id,
                    'is_village' => $branch->is_village ?? false,
                ];

                $dropoff = [
                    'latitude' => $userAddress->latitude,
                    'longitude' => $userAddress->longitude,
                    'city_id' => $userAddress->city_id,
                    'state_id' => $userAddress->state_id,
                    'zone_id' => $userAddress->zone_id,
                    'is_village' => $userAddress->is_village ?? false,
                ];

                // Attach city names (to match service requirements)
                $pickup['city_name']  = $branch->city?->name_en;
                $dropoff['city_name'] = $userAddress->city?->name_en;

                // ===========================
                // 4. Calculate direct price for ALL companies
                // ===========================
                $maxPrice = 0;
                $maxCompany = null;

                foreach ($companies as $company) {

                    // Check direct coverage
                    $result = $shipmentService->directPriceForCompany(
                        $company->id,
                        $package,
                        $pickup,
                        $dropoff
                    );

                    if (!$result['covered']) {
                        continue;
                    }

                    $priceData  = $result['price'];
                    $clientPrice = $priceData['client_total'] ?? 0;


                    if ($clientPrice > $maxPrice) {
                        $maxPrice = $clientPrice;
                        $maxCompany = $company;
                    }
                }

                // No company found
                if ($maxPrice == 0 || !$maxCompany) {
                    return responseJson(false, "No shipment company covers {$product->name}", 422);
                }

                // ===========================
                // Add to list
                // ===========================
                $itemsWithShipment[] = [
                    'cart_item'            => $item,
                    'product'              => $product,
                    'branch'               => $branch,
                    // 'shipment_company_id'  => $maxCompany->id,
                    'shipment_price'       => $maxPrice,
                    'distance_km'          => $priceData['distance_km'] ?? 0,
                ];

                $totalShipmentPrice += $maxPrice;
            }

            // ===========================
            // 5. Order Totals
            // ===========================
            $subtotal = $selectedItems->sum('total_price');
            $total = $subtotal + $totalShipmentPrice;

            // ===========================
            // 6. Create Order
            // ===========================
            $order = EcommerceOrder::create([
                'user_id'           => $user->id,
                'ecommerce_cart_id' => $cart->id,
                'user_address_id'   => $userAddressId,
                'phone'             => $validatedData['phone'],
                'status'            => OrderStatus::PENDING->value,
                'order_number'      => null,
                'subtotal'          => $subtotal,
                'shipping_price'    => $totalShipmentPrice,
                'discount'          => 0,
                'total_amount'      => $total,
                'paid_amount'       => 0,
                'remaining_amount'  => $total,
                'final_price'       => $total,
                'payment_method'    => null,
                'promo_code_id'     => null,
            ]);

            // ===========================
            // 7. Order Items
            // ===========================
            foreach ($itemsWithShipment as $itemData) {
                $cartItem = $itemData['cart_item'];
                $order->items()->create([
                    'product_id'             => $cartItem->product_id,
                    'product_variant_id'     => $cartItem->variant_id,
                    'quantity'               => $cartItem->quantity,
                    'unit_price'             => $cartItem->unit_price,
                    'price'                  => $cartItem->price,
                    'product_discount'       => $cartItem->product_discount,
                    'total_price'            => $cartItem->total_price,
                    'pickup_branch_id'       => $itemData['branch']->id,
                    // 'shipment_company_id'    => $itemData['shipment_company_id'],
                    'shipment_price'         => $itemData['shipment_price'],
                    'shipment_price_company' => 0,
                    'distance'               => $itemData['distance_km'],
                    'final_price'            => $cartItem->total_price + $itemData['shipment_price'],
                    'paid_amount'            => 0,
                    'remaining_amount'       => $cartItem->total_price + $itemData['shipment_price'],
                ]);
            }

            // ===========================
            // 8. Remove items from cart
            // ===========================
            $cart->items()->whereIn('id', $request->items)->delete();

            // ===========================
            // 9. Notifications
            // ===========================
            if ($user->notifications_enabled) {
                $title = __('notifications.order_created_title');
                $body  = __('notifications.order_created_body', [
                    'order_number' => $order->order_number
                ]);

                $user->notify(new \App\Notifications\OrderStatusUpdated(
                    title: $title,
                    body: $body,
                    data: [
                        'key' => 'order_created',
                        'order_number' => $order->order_number,
                        'id' => $order->id,
                        'image' => null,
                        'notification_type' => 'ecommerce',
                        'navigation_type' => 'order_tracking',
                    ],
                    type: 'ecommerce',
                    navigationType: 'order_tracking'
                ));
            }

            // Notify admins
            foreach (Admin::all() as $admin) {
                $admin->notify(new NewEcommerceOrder($order));
            }

            // ===========================
            // 10. Response
            // ===========================
            return responseJson(
                true,
                'Order created successfully',
                new EcommerceOrderResource(
                    $order->load(['items.pickupBranch', 'items.shipmentCompany', 'user', 'userAddress'])
                )
            );

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    // 2️⃣ فانكشن الـ Checkout (للدفع وتطبيق البرومو كود)
// Enhanced Checkout function with proportional discount and payment distribution
public function checkout(Request $request, $orderId)
{
    try {
        $request->validate([
            'payment_method' => 'required|in:full,partial',
            'paid_amount' => 'required_if:payment_method,partial|numeric|min:0',
            'promo_code' => 'nullable|string00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000',
        ]);

        $user = auth()->user();
        $order = EcommerceOrder::with('items')->findOrFail($orderId);

        // Verify ownership
        if ($order->user_id !== $user->id) {
            return responseJson(false, 'Unauthorized', null, 403);
        }

        // Check order status
        if ($order->status !== OrderStatus::ACCEPTED->value) {
            return responseJson(
                false,
                trans('messages.order_must_be_accepted_before_payment'),
                null,
                422
            );
        }

        // Check payment status
        if ($order->payment_status == PaymentStatus::PAID) {
            return responseJson(false, 'Order already paid', null, 422);
        }

        $subtotal = $order->subtotal;
        $shipping = $order->shipping_price;
        $discount = 0;
        $promoCode = null;

        // 1. Handle Promo Code
        if ($request->filled('promo_code')) {
            try {
                $promoCode = $this->validatePromoCode($request->promo_code, $user->id);

                if ($promoCode) {
                    [$discount, $promoCode] = $this->calculateDiscount(
                        $request->promo_code,
                        $subtotal,
                        $user->id
                    );
                }
            } catch (\Exception $e) {
                return responseJson(false, $e->getMessage(), null, 422);
            }
        }

        $total = $subtotal + $shipping - $discount;

        // 2. Calculate Payment Amount
        if ($request->payment_method === 'full') {
            $paid = $total;
        } else {
            $paid = $request->paid_amount;
            $minPartial = $total * 0.5;

            if ($paid < $minPartial) {
                return responseJson(
                    false,
                    trans('messages.partial_payment_must_be_50_percent_or_more'),
                    [
                        'minimum_required' => $minPartial,
                        'paid_amount' => $paid
                    ],
                    422
                );
            }

            if ($paid > $total) {
                return responseJson(
                    false,
                    trans('messages.paid_cannot_exceed_total'),
                    null,
                    422
                );
            }
        }

        $remaining = $total - $paid;

        // 3. Distribute discount and payment proportionally across items
        $this->distributeDiscountAndPayment($order, $discount, $paid, $total);

        // 4. Update Order
        $order->update([
            'discount' => $discount,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'final_price' => $remaining,
            'payment_method' => $request->payment_method,
            'promo_code_id' => $promoCode?->id ?? null,
            'payment_status' => PaymentStatus::PAID->value
        ]);

        // 5. Increase promo code usage
        if ($promoCode) {
            $promoCode->increment('uses');
        }

        // Notify admins
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new NewEcommerceOrder($order));
        }

        return responseJson(
            true,
            'Payment processed successfully',
            new EcommerceOrderResource($order->fresh(['items.pickupBranch', 'items.product', 'user', 'userAddress']))
        );
    } catch (\Throwable $th) {
        return responseJson(false, $th->getMessage(), null, 500);
    }
}

/**
 * Distribute discount and payment proportionally across order items
 */
private function distributeDiscountAndPayment($order, $discount, $paidAmount, $orderTotal)
{
    $items = $order->items;

    if ($items->isEmpty()) {
        return;
    }

    // Calculate total before discount (subtotal + shipping)
    $totalBeforeDiscount = $order->subtotal + $order->shipping_price;

    foreach ($items as $item) {
        // Calculate item's proportion of the total order
        $itemProportion = $item->final_price / $totalBeforeDiscount;

        // Distribute discount proportionally
        $itemDiscount = round($discount * $itemProportion, 2);

        // Calculate item final price after discount
        $itemFinalAfterDiscount = $item->final_price - $itemDiscount;

        // Distribute paid amount proportionally
        $itemPaidAmount = round($paidAmount * $itemProportion, 2);

        // Calculate remaining amount for this item
        $itemRemainingAmount = $itemFinalAfterDiscount - $itemPaidAmount;

        // Update item with distributed amounts
        $item->update([
            'discount_price' => $itemDiscount, // You may need to add this column
            'final_price' => $itemFinalAfterDiscount,
            'paid_amount' => $itemPaidAmount,
            'remaining_amount' => max(0, $itemRemainingAmount), // Ensure no negative
        ]);
    }

    // Handle rounding differences - add/subtract difference to/from last item
    $totalItemsPaid = $items->sum('paid_amount');
    $difference = $paidAmount - $totalItemsPaid;

    if (abs($difference) > 0.01) {
        $lastItem = $items->last();
        $lastItem->update([
            'paid_amount' => $lastItem->paid_amount + $difference,
            'remaining_amount' => $lastItem->remaining_amount - $difference,
        ]);
    }
}

    // 3️⃣ Route في api.php
    // Route::post('orders', [OrderController::class, 'store']);
    // Route::post('orders/{orderId}/checkout', [OrderController::class, 'checkout']);



    /**
     * Display the specified resource.
     */
    public function show(string $orderId)
    {
        try{
            $order = EcommerceOrder::with('user','userAddress','cart','items.product.media','items.variant')->where('user_id',auth()->id())->findOrFail($orderId);
            return responseJson(true,'Order retrieved successfully',new EcommerceOrderResource($order));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $orderId)
    // {
    //     try{
    //         $order = EcommerceOrder::with('user','userAddress','cart','items.product.media','items.variant')->where('user_id',auth()->id())->findOrFail($orderId);
    //         return responseJson(true,'Order retrieved successfully',new EcommerceOrderResource($order));
    //     }catch(\Throwable $th){
    //         return responseJson(false,$th->getMessage(),null,500);
    //     }
    // }
    public function update(UpdateEcommerceOrderRequest $request, string $orderId)
    {
        try {
            $validatedData = $request->validated();

            $order = EcommerceOrder::with('user', 'userAddress', 'cart', 'items.product.media', 'items.variant')
                ->where('user_id', auth()->id())
                ->findOrFail($orderId);

            // لو المستخدم حاول يغير العنوان
            if (array_key_exists('user_address_id', $validatedData)) {
                if ($order->status !== OrderStatus::PENDING->value) {
                    return responseJson(
                        false,
                        trans('messages.order_cannot_change_address'),
                        null,
                        422
                    );
                }

                $order->user_address_id = $validatedData['user_address_id'];
            }

            // تحديث باقي البيانات المسموح بها
            $order->fill($validatedData);
            $order->save();

            return responseJson(true, trans('messages.order_updated_successfully'), new EcommerceOrderResource($order));

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    public function updateStatus(UpdateEcommerceOrderRequest $request, string $orderId)
    {
        try {

            $order = EcommerceOrder::with([
                'user',
                'userAddress',
                'cart',
                'items.product.media',
                'items.variant'
            ])
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

            $newStatus = $request->status;

            $currentStatus = $order->status instanceof \App\Enum\OrderStatus
                ? $order->status->value
                : (string) $order->status;

            // ✅ Prevent backward movement
            $sequence = ['pending', 'accepted', 'pickup', 'on_way', 'delivered'];

            if ($newStatus !== 'cancelled') {
                if (array_search($newStatus, $sequence) < array_search($currentStatus, $sequence)) {
                    return responseJson(false, __('messages.status_backward_not_allowed'), null, 422);
                }
            }

            // ✅ Prevent cancel after shipping started
            if ($newStatus === 'cancelled' && !in_array($currentStatus, ['pending', 'accepted'])) {
                return responseJson(false, __('messages.cannot_cancel_after_shipping'), null, 422);
            }

            // ✅ Update order
            $order->update([
                'status' => $newStatus,
            ]);

            if (
                $newStatus === \App\Enum\OrderStatus::DELIVERED->value &&
                $currentStatus !== \App\Enum\OrderStatus::DELIVERED->value
            ) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('sold_count', $item->quantity);
                    }
                }
            }

            // ✅ Notification mapping
            $statusToKey = [
                'pending'   => 'order_pending',
                'accepted'  => 'order_accepted',
                'pickup'    => 'order_pickup',
                'on_way'    => 'order_on_way',
                'delivered' => 'order_delivered',
                'cancelled' => 'order_cancelled',
            ];

            $key = $statusToKey[$newStatus] ?? 'order_updated';

            // ✅ Send notification
            if ($order->user?->notifications_enabled) {

                app()->setLocale($order->user->default_lang ?? 'en');

                $title = __("notifications.$key.title");
                $body  = __("notifications.$key.body", [
                    'order_number' => $order->order_number,
                ]);

                $order->user->notify(
                    new \App\Notifications\OrderStatusUpdated(
                        title: $title,
                        body: $body,
                        data: [
                            'key' => $key,
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'status' => $newStatus,
                            'notification_type' => 'ecommerce',
                            'navigation_type' => 'order_tracking',
                        ],
                        type: 'ecommerce',
                        navigationType: 'order_tracking'
                    )
                );
            }

            return responseJson(true, __('messages.order_updated_successfully'), new EcommerceOrderResource($order));

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function cancelOrder(string $orderId)
    {
        try {
            $order = EcommerceOrder::where('user_id', auth()->id())
                ->findOrFail($orderId);

            // لو الأوردر بالفعل ملغي
            if ($order->status === OrderStatus::CANCELLED->value) {
                return responseJson(
                    false,
                    trans('messages.order_already_cancelled'),
                    new EcommerceOrderResource($order),
                    422
                );
            }

            // الحالات اللي يُسمح فيها بالإلغاء
            if (!in_array($order->status, [OrderStatus::PENDING->value, OrderStatus::ACCEPTED->value])) {
                return responseJson(
                    false,
                    trans('messages.order_cannot_be_cancelled'),
                    null,
                    422
                );
            }

            // تحديث الحالة إلى ملغي
            $order->status = OrderStatus::CANCELLED;
            $order->save();

            return responseJson(
                true,
                trans('messages.order_cancelled_successfully'),
                new EcommerceOrderResource($order)
            );

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    public function destroy(string $orderId)
    {
        try{
            $order = EcommerceOrder::where('user_id',auth()->id())->findOrFail($orderId);
            $order->delete();
            return responseJson(true,'Order deleted successfully',null);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    public function preview(EcommerceOrderPreview $request)
    {
        try {
            $validated = $request->validated();

            $cart = EcommerceCart::with(['items.product', 'items.variant'])
                ->where('id', $validated['cart_id'])
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $selectedItemIds = collect($validated['items'])->pluck('id')->toArray();
            $items = $cart->items->whereIn('id', $selectedItemIds);

            if ($items->isEmpty()) {
                return responseJson(false, 'No valid items selected', null, 422);
            }

            $subtotal = $items->sum(fn($item) => $item->quantity * $item->unit_price);

            $shipping = 50;
            $discount = 0;
            if (!empty($validated['promo_code'])) {
                $promo = PromoCode::where('code', $validated['promo_code'])->first();
                if ($promo && $promo->isValid()) {
                    $discount = $promo->calculateDiscount($subtotal);
                }
            }

            $total = max(0, $subtotal + $shipping - $discount);

            $paidAmount = ($validated['payment_method'] ?? 'full') === 'partial'
                ? ($validated['paid_amount'] ?? 0)
                : $total;
            $address = !empty($validated['user_address_id'])
                ? UserAddress::find($validated['user_address_id'])
                : UserAddress::where('user_id', auth()->id())->latest()->first();

            return responseJson(true, 'Order preview', [
                'items' =>  $items->map(function ($item) {
                    return [
                        'product_id'   => $item->product_id,
                        'name'         => $item->product->name,
                        'price'        => $item->unit_price,
                        'quantity'     => $item->quantity,
                        'total'        => $item->unit_price * $item->quantity,
                        'media'        => $item->product->media->map(function ($media) {
                            return [
                                'id'  => $media->id,
                                'url' => asset($media->file_path),
                            ];
                        }),
                    ];
                }),
                'subtotal'      => $subtotal,
                'shipping'      => $shipping,
                'total'         => $total,
                'address'       => $address,
                'phone'         => auth()->user()->phone,
                'country_code'  => auth()->user()->country_code,
            ]);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    public function applyPromoCode(ApplyPromoCodeRequest $request)
    {
        try {
            $validated = $request->validated();

            // ✅ 1. نجيب الأوردر
            $order = EcommerceOrder::findOrFail($validated['order_id']);

            // ✅ 2. نجيب اليوزر
            $userId = $order->user_id;

            // ✅ 3. نستخدم total_amount من الأوردر
            $subtotal = $order->total_amount;

            // ✅ 4. نحسب الخصم بناءً على البروموكود
            [$discountAmount, $promoCode] = $this->calculateDiscount(
                $validated['promo_code'] ?? null,
                $subtotal,
                $userId
            );

            // ✅ 5. نخصم ونحدّث البيانات
            $finalTotal = $subtotal - $discountAmount;

            $order->update([
                'promo_code_id' => $promoCode?->id,
                'discount'      => $discountAmount,
                'final_price'   => $finalTotal,
            ]);

            // ✅ 6. نرجع الريسبونس
            return responseJson(true, 'Promo code applied successfully.', [
                'order_id'        => $order->id,
                'subtotal'        => $subtotal,
                'promo_code'      => $promoCode?->code,
                'discount_amount' => $discountAmount,
                'final_total'     => $finalTotal,
            ]);

        } catch (\Exception $e) {
            return responseJson(false, $e->getMessage(), null, 422);
        } catch (\Throwable $th) {
            return responseJson(false, 'Something went wrong.', null, 500);
        }
    }



    private function validatePromoCode(string $code, int $userId): PromoCode
    {
        $promoCode = PromoCode::where('code', $code)
            ->where('type', PromoCodeType::ECOMMERCE)
            ->first();

        if (!$promoCode) {
            throw new \Exception('Promo code is not valid.');
        }

        if (!$promoCode->is_active) {
            throw new \Exception('Promo code is not active.');
        }

        if ($promoCode->valid_from && $promoCode->valid_from->isFuture()) {
            throw new \Exception('Promo code is not yet valid.');
        }

        if ($promoCode->valid_to && $promoCode->valid_to->isPast()) {
            throw new \Exception('Promo code has expired.');
        }

        if ($promoCode->max_uses !== null && $promoCode->uses >= $promoCode->max_uses) {
            throw new \Exception('Promo code usage limit has been reached.');
        }

        // تحقق من عدد مرات الاستخدام لكل يوزر
        if ($promoCode->user_max_uses !== null) {
            $userUses = $promoCode->ecommer()->where('user_id', $userId)->count();
            if ($userUses >= $promoCode->user_max_uses) {
                throw new \Exception('You have already used this promo code the maximum allowed times.');
            }
        }

        return $promoCode;
    }
    private function calculateDiscount(?string $promoCodeInput, float $subtotal, int $userId): array
    {
        if (empty($promoCodeInput)) {
            return [0, null];
        }

        $promoCode = $this->validatePromoCode($promoCodeInput, $userId);

        $discountAmount = 0;
        if ($promoCode->discount_type->value === 'percentage') {
            $discountAmount = ($subtotal * $promoCode->discount_value) / 100;
        } else {
            $discountAmount = $promoCode->discount_value;
        }

        $discountAmount = min($discountAmount, $subtotal);

        return [$discountAmount, $promoCode];
    }

    public function cancelOrderItem($orderItemId)
    {
        try {
            $user = auth()->user();

            $item = EcommerceOrderItem::with('order')
                ->where('id', $orderItemId)
                ->first();

            if (!$item) {
                return responseJson(false, 'Order item not found', null, 404);
            }

            // ✅ Ensure user owns the order
            if ($item->order->user_id !== $user->id) {
                return responseJson(false, 'Unauthorized', null, 403);
            }

            // ✅ Business rules: cannot cancel once shipped or delivered
            if (in_array($item->status->value, [
                OrderStatus::PICKUP->value,
                OrderStatus::ON_WAY->value,
                OrderStatus::DELIVERED->value,
            ])) {
                return responseJson(false, 'This item can no longer be cancelled', null, 422);
            }

            DB::beginTransaction();

            // ✅ Update status to cancelled
            $item->update([
                'status'         => OrderStatus::CANCELLED->value,
                'vendor_status'  => null,
            ]);

            // ✅ Soft delete
            $item->delete();

            // ✅ Update parent order totals
            $order = $item->order;
            $order->update([
                'subtotal'         => $order->subtotal - $item->total_price,
                'shipping_price'   => max(0, $order->shipping_price - $item->shipment_price),
                'total_amount'     => max(0, $order->total_amount - $item->final_price),
                'final_price'      => max(0, $order->final_price - $item->final_price),
            ]);

            DB::commit();

            return responseJson(true, 'Order item cancelled successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            return responseJson(false, $e->getMessage(), null, 500);
        }
    }

}
