<?php

use App\Models\Prompt;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.web')] class extends Component {
    public $trendingPrompts;
    public $newestPrompts;
    public $popularPrompts;
    public $featuredPrompts;
    public $viewMode = 'grid';

    public function mount()
    {
        $this->trendingPrompts = Prompt::with(['user', 'tags', 'category'])
            ->withViewsCount()
            ->orderByDesc('views_count')
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
            ->orderByDesc('likes_count')
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
                    class="pl-10 h-12 w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-4 focus:outline-none focus:ring-2 focus:ring-zinc-500"
                    wire:keydown.enter="$dispatch('search', { query: $event.target.value })"
                >
            </div>
        </section>

        @if($featuredPrompts->count() > 0)
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-6">Featured Prompts</h2>
            <x-card-grid>
                @foreach($featuredPrompts as $prompt)
                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" wire:key="featured-{{ $prompt->id }}" />
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
                            <button 
                                wire:click="$set('viewMode', 'grid')"
                                class="p-2 text-sm border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors {{ $viewMode === 'grid' ? 'bg-white dark:bg-zinc-700' : '' }} text-zinc-700 dark:text-zinc-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                            <button 
                                wire:click="$set('viewMode', 'list')"
                                class="p-2 text-sm border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors {{ $viewMode === 'list' ? 'bg-white dark:bg-zinc-700' : '' }} text-zinc-700 dark:text-zinc-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div x-show="activeTab === 'trending'">
                        @if($viewMode === 'grid')
                            <x-card-grid>
                                @foreach($trendingPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" wire:key="trending-{{ $prompt->id }}" />
                                @endforeach
                            </x-card-grid>
                        @else
                            <div class="space-y-4">
                                @foreach($trendingPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" :layout="'list'" wire:key="trending-list-{{ $prompt->id }}" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div x-show="activeTab === 'newest'">
                        @if($viewMode === 'grid')
                            <x-card-grid>
                                @foreach($newestPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" wire:key="newest-{{ $prompt->id }}" />
                                @endforeach
                            </x-card-grid>
                        @else
                            <div class="space-y-4">
                                @foreach($newestPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" :layout="'list'" wire:key="newest-list-{{ $prompt->id }}" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div x-show="activeTab === 'popular'">
                        @if($viewMode === 'grid')
                            <x-card-grid>
                                @foreach($popularPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" wire:key="popular-{{ $prompt->id }}" />
                                @endforeach
                            </x-card-grid>
                        @else
                            <div class="space-y-4">
                                @foreach($popularPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" :layout="'list'" wire:key="popular-list-{{ $prompt->id }}" />
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
