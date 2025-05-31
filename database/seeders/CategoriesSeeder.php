<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Text Generation',
                'slug' => 'text-generation',
                'description' => 'AI models and tools for generating, summarizing, and manipulating text.',
                'color' => '#7B3FE4',
                'icon' => 'heroicon-o-document-text',
            ],
            [
                'name' => 'Image Generation',
                'slug' => 'image-generation',
                'description' => 'AI models for creating, editing, or understanding images.',
                'color' => '#F59E42',
                'icon' => 'heroicon-o-photo',
            ],
            [
                'name' => 'Code Generation',
                'slug' => 'code-generation',
                'description' => 'AI tools for generating, reviewing, or debugging code.',
                'color' => '#38BDF8',
                'icon' => 'heroicon-o-code-bracket',
            ],
            [
                'name' => 'Speech & Voice',
                'slug' => 'speech-voice',
                'description' => 'AI models for speech recognition, synthesis, and voice cloning.',
                'color' => '#EF4444',
                'icon' => 'heroicon-o-microphone',
            ],
            [
                'name' => 'Multimodal',
                'slug' => 'multimodal',
                'description' => 'AI that understands or generates across text, image, audio, and video.',
                'color' => '#14B8A6',
                'icon' => 'heroicon-o-square-3-stack-3d',
            ],
            [
                'name' => 'Chatbots',
                'slug' => 'chatbots',
                'description' => 'Conversational AI for customer service, support, and more.',
                'color' => '#F472B6',
                'icon' => 'heroicon-o-chat-bubble-left-right',
            ],
            [
                'name' => 'Data Analysis',
                'slug' => 'data-analysis',
                'description' => 'AI tools for analyzing, visualizing, and extracting insights from data.',
                'color' => '#8B5CF6',
                'icon' => 'heroicon-o-chart-bar',
            ],
            [
                'name' => 'Video Generation',
                'slug' => 'video-generation',
                'description' => 'AI models for generating or editing videos.',
                'color' => '#FACC15',
                'icon' => 'heroicon-o-video-camera',
            ],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
