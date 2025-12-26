<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prompt>
 */
class PromptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(['system', 'task', 'review', 'documentation']),
            'submitted_by' => User::factory(),
            'source_url' => fake()->optional()->url(),
            'source_author' => fake()->optional()->userName(),
            'downloads' => fake()->numberBetween(0, 5000),
            'vote_score' => fake()->numberBetween(-10, 500),
            'is_featured' => fake()->boolean(10),
        ];
    }

    /**
     * Indicate that the prompt is a system prompt.
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'system',
        ]);
    }

    /**
     * Indicate that the prompt is a task prompt.
     */
    public function task(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'task',
        ]);
    }

    /**
     * Indicate that the prompt is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
