<?php

use App\Models\Prompt;
use App\Models\Provider;
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
    
    #[Url]
    public $providers = [];

    #[Url]
    public $search = '';

    public $viewMode = 'grid';

    public $availableCategories;
    public $availablePlatforms;
    public $availableModels;
    public $availableProviders;

    // Search filters
    public $categorySearch = '';
    public $platformSearch = '';
    public $modelSearch = '';
    public $providerSearch = '';

    // Filter toggles
    public $showCategories = true;
    public $showPlatforms = false;
    public $showModels = false;
    public $showProviders = false;

    public function mount()
    {
        $this->availableCategories = Category::with(['children' => function($query) {
            $query->with(['children' => function($subQuery) {
                $subQuery->withCount('prompts');
            }])->withCount('prompts');
        }])->whereNull('parent_id')->withCount('prompts')->get();
        
        $this->availablePlatforms = Platform::withCount('prompts')->get();
        
        $this->availableProviders = Provider::with(['aiModels' => function($query) {
            $query->withCount('prompts');
        }])->get()->map(function($provider) {
            $provider->total_prompts_count = $provider->aiModels->sum('prompts_count');
            return $provider;
        });
        
        $this->availableModels = AiModel::with('provider')->withCount('prompts')->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
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
    
    public function updatedProviders()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->categories = [];
        $this->platforms = [];
        $this->models = [];
        $this->providers = [];
        $this->search = '';
        $this->resetPage();
    }

    public function getPromptsProperty()
    {
        $query = Prompt::with(['user', 'tags', 'category', 'platforms', 'aiModels'])
            ->withViewsCount()
            ->published()
            ->visible();

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('tags', function($tagQuery) {
                      $tagQuery->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('category', function($catQuery) {
                      $catQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

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
        
        if (!empty($this->providers)) {
            $query->whereHas('aiModels.provider', function($q) {
                $q->whereIn('providers.id', $this->providers);
            });
        }

        // Apply sorting based on active tab
        switch ($this->activeTab) {
            case 'trending':
                $query->orderByDesc('views_count');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'popular':
                $query->withCount('likes')->orderByDesc('likes_count');
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
        
        // Add search filter if active
        if (!empty($this->search)) {
            $filters[] = ['type' => 'Search', 'value' => $this->search];
        }
        
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
        
        foreach ($this->providers as $providerId) {
            $provider = $this->availableProviders->find($providerId);
            if ($provider) {
                $filters[] = ['type' => 'Provider', 'value' => $provider->name];
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
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search for prompts..." 
                    class="w-full pl-10 pr-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-800 focus:ring-2 focus:ring-zinc-500 focus:border-transparent text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500"
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
            <div class="w-full md:w-60 shrink-0">
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    
                    <!-- Categories Filter (Always Visible) -->
                    <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between mb-1 cursor-pointer" wire:click="$toggle('showCategories')">
                            <h3 class="font-medium text-zinc-900 dark:text-white text-sm">Categories</h3>
                            <button 
                                class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                            >
                                <svg class="w-4 h-4 transform transition-transform {{ $showCategories ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                        
                        @if($showCategories)
                            @php
                                $filteredCategories = $availableCategories->filter(function($category) {
                                    return empty($this->categorySearch) || str_contains(strtolower($category->name), strtolower($this->categorySearch));
                                });
                                $categoriesToShow = empty($this->categorySearch) ? $availableCategories->take(10) : $filteredCategories;
                            @endphp
                            
                            <div class="relative mb-2">
                                <input 
                                    type="text" 
                                    wire:model.live="categorySearch"
                                    placeholder="Search categories..."
                                    class="w-full pl-8 pr-3 py-1.5 text-sm border border-zinc-200 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-700 focus:ring-2 focus:ring-zinc-500 focus:border-transparent text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500"
                                >
                                <svg class="absolute left-2.5 top-2 w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            
                            <div class="space-y-1.5">
                                @foreach($categoriesToShow as $category)
                                    <div class="space-y-1" x-data="{ open: false }">
                                        <!-- Main Category -->
                                        <div class="flex items-center group py-0.5">
                                            <input 
                                                type="checkbox" 
                                                wire:model.live="categories" 
                                                value="{{ $category->id }}"
                                                class="rounded border-zinc-300 dark:border-zinc-600 text-zinc-600 focus:ring-zinc-500 dark:bg-zinc-700 scale-90"
                                                id="category-{{ $category->id }}"
                                            >
                                            <div class="ml-2 flex items-center flex-1 min-w-0">
                                                <label 
                                                    for="category-{{ $category->id }}"
                                                    class="text-sm text-zinc-700 dark:text-zinc-300 truncate cursor-pointer flex-1"
                                                >{{ $category->name }}</label>
                                                @if($category->children->count() > 0)
                                                    <button 
                                                        type="button" 
                                                        class="ml-1 p-0.5 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                                        @click="open = !open"
                                                    >
                                                        <svg class="w-3 h-3 transform transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                            {{-- <livewire:components.badge 
                                                variant="secondary" 
                                                size="xs" 
                                                :text="$category->prompts_count" 
                                                wire:key="cat-badge-{{ $category->id }}"
                                                class="ml-1 shrink-0"
                                            /> --}}
                                        </div>

                                        <!-- Subcategories -->
                                        @if($category->children->count() > 0)
                                            <div x-show="open" x-collapse class="ml-5 space-y-1">
                                                @foreach($category->children as $subcategory)
                                                    <div class="space-y-1" x-data="{ subOpen: false }">
                                                        <div class="flex items-center group py-0.5">
                                                            <input 
                                                                type="checkbox" 
                                                                wire:model.live="categories" 
                                                                value="{{ $subcategory->id }}"
                                                                class="rounded border-zinc-300 dark:border-zinc-600 text-zinc-600 focus:ring-zinc-500 dark:bg-zinc-700 scale-90"
                                                                id="subcategory-{{ $subcategory->id }}"
                                                            >
                                                            <div class="ml-2 flex items-center flex-1 min-w-0">
                                                                <label 
                                                                    for="subcategory-{{ $subcategory->id }}"
                                                                    class="text-xs text-zinc-600 dark:text-zinc-400 truncate cursor-pointer flex-1"
                                                                >{{ $subcategory->name }}</label>
                                                                @if($subcategory->children->count() > 0)
                                                                    <button 
                                                                        type="button" 
                                                                        class="ml-1 p-0.5 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                                                        @click="subOpen = !subOpen"
                                                                    >
                                                                        <svg class="w-3 h-3 transform transition-transform" :class="{ 'rotate-90': subOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                                        </svg>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                            {{-- <livewire:components.badge 
                                                                variant="secondary" 
                                                                size="xs" 
                                                                :text="$subcategory->prompts_count" 
                                                                wire:key="subcat-badge-{{ $subcategory->id }}"
                                                                class="ml-1 shrink-0"
                                                            /> --}}
                                                        </div>

                                                        <!-- Sub-subcategories -->
                                                        @if($subcategory->children->count() > 0)
                                                            <div x-show="subOpen" x-collapse class="ml-5 space-y-1">
                                                                @foreach($subcategory->children as $subSubcategory)
                                                                    <div class="flex items-center group py-0.5">
                                                                        <input 
                                                                            type="checkbox" 
                                                                            wire:model.live="categories" 
                                                                            value="{{ $subSubcategory->id }}"
                                                                            class="rounded border-zinc-300 dark:border-zinc-600 text-zinc-600 focus:ring-zinc-500 dark:bg-zinc-700 scale-90"
                                                                            id="subsubcategory-{{ $subSubcategory->id }}"
                                                                        >
                                                                        <label 
                                                                            for="subsubcategory-{{ $subSubcategory->id }}"
                                                                            class="ml-2 text-xs text-zinc-500 dark:text-zinc-500 flex-1 truncate cursor-pointer"
                                                                        >{{ $subSubcategory->name }}</label>
                                                                        {{-- <livewire:components.badge 
                                                                            variant="secondary" 
                                                                            size="xs" 
                                                                            :text="$subSubcategory->prompts_count" 
                                                                            wire:key="subsubcat-badge-{{ $subSubcategory->id }}"
                                                                            class="ml-1 shrink-0"
                                                                        /> --}}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- AI Platforms Filter (Collapsible) -->
                    <div class="border-b border-zinc-200 dark:border-zinc-700">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-1 cursor-pointer" wire:click="$toggle('showPlatforms')">
                                <h3 class="font-medium text-zinc-900 dark:text-white text-sm">AI Platforms</h3>
                                <button 
                                    class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                >
                                    <svg class="w-4 h-4 transform transition-transform {{ $showPlatforms ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            @if($showPlatforms)
                                @php
                                    $filteredPlatforms = $availablePlatforms->filter(function($platform) {
                                        return empty($this->platformSearch) || str_contains(strtolower($platform->name), strtolower($this->platformSearch));
                                    });
                                    $platformsToShow = empty($this->platformSearch) ? $availablePlatforms->take(10) : $filteredPlatforms;
                                @endphp
                                
                                <div class="relative mb-2">
                                    <input 
                                        type="text" 
                                        wire:model.live="platformSearch"
                                        placeholder="Search platforms..."
                                        class="w-full pl-8 pr-3 py-1.5 text-sm border border-zinc-200 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-700 focus:ring-2 focus:ring-zinc-500 focus:border-transparent text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500"
                                    >
                                    <svg class="absolute left-2.5 top-2 w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                
                                <div class="space-y-1.5">
                                    @foreach($platformsToShow as $platform)
                                        <div class="flex items-center group py-0.5">
                                            <input 
                                                type="checkbox" 
                                                wire:model.live="platforms" 
                                                value="{{ $platform->id }}"
                                                class="rounded border-zinc-300 dark:border-zinc-600 text-zinc-600 focus:ring-zinc-500 dark:bg-zinc-700 scale-90"
                                                id="platform-{{ $platform->id }}"
                                            >
                                            <label 
                                                for="platform-{{ $platform->id }}"
                                                class="ml-2 text-sm text-zinc-700 dark:text-zinc-300 flex-1 truncate cursor-pointer"
                                            >{{ $platform->name }}</label>
                                            {{-- <livewire:components.badge 
                                                variant="primary" 
                                                size="xs" 
                                                :text="$platform->prompts_count" 
                                                wire:key="platform-badge-{{ $platform->id }}"
                                                class="ml-1 shrink-0"
                                            /> --}}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- AI Providers Filter (Collapsible) -->
                    <div class="border-b border-zinc-200 dark:border-zinc-700">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-1 cursor-pointer" wire:click="$toggle('showProviders')">
                                <h3 class="font-medium text-zinc-900 dark:text-white text-sm">AI Providers</h3>
                                <button 
                                    class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                >
                                    <svg class="w-4 h-4 transform transition-transform {{ $showProviders ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            @if($showProviders)
                                @php
                                    $filteredProviders = $availableProviders->filter(function($provider) {
                                        return empty($this->providerSearch) || str_contains(strtolower($provider->name), strtolower($this->providerSearch));
                                    });
                                    $providersToShow = empty($this->providerSearch) ? $availableProviders->take(10) : $filteredProviders;
                                @endphp
                                
                                <div class="relative mb-2">
                                    <input 
                                        type="text" 
                                        wire:model.live="providerSearch"
                                        placeholder="Search providers..."
                                        class="w-full pl-8 pr-3 py-1.5 text-sm border border-zinc-200 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-700 focus:ring-2 focus:ring-zinc-500 focus:border-transparent text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500"
                                    >
                                    <svg class="absolute left-2.5 top-2 w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                
                                <div class="space-y-1.5">
                                    @foreach($providersToShow as $provider)
                                        <div class="space-y-1" x-data="{ open: false }">
                                            <!-- Provider -->
                                            <div class="flex items-center group py-0.5">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model.live="providers" 
                                                    value="{{ $provider->id }}"
                                                    class="rounded border-zinc-300 dark:border-zinc-600 text-zinc-600 focus:ring-zinc-500 dark:bg-zinc-700 scale-90"
                                                    id="provider-{{ $provider->id }}"
                                                >
                                                <div class="ml-2 flex items-center flex-1 min-w-0">
                                                    <label 
                                                        for="provider-{{ $provider->id }}"
                                                        class="text-sm text-zinc-700 dark:text-zinc-300 truncate cursor-pointer flex-1"
                                                    >{{ $provider->name }}</label>
                                                    @if($provider->aiModels->count() > 0)
                                                        <button 
                                                            type="button" 
                                                            class="ml-1 p-0.5 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                                                            @click="open = !open"
                                                        >
                                                            <svg class="w-3 h-3 transform transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                                {{-- <livewire:components.badge 
                                                    variant="warning" 
                                                    size="xs" 
                                                    :text="$provider->total_prompts_count" 
                                                    wire:key="provider-badge-{{ $provider->id }}"
                                                    class="ml-1 shrink-0"
                                                /> --}}
                                            </div>

                                            <!-- AI Models under this Provider -->
                                            @if($provider->aiModels->count() > 0)
                                                <div x-show="open" x-collapse class="ml-5 space-y-1">
                                                    @foreach($provider->aiModels as $model)
                                                        <div class="flex items-center group py-0.5">
                                                            <input 
                                                                type="checkbox" 
                                                                wire:model.live="models" 
                                                                value="{{ $model->id }}"
                                                                class="rounded border-zinc-300 dark:border-zinc-600 text-zinc-600 focus:ring-zinc-500 dark:bg-zinc-700 scale-90"
                                                                id="model-{{ $model->id }}"
                                                            >
                                                            <label 
                                                                for="model-{{ $model->id }}"
                                                                class="ml-2 text-xs text-zinc-600 dark:text-zinc-400 flex-1 truncate cursor-pointer"
                                                            >{{ $model->name }}</label>
                                                            {{-- <livewire:components.badge 
                                                                variant="success" 
                                                                size="xs" 
                                                                :text="$model->prompts_count" 
                                                                wire:key="provider-model-badge-{{ $model->id }}"
                                                                class="ml-1 shrink-0"
                                                            /> --}}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
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
