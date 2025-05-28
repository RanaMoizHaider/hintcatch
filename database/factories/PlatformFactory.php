<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Platform>
 */
class PlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['ChatGPT', 'Claude', 'Gemini', 'Copilot', 'Midjourney', 'DALL-E', 'Stable Diffusion']);
        return [
            'name' => $name,
            'slug' => \Str::slug($name),
            'description' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(400, 300, 'tech'),
            'color' => $this->faker->hexColor(),
            'icon' => 'heroicon-o-' . $this->faker->randomElement(['cpu-chip', 'bolt', 'sparkles', 'beaker']),
            'features' => $this->faker->sentences(rand(1, 5)),
            'best_practices' => $this->faker->sentences(rand(1, 5)),
        ];
    }
}
