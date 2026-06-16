<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductSize;
use Illuminate\Http\Request;

class ProductSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $sizes = ProductSize::all();
            return responseJson(true, trans('messages.Sizes fetched successfully'), $sizes);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $size = ProductSize::create($request->all());
            return responseJson(true, trans('messages.Size created successfully'), $size);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductSize $productSize)
    {
        try {
            $size = ProductSize::findOrFail($productSize->id);
            return responseJson(true, trans('messages.Size fetched successfully'), $size);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductSize $productSize)
    {
        try {
            $size = ProductSize::findOrFail($productSize->id);
            $size->update($request->all());
            return responseJson(true, trans('messages.Size updated successfully'), $size);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductSize $productSize)
    {
        try {
            $size = ProductSize::findOrFail($productSize->id);
            $size->delete();
            return responseJson(true, trans('messages.Size deleted successfully'), $size);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
