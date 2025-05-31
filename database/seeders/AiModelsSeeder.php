<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\Provider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AiModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch provider IDs
        $providers = Provider::pluck('id', 'slug');

        $models = [
            // OpenAI
            [
                'name' => 'GPT-4o',
                'slug' => 'gpt-4o',
                'provider_id' => $providers['openai'],
                'description' => 'OpenAI\'s flagship multimodal model (text, vision, audio).',
                'image' => 'models/gpt-4o.png',
                'color' => '#10A37F',
                'icon' => 'heroicon-o-cpu-chip',
                'features' => ['text', 'image', 'audio', 'multimodal'],
                'release_date' => '2024-05-13',
            ],
            [
                'name' => 'GPT-4 Turbo',
                'slug' => 'gpt-4-turbo',
                'provider_id' => $providers['openai'],
                'description' => 'Faster, cheaper version of GPT-4 for production workloads.',
                'image' => 'models/gpt-4-turbo.png',
                'color' => '#10A37F',
                'icon' => 'heroicon-o-bolt',
                'features' => ['text'],
                'release_date' => '2023-11-06',
            ],
            [
                'name' => 'DALL·E 3',
                'slug' => 'dalle-3',
                'provider_id' => $providers['openai'],
                'description' => 'OpenAI\'s latest image generation model.',
                'image' => 'models/dalle-3.png',
                'color' => '#F59E42',
                'icon' => 'heroicon-o-photo',
                'features' => ['image'],
                'release_date' => '2023-10-01',
            ],
            [
                'name' => 'Whisper',
                'slug' => 'whisper',
                'provider_id' => $providers['openai'],
                'description' => 'Automatic speech recognition (ASR) model by OpenAI.',
                'image' => 'models/whisper.png',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-microphone',
                'features' => ['audio'],
                'release_date' => '2022-09-21',
            ],
            // Google
            [
                'name' => 'Gemini 1.5 Pro',
                'slug' => 'gemini-1-5-pro',
                'provider_id' => $providers['google-ai'],
                'description' => 'Google\'s flagship multimodal LLM.',
                'image' => 'models/gemini-1-5-pro.png',
                'color' => '#4285F4',
                'icon' => 'heroicon-o-sparkles',
                'features' => ['text', 'image', 'audio', 'multimodal'],
                'release_date' => '2024-02-08',
            ],
            [
                'name' => 'Gemini 1.0 Ultra',
                'slug' => 'gemini-1-0-ultra',
                'provider_id' => $providers['google-ai'],
                'description' => 'First generation Gemini Ultra model.',
                'image' => 'models/gemini-1-0-ultra.png',
                'color' => '#4285F4',
                'icon' => 'heroicon-o-star',
                'features' => ['text', 'image', 'multimodal'],
                'release_date' => '2023-12-06',
            ],
            // Anthropic
            [
                'name' => 'Claude 3 Opus',
                'slug' => 'claude-3-opus',
                'provider_id' => $providers['anthropic'],
                'description' => 'Anthropic\'s most capable model for complex tasks.',
                'image' => 'models/claude-3-opus.png',
                'color' => '#FFB300',
                'icon' => 'heroicon-o-academic-cap',
                'features' => ['text', 'multimodal'],
                'release_date' => '2024-03-04',
            ],
            [
                'name' => 'Claude 3 Sonnet',
                'slug' => 'claude-3-sonnet',
                'provider_id' => $providers['anthropic'],
                'description' => 'Balanced Claude 3 model for everyday tasks.',
                'image' => 'models/claude-3-sonnet.png',
                'color' => '#FFB300',
                'icon' => 'heroicon-o-beaker',
                'features' => ['text', 'multimodal'],
                'release_date' => '2024-03-04',
            ],
            // Mistral
            [
                'name' => 'Mistral Large',
                'slug' => 'mistral-large',
                'provider_id' => $providers['mistral-ai'],
                'description' => 'Mistral\'s top-tier LLM for commercial use.',
                'image' => 'models/mistral-large.png',
                'color' => '#2D2D2D',
                'icon' => 'heroicon-o-cloud',
                'features' => ['text'],
                'release_date' => '2024-02-26',
            ],
            [
                'name' => 'Mixtral 8x22B',
                'slug' => 'mixtral-8x22b',
                'provider_id' => $providers['mistral-ai'],
                'description' => 'Mixture-of-experts model from Mistral.',
                'image' => 'models/mixtral-8x22b.png',
                'color' => '#2D2D2D',
                'icon' => 'heroicon-o-squares-plus',
                'features' => ['text'],
                'release_date' => '2024-05-01',
            ],
            // Meta
            [
                'name' => 'Llama 3 70B',
                'slug' => 'llama-3-70b',
                'provider_id' => $providers['meta-ai'],
                'description' => 'Meta\'s latest open-source LLM.',
                'image' => 'models/llama-3-70b.png',
                'color' => '#1877F3',
                'icon' => 'heroicon-o-globe-alt',
                'features' => ['text'],
                'release_date' => '2024-04-18',
            ],
            [
                'name' => 'Llama 2 70B',
                'slug' => 'llama-2-70b',
                'provider_id' => $providers['meta-ai'],
                'description' => 'Second-generation open-source LLM from Meta.',
                'image' => 'models/llama-2-70b.png',
                'color' => '#1877F3',
                'icon' => 'heroicon-o-shield-check',
                'features' => ['text'],
                'release_date' => '2023-07-18',
            ],
        ];

        foreach ($models as $model) {
            AiModel::create($model);
        }
    }
}
