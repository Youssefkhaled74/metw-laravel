<?php

namespace App\Services;

use App\Enum\OrderStatus;
use App\Models\PackageTracking;

class PackageTrackingService
{
    public static function createStatus(
        int $packageId,
        ?int $orderItemId,
        OrderStatus $status,
        ?string $location = null,
        ?string $description = null,
        array $metadata = []
    ): PackageTracking {
        return PackageTracking::create([
            'package_id' => $packageId,
            'order_item_id' => $orderItemId,
            'status' => $status->value,
            'location' => $location,
            'description' => $description,
            'occurred_at' => now(),
            'metadata' => $metadata,
        ]);
    }
}
