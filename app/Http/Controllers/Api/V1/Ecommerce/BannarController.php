<?php

namespace App\Http\Controllers\Api\V1\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannarRequest;
use App\Http\Requests\UpdateBannarRequest;
use App\Http\Resources\BannarResource;
use App\Models\Bannar;
use Illuminate\Http\Request;

class BannarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $bannars = Bannar::active()->get();
            return responseJson(true,'Bannars fetched successfully', BannarResource::collection($bannars));
        }catch(\Throwable $th){
            return responseJson(false,'Bannars not found',$th->getMessage(),500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBannarRequest $request)
    {
        try{
            $validatedData = $request->validated();

            if($request->hasFile('image')){
                $validatedData['image'] = uploadImage($request,'image','storage/bannars');
            }

            $bannar = Bannar::create($validatedData);
            return responseJson(true,'Bannar created successfully',new BannarResource($bannar));
        }catch(\Throwable $th){
            return responseJson(false,'Bannar not created',$th->getMessage(),500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($bannarId)
    {
        try{
            $bannar = Bannar::findOrFail($bannarId);
            return responseJson(true,'Bannar fetched successfully',new BannarResource($bannar));
        }catch(\Throwable $th){
            return responseJson(false,'Bannar not found',$th->getMessage(),500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBannarRequest $request, $bannarId)
    {
        try{
            $validatedData = $request->validated();
            $bannar = Bannar::findOrFail($bannarId);
            if($request->hasFile('image')){
                $validatedData['image'] = uploadImage($request,'image','storage/bannars');
            }
            $bannar->update($validatedData);
            return responseJson(true,'Bannar updated successfully',new BannarResource($bannar));
        }catch(\Throwable $th){
            return responseJson(false,'Bannar not updated',$th->getMessage(),500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($bannarId)
    {
        try{
            $bannar = Bannar::findOrFail($bannarId);
            $bannar->delete();
            return responseJson(true,'Bannar deleted successfully',new BannarResource($bannar));
        }catch(\Throwable $th){
            return responseJson(false,'Bannar not deleted',$th->getMessage(),500);
        }
    }
}
