<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Platform;
use App\Models\Prompt;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific users
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@hintcatch.com',
            'username' => 'admin',
            'bio' => 'Admin of Hint Catch - AI prompt sharing platform',
            'is_admin' => true,
        ]);

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@hintcatch.com',
            'username' => 'testuser',
            'bio' => 'Test user for development purposes',
        ]);

        // Create additional random users
        $users = User::factory(18)->create();
        $allUsers = collect([$adminUser, $testUser])->merge($users);

        // Create prompts
        $prompts = Prompt::factory(100)->create([
            'user_id' => fn () => $allUsers->random()->id,
            'category_id' => fn () => Category::withUnapproved()->get()->random()->id,
        ]);

        // Attach platforms and AI models to prompts
        $prompts->each(function ($prompt) {
            // Attach 1-3 platforms to each prompt
            $prompt->platforms()->attach(
                Platform::withUnapproved()->get()->random()->id
            );

            // Attach 1-2 AI models to each prompt
            $prompt->aiModels()->attach(
                AiModel::withUnapproved()->get()->random()->id
            );

            // Add some tags
            $tags = collect([
                'productivity', 'creative', 'coding', 'writing', 'analysis',
                'marketing', 'education', 'business', 'research', 'automation',
            ])->random(rand(1, 4));

            $prompt->attachTags($tags);
        });

        // Create likes (avoiding duplicates)
        $likeCombinations = collect();
        for ($i = 0; $i < 500; $i++) {
            $userId = $allUsers->random()->id;
            $promptId = $prompts->random()->id;
            $combination = "{$userId}-{$promptId}";

            if (! $likeCombinations->contains($combination)) {
                $likeCombinations->push($combination);
                Like::create([
                    'user_id' => $userId,
                    'likeable_id' => $promptId,
                    'likeable_type' => Prompt::class,
                ]);
            }
        }

        // Create comments
        $comments = Comment::factory(200)->create([
            'user_id' => fn () => $allUsers->random()->id,
            'commentable_id' => fn () => $prompts->random()->id,
            'commentable_type' => Prompt::class,
        ]);

        // Create reply comments
        Comment::factory(50)->create([
            'user_id' => fn () => $allUsers->random()->id,
            'parent_id' => fn () => $comments->random()->id,
            'commentable_id' => fn () => $prompts->random()->id,
            'commentable_type' => Prompt::class,
        ]);

        // Create followers relationships
        $allUsers->each(function ($user) use ($allUsers) {
            $followersCount = rand(0, 10);
            $potentialFollowers = $allUsers->where('id', '!=', $user->id);

            if ($potentialFollowers->count() > 0) {
                $followers = $potentialFollowers->random(min($followersCount, $potentialFollowers->count()));

                foreach ($followers as $follower) {
                    DB::table('followers')->insertOrIgnore([
                        'user_id' => $user->id,
                        'follower_id' => $follower->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        // Add some views to prompts
        $prompts->each(function ($prompt) {
            $viewsCount = rand(0, 50);
            for ($i = 0; $i < $viewsCount; $i++) {
                views($prompt)->record();
            }
        });

        $this->command->info('Dummy data seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- '.User::count().' users');
        $this->command->info('- '.Category::count().' categories');
        $this->command->info('- '.Platform::count().' platforms');
        $this->command->info('- '.AiModel::count().' AI models');
        $this->command->info('- '.Prompt::count().' prompts');
        $this->command->info('- '.Like::count().' likes');
        $this->command->info('- '.Comment::count().' comments');
    }
}
