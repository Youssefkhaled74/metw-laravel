<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryTypeRequest;
use App\Http\Requests\UpdateDeliveryTypeRequest;
use App\Models\DeliveryType;
use Illuminate\Http\Request;

class DeliveryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $deliveryTypes = DeliveryType::all();
            return responseJson(true,'Delivery types fetched successfully',$deliveryTypes);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeliveryTypeRequest $request)
    {
        try{
            $validated = $request->validated();
            $deliveryType = DeliveryType::create($validated);
            return responseJson(true,'Delivery type created successfully',$deliveryType);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $deliveryType = DeliveryType::findOrFail($id);
            return responseJson(true,'Delivery type fetched successfully',$deliveryType);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliveryTypeRequest $request, string $id)
    {
        try{
            $validated = $request->validated();
            $deliveryType = DeliveryType::findOrFail($id);
            $deliveryType->update($validated);
            return responseJson(true,'Delivery type updated successfully',$deliveryType);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $deliveryType = DeliveryType::findOrFail($id);
            $deliveryType->delete();
            return responseJson(true,'Delivery type deleted successfully',$deliveryType);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
}
