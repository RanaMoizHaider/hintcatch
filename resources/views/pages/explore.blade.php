<?php

use App\Models\Prompt;
use App\Models\Category;
use App\Models\Platform;
use App\Models\AiModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.web')] class extends Component {
    use WithPagination;

    #[Url]
    public $activeTab = 'all';
    
    #[Url]
    public $categories = [];
    
    #[Url]
    public $platforms = [];
    
    #[Url] 
    public $models = [];

    public $viewMode = 'grid';

    public $availableCategories;
    public $availablePlatforms;
    public $availableModels;

    public function mount()
    {
        $this->availableCategories = Category::withCount('prompts')->get();
        $this->availablePlatforms = Platform::withCount('prompts')->get();
        $this->availableModels = AiModel::withCount('prompts')->get();
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function updatedCategories()
    {
        $this->resetPage();
    }

    public function updatedPlatforms()
    {
        $this->resetPage();
    }

    public function updatedModels()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->categories = [];
        $this->platforms = [];
        $this->models = [];
        $this->resetPage();
    }

    public function getPromptsProperty()
    {
        $query = Prompt::with(['user', 'tags', 'category', 'platforms', 'aiModels'])
            ->withViewsCount()
            ->published()
            ->visible();

        // Apply filters
        if (!empty($this->categories)) {
            $query->whereIn('category_id', $this->categories);
        }

        if (!empty($this->platforms)) {
            $query->whereHas('platforms', function($q) {
                $q->whereIn('platforms.id', $this->platforms);
            });
        }

        if (!empty($this->models)) {
            $query->whereHas('aiModels', function($q) {
                $q->whereIn('ai_models.id', $this->models);
            });
        }

        // Apply sorting based on active tab
        switch ($this->activeTab) {
            case 'trending':
                $query->orderByViews('desc');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'popular':
                $query->withCount('likes')->orderBy('likes_count', 'desc');
                break;
            default: // 'all'
                $query->latest();
                break;
        }

        return $query->paginate(12);
    }

    public function getAppliedFiltersProperty()
    {
        $filters = [];
        
        foreach ($this->categories as $categoryId) {
            $category = $this->availableCategories->find($categoryId);
            if ($category) {
                $filters[] = ['type' => 'Category', 'value' => $category->name];
            }
        }
        
        foreach ($this->platforms as $platformId) {
            $platform = $this->availablePlatforms->find($platformId);
            if ($platform) {
                $filters[] = ['type' => 'Platform', 'value' => $platform->name];
            }
        }
        
        foreach ($this->models as $modelId) {
            $model = $this->availableModels->find($modelId);
            if ($model) {
                $filters[] = ['type' => 'Model', 'value' => $model->name];
            }
        }
        
        return $filters;
    }
}; ?>

<div>
    <x-slot name="title">Explore Prompts - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <section class="mb-8">
            <h1 class="text-3xl font-bold mb-4">Explore Prompts</h1>
            <p class="text-zinc-700 dark:text-gray-400 max-w-2xl mb-6">
                Discover and filter through thousands of high-quality prompts for various AI platforms and models.
            </p>
            <div class="relative max-w-md">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-zinc-400 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input 
                    type="text" 
                    placeholder="Search for prompts..." 
                    class="w-full pl-10 pr-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-800 focus:ring-2 focus:ring-zinc-500 focus:border-transparent text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500"
                    wire:keydown.enter="$dispatch('search', { query: $event.target.value })"
                >
            </div>
        </section>

        @if(count($this->appliedFilters) > 0)
            <div class="mb-6">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-medium">Applied filters:</span>
                    @foreach($this->appliedFilters as $filter)
                        <livewire:components.badge variant="default" size="sm" text="{{ $filter['type'] }}: {{ $filter['value'] }}" />
                    @endforeach
                    <button wire:click="clearFilters" class="text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:underline">Clear all</button>
                </div>
            </div>
        @endif

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Filter Sidebar -->
            <div class="w-full md:w-64 shrink-0">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6">
                    <h3 class="font-medium mb-4 text-zinc-900 dark:text-white">Filter by Category</h3>
                    <div class="space-y-2">
                        @foreach($availableCategories as $category)
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="categories" 
                                    value="{{ $category->id }}"
                                    class="rounded border-zinc-300 dark:border-zinc-600 text-zinc-600 focus:ring-zinc-500 dark:bg-zinc-700"
                                >
                                <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">{{ $category->name }} ({{ $category->prompts_count }})</span>
                            </label>
                        @endforeach
                    </div>

                    <h3 class="font-medium mb-4 mt-6 text-zinc-900 dark:text-white">AI Platform</h3>
                    <div class="space-y-2">
                        @foreach($availablePlatforms as $platform)
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="platforms" 
                                    value="{{ $platform->id }}"
                                    class="rounded border-zinc-300 dark:border-zinc-600 text-zinc-600 focus:ring-zinc-500 dark:bg-zinc-700"
                                >
                                <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">{{ $platform->name }} ({{ $platform->prompts_count }})</span>
                            </label>
                        @endforeach
                    </div>

                    <h3 class="font-medium mb-4 mt-6 text-zinc-900 dark:text-white">AI Model</h3>
                    <div class="space-y-2">
                        @foreach($availableModels as $model)
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="models" 
                                    value="{{ $model->id }}"
                                    class="rounded border-zinc-300 dark:border-zinc-600 text-zinc-600 focus:ring-zinc-500 dark:bg-zinc-700"
                                >
                                <span class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">{{ $model->name }} ({{ $model->prompts_count }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex space-x-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg">
                            <button wire:click="$set('activeTab', 'all')" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 {{ $activeTab === 'all' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">All</button>
                            <button wire:click="$set('activeTab', 'trending')" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 {{ $activeTab === 'trending' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">Trending</button>
                            <button wire:click="$set('activeTab', 'newest')" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 {{ $activeTab === 'newest' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">Newest</button>
                            <button wire:click="$set('activeTab', 'popular')" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-zinc-700 dark:text-zinc-300 {{ $activeTab === 'popular' ? 'bg-white dark:bg-zinc-700 shadow-sm text-zinc-900 dark:text-white' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">Popular</button>
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
                    
                    @if($this->prompts->count() > 0)
                        @if($viewMode === 'grid')
                            <x-card-grid :columns="2">
                                @foreach($this->prompts as $prompt)
                                    <livewire:components.prompt-card 
                                        :prompt="$prompt"
                                        :show-user="true"
                                        :show-stats="true" 
                                        :show-tags="true"
                                        :tag-limit="2"
                                        wire:key="prompt-{{ $prompt->id }}"
                                    />
                                @endforeach
                            </x-card-grid>
                        @else
                            <div class="space-y-4">
                                @foreach($this->prompts as $prompt)
                                    <livewire:components.prompt-card 
                                        :prompt="$prompt"
                                        :show-user="true"
                                        :show-stats="true" 
                                        :show-tags="true"
                                        :tag-limit="2"
                                        :layout="'list'"
                                        wire:key="prompt-list-{{ $prompt->id }}"
                                    />
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-8">
                            {{ $this->prompts->links() }}
                        </div>
                    @else
                        <x-empty-state 
                            icon="archive"
                            title="No prompts found"
                            description="No prompts match your current filters. Try adjusting your filters or clearing them to see more results."
                        >
                            <x-slot name="actions">
                                <button 
                                    wire:click="clearFilters" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-zinc-800 hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500"
                                >
                                    Clear Filters
                                </button>
                            </x-slot>
                        </x-empty-state>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
