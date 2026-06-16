<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserAddressRequest;
use App\Http\Requests\UpdateUserAddressRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserAddressResource;
use App\Http\Resources\ZoneResource;
use App\Models\City;
use App\Models\Country;
use App\Models\ShipmentLocation;
use App\Models\State;
use App\Models\UserAddress;
use App\Models\Zone;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index()
    {
        try {
            $userAddresses = UserAddress::with('city', 'country', 'state', 'zone')
                ->where('user_id', auth()->user()->id)->get();
            return responseJson(true, trans('messages.User addresses fetched successfully'), UserAddressResource::collection($userAddresses));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.User addresses not found'), $th->getMessage(), 500);
        }
    }

    public function store(StoreUserAddressRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = auth()->id();

            $userId = auth()->id();
            $hasAddresses = UserAddress::where('user_id', $userId)->exists();

            // لو مفيش عناوين قبل كده، خليه الافتراضي
            if (! $hasAddresses) {
                $validatedData['is_default'] = true;
            }

            // لو الريكوست فيه is_default = true خلى الباقي false
            if (!empty($validatedData['is_default']) && $validatedData['is_default'] == true) {
                UserAddress::where('user_id', $userId)->update(['is_default' => false]);
            }

            $userAddress = UserAddress::create($validatedData);

            return responseJson(true, trans('messages.User address created successfully'), new UserAddressResource($userAddress));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.User address not created'), $th->getMessage(), 500);
        }
    }

    public function setDefault($userAddressId)
    {
        try {
            $userAddress = UserAddress::where('user_id', auth()->user()->id)->findOrFail($userAddressId);

            // Unset previous default addresses
            UserAddress::where('user_id', auth()->user()->id)->update(['is_default' => false]);

            // Set the selected address as default
            $userAddress->is_default = true;
            $userAddress->save();

            return responseJson(true, trans('messages.User address set as default successfully'), new UserAddressResource($userAddress));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.User address not set as default'), $th->getMessage(), 500);
        }
    }

    public function show($userAddressId)
    {
        try {
            $userAddress = UserAddress::with('city')->where('user_id', auth()->user()->id)->findOrFail($userAddressId);
            return responseJson(true, trans('messages.User address fetched successfully'), new UserAddressResource($userAddress));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.User address not found'), $th->getMessage(), 500);
        }
    }

    public function update(UpdateUserAddressRequest $request, $userAddressId)
    {
        try {
            $validatedData = $request->validated();
            $userId = auth()->id();

            $userAddress = UserAddress::where('user_id', $userId)->findOrFail($userAddressId);

            // لو الريكوست فيه is_default = true خلى الباقي false
            if (!empty($validatedData['is_default']) && $validatedData['is_default'] == true) {
                UserAddress::where('user_id', $userId)
                    ->where('id', '!=', $userAddressId)
                    ->update(['is_default' => false]);
            }

            $userAddress->update($validatedData);

            return responseJson(true, trans('messages.User address updated successfully'), new UserAddressResource($userAddress));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.User address not updated'), $th->getMessage(), 500);
        }
    }

    public function destroy($userAddressId)
    {
        try {
            $userAddress = UserAddress::where('user_id', auth()->id())
                ->findOrFail($userAddressId);

            $wasDefault = $userAddress->is_default;
            $userAddress->delete();

            if ($wasDefault) {
                $newDefault = UserAddress::where('user_id', auth()->id())
                    ->where('id', '!=', $userAddressId)
                    ->first();

                if ($newDefault) {
                    $newDefault->is_default = true;
                    $newDefault->save();
                }
            }

            return responseJson(true, trans('messages.User address deleted successfully'), new UserAddressResource($userAddress));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.User address not deleted'), $th->getMessage(), 500);
        }
    }


    public function country()
    {
        try {
            $countries = Country::active()->get();
            return responseJson(true, trans('messages.Countries fetched successfully'), CountryResource::collection($countries));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.Countries not found'), $th->getMessage(), 500);
        }
    }

    public function state(Request $request)
    {
        try {
            $isShipment = filter_var($request->query('is_shipment'), FILTER_VALIDATE_BOOLEAN);

            $query = State::active()->with('cities.zones');

            if ($isShipment) {
                $stateIds = ShipmentLocation::active()
                    ->whereHas('shipmentCompany')
                    ->pluck('state')
                    ->flatten()
                    ->unique()
                    ->values();

                $query->whereIn('id', $stateIds);
            }

            $states = $query->get();

            return responseJson(
                true,
                trans('messages.States fetched successfully'),
                StateResource::collection($states)
            );
        } catch (\Throwable $th) {
            return responseJson(
                false,
                trans('messages.States not found'),
                $th->getMessage(),
                500
            );
        }
    }



    public function city(Request $request, $stateId)
    {
        try {
            $isShipment = filter_var($request->query('is_shipment'), FILTER_VALIDATE_BOOLEAN);

            $query = City::active()
                ->where('state_id', $stateId)
                ->with('zones');

            if ($isShipment) {
                $cityIds = ShipmentLocation::active()
                    ->whereJsonContains('state', (string) $stateId)
                    ->pluck('city')
                    ->flatten()
                    ->unique()
                    ->values();

                $query->whereIn('id', $cityIds);
            }

            $cities = $query->get();

            return responseJson(
                true,
                trans('messages.Cities fetched successfully'),
                CityResource::collection($cities)
            );
        } catch (\Throwable $th) {
            return responseJson(
                false,
                trans('messages.Cities not found'),
                $th->getMessage(),
                500
            );
        }
    }


    public function zone(Request $request, $cityId)
    {
        try {
            $isShipment = filter_var($request->query('is_shipment'), FILTER_VALIDATE_BOOLEAN);

            $query = Zone::active()
                ->where('city_id', $cityId);

            if ($isShipment) {
                $zoneIds = ShipmentLocation::active()
                    ->whereHas('shipmentCompany')
                    ->whereJsonContains('city', (string) $cityId)
                    ->pluck('zone')
                    ->flatten()
                    ->unique()
                    ->values();

                $query->whereIn('id', $zoneIds);
            }

            $zones = $query->get();

            return responseJson(
                true,
                trans('messages.Zones fetched successfully'),
                ZoneResource::collection($zones)
            );
        } catch (\Throwable $th) {
            return responseJson(
                false,
                trans('messages.Zones not found'),
                $th->getMessage(),
                500
            );
        }
    }


}
