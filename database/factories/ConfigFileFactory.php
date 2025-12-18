<?php

namespace Database\Factories;

use App\Models\Config;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConfigFile>
 */
class ConfigFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extensions = ['json', 'md', 'ts', 'yaml'];
        $extension = fake()->randomElement($extensions);
        $languages = [
            'json' => 'json',
            'md' => 'markdown',
            'ts' => 'typescript',
            'yaml' => 'yaml',
        ];

        return [
            'config_id' => Config::factory(),
            'filename' => fake()->word().'.'.$extension,
            'path' => null,
            'content' => $this->generateContent($extension),
            'language' => $languages[$extension],
            'is_primary' => true,
            'order' => 0,
        ];
    }

    /**
     * Generate sample content based on file extension.
     */
    private function generateContent(string $extension): string
    {
        return match ($extension) {
            'json' => json_encode(['name' => fake()->word(), 'version' => '1.0.0'], JSON_PRETTY_PRINT),
            'md' => '# '.fake()->sentence()."\n\n".fake()->paragraphs(3, true),
            'ts' => "export default {\n  name: '".fake()->word()."',\n};",
            'yaml' => 'name: '.fake()->word()."\nversion: 1.0.0",
            default => fake()->paragraphs(2, true),
        };
    }

    /**
     * Indicate that this is a secondary file.
     */
    public function secondary(int $order = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => false,
            'order' => $order,
        ]);
    }

    /**
     * Set file to be in a subdirectory.
     */
    public function inPath(string $path): static
    {
        return $this->state(fn (array $attributes) => [
            'path' => $path,
        ]);
    }
}
