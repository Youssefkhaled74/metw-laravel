<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $reviews = Review::where('user_id', auth()->id())->get();
            return responseJson(true, 'Reviews', $reviews);
        }catch(\Throwable $th){
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'shipment_company_id' => ['required', 'integer'],
                'rate' => ['required', 'integer', 'min:1', 'max:5'],
                'comment' => ['nullable', 'string'],
            ]);
            $review = Review::create([
                'user_id' => auth()->id(),
                'shipment_company_id' => $validated['shipment_company_id'],
                'rate' => $validated['rate'],
                'comment' => $validated['comment'],
            ]);
            return responseJson(true, 'Review created', $review);
        }catch(\Throwable $th){
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $review = Review::findOrFail($id);
            return responseJson(true, 'Review', $review);
        }catch(\Throwable $th){
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $validated = $request->validate([
                'shipment_company_id' => ['required', 'integer'],
                'rate' => ['required', 'integer', 'min:1', 'max:5'],
                'comment' => ['nullable', 'string'],
            ]);
            $review = Review::findOrFail($id);
            $review->update([
                'shipment_company_id' => $validated['shipment_company_id'],
                'rate' => $validated['rate'],
                'comment' => $validated['comment'],
            ]);
            return responseJson(true, 'Review updated', $review);
        }catch(\Throwable $th){
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $review = Review::findOrFail($id);
            $review->delete();
            return responseJson(true, 'Review deleted', $review);
        }catch(\Throwable $th){
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }
}
