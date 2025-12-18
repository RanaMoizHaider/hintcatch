<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConfigType>
 */
class ConfigTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'allowed_formats' => fake()->randomElements(['json', 'md', 'yaml', 'ts', 'js', 'txt'], fake()->numberBetween(1, 3)),
            'allows_multiple_files' => fake()->boolean(30),
        ];
    }

    public function multipleFiles(): static
    {
        return $this->state(fn (array $attributes) => [
            'allows_multiple_files' => true,
        ]);
    }
}
