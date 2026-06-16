<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageTypeRequest;
use App\Http\Requests\UpdatePackageTypeRequest;
use App\Models\PackageType;
use Illuminate\Http\Request;

class PackageTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $packageTypes = PackageType::all();
            return responseJson(true,'Packages fetched successfully',$packageTypes);

        } catch (\Throwable $th) {
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePackageTypeRequest $request)
    {
        try {
            $validated = $request->validated();
            $packageType = PackageType::create($validated);
            return responseJson(true,'Package type created successfully',$packageType);

        } catch (\Throwable $th) {
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $packageType = PackageType::findOrFail($id);
            return responseJson(true,'Package type fetched successfully',$packageType);

        } catch (\Throwable $th) {
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageTypeRequest $request, string $id)
    {
        try {
            $validated = $request->validated();
            $packageType = PackageType::findOrFail($id);
            $packageType->update($validated);
            return responseJson(true,'Package type updated successfully',$packageType);

        } catch (\Throwable $th) {
            return responseJson(false,$th->getMessage(),null,500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $packageType = PackageType::findOrFail($id);
            $packageType->delete();
            return responseJson(true,'Package type deleted successfully',$packageType);

        } catch (\Throwable $th) {
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
}
