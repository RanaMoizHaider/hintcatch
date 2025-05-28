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
        $models = ['GPT-4', 'GPT-4.5', 'GPT-4o', 'Claude-3', 'Claude-3.5', 'Claude-4', 'Gemini Pro', 'LLaMA 2', 'PaLM 2', 'Mistral 7B'];
        $name = $this->faker->unique()->randomElement($models);
        
        return [
            'name' => $name,
            'slug' => \Str::slug($name),
            'provider_id' => function () {
                // Try to get an existing provider, or create one if none exist
                return \App\Models\Provider::inRandomOrder()->first()?->id 
                    ?? \App\Models\Provider::factory()->create()->id;
            },
            'description' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(400, 300, 'tech'),
            'color' => $this->faker->hexColor(),
            'icon' => 'heroicon-o-' . $this->faker->randomElement(['cpu-chip', 'bolt', 'sparkles', 'beaker']),
            'features' => $this->faker->sentences(3),
            'release_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
