<?php

namespace App\Services;

use App\Enum\AddressType;
use App\Models\Package;
use App\Models\PackageAddress;
use App\Models\PackageDetails;
use App\Models\PackageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageService
{
    public static function createFromPayload(array $data, Request $request): Package
    {
        $userId = auth()->id();

        $pickupData = $data['pickup_address'] ?? [];
        $dropoffData = $data['dropoff_address'] ?? [];

        // Handle pickup address
        if (!empty($pickupData['saved_address_id'])) {
            $pickup = PackageAddress::find($pickupData['saved_address_id']);

            // Only update if any data except is_saved has changed
            if ($pickup && array_diff_assoc($pickupData, ['is_saved' => $pickupData['is_saved'] ?? false])) {
                $pickup->update($pickupData);
            }
        } else {
            $pickup = PackageAddress::create(
                array_merge($pickupData, [
                    'type' => AddressType::PICKUP->value,
                    'user_id' => $userId,
                    'is_saved' => $pickupData['is_saved'] ?? false,
                ])
            );
        }
        // Handle dropoff address
        if (!empty($dropoffData['saved_address_id'])) {
            $dropoff = PackageAddress::find($dropoffData['saved_address_id']);

            // Only update if any data except is_saved has changed
            if ($dropoff && array_diff_assoc($dropoffData, ['is_saved' => $dropoffData['is_saved'] ?? false])) {
                $dropoff->update($dropoffData);
            }
        } else {
            $dropoff = PackageAddress::create(
                array_merge($dropoffData, [
                    'type' => AddressType::DROPOFF->value,
                    'user_id' => $userId,
                    'is_saved' => $dropoffData['is_saved'] ?? false,
                ])
            );
        }
        $details = PackageDetails::create($data['details'] ?? []);

        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = strtoupper(substr(str_shuffle(str_repeat($pool, 5)), 0, 7));

        $package = Package::create([
            'package_number'      => $code,
            'type_id'             => $data['type_id'] ?? null,
            'size'             => $data['size'] ?? null,
            'piece'             => $data['piece'] ?? null,
            'pickup_address_id'   => $pickup->id,
            'dropoff_address_id'  => $dropoff->id,
            'package_details_id'  => $details->id,
            'shipment_company_id' => $data['shipment_company_id'] ?? null,
            'delivery_type_id'    => $data['delivery_type_id'] ?? null,
            'consignment_type_id' => $data['consignment_type_id'] ?? null,
            'category_id'         => $data['category_id'],
            'sub_category_id'     => $data['sub_category_id'],
            'weight'              => $data['weight'],
            // 'user_id'             => auth()->id(),
        ]);

        if (!empty($data['images']) && is_array($data['images'])) {

            $images = uploadImages($request, 'images', 'storage/packages');
            $images = explode(',', $images);

            foreach ($images as $image) {
                PackageImage::create([
                    'package_id' => $package->id,
                    'image'      => $image,
                ]);
            }
        }

        return $package->fresh(['pickupAddress', 'dropoffAddress', 'packageDetails', 'images']);
    }

    public static function updateFromPayload(Package $package, array $data, Request $request): Package
    {
        $package->update($data);

        if (!empty($data['images']) && is_array($data['images'])) {
            $images = updateImages($request, 'images', 'storage/packages');
            $images = explode(',', $images);

            foreach ($images as $image) {
                PackageImage::create([
                    'package_id' => $package->id,
                    'image'      => $image,
                ]);
            }
        }

        return $package->fresh(['pickupAddress', 'dropoffAddress', 'packageDetails', 'images']);
    }
}
