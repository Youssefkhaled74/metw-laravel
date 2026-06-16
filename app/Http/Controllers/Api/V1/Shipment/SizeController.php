<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSizeRequest;
use App\Http\Requests\UpdateSizeRequest;
use App\Http\Resources\SizeResource;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $sizes = Size::all();
            return responseJson(true,'Sizes fetched successfully',SizeResource::collection($sizes));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSizeRequest $request)
    {
        try{
            $validated = $request->validated();
            if($request->hasFile('icon')){
                $validated['icon'] = uploadImage($request,'icon', 'storage/sizes');
            }
            $size = Size::create($validated);
            return responseJson(true,'Size created successfully',$size);
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
            $size = Size::findOrFail($id);
            return responseJson(true,'Size fetched successfully',new SizeResource($size));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSizeRequest $request, string $id)
    {
        try{
            $validated = $request->validated();
            $size = Size::findOrFail($id);
            if($request->hasFile('icon')){
                $validated['icon'] = uploadImage($request,'icon','storage/sizes');
            }
            $size->update($validated);
            return responseJson(true,'Size updated successfully',$size);
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
            $size = Size::findOrFail($id);
            $size->delete();
            return responseJson(true,'Size deleted successfully',$size);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
}
