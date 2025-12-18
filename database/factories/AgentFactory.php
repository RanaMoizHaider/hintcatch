<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agent>
 */
class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'website' => fake()->url(),
            'docs_url' => fake()->url(),
            'github_url' => fake()->optional()->url(),
            'supported_config_types' => fake()->randomElements(
                ['mcp-servers', 'rules', 'agents', 'plugins', 'custom-tools', 'hooks', 'slash-commands', 'skills', 'prompts'],
                fake()->numberBetween(2, 5)
            ),
            'supported_file_formats' => fake()->randomElements(['json', 'md', 'yaml', 'ts', 'js'], fake()->numberBetween(1, 3)),
            'supports_mcp' => fake()->boolean(80),
            'mcp_transport_types' => fake()->optional()->randomElements(['stdio', 'sse', 'http'], fake()->numberBetween(1, 3)),
            'mcp_config_paths' => [
                'project' => '.'.fake()->word().'/config.json',
                'global' => '~/.'.fake()->word().'/config.json',
            ],
            'mcp_config_template' => null,
            'rules_filename' => fake()->optional()->word().'.md',
        ];
    }

    public function withMcpSupport(): static
    {
        return $this->state(fn (array $attributes) => [
            'supports_mcp' => true,
            'mcp_transport_types' => ['stdio', 'sse'],
        ]);
    }

    public function withoutMcpSupport(): static
    {
        return $this->state(fn (array $attributes) => [
            'supports_mcp' => false,
            'mcp_transport_types' => null,
            'mcp_config_paths' => null,
            'mcp_config_template' => null,
        ]);
    }
}
