<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name;
        $slug = str_replace(' ', '-', $name);
        return [
            'name' => $name,
            'slug' => $slug,
            'position' => $this->faker->unique()->randomNumber(2)
        ];
    }
}
