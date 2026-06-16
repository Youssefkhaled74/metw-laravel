<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $locations = Location::all();
            return responseJson(true,'Locations fetched successfully',['locations' => LocationResource::collection($locations)]);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validatedData = $request->validated();
            $location = Location::create($validatedData);
            return responseJson(true,'Location created successfully',['location' => new LocationResource($location)]);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        try{
            return responseJson(true,'Location fetched successfully',['location' => new LocationResource($location)]);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        try{
            $validatedData = $request->validated();
            $location->update($validatedData);
            return responseJson(true,'Location updated successfully',['location' => new LocationResource($location)]);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        try{
            $location->delete();
            return responseJson(true,'Location deleted successfully',$location);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
}
