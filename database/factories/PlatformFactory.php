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
            'website' => $this->faker->url(),
            'logo' => $this->faker->imageUrl(400, 300, 'tech'),
            'open_in_format' => $this->faker->randomElement(['_blank', '_self']),
            'features' => $this->faker->sentences(rand(1, 5)),
            'best_practices' => $this->faker->sentences(rand(1, 5)),
        ];
    }
}
