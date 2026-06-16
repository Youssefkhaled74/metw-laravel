<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductColor;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $colors = ProductColor::all();
            return responseJson(true, trans('messages.Colors fetched successfully'), $colors);
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
            $color = ProductColor::create($request->all());
            return responseJson(true, trans('messages.Color created successfully'), $color);
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
            $color = ProductColor::findOrFail($id);
            return responseJson(true, trans('messages.Color fetched successfully'), $color);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $color = ProductColor::findOrFail($id);
            $color->update($request->all());
            return responseJson(true, trans('messages.Color updated successfully'), $color);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $color = ProductColor::findOrFail($id);
            $color->delete();
            return responseJson(true, trans('messages.Color deleted successfully'), $color);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
