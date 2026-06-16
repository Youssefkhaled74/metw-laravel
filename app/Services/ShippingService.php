<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\PackageAddress;
use App\Models\ShipmentCompany;
use App\Models\ShipmentLocation;

class ShippingService
{
    /**
     * Calculate Haversine distance in KM between two coordinates.
     */
    public static function calculateDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371.0;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * Calculate distance in KM for a package from pickup to dropoff.
     */
    public static function calculatePackageDistanceKm(Package $package): ?float
    {
        $pickup = $package->pickupAddress;
        $dropoff = $package->dropoffAddress;

        if (!$pickup || !$dropoff) {
            return null;
        }

        if (
            is_null($pickup->latitude) || is_null($pickup->longitude)
            || is_null($dropoff->latitude) || is_null($dropoff->longitude)
        ) {
            return null;
        }

        return self::calculateDistanceKm(
            (float) $pickup->latitude,
            (float) $pickup->longitude,
            (float) $dropoff->latitude,
            (float) $dropoff->longitude
        );
    }

    /**
     * Estimate shipping price for an order item using company's price per km.
     */
    public static function estimateItemPrice(ShipmentCompany $company, OrderItem $orderItem): ?float
    {
        $package = $orderItem->package;
        if (!$package) {
            return null;
        }

        $distanceKm = self::calculatePackageDistanceKm($package);
        if ($distanceKm === null) {
            return null;
        }

        $pricePerKm = (float) ($company->price_per_km ?? 0);
        return round($distanceKm * $pricePerKm, 2);
    }

    /**
     * Determine if a shipment location record covers a package address.
     * Matching is done by most specific field present on the location JSON.
     */
    public static function locationCoversAddress(ShipmentLocation $location, PackageAddress $address): bool
    {
        // Location JSONs can be arrays like ["id" => 1, "name" => "..."]
        $countryOk = true;
        $stateOk = true;
        $cityOk = true;
        $zoneOk = true;

        $country = $location->country ?? null;
        $state = $location->state ?? null;
        $city = $location->city ?? null;
        $zone = $location->zone ?? null;

        if (is_array($country) && isset($country['id'])) {
            $countryOk = (int) $address->country_id === (int) $country['id'];
        }
        if (is_array($state) && isset($state['id'])) {
            $stateOk = (int) $address->state_id === (int) $state['id'];
        }
        if (is_array($city) && isset($city['id'])) {
            $cityOk = (int) $address->city_id === (int) $city['id'];
        }
        if (is_array($zone) && isset($zone['id'])) {
            $zoneOk = (int) $address->zone_id === (int) $zone['id'];
        }

        return $countryOk && $stateOk && $cityOk && $zoneOk;
    }

    /**
     * Check if shipment company can cover a package (pickup and dropoff).
     */
    public static function companyCoversPackage(ShipmentCompany $company, Package $package): bool
    {
        $locations = $company->locations()->active()->get();
        if ($locations->isEmpty()) {
            return false;
        }

        $pickup = $package->pickupAddress;
        $dropoff = $package->dropoffAddress;
        if (!$pickup || !$dropoff) {
            return false;
        }

        $pickupCovered = $locations->contains(function (ShipmentLocation $loc) use ($pickup) {
            return self::locationCoversAddress($loc, $pickup);
        });
        $dropoffCovered = $locations->contains(function (ShipmentLocation $loc) use ($dropoff) {
            return self::locationCoversAddress($loc, $dropoff);
        });

        return $pickupCovered && $dropoffCovered;
    }

    /**
     * Check that company covers every package in the order.
     */
    public static function companyCoversOrder(ShipmentCompany $company, Order $order): bool
    {
        $order->loadMissing(['orderItems.package.pickupAddress', 'orderItems.package.dropoffAddress']);
        foreach ($order->orderItems as $item) {
            if (!$item->package || !self::companyCoversPackage($company, $item->package)) {
                return false;
            }
        }
        return true;
    }
}
