<?php

namespace Database\Factories;

use App\Enum\BusinessProfileStatus;
use App\Models\Vendor;
use App\Models\VendorBusinessProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VendorBusinessProfile>
 */
class VendorBusinessProfileFactory extends Factory
{
    protected $model = VendorBusinessProfile::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'legal_name' => fake()->company(),
            'commercial_name' => fake()->company(),
            'tax_number' => fake()->numerify('TAX-########'),
            'commercial_register_number' => fake()->numerify('CR-########'),
            'contact_name' => fake()->name(),
            'contact_phone' => fake()->phoneNumber(),
            'status' => BusinessProfileStatus::INCOMPLETE->value,
            'rejection_reason' => null,
            'submitted_at' => null,
            'reviewed_at' => null,
            'approved_at' => null,
            'metadata' => null,
        ];
    }
}
