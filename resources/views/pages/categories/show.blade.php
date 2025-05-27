<?php

use App\Models\Category;
use App\Models\Prompt;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.web')] class extends Component {
    public Category $category;
    public $activeTab = 'popular';
    public $perPage = 12;
    public $page = 1;

    public function mount(Category $category)
    {
        $this->category = $category->load(['children', 'parent']);
    }

    public function getPrompts()
    {
        $query = $this->category->prompts()
            ->with(['user', 'category', 'platforms', 'aiModels'])
            ->withCount(['likes', 'comments']);

        return match($this->activeTab) {
            'newest' => $query->latest()->paginate($this->perPage, ['*'], 'page', $this->page),
            'trending' => $query->withCount(['likes' => function($q) {
                    $q->where('created_at', '>=', now()->subDays(7));
                }])
                ->orderByDesc('likes_count')
                ->paginate($this->perPage, ['*'], 'page', $this->page),
            default => $query->orderByDesc('likes_count')->paginate($this->perPage, ['*'], 'page', $this->page),
        };
    }

    public function getPromptsProperty()
    {
        return $this->getPrompts();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->page = 1;
    }

    public function loadMore()
    {
        $this->page++;
    }
}; ?>

<div>
    <x-slot name="title">{{ $category->name }} - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <a href="{{ route('categories.index') }}" class="inline-flex items-center text-sm text-zinc-700 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Categories
            </a>
            @if($category->parent)
                <span class="mx-2 text-zinc-400">/</span>
                <a href="{{ route('categories.show', $category->parent->slug) }}" class="text-sm text-zinc-700 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
                    {{ $category->parent->name }}
                </a>
            @endif
        </nav>

        <!-- Category Header -->
        <section class="mb-8">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="text-zinc-700 dark:text-zinc-400 max-w-3xl mb-4">{{ $category->description }}</p>
                    @endif
                </div>
                <div class="text-right shrink-0 ml-4">
                    <div class="text-2xl font-bold text-zinc-800 dark:text-white">{{ $category->prompts()->count() }}</div>
                    <div class="text-sm text-zinc-700 dark:text-zinc-400">{{ Str::plural('prompt', $category->prompts()->count()) }}</div>
                </div>
            </div>
        </section>

        <!-- Subcategories -->
        @if($category->children->count() > 0)
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-4">Subcategories</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($category->children as $subcategory)
                        <a href="{{ route('categories.show', $subcategory->slug) }}" 
                           class="px-3 py-2 text-sm border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors text-center text-zinc-700 dark:text-zinc-300">
                            <div class="font-medium">{{ $subcategory->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $subcategory->prompts()->count() }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Prompts Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Prompts</h2>
            </div>

            <!-- Tab Navigation -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex space-x-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg">
                    <button 
                        wire:click="setActiveTab('popular')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 {{ $activeTab === 'popular' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">
                        Popular
                    </button>
                    <button 
                        wire:click="setActiveTab('newest')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 {{ $activeTab === 'newest' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">
                        Newest
                    </button>
                    <button 
                        wire:click="setActiveTab('trending')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 {{ $activeTab === 'trending' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">
                        Trending
                    </button>
                </div>
            </div>
            
            <!-- Prompts Grid -->
            <x-card-grid :columns="3" wire:loading.class="opacity-50">
                @forelse($this->prompts as $prompt)
                    <livewire:components.prompt-card 
                        :prompt="$prompt"
                        :show-user="false"
                        :show-stats="true" 
                        :show-platforms="true"
                        :show-models="true"
                        :platform-limit="2"
                        :model-limit="1"
                    />
                @empty
                    <x-empty-state 
                        icon="document"
                        title="No prompts found"
                        description="This category doesn't have any prompts yet."
                        class="col-span-full"
                    />
                @endforelse
            </x-card-grid>

            <!-- Load More Button -->
            @if($this->prompts->hasMorePages())
                <div class="flex justify-center mt-8">
                    <button 
                        wire:click="loadMore" 
                        wire:loading.attr="disabled"
                        class="px-6 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors disabled:opacity-50 text-zinc-700 dark:text-zinc-300">
                        <span wire:loading.remove wire:target="loadMore">Load More</span>
                        <span wire:loading wire:target="loadMore">Loading...</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
