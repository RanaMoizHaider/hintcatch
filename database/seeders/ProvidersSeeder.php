<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'name' => 'OpenAI',
                'slug' => 'openai',
                'description' => 'Creators of GPT, DALL·E, and other leading AI models.',
                'website' => 'https://openai.com',
                'api_endpoint' => 'https://api.openai.com/v1/',
                'logo' => 'providers/openai.png',
                'color' => '#10A37F',
                'supported_features' => ['text', 'image', 'code', 'voice', 'multimodal'],
                'pricing_model' => ['type' => 'pay-per-use', 'currency' => 'USD'],
                'is_active' => true,
            ],
            [
                'name' => 'Google AI',
                'slug' => 'google-ai',
                'description' => 'Google\'s AI division, creators of Gemini and more.',
                'website' => 'https://ai.google',
                'api_endpoint' => 'https://generativelanguage.googleapis.com/v1beta/',
                'logo' => 'providers/google.png',
                'color' => '#4285F4',
                'supported_features' => ['text', 'image', 'code', 'voice', 'multimodal'],
                'pricing_model' => ['type' => 'pay-per-use', 'currency' => 'USD'],
                'is_active' => true,
            ],
            [
                'name' => 'Anthropic',
                'slug' => 'anthropic',
                'description' => 'Makers of Claude, focused on safe and steerable AI.',
                'website' => 'https://www.anthropic.com',
                'api_endpoint' => 'https://api.anthropic.com/v1/',
                'logo' => 'providers/anthropic.png',
                'color' => '#FFB300',
                'supported_features' => ['text', 'multimodal'],
                'pricing_model' => ['type' => 'pay-per-use', 'currency' => 'USD'],
                'is_active' => true,
            ],
            [
                'name' => 'Mistral AI',
                'slug' => 'mistral-ai',
                'description' => 'European open-source AI provider.',
                'website' => 'https://mistral.ai',
                'api_endpoint' => 'https://api.mistral.ai/v1/',
                'logo' => 'providers/mistral.png',
                'color' => '#2D2D2D',
                'supported_features' => ['text'],
                'pricing_model' => ['type' => 'pay-per-use', 'currency' => 'USD'],
                'is_active' => true,
            ],
            [
                'name' => 'Meta AI',
                'slug' => 'meta-ai',
                'description' => 'Makers of Llama models and other open-source AI.',
                'website' => 'https://ai.meta.com',
                'api_endpoint' => null,
                'logo' => 'providers/meta.png',
                'color' => '#1877F3',
                'supported_features' => ['text', 'multimodal'],
                'pricing_model' => ['type' => 'open-source'],
                'is_active' => true,
            ],
        ];

        foreach ($providers as $provider) {
            Provider::create($provider);
        }
    }
}
