<?php

namespace App\Http\Controllers\Api\V1\Shipment\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Enum\AddressType;
use App\Models\PackageAddress;

class SavedAddressController extends Controller
{
    /**
     * Get all saved addresses for the authenticated user
     */
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:pickup,dropoff',
        ]);

        $type = $request->input('type');

        $query = PackageAddress::onlySaved()
            ->forUser(auth()->id())
            ->with([
                'city',
                'state',
                'governorate',
                'zone',
                'country',
                'pickupPackages.packageDetails', // load packageDetails for pickup packages only
            ]);

        if ($type) {
            $addressType = $type === 'pickup' ? AddressType::PICKUP : AddressType::DROPOFF;
            $query->byType($addressType);
        }

        $addresses = $query
            ->selectRaw('
                MAX(id) as id,
                user_id,
                type,
                address,
                latitude,
                longitude,
                city_id,
                state_id,
                governorate_id,
                zone_id,
                generated_address_number,
                address_name,
                district_or_village_name,
                district_or_village_type,
                street_name,
                branch_from_street,
                building_number,
                floor_number,
                building_name,
                nearby_landmark,
                address_description
            ')
            ->groupBy(
                'user_id',
                'type',
                'address',
                'latitude',
                'longitude',
                'city_id',
                'state_id',
                'governorate_id',
                'zone_id'
                ,
                'generated_address_number',
                'address_name',
                'district_or_village_name',
                'district_or_village_type',
                'street_name',
                'branch_from_street',
                'building_number',
                'floor_number',
                'building_name',
                'nearby_landmark',
                'address_description'
            )
            ->orderByDesc('id')
            ->get();
        // Transform the response: replace pickupPackages with package_details
        $addressesTransformed = $addresses->map(function ($address) {
            return [
                'id' => $address->id,
                'user_id' => $address->user_id,
                'is_saved' => $address->is_saved,
                'location' => $address->location,
                'country_id' => $address->country_id,
                'state_id' => $address->state_id,
                'city_id' => $address->city_id,
                'zone_id' => $address->zone_id,
                'governorate_id' => $address->governorate_id,
                'landmark' => $address->landmark,
                'phone' => $address->phone,
                'address' => $address->address,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'type' => $address->type,
                'generated_address_number' => $address->generated_address_number,
                'address_name' => $address->address_name,
                'district_or_village_name' => $address->district_or_village_name,
                'district_or_village_type' => $address->district_or_village_type,
                'street_name' => $address->street_name,
                'branch_from_street' => $address->branch_from_street,
                'building_number' => $address->building_number,
                'floor_number' => $address->floor_number,
                'building_name' => $address->building_name,
                'nearby_landmark' => $address->nearby_landmark,
                'address_description' => $address->address_description,
                'created_at' => $address->created_at,
                'updated_at' => $address->updated_at,
                'city' => $address->city,
                'state' => $address->state,
                'governorate' => $address->governorate,
                'zone' => $address->zone,
                'country' => $address->country,
                // Replace pickupPackages with package_details
                'package_details' => $address->pickupPackages->map(function ($pkg) {
                    return $pkg->packageDetails;
                }),
            ];
        });

        return responseJson(true, "Saved addresses retrieved", [
            'addresses' => $addressesTransformed,
            'count' => $addressesTransformed->count(),
            'filters' => [
                'type' => $type,
            ]
        ]);
    }



    /**
     * Save a new address
     */
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'phone' => 'required|string',
            'city_id' => 'required|integer|exists:cities,id',
            'governorate_id' => 'nullable|integer|exists:governorates,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'zone_id' => 'nullable|integer|exists:zones,id',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'is_village' => 'required|boolean',
            'type' => 'required|in:pickup,dropoff',
            'name' => 'nullable|string|max:100',
            'location' => 'nullable|string',
            'landmark' => 'nullable|string',
            'address_name' => 'nullable|string|max:255',
            'district_or_village_name' => 'nullable|string|max:255',
            'district_or_village_type' => 'nullable|in:district,village',
            'street_name' => 'nullable|string|max:255',
            'branch_from_street' => 'nullable|string|max:255',
            'building_number' => 'nullable|integer|min:0',
            'floor_number' => 'nullable|integer|min:0',
            'building_name' => 'nullable|string|max:255',
            'nearby_landmark' => 'nullable|string|max:255',
            'address_description' => 'nullable|string|max:1000',
        ]);

        $addressType = $request->type === 'pickup' ? AddressType::PICKUP : AddressType::DROPOFF;
        $governorateId = $request->governorate_id;

        if (! $governorateId && $request->filled('city_id')) {
            $governorateId = City::withoutGlobalScopes()->find($request->city_id)?->governorate_id;
        }

        $savedAddress = PackageAddress::create([
            'address' => $request->address,
            'phone' => $request->phone,
            'city_id' => $request->city_id,
            'governorate_id' => $governorateId,
            'state_id' => $request->state_id,
            'zone_id' => $request->zone_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_village' => $request->is_village,
            'type' => $addressType,
            'is_saved' => true,
            'name' => $request->name,
            'location' => $request->location,
            'landmark' => $request->landmark,
            'address_name' => $request->address_name,
            'district_or_village_name' => $request->district_or_village_name,
            'district_or_village_type' => $request->district_or_village_type,
            'street_name' => $request->street_name,
            'branch_from_street' => $request->branch_from_street,
            'building_number' => $request->building_number,
            'floor_number' => $request->floor_number,
            'building_name' => $request->building_name,
            'nearby_landmark' => $request->nearby_landmark,
            'address_description' => $request->address_description,
            'user_id' => auth()->id(),
        ]);

        return responseJson(true, "Address saved successfully", [
            'address' => $savedAddress->load(['city', 'state', 'governorate', 'zone', 'country']),
        ]);
    }

    /**
     * Update a saved address
     */
    public function update(Request $request, PackageAddress $address)
    {
        // Check if user owns this address
        if ($address->user_id !== auth()->id()) {
            return responseJson(false, "Unauthorized", [], 403);
        }

        $request->validate([
            'address' => 'sometimes|string',
            'phone' => 'sometimes|string',
            'city_id' => 'sometimes|integer|exists:cities,id',
            'governorate_id' => 'sometimes|integer|exists:governorates,id',
            'state_id' => 'sometimes|integer|exists:states,id',
            'zone_id' => 'sometimes|integer|exists:zones,id',
            'latitude' => 'sometimes|string',
            'longitude' => 'sometimes|string',
            'is_village' => 'sometimes|boolean',
            'name' => 'nullable|string|max:100',
            'location' => 'nullable|string',
            'landmark' => 'nullable|string',
            'address_name' => 'nullable|string|max:255',
            'district_or_village_name' => 'nullable|string|max:255',
            'district_or_village_type' => 'nullable|in:district,village',
            'street_name' => 'nullable|string|max:255',
            'branch_from_street' => 'nullable|string|max:255',
            'building_number' => 'nullable|integer|min:0',
            'floor_number' => 'nullable|integer|min:0',
            'building_name' => 'nullable|string|max:255',
            'nearby_landmark' => 'nullable|string|max:255',
            'address_description' => 'nullable|string|max:1000',
        ]);

        $governorateId = $request->governorate_id;
        if (! $governorateId && $request->filled('city_id')) {
            $governorateId = City::withoutGlobalScopes()->find($request->city_id)?->governorate_id;
        }

        $address->update($request->only([
            'address', 'phone', 'city_id', 'governorate_id', 'state_id', 'zone_id',
            'latitude', 'longitude', 'is_village', 'name',
            'location', 'landmark', 'address_name', 'district_or_village_name',
            'district_or_village_type', 'street_name', 'branch_from_street',
            'building_number', 'floor_number', 'building_name', 'nearby_landmark',
            'address_description'
        ]));

        if ($governorateId) {
            $address->update(['governorate_id' => $governorateId]);
        }

        return responseJson(true, "Address updated successfully", [
            'address' => $address->fresh()->load(['city', 'state', 'governorate', 'zone', 'country']),
        ]);
    }

    /**
     * Delete a saved address
     */
    public function destroy($address)
    {
        $address = PackageAddress::find($address);
        // Check if user owns this address
        if ($address->user_id !== auth()->id()) {
            return responseJson(false, "Unauthorized", [], 403);
        }

        // Only allow deletion of saved addresses (not addresses linked to packages)
        if ($address->exists()) {
            // Instead of deleting, just mark as not saved
            $address->update(['is_saved' => false]);
            return responseJson(true, "Address removed from saved list");
        }

        // $address->delete();

        return responseJson(true, "Address deleted successfully");
    }

    /**
     * Get address by ID
     */
    public function show(PackageAddress $address)
    {
        // Check if user owns this address
        if ($address->user_id !== auth()->id()) {
            return responseJson(false, "Unauthorized", [], 403);
        }

        return responseJson(true, "Address retrieved", [
            'address' => $address->load(['city', 'state', 'governorate', 'zone', 'country']),
        ]);
    }
}
