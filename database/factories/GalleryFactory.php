<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'image' => 'gallery/example.jpg',
            'caption' => fake()->sentence(),
            'sort_order' => 0,
        ];
    }
}
