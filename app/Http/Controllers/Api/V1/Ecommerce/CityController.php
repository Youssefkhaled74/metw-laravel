<?php

namespace App\Http\Controllers\Api\V1\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $cities = City::active()->get();
            return responseJson(true,'Cities fetched successfully', CityResource::collection($cities));
        }catch(\Throwable $th){
            return responseJson(false,'Cities not found',$th->getMessage(),500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validatedData = $request->validated();
            $city = City::create($validatedData);
            return responseJson(true,'City created successfully',new CityResource($city));
        }catch(\Throwable $th){
            return responseJson(false,'City not created',$th->getMessage(),500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($cityId)
    {
        try{
            $city = City::findOrFail($cityId);
            return responseJson(true,'City fetched successfully',new CityResource($city));
        }catch(\Throwable $th){
            return responseJson(false,'City not found',$th->getMessage(),500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $cityId)
    {
        try{
            $validatedData = $request->validated();
            $city = City::findOrFail($cityId);
            $city->update($validatedData);
            return responseJson(true,'City updated successfully',new CityResource($city));
        }catch(\Throwable $th){
            return responseJson(false,'City not updated',$th->getMessage(),500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($cityId)
    {
        try{
            $city = City::findOrFail($cityId);
            $city->delete();
            return responseJson(true,'City deleted successfully',new CityResource($city));
        }catch(\Throwable $th){
            return responseJson(false,'City not deleted',$th->getMessage(),500);
        }
    }
}
