<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        return [
            'name_ar' => 'براند ' . fake()->unique()->word(),
            'name_en' => 'Brand ' . fake()->unique()->word(),
            'image' => null,
            'is_active' => true,
        ];
    }
}
