<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\McpServer>
 */
class McpServerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word().'-mcp';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'type' => 'remote',
            'url' => fake()->url(),
            'command' => null,
            'args' => null,
            'env' => null,
            'headers' => null,
            'user_id' => User::factory(),
            'source_url' => fake()->optional()->url(),
            'source_author' => fake()->optional()->userName(),
            'downloads' => fake()->numberBetween(0, 5000),
            'vote_score' => fake()->numberBetween(-10, 500),
            'is_featured' => fake()->boolean(10),
        ];
    }

    /**
     * Indicate that the MCP server is local.
     */
    public function local(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'local',
            'url' => null,
            'command' => 'npx',
            'args' => ['-y', '@modelcontextprotocol/server-'.fake()->word()],
            'env' => ['NODE_ENV' => 'production'],
        ]);
    }

    /**
     * Indicate that the MCP server is remote with headers.
     */
    public function remoteWithHeaders(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'remote',
            'headers' => ['API_KEY' => '${API_KEY}'],
        ]);
    }

    /**
     * Indicate that the MCP server is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
