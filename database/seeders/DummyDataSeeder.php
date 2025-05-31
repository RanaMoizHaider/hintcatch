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
            'email' => 'test@example.com',
            'username' => 'testuser',
            'bio' => 'Test user for development purposes',
        ]);

        // Create additional random users
        $users = User::factory(18)->create();
        $allUsers = collect([$adminUser, $testUser])->merge($users);

        // Create categories
        $categories = Category::factory(10)->create();

        // Create subcategories
        Category::factory(5)->create([
            'parent_id' => fn () => $categories->random()->id,
        ]);

        // Create platforms
        $platforms = Platform::factory(5)->create();

        // Create specific AI providers first
        $providers = collect([
            ['name' => 'OpenAI', 'slug' => 'openai', 'website' => 'https://openai.com', 'color' => '#412991'],
            ['name' => 'Anthropic', 'slug' => 'anthropic', 'website' => 'https://anthropic.com', 'color' => '#CA8A04'],
            ['name' => 'Google', 'slug' => 'google', 'website' => 'https://ai.google', 'color' => '#4285F4'],
            ['name' => 'Microsoft', 'slug' => 'microsoft', 'website' => 'https://azure.microsoft.com', 'color' => '#0078D4'],
            ['name' => 'Meta', 'slug' => 'meta', 'website' => 'https://ai.meta.com', 'color' => '#1877F2'],
        ])->map(function ($providerData) {
            return Provider::create([
                'name' => $providerData['name'],
                'slug' => $providerData['slug'],
                'description' => 'Leading AI provider offering cutting-edge machine learning models and services.',
                'website' => $providerData['website'],
                'api_endpoint' => $providerData['website'].'/api/v1',
                'logo' => null,
                'color' => $providerData['color'],
                'supported_features' => ['text', 'image', 'code'],
                'pricing_model' => ['type' => 'pay-per-use', 'currency' => 'USD'],
                'is_active' => true,
            ]);
        });

        // Create AI models with specific providers
        $aiModels = collect([
            ['name' => 'GPT-4', 'provider' => 'OpenAI'],
            ['name' => 'GPT-4o', 'provider' => 'OpenAI'],
            ['name' => 'Claude-3.5 Sonnet', 'provider' => 'Anthropic'],
            ['name' => 'Claude-3 Opus', 'provider' => 'Anthropic'],
            ['name' => 'Gemini Pro', 'provider' => 'Google'],
            ['name' => 'Gemini Ultra', 'provider' => 'Google'],
            ['name' => 'Llama 3', 'provider' => 'Meta'],
            ['name' => 'Copilot', 'provider' => 'Microsoft'],
        ])->map(function ($modelData) use ($providers) {
            $provider = $providers->firstWhere('name', $modelData['provider']);

            return AiModel::create([
                'name' => $modelData['name'],
                'slug' => \Str::slug($modelData['name']),
                'provider_id' => $provider->id,
                'description' => 'Advanced AI model for various tasks including text generation, analysis, and creative writing.',
                'image' => null,
                'color' => $provider->color,
                'icon' => 'heroicon-o-cpu-chip',
                'features' => ['Text Generation', 'Code Assistance', 'Creative Writing', 'Analysis'],
                'release_date' => now()->subMonths(rand(1, 24)),
            ]);
        });

        // Create prompts
        $prompts = Prompt::factory(100)->create([
            'user_id' => fn () => $allUsers->random()->id,
            'category_id' => fn () => Category::all()->random()->id,
        ]);

        // Attach platforms and AI models to prompts
        $prompts->each(function ($prompt) use ($platforms, $aiModels) {
            // Attach 1-3 platforms to each prompt
            $prompt->platforms()->attach(
                $platforms->random(rand(1, 3))->pluck('id')
            );

            // Attach 1-2 AI models to each prompt
            $prompt->aiModels()->attach(
                $aiModels->random(rand(1, 2))->pluck('id')
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
