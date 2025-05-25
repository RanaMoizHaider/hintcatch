<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AiModel>
 */
class AiModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'provider' => $this->faker->word,
            'description' => $this->faker->sentence,
            'image' => $this->faker->imageUrl,
            'color' => $this->faker->hexColor,
            'icon' => $this->faker->imageUrl,
            'features' => $this->faker->paragraphs(3),
        ];
    }
}
