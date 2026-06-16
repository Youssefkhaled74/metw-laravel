<?php

namespace App\Http\Controllers\Api\V1\Shipment;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavouriteResource;
use App\Models\Favourite;
use App\Models\ShipmentCompany;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $favourites = Favourite::where('user_id', auth()->id())
                ->whereHasMorph(
                    'favouriteable',
                    [ShipmentCompany::class],
                    function ($q) {
                        $q->where('is_active', true);
                    }
                )
                ->with('favouriteable')
                ->get();

            return responseJson(true, 'Favourites fetched successfully', FavouriteResource::collection($favourites));
        } catch (\Throwable $e) {
            return responseJson(false, 'Failed to fetch favourites', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function toggle(Request $request, $shipmentCompanyId)
    {
        try {
            $company = ShipmentCompany::where('id', $shipmentCompanyId)->first();
            if (!$company) {
                return responseJson(false, 'Company not found', null, 404);
            }
            $favourite = $company->favourites()->where('user_id', auth()->id())->first();
            if ($favourite) {
                $favourite->delete();
                return responseJson(true, 'Favourite removed successfully', $favourite);
            }
            $favourite = $company->favourites()->create([
                'user_id' => auth()->id(),
            ]);
            return responseJson(true, 'Favourite added successfully', $favourite);
        } catch (\Throwable $e) {
            return responseJson(false, 'Failed to add favourite', $e->getMessage());
        }
    }
    public function addFavourite(Request $request, $shipmentCompanyId)
    {
        try {
            $company = ShipmentCompany::find($shipmentCompanyId);
            if (!$company) {
                return responseJson(false, 'Company not found', null, 404);
            }

            // check if already favourited
            $favourite = Favourite::where('user_id', auth()->id())
                ->where('favouriteable_id', $company->id)
                ->where('favouriteable_type', ShipmentCompany::class)
                ->first();

            if ($favourite) {
                return responseJson(false, 'Company already added to favourites', null, 400);
            }

            // create favourite
            $favourite = $company->favourites()->create([
                'user_id' => auth()->id(),
            ]);

            return responseJson(true, 'Favourite added successfully', $favourite);
        } catch (\Throwable $e) {
            return responseJson(false, 'Failed to add favourite', $e->getMessage());
        }
    }

    public function removefavourite(Request $request, $shipmentCompanyId)
    {
        try {
            $company = ShipmentCompany::where('id', $shipmentCompanyId)->first();
            if (!$company) {
                return responseJson(false, 'Company not found', null, 404);
            }
            $favourite = Favourite::where('user_id', auth()->id())
                ->where('favouriteable_id', $company->id)
                ->where('favouriteable_type', ShipmentCompany::class)
                ->first();
            if (!$favourite) {
                return responseJson(false, 'Company not found in favourites', null, 404);
            }
            $favourite->delete();
            return responseJson(true, 'Favourite removed successfully', $favourite);
        } catch (\Throwable $e) {
            return responseJson(false, 'Failed to remove favourite', $e->getMessage());
        }
    }
}
