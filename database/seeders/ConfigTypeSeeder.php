<?php

namespace Database\Seeders;

use App\Models\ConfigType;
use Illuminate\Database\Seeder;

class ConfigTypeSeeder extends Seeder
{
    public function run(): void
    {
        $configTypes = [
            [
                'name' => 'Rules',
                'slug' => 'rules',
                'description' => 'Custom instructions that guide agent behavior for specific projects or globally',
                'allowed_formats' => ['md'],
                'allows_multiple_files' => false,
                'is_standard' => false,
                'requires_agent' => true,
            ],
            [
                'name' => 'Agents',
                'slug' => 'agents',
                'description' => 'Specialized AI assistants configured for specific tasks with custom prompts, models, and tool access',
                'allowed_formats' => ['json', 'md', 'yaml'],
                'allows_multiple_files' => false,
                'is_standard' => false,
                'requires_agent' => false,
            ],
            [
                'name' => 'Plugins',
                'slug' => 'plugins',
                'description' => 'Modular extensions that bundle commands, agents, hooks, Skills, and MCP servers into distributable packages',
                'allowed_formats' => ['json', 'md', 'ts', 'js', 'sh'],
                'allows_multiple_files' => true,
                'is_standard' => false,
                'requires_agent' => false,
            ],
            [
                'name' => 'Custom Tools',
                'slug' => 'custom-tools',
                'description' => 'Functions that the LLM can call during conversations, extending built-in tools with project-specific functionality',
                'allowed_formats' => ['ts', 'js'],
                'allows_multiple_files' => false,
                'is_standard' => false,
                'requires_agent' => false,
            ],
            [
                'name' => 'Hooks',
                'slug' => 'hooks',
                'description' => 'Execute custom logic before or after specific events during agent operation',
                'allowed_formats' => ['json', 'yaml'],
                'allows_multiple_files' => false,
                'is_standard' => false,
                'requires_agent' => false,
            ],
            [
                'name' => 'Slash Commands',
                'slug' => 'slash-commands',
                'description' => 'User-invoked prompts stored as Markdown or TOML files that can be executed with /command-name',
                'allowed_formats' => ['md', 'toml'],
                'allows_multiple_files' => false,
                'is_standard' => false,
                'requires_agent' => true,
            ],
        ];

        foreach ($configTypes as $configType) {
            ConfigType::updateOrCreate(
                ['slug' => $configType['slug']],
                $configType
            );
        }

        ConfigType::where('slug', 'skills')->delete();
    }
}
