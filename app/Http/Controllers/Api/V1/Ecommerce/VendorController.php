<?php

namespace App\Http\Controllers\Api\V1\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginVendorRequest;
use App\Http\Requests\RegisterVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit  = $request->limit ?? 10;
            $page   = $request->page ?? 1;
            $search = $request->search;
            $isActive = $request->is_active; // Optional filter

            $vendors = Vendor::query();

            // ✅ Filter by activation status if provided
            if (!is_null($isActive)) {
                $vendors->where('is_active', (bool)$isActive);
            }

            // 🔍 Apply search if provided
            if (!empty($search)) {
                $vendors->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            $payload = paginate($vendors, VendorResource::class, $limit, $page);

            return responseJson(true, 'Vendors fetched successfully', $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterVendorRequest $request)
    {
        try{
            $validatedData = $request->validated();

            if ($request->hasFile('logo')) {
                $validatedData['logo'] = uploadImage($request, 'logo', 'storage/vendors');
            }

            $vendor = Vendor::create($validatedData);

            return responseJson(true,'Vendor created successfully',$vendor);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,$VendorId)
    {
        try{
            $vendor = Vendor::with('products.media')->findOrFail($VendorId);
            return responseJson(true,'Vendor fetched successfully',new VendorResource($vendor));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendorRequest $request,$VendorId)
    {
        try{
            $validatedData = $request->validated();
            $vendor = Vendor::findOrFail($VendorId);
            if ($request->hasFile('logo')) {
                $validatedData['logo'] = uploadImage($request, 'logo', 'storage/vendors');
            }
            $vendor->update($validatedData);
            return responseJson(true,'Vendor updated successfully',new VendorResource($vendor));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($VendorId)
    {
        try{
            $vendor = Vendor::findOrFail($VendorId);
            $vendor->delete();
            return responseJson(true,'Vendor deleted successfully',$vendor);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    public function login(LoginVendorRequest $request)
    {
        try{
            $validatedData = $request->validated();
            $vendor = Vendor::where('email',$validatedData['email'])->first();
            if(!$vendor){
                return responseJson(false,'Vendor not found',null,404);
            }
            if(!Hash::check($validatedData['password'],$vendor->password)){
                return responseJson(false,'Invalid credentials',null,401);
            }
            $token = $vendor->createToken('vendor-token')->plainTextToken;
            return responseJson(true,'Vendor logged in successfully',['vendor'=>$vendor,'token'=>$token]);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
}
