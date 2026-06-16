<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreConsignmentTypeRequest;
use App\Http\Requests\UpdateConsignmentTypeRequest;
use App\Models\ConsignmentType;

class ConsignmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $consignmentTypes = ConsignmentType::all();
            return responseJson(true,'Consignment types fetched successfully',$consignmentTypes);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConsignmentTypeRequest $request)
    {
        try{
            $validated = $request->validated();
            $consignmentType = ConsignmentType::create($validated);
            return responseJson(true,'Consignment type created successfully',$consignmentType);
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
            $consignmentType = ConsignmentType::findOrFail($id);
            return responseJson(true,'Consignment type fetched successfully',$consignmentType);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConsignmentTypeRequest $request, string $id)
    {
        try{
            $validated = $request->validated();
            $consignmentType = ConsignmentType::findOrFail($id);
            $consignmentType->update($validated);
            return responseJson(true,'Consignment type updated successfully',$consignmentType);
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
            $consignmentType = ConsignmentType::findOrFail($id);
            $consignmentType->delete();
            return responseJson(true,'Consignment type deleted successfully',$consignmentType);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
}
