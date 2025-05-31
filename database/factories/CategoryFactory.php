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
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => \Str::slug($name),
            'description' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(400, 300, 'tech'),
            'color' => $this->faker->hexColor(),
            'icon' => 'heroicon-o-'.$this->faker->randomElement(['code-bracket', 'cpu-chip', 'cog', 'light-bulb', 'academic-cap']),
            'parent_id' => null,
        ];
    }
}
