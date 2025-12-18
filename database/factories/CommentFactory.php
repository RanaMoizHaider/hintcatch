<?php

namespace Database\Factories;

use App\Models\Config;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'commentable_type' => Config::class,
            'commentable_id' => Config::factory(),
            'parent_id' => null,
            'body' => fake()->paragraph(),
            'is_edited' => false,
            'edited_at' => null,
        ];
    }

    /**
     * Indicate that the comment is a reply to another comment.
     */
    public function replyTo(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Indicate that the comment has been edited.
     */
    public function edited(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_edited' => true,
            'edited_at' => now(),
        ]);
    }
}
