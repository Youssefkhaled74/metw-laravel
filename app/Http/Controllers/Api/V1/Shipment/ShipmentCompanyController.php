<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentCompanyRequest;
use App\Http\Requests\UpdateShipmentCompanyRequest;
use App\Http\Resources\ShipmentCompanyResource;
use App\Http\Resources\ShipmentLocationResource;
use App\Models\Location;
use App\Models\ShipmentCompany;
use Illuminate\Http\Request;

class ShipmentCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            $query = ShipmentCompany::query()
                ->active()
                ->withAvg('reviews', 'rate')
                ->withCount('reviews');

            if (auth()->check()) {
                $userId = auth()->id();
                $query->withExists([
                    'favourites as is_favourite' => fn($q) => $q->where('user_id', $userId),
                ]);
            }

            $payload = paginate($query, ShipmentCompanyResource::class, $limit, $page);

            return responseJson(true, 'Shipment companies fetched successfully', $payload);
        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShipmentCompanyRequest $request)
    {
        try{
            $validatedData = $request->validated();
            if($validatedData['logo']){
                $validatedData['logo'] = uploadImage($request,'logo','storage/shipment_companies');
            }
            $shipmentCompany = ShipmentCompany::create($validatedData);
            $shipmentCompany->coverages()->attach($validatedData['coverages']);
            return responseJson(true,'Shipment company created successfully', new ShipmentCompanyResource($shipmentCompany));
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }


    public function show(string $id)
    {
        try {
            $company = ShipmentCompany::with([
                    'reviews.user',
                    'shipmentLocations' => fn ($q) => $q->active()
                ])
                ->withCount('reviews')
                ->withAvg('reviews', 'rate')
                ->findOrFail($id);

            $locations = $company->shipmentLocations;

            $coverage = ShipmentLocationResource::make($locations)->resolve();

            $companyResource = (new ShipmentCompanyResource($company))->toArray(request());

            $companyResource['count_reviews'] = $company->reviews_count;
            $companyResource['average_rating'] = round(
                $company->reviews_avg_rate ?? 0,
                2
            );

            $companyResource['coverage'] = $coverage;

            return responseJson(true, 'Shipment company fetched successfully', $companyResource);

        } catch (\Throwable $th) {
            return responseJson(false, $th->getMessage(), null, 500);
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipmentCompanyRequest $request, string $id)
    {
        try{
            $validatedData = $request->validated();
            if($validatedData['logo']){
                $validatedData['logo'] = uploadImage($request,'logo','storage/shipment_companies');
            }
            $shipmentCompany = ShipmentCompany::findOrFail($id);
            $shipmentCompany->update($validatedData);
            return responseJson(true,'Shipment company updated successfully', new ShipmentCompanyResource($shipmentCompany));
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
            $shipmentCompany = ShipmentCompany::findOrFail($id);
            $shipmentCompany->delete();
            return responseJson(true,'Shipment company deleted successfully',$shipmentCompany);
        }catch(\Throwable $th){
            return responseJson(false,$th->getMessage(),null,500);
        }
    }
}
