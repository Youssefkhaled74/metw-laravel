<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Product::with('media')->whereHas('favourite', function ($query) {
                $query->where('user_id', auth()->id());
            })->get();
            return responseJson(true, trans('messages.Favourites products fetched successfully'), ProductResource::collection($products));
        } catch (\Throwable $th) {
            return responseJson(false, 'Failed to get products', $th->getMessage());
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $product_id)
    {
        try {
            $product = Product::find($product_id);

            if (!$product) {
                return responseJson(false, trans('messages.Product not found'), null, 404);
            }
            $alreadyFav = $product->favourite()
                ->where('user_id', auth()->id())
                ->exists();

            if ($alreadyFav) {
                return responseJson(false, trans('messages.Product already in favourites'), null, 400);
            }
            $product->favourite()->create([
                'user_id' => auth()->id(),
            ]);
            return responseJson(true, trans('messages.Product added to favourites'), new ProductResource($product));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.Failed to add product to favourites'), $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $product_id)
    {
        try {
            $product = Product::where('id', $product_id)->first();
            if (!$product) {
                return responseJson(false, trans('messages.Product not found'), null, 404);
            }
            $product->favourite()->where('user_id', auth()->id())->delete();
            return responseJson(true, trans('messages.Product removed from favourites'), $product);
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.Failed to remove product from favourites'), $th->getMessage());
        }
    }
}
