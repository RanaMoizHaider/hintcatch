<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\SkillFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SkillFile>
 */
class SkillFileFactory extends Factory
{
    protected $model = SkillFile::class;

    public function definition(): array
    {
        return [
            'skill_id' => Skill::factory(),
            'filename' => fake()->randomElement(['skill.md', 'README.md', 'instructions.md']),
            'path' => null,
            'content' => $this->generateContent(),
            'language' => 'markdown',
            'is_primary' => false,
            'order' => 0,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn () => [
            'filename' => 'skill.md',
            'is_primary' => true,
            'order' => 0,
        ]);
    }

    public function script(): static
    {
        return $this->state(fn () => [
            'filename' => fake()->randomElement(['setup.sh', 'install.sh', 'run.sh']),
            'path' => 'scripts/',
            'content' => "#!/bin/bash\necho 'Running script...'",
            'language' => 'bash',
            'is_primary' => false,
        ]);
    }

    public function reference(): static
    {
        return $this->state(fn () => [
            'filename' => fake()->randomElement(['docs.md', 'guide.md', 'reference.md']),
            'path' => 'references/',
            'content' => "# Reference\n\n".fake()->paragraph(),
            'language' => 'markdown',
            'is_primary' => false,
        ]);
    }

    public function asset(): static
    {
        return $this->state(fn () => [
            'filename' => fake()->randomElement(['template.json', 'config.yaml', 'data.json']),
            'path' => 'assets/',
            'content' => json_encode(['sample' => 'data']),
            'language' => 'json',
            'is_primary' => false,
        ]);
    }

    private function generateContent(): string
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
