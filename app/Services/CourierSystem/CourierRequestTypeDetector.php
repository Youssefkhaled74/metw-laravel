<?php

namespace App\Services\CourierSystem;

use App\Enum\CourierCategory;
use App\Enum\CourierRequestType;
use App\Enum\ShippingType;

/**
 * Detects the automatic request type (Section 2 & 4) from the request profile.
 */
class CourierRequestTypeDetector
{
    public function detect(CourierRequestProfile $profile): CourierRequestType
    {
        if (! $profile->isInterGovernorate()) {
            return CourierRequestType::FAST_DELIVERY;
        }

        // "Direct only" → Type 1 (single path).
        if ($profile->shippingType === ShippingType::DIRECT_ONLY) {
            return CourierRequestType::SINGLE_PATH;
        }

        // "Direct + Multi" + category 3 → Type 2 (dual path: direct + warehouses).
        if ($profile->courierCategory === CourierCategory::CATEGORY_3) {
            return CourierRequestType::DUAL_PATH;
        }

        // "Direct + Multi" + category 1 or 2 (or unknown) → Type 3 (triple path).
        return CourierRequestType::TRIPLE_PATH;
    }
}
