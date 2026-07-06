<?php

namespace App\Http\Controllers\Api\V1\Ecommerce\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserAddressRequest;
use App\Http\Requests\UpdateUserAddressRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\GovernorateResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\UserAddressResource;
use App\Http\Resources\ZoneResource;
use App\Models\City;
use App\Models\ShipmentLocation;
use App\Models\Governorate;
use App\Models\State;
use App\Models\UserAddress;
use App\Models\Zone;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index()
    {
        try {
            $userAddresses = UserAddress::with('city', 'country', 'state', 'zone', 'governorate')
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
            $validatedData = $this->normalizeAddressData($validatedData);

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
            $userAddress = UserAddress::with('city', 'governorate')->where('user_id', auth()->user()->id)->findOrFail($userAddressId);
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
            $validatedData = $this->normalizeAddressData($validatedData, true);

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
            $governorates = Governorate::active()->orderBy('name_ar')->get();
            return responseJson(true, trans('messages.Governorates fetched successfully'), GovernorateResource::collection($governorates));
        } catch (\Throwable $th) {
            return responseJson(false, trans('messages.Governorates not found'), $th->getMessage(), 500);
        }
    }

    public function state(Request $request)
    {
        try {
            $governorates = Governorate::active()->orderBy('name_ar')->get();

            return responseJson(
                true,
                trans('messages.Governorates fetched successfully'),
                GovernorateResource::collection($governorates)
            );
        } catch (\Throwable $th) {
            return responseJson(
                false,
                trans('messages.Governorates not found'),
                $th->getMessage(),
                500
            );
        }
    }



    public function city(Request $request, $stateId)
    {
        try {
            $query = City::active()
                ->where('governorate_id', $stateId)
                ->with('zones');

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
            $query = Zone::active()
                ->where('city_id', $cityId);

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

    public function governorates()
    {
        return $this->country();
    }

    protected function normalizeAddressData(array $data, bool $isUpdate = false): array
    {
        if (! array_key_exists('governorate_id', $data) || blank($data['governorate_id'] ?? null)) {
            if (! empty($data['city_id'])) {
                $city = City::withoutGlobalScopes()->find($data['city_id']);
                if ($city) {
                    $data['governorate_id'] = $city->governorate_id;
                    $data['state_id'] = $data['state_id'] ?? $city->state_id;
                }
            } elseif (array_key_exists('state_id', $data)) {
                $data['governorate_id'] = $data['state_id'];
            }
        }

        if (! array_key_exists('address_name', $data) || blank($data['address_name'] ?? null)) {
            $data['address_name'] = $data['address_name'] ?? ($data['district_or_village_name'] ?? 'العنوان');
        }

        if (array_key_exists('building', $data) && ! array_key_exists('building_number', $data)) {
            $data['building_number'] = is_numeric($data['building']) ? (int) $data['building'] : $data['building'];
        }

        if (array_key_exists('floor', $data) && ! array_key_exists('floor_number', $data)) {
            $data['floor_number'] = is_numeric($data['floor']) ? (int) $data['floor'] : $data['floor'];
        }

        if (! array_key_exists('nearby_landmark', $data) && array_key_exists('landmark', $data)) {
            $data['nearby_landmark'] = $data['landmark'];
        }

        if (! array_key_exists('district_or_village_name', $data)) {
            $data['district_or_village_name'] = $data['district_or_village_name'] ?? null;
        }

        return $data;
    }


}
