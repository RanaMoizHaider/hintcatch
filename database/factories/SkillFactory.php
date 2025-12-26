<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'submitted_by' => User::factory(),
            'category_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'content' => $this->generateSkillContent(),
            'license' => fake()->randomElement(['MIT', 'Apache-2.0', 'BSD-3-Clause', null]),
            'compatibility' => [
                'agents' => fake()->randomElements(['opencode', 'claude', 'cursor', 'codex'], rand(1, 4)),
            ],
            'metadata' => null,
            'allowed_tools' => null,
            'scripts' => null,
            'references' => null,
            'assets' => null,
            'source_url' => fake()->optional()->url(),
            'source_author' => fake()->optional()->name(),
            'github_url' => fake()->optional()->url(),
            'downloads' => fake()->numberBetween(0, 10000),
            'vote_score' => fake()->numberBetween(-10, 100),
            'is_featured' => false,
        ];
    }

    public function withCategory(): static
    {
        return $this->state(fn () => [
            'category_id' => Category::factory(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn () => [
            'is_featured' => true,
            'vote_score' => fake()->numberBetween(50, 200),
        ]);
    }

    public function withScripts(): static
    {
        return $this->state(fn () => [
            'scripts' => [
                [
                    'filename' => 'setup.sh',
                    'content' => "#!/bin/bash\necho 'Setting up skill...'",
                    'description' => 'Setup script for the skill',
                ],
            ],
        ]);
    }

    public function withReferences(): static
    {
        return $this->state(fn () => [
            'references' => [
                [
                    'title' => 'Documentation',
                    'url' => fake()->url(),
                    'description' => 'Official documentation',
                ],
            ],
        ]);
    }

    public function withAssets(): static
    {
        return $this->state(fn () => [
            'assets' => [
                [
                    'filename' => 'template.md',
                    'content' => '# Template\n\nThis is a template file.',
                    'description' => 'Template file for the skill',
                ],
            ],
        ]);
    }

    public function complete(): static
    {
        return $this->withScripts()->withReferences()->withAssets()->state(fn () => [
            'allowed_tools' => ['read', 'write', 'bash'],
            'metadata' => [
                'version' => '1.0.0',
                'author' => fake()->name(),
            ],
        ]);
    }

    private function generateSkillContent(): string
    {
        $description = fake()->paragraph(2);
        $instructions = fake()->paragraphs(3, true);
        $keywords = implode(', ', fake()->words(5));

        return <<<MARKDOWN
## When to use this skill

{$description}

## How to use this skill

{$instructions}

## Keywords
{$keywords}
MARKDOWN;
    }
}
