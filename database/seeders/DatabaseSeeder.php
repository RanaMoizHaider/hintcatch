<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create system user for seeded content (submitter for official configs/servers/etc.)
        User::firstOrCreate(
            ['username' => 'ranamoizhaider'],
            [
                'name' => 'Moiz Haider',
                'email' => 'oss@moizhaider.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );

        // Seed core lookup data
        $this->call([
            AgentSeeder::class,
            ConfigTypeSeeder::class,
            CategorySeeder::class,
            McpServerSeeder::class,
            SkillSeeder::class,
            ConfigSeeder::class,
        ]);

        // Create test user in local/testing environments
        if (app()->environment('local', 'testing')) {
            User::firstOrCreate(
                ['email' => 'test@example.com'],
                [
                    'name' => 'Test User',
                    'username' => 'testuser',
                    'password' => 'password',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
