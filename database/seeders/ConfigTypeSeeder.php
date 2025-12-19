<?php

namespace Database\Seeders;

use App\Models\ConfigType;
use Illuminate\Database\Seeder;

class ConfigTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configTypes = [
            [
                'name' => 'Rules',
                'slug' => 'rules',
                'description' => 'Custom instructions that guide agent behavior for specific projects or globally',
                'allowed_formats' => ['md'],
                'allows_multiple_files' => false,
            ],
            [
                'name' => 'Agents',
                'slug' => 'agents',
                'description' => 'Specialized AI assistants configured for specific tasks with custom prompts, models, and tool access',
                'allowed_formats' => ['json', 'md', 'yaml'],
                'allows_multiple_files' => false,
            ],
            [
                'name' => 'Plugins',
                'slug' => 'plugins',
                'description' => 'Modular extensions that bundle commands, agents, hooks, Skills, and MCP servers into distributable packages',
                'allowed_formats' => ['json', 'md', 'ts', 'js', 'sh'],
                'allows_multiple_files' => true,
            ],
            [
                'name' => 'Custom Tools',
                'slug' => 'custom-tools',
                'description' => 'Functions that the LLM can call during conversations, extending built-in tools with project-specific functionality',
                'allowed_formats' => ['ts', 'js'],
                'allows_multiple_files' => false,
            ],
            [
                'name' => 'Hooks',
                'slug' => 'hooks',
                'description' => 'Execute custom logic before or after specific events during agent operation',
                'allowed_formats' => ['json', 'yaml'],
                'allows_multiple_files' => false,
            ],
            [
                'name' => 'Slash Commands',
                'slug' => 'slash-commands',
                'description' => 'User-invoked prompts stored as Markdown files that can be executed with /command-name',
                'allowed_formats' => ['md'],
                'allows_multiple_files' => false,
            ],
            [
                'name' => 'Agent Skills',
                'slug' => 'skills',
                'description' => 'Model-invoked capabilities that the agent autonomously uses based on context',
                'allowed_formats' => ['md', 'json', 'sh'],
                'allows_multiple_files' => true,
            ],
        ];

        foreach ($configTypes as $configType) {
            ConfigType::updateOrCreate(
                ['slug' => $configType['slug']],
                $configType
            );
        }
    }
}
