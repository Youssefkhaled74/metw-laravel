<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEcommerceCartItemRequest;
use App\Http\Requests\UpdateEcommerceCartItemRequest;
use App\Http\Resources\EcommerceCartItemResource;
use App\Http\Resources\EcommerceCartResource;
use App\Models\EcommerceCart;
use App\Models\EcommerceCartItem;
use App\Models\Product;
use App\Models\ProductVariant;

class EcommerceCartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $cart = EcommerceCart::with('items.product.media', 'items.variant')->firstOrCreate([
                'user_id' => auth()->id()
            ]);
            return responseJson(true, 'Cart retrieved successfully', new EcommerceCartResource($cart));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEcommerceCartItemRequest $request)
    {
        try {
            $cart = EcommerceCart::firstOrCreate([
                'user_id' => auth()->id()
            ]);

            $product = Product::findOrFail($request->product_id);

            if ($product->stock < $request->quantity) {
                return responseJson(false, __('Not enough stock'), null, 422);
            }

            // السعر الأساسي للمنتج
            $basePrice = $product->price;
            $discountValue = 0;
            $finalPrice = $basePrice;

            // ✅ تحقق من وجود خصم صالح على المنتج
            if ($product->is_sale) {
                $finalPrice = $product->discounted_price;
                $discountValue = $basePrice - $finalPrice;
            }

            // ✅ لو فيه variant أضف سعره
            if ($request->variant_id) {
                $variant = ProductVariant::where('product_id', $request->product_id)
                    ->where('id', $request->variant_id)
                    ->first();

                if (!$variant) {
                    return responseJson(false, __('Variant not found'), null, 422);
                }

                $finalPrice += $variant->price;
                $basePrice += $variant->price;
            }

            // ✅ شوف لو العنصر موجود مسبقًا في الكارت
            $cartItem = EcommerceCartItem::where('ecommerce_cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->where('variant_id', $request->variant_id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $request->quantity;

                if ($product->stock < $newQuantity) {
                    return responseJson(false, __('Not enough stock to update quantity'), null, 422);
                }

                $cartItem->update([
                    'quantity' => $newQuantity,
                    'unit_price' => $basePrice,
                    'product_discount' => $discountValue,
                    'final_price' => $finalPrice,
                    'total_price' => $finalPrice * $newQuantity,
                ]);
            } else {
                $cartItem = EcommerceCartItem::create([
                    'ecommerce_cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'variant_id' => $request->variant_id,
                    'quantity' => $request->quantity,
                    'unit_price' => $basePrice,
                    'product_discount' => $discountValue,
                    'final_price' => $finalPrice,
                    'total_price' => $finalPrice * $request->quantity,
                ]);
            }

            $this->updateCartSummary($cart);

            return responseJson(true, __('Cart retrieved successfully'), new EcommerceCartResource($cart));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($cartItemId)
    {
        try {
            $cart = EcommerceCartItem::with('product.media', 'variant')->findOrFail($cartItemId);
            return responseJson(true, 'Cart retrieved successfully', new EcommerceCartItemResource($cart));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEcommerceCartItemRequest $request, $cartItemId)
    {
        try {
            $user = auth()->user();

            $cart = EcommerceCart::where('user_id', $user->id)->first();

            if (!$cart) {
                return responseJson(false, 'Cart not found', null, 404);
            }

            $item = $cart->items()->where('id', $cartItemId)->first();

            if (!$item) {
                return responseJson(false, 'Cart item not found', null, 404);
            }

            $item->quantity = $request->quantity;
            $item->total_price = $item->unit_price * $request->quantity;
            $item->save();

            // update cart summary
            $this->updateCartSummary($cart);

            return responseJson(true, 'Cart updated successfully', new EcommerceCartItemResource($item));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($cartItemId)
    {
        try {
            $user = auth()->user();

            $cart = EcommerceCart::where('user_id', $user->id)->first();

            if (!$cart) {
                return responseJson(false, 'Cart not found', null, 404);
            }

            $cartItem = $cart->items()->where('id', $cartItemId)->first();

            if (!$cartItem) {
                return responseJson(false, 'Cart item not found', null, 404);
            }

            $cartItem->delete();

            //update cart
            $this->updateCartSummary($cart);

            return responseJson(true, 'Cart item removed successfully', new EcommerceCartResource($cart->load('items')));
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    private function updateCartSummary(EcommerceCart $cart)
    {
        $cart->items_count = $cart->items()->count();
        $cart->total_price = $cart->items()->sum('total_price');
        $cart->save();
    }
}
