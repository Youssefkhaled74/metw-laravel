<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Package;
use App\Services\PackageService;
use App\Services\PackageTrackingService;
use App\Enum\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
        ],);

        $cart->load(['items.package', 'items.shipmentCompany']);

        $total = (float) $cart->items()->sum('est_price');

        return responseJson(true, 'Cart fetched', [
            'cart' => new CartResource($cart),
            'total' => $total,
        ]);
    }
    public function getItem(Request $request, string $id)
    {
        try {
            $item = CartItem::with(['package', 'shipmentCompany'])->where('id', $id)->whereHas('cart', fn($q) => $q->where('user_id', auth()->id()))->firstOrFail();
            return responseJson(true, 'Item fetched', new CartItemResource($item));
        } catch (\Exception $e) {
            return responseJson(false, 'Item not found', $e->getMessage());
        }
    }

    public function add(AddToCartRequest $request)
    {
        $validated = $request->validated();

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()], [
            // 'shipment_company_id' => $validated['shipment_company_id'],
            // 'est_date' => $validated['est_date'] ?? null,
            // 'est_price' => 0,
        ]);

        // // Optional: enforce one company per cart
        // if ($cart->shipment_company_id && $cart->shipment_company_id !== (int) $validated['shipment_company_id']) {
        //     // clear cart if different company
        //     $cart->items()->delete();
        //     $cart->shipment_company_id = $validated['shipment_company_id'];
        //     $cart->save();
        // }

        // Ensure we have a package id (create inline if absent)
        $packageId = $validated['package_id'] ?? null;
        if (!$packageId) {
            $package = PackageService::createFromPayload($validated, $request);
            $packageId = $package->id;
        }

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'package_id' => $packageId,
            'shipment_company_id' => $validated['shipment_company_id'],
            'est_date' => $validated['est_date'] ?? null,
            'est_price' => $validated['est_price'] ?? null,
        ]);

        // // create a tracking entry for draft/cart stage (optional)
        // PackageTrackingService::createStatus(
        //     packageId: $packageId,
        //     orderItemId: null,
        //     status: OrderStatus::PENDING,
        //     location: $item->package->pickupAddress->address ?? null,
        //     description: 'Added to cart',
        //     metadata: ['context' => 'cart']
        // );
        $cart->update([
            'items_count' => $cart->items()->count(),
            'item_total_price' => $cart->items()->sum('est_price'),
        ]);

        return responseJson(true, 'Added to cart', $item);
    }

    public function remove(Request $request, string $id)
    {
        $item = CartItem::where('id', $id)->whereHas('cart', fn($q) => $q->where('user_id', auth()->id()))->firstOrFail();
        $item->delete();
        $item->cart->update([
            'items_count' => $item->cart->items()->count(),
            'item_total_price' => $item->cart->items()->sum('est_price'),
        ]);
        return responseJson(true, 'Removed from cart');
    }
}
