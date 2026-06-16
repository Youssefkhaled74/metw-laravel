<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductReview;
use App\Http\Resources\ProductReviewResource;
use App\Models\EcommerceOrderItem;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($productId)
    {
        try{
            $reviews = ProductReview::with('user')->where('product_id', $productId);

            $payload = paginate($reviews, ProductReviewResource::class);
            return responseJson(true, trans('messages.Product reviews fetched successfully'), $payload);
        }catch(\Throwable $th){
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductReview $request)
    {
        try {
            $validated = $request->validated();
            $user_id = auth()->user()->id;

            $orderItem = EcommerceOrderItem::with('order')
                ->where('id', $validated['order_item_id'])
                ->where('product_id', $validated['product_id'])
                ->first();

            if (!$orderItem) {
                return responseJson(false, trans('messages.Order item not found'), null, 404);
            }

            if ($orderItem->order->user_id !== $user_id) {
                return responseJson(false, trans('messages.Not allowed to review this item'), null, 403);
            }

            if (
                ProductReview::where('user_id', $user_id)
                ->where('product_id', $validated['product_id'])
                ->where('ecommerce_order_item_id', $validated['order_item_id'])
                ->exists()
            ) {
                return responseJson(false, trans('messages.Already reviewed this item'), null, 400);
            }

            $review = ProductReview::create([
                'user_id'     => $user_id,
                'product_id'  => $validated['product_id'],
                'ecommerce_order_item_id' => $validated['order_item_id'],
                'rating'      => $validated['rating'],
                'comment'     => $validated['comment'],
            ]);

            $orderItem->product()->update([
                'rating' => $orderItem->product->reviews()->avg('rating'),
                'rating_count' => $orderItem->product->reviews()->count(),
            ]);

            return responseJson(true, trans('messages.Review created'), $review);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{
            $productReview = ProductReview::findOrFail($id);
            return responseJson(true, trans('messages.Review fetched successfully'), $productReview);
        }catch(\Throwable $th){
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            $validated = $request->validated();
            $productReview = ProductReview::findOrFail($id);
            $productReview->update($validated);
            return responseJson(true, trans('messages.Review updated'), $productReview);
        }catch(\Throwable $th){
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $productReview = ProductReview::findOrFail($id);
            $productReview->delete();
            return responseJson(true, trans('messages.Review deleted'), $productReview);
        }catch(\Throwable $th){
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
