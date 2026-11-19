<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $name = fake()->sentence(3);

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'title' => $name,
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['published', 'draft']),
            'thumbnail' => null,
        ];
    }
}
