<?php

namespace Database\Factories;

use App\Enum\RepresentativeWorkType as RepresentativeWorkTypeEnum;
use App\Models\Representative;
use App\Models\RepresentativeWorkType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RepresentativeWorkType>
 */
class RepresentativeWorkTypeFactory extends Factory
{
    protected $model = RepresentativeWorkType::class;

    public function definition(): array
    {
        return [
            'representative_id' => Representative::factory(),
            'work_type' => fake()->randomElement(array_column(RepresentativeWorkTypeEnum::cases(), 'value')),
        ];
    }
}
