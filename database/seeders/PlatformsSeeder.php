<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            // First-party
            [
                'name' => 'OpenAI Platform',
                'slug' => 'openai-platform',
                'description' => 'Official OpenAI API platform for GPT, DALL·E, Whisper, and more.',
                'website' => 'https://platform.openai.com',
                'logo' => 'platforms/openai.png',
                'open_in_format' => '_blank',
                'features' => ['API', 'Playground', 'Fine-tuning'],
                'best_practices' => ['Use API keys securely', 'Monitor usage', 'Review model updates'],
            ],
            [
                'name' => 'Google Vertex AI',
                'slug' => 'google-vertex-ai',
                'description' => 'Google Cloud\'s managed AI platform for Gemini and other models.',
                'website' => 'https://cloud.google.com/vertex-ai',
                'logo' => 'platforms/google.png',
                'open_in_format' => '_blank',
                'features' => ['API', 'Studio', 'Model Garden'],
                'best_practices' => ['Use IAM for access control', 'Monitor quotas', 'Stay updated on model changes'],
            ],
            [
                'name' => 'Anthropic Console',
                'slug' => 'anthropic-console',
                'description' => 'Anthropic\'s platform for Claude models.',
                'website' => 'https://console.anthropic.com',
                'logo' => 'platforms/anthropic.png',
                'open_in_format' => '_blank',
                'features' => ['API', 'Chat Interface'],
                'best_practices' => ['Use API responsibly', 'Monitor usage'],
            ],
            [
                'name' => 'Mistral API',
                'slug' => 'mistral-api',
                'description' => 'API access to Mistral models.',
                'website' => 'https://mistral.ai',
                'logo' => 'platforms/mistral.png',
                'open_in_format' => '_blank',
                'features' => ['API', 'Open-source models'],
                'best_practices' => ['Check model licenses', 'Monitor usage'],
            ],
            [
                'name' => 'Meta AI Platform',
                'slug' => 'meta-ai-platform',
                'description' => 'Meta\'s platform for Llama and other open-source models.',
                'website' => 'https://ai.meta.com',
                'logo' => 'platforms/meta.png',
                'open_in_format' => '_blank',
                'features' => ['Open-source models', 'Community support'],
                'best_practices' => ['Review open-source licenses'],
            ],

            // Third-party
            [
                'name' => 'Perplexity AI',
                'slug' => 'perplexity-ai',
                'description' => 'AI-powered search and chat with access to multiple models.',
                'website' => 'https://perplexity.ai',
                'logo' => 'platforms/perplexity.png',
                'open_in_format' => '_blank',
                'features' => ['Search', 'Chat', 'Multiple Models', 'Citations'],
                'best_practices' => ['Check model source', 'Review citations'],
            ],
            [
                'name' => 'Vercel v0',
                'slug' => 'vercel-v0',
                'description' => 'Vercel\'s AI platform for building and deploying AI-powered apps with multiple models.',
                'website' => 'https://v0.dev',
                'logo' => 'platforms/v0.png',
                'open_in_format' => '_blank',
                'features' => ['App Builder', 'Multiple Models', 'Deploy'],
                'best_practices' => ['Monitor API usage', 'Check model compatibility'],
            ],
            [
                'name' => 'Cursor',
                'slug' => 'cursor',
                'description' => 'AI-powered coding assistant and IDE with access to multiple models.',
                'website' => 'https://cursor.sh',
                'logo' => 'platforms/cursor.png',
                'open_in_format' => '_blank',
                'features' => ['Coding Assistant', 'Multiple Models', 'IDE Integration'],
                'best_practices' => ['Review code suggestions', 'Check model source'],
            ],
            [
                'name' => 'Bolt AI',
                'slug' => 'bolt-ai',
                'description' => 'Unified AI chat and productivity platform with access to many models.',
                'website' => 'https://bolt.new',
                'logo' => 'platforms/bolt.png',
                'open_in_format' => '_blank',
                'features' => ['Chat', 'Productivity', 'Multiple Models'],
                'best_practices' => ['Monitor privacy', 'Check model source'],
            ],
            [
                'name' => 'Lovable',
                'slug' => 'lovable',
                'description' => 'AI platform for creative and productivity tools, aggregating top models.',
                'website' => 'https://lovable.dev',
                'logo' => 'platforms/lovable.png',
                'open_in_format' => '_blank',
                'features' => ['Creative Tools', 'Multiple Models'],
                'best_practices' => ['Check licensing', 'Monitor usage'],
            ],
            [
                'name' => 'GitHub Copilot',
                'slug' => 'github-copilot',
                'description' => 'AI-powered code completion and suggestions, powered by OpenAI and GitHub.',
                'website' => 'https://github.com/features/copilot',
                'logo' => 'platforms/copilot.png',
                'open_in_format' => '_blank',
                'features' => ['Code Completion', 'Multiple Models'],
                'best_practices' => ['Review code for correctness'],
            ],
        ];

        foreach ($platforms as $platform) {
            Platform::create(array_merge($platform, ['is_approved' => true]));
        }
    }
}
