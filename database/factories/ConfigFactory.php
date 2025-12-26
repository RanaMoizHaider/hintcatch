<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\Category;
use App\Models\ConfigType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Config>
 */
class ConfigFactory extends Factory
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
            'description' => fake()->paragraph(),
            'config_type_id' => ConfigType::factory(),
            'agent_id' => Agent::factory(),
            'submitted_by' => User::factory(),
            'category_id' => null,
            'source_url' => fake()->optional()->url(),
            'github_url' => fake()->optional(0.3)->url(),
            'source_author' => fake()->optional()->userName(),
            'vote_score' => fake()->numberBetween(-10, 500),
            'version' => fake()->semver(),
            'is_featured' => fake()->boolean(10),
        ];
    }

    /**
     * Indicate that the config is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the config has a category.
     */
    public function withCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => Category::factory(),
        ]);
    }

    /**
     * Indicate that the config is multi-agent (no direct agent_id).
     */
    public function multiAgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'agent_id' => null,
        ]);
    }

    /**
     * Indicate that the config has a GitHub URL.
     */
    public function withGithubUrl(?string $url = null): static
    {
        return $this->state(fn (array $attributes) => [
            'github_url' => $url ?? fake()->url(),
        ]);
    }
}
