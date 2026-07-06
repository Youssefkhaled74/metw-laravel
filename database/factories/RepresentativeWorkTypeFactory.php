<?php

namespace Database\Factories;

use App\Enum\RepresentativeWorkType as RepresentativeWorkTypeEnum;
use App\Models\Representative;
use App\Models\RepresentativeWorkType;
use App\Models\RepresentativeWorkTypeOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RepresentativeWorkType>
 */
class RepresentativeWorkTypeFactory extends Factory
{
    protected $model = RepresentativeWorkType::class;

    public function definition(): array
    {
        $codes = RepresentativeWorkTypeOption::activeCodes();

        return [
            'representative_id' => Representative::factory(),
            'work_type' => ! empty($codes)
                ? fake()->randomElement($codes)
                : fake()->randomElement(array_map(
                    fn (RepresentativeWorkTypeEnum $case) => $case->value,
                    RepresentativeWorkTypeEnum::cases()
                )),
        ];
    }
}
