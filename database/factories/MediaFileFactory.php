<?php

namespace Database\Factories;

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaFile>
 */
class MediaFileFactory extends Factory
{
    protected $model = MediaFile::class;

    public function definition(): array
    {
        $user = User::query()->first() ?? User::query()->create([
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('201#########'),
            'country_code' => '+20',
            'password' => 'password',
        ]);

        return [
            'mediable_type' => User::class,
            'mediable_id' => $user->id,
            'collection_name' => fake()->randomElement(['avatars', 'documents']),
            'disk' => 'public',
            'directory' => fake()->randomElement(['uploads/users', 'uploads/shared']),
            'filename' => fake()->uuid() . '.jpg',
            'original_name' => fake()->word() . '.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(1024, 5242880),
            'url' => fake()->imageUrl(),
            'title' => fake()->optional()->sentence(3),
            'alt_text' => fake()->optional()->sentence(4),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_primary' => false,
            'metadata' => ['source' => 'factory'],
        ];
    }
}
