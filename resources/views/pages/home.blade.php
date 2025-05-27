<?php

use App\Models\Prompt;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.web')] class extends Component {
    public $trendingPrompts;
    public $newestPrompts;
    public $popularPrompts;
    public $featuredPrompts;

    public function mount()
    {
        $this->trendingPrompts = Prompt::with(['user', 'tags', 'category'])
            ->withViewsCount()
            ->orderByViews('desc')
            ->take(12)
            ->get();
            
        $this->newestPrompts = Prompt::with(['user', 'tags', 'category'])
            ->withViewsCount()
            ->latest()
            ->take(12)
            ->get();
            
        $this->popularPrompts = Prompt::with(['user', 'tags', 'category'])
            ->withViewsCount()
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->take(12)
            ->get();
            
        $this->featuredPrompts = Prompt::featured()
            ->with(['user', 'tags', 'category'])
            ->withViewsCount()
            ->take(6)
            ->get();
    }
}; ?>

<div>
    <x-slot name="title">Hint Catch - Modern AI Prompts Directory</x-slot>
    
    <div class="container mx-auto px-4 py-8">
        <section class="mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 tracking-tight">Discover the Best AI Prompts</h1>
            <p class="text-lg text-zinc-700 dark:text-zinc-400 max-w-2xl mx-auto mb-8">
                Find, share, and use high-quality prompts for your favorite AI platforms and models
            </p>
            <div class="relative max-w-xl mx-auto">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input 
                    type="text" 
                    placeholder="Search for prompts..." 
                    class="pl-10 h-12 w-full rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-4 focus:outline-none focus:ring-2 focus:ring-zinc-500"
                    wire:keydown.enter="$dispatch('search', { query: $event.target.value })"
                >
            </div>
        </section>

        @if($featuredPrompts->count() > 0)
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6">Featured Prompts</h2>
            <x-card-grid>
                @foreach($featuredPrompts as $prompt)
                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" />
                @endforeach
            </x-card-grid>
        </section>
        @endif

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Main Content -->
            <div class="flex-1">
                <div x-data="{ activeTab: 'trending' }">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex space-x-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg">
                            <button @click="activeTab = 'trending'" :class="{ 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white': activeTab === 'trending' }" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700">Trending</button>
                            <button @click="activeTab = 'newest'" :class="{ 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white': activeTab === 'newest' }" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700">Newest</button>
                            <button @click="activeTab = 'popular'" :class="{ 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white': activeTab === 'popular' }" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700">Popular</button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="px-3 py-1.5 text-sm border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300">Grid</button>
                            <button class="px-3 py-1.5 text-sm border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300">List</button>
                        </div>
                    </div>
                    
                    <div x-show="activeTab === 'trending'">
                        <x-card-grid>
                            @foreach($trendingPrompts as $prompt)
                                <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" />
                            @endforeach
                        </x-card-grid>
                    </div>

                    <div x-show="activeTab === 'newest'">
                        <x-card-grid>
                            @foreach($newestPrompts as $prompt)
                                <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" />
                            @endforeach
                        </x-card-grid>
                    </div>

                    <div x-show="activeTab === 'popular'">
                        <x-card-grid>
                            @foreach($popularPrompts as $prompt)
                                <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" />
                            @endforeach
                        </x-card-grid>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
