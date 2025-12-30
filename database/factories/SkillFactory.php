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

    public function withFiles(int $count = 1): static
    {
        return $this->afterCreating(function (Skill $skill) use ($count) {
            \App\Models\SkillFile::factory()
                ->count($count)
                ->sequence(fn ($sequence) => [
                    'is_primary' => $sequence->index === 0,
                    'order' => $sequence->index,
                ])
                ->create(['skill_id' => $skill->id]);
        });
    }

    public function complete(): static
    {
        return $this->state(fn () => [
            'allowed_tools' => ['read', 'write', 'bash'],
            'metadata' => [
                'version' => '1.0.0',
                'author' => fake()->name(),
            ],
        ])->withFiles(3);
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
