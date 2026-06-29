<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\MainCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'image' => null,
            'main_category_id' => MainCategory::factory(),
            'is_active' => true,
            'type' => 'piece',
        ];
    }
}
