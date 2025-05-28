<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company() . ' AI';
        
        return [
            'name' => $name,
            'slug' => \Str::slug($name) . '-' . $this->faker->randomNumber(3),
            'description' => $this->faker->paragraph(),
            'website' => $this->faker->url(),
            'api_endpoint' => $this->faker->url() . '/api/v1',
            'logo' => null,
            'color' => $this->faker->hexColor(),
            'supported_features' => $this->faker->randomElements(['text', 'image', 'code', 'voice', 'video'], rand(2, 4)),
            'pricing_model' => [
                'type' => $this->faker->randomElement(['pay-per-use', 'subscription', 'freemium']),
                'currency' => 'USD'
            ],
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
