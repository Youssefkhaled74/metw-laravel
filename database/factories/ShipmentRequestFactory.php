<?php

namespace Database\Factories;

use App\Enum\ShipmentRequestStatus;
use App\Models\ShipmentContact;
use App\Models\ShipmentRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipmentRequest>
 */
class ShipmentRequestFactory extends Factory
{
    protected $model = ShipmentRequest::class;

    public function definition(): array
    {
        $user = User::factory();

        return [
            'user_id' => $user,
            'request_number' => 'SHR-' . fake()->unique()->numerify('######'),
            'sender_contact_id' => ShipmentContact::factory()->state(['user_id' => $user, 'type' => 'sender']),
            'receiver_contact_id' => ShipmentContact::factory()->state(['user_id' => $user, 'type' => 'receiver']),
            'status' => ShipmentRequestStatus::DRAFT->value,
            'notes' => fake()->optional()->sentence(),
            'submitted_at' => null,
            'metadata' => null,
        ];
    }
}
