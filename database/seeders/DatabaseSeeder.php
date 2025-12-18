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
        // Seed core lookup data
        $this->call([
            AgentSeeder::class,
            ConfigTypeSeeder::class,
            CategorySeeder::class,
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
