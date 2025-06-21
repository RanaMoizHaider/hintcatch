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

new
#[Layout('components.layouts.web')]
class extends Component {
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
        // Frontend - only get approved categories due to global scope
        $this->availableCategories = Category::with(['children' => function($query) {
            $query->with(['children' => function($subQuery) {
                $subQuery->withCount('prompts');
            }])->withCount('prompts');
        }])->whereNull('parent_id')->withCount('prompts')->get();
        
        // Frontend - only get approved platforms due to global scope
        $this->availablePlatforms = Platform::withCount('prompts')->get();
        
        // Frontend - only get approved providers due to global scope
        $this->availableProviders = Provider::with(['aiModels' => function($query) {
            $query->withCount('prompts');
        }])->get()->map(function($provider) {
            $provider->total_prompts_count = $provider->aiModels->sum('prompts_count');
            return $provider;
        });
        
        // Frontend - only get approved AI models due to global scope
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
            <p class="text-zinc-700 dark:text-zinc-400 max-w-2xl mb-6">
                Discover and filter through many high-quality prompts for various AI platforms and models.
            </p>
        </section>

        <!-- Search and Filters/Sort -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="relative flex-1 max-w-md">
                    <flux:input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search prompts..." 
                        icon="magnifying-glass"
                    />
                </div>
            </div>
        </div>

        @if(count($this->appliedFilters) > 0)
            <div class="mb-6">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-medium">Applied filters:</span>
                    @foreach($this->appliedFilters as $filter)
                        <flux:badge color="zinc" size="sm">{{ $filter['type'] }}: {{ $filter['value'] }}</flux:badge>
                    @endforeach
                    <flux:button wire:click="clearFilters" variant="ghost" size="sm">Clear all</flux:button>
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
                            <flux:button 
                                variant="ghost" 
                                icon="chevron-down"
                                size="xs"
                                square
                                class="transform transition-transform {{ $showCategories ? 'rotate-180' : '' }}"
                            />
                        </div>
                        
                        @if($showCategories)
                            @php
                                $filteredCategories = $availableCategories->filter(function($category) {
                                    return empty($this->categorySearch) || str_contains(strtolower($category->name), strtolower($this->categorySearch));
                                });
                                $categoriesToShow = empty($this->categorySearch) ? $availableCategories->take(10) : $filteredCategories;
                            @endphp
                            
                            <div class="relative mb-2">
                                <flux:input 
                                    type="text" 
                                    wire:model.live="categorySearch"
                                    placeholder="Search categories..."
                                    icon="magnifying-glass"
                                    size="sm"
                                />
                            </div>
                            
                            <div class="space-y-1.5">
                                @foreach($categoriesToShow as $category)
                                    <div class="space-y-1" x-data="{ open: false }">
                                        <!-- Main Category -->
                                        <div class="flex items-center group py-0.5">
                                            <flux:checkbox 
                                                wire:model.live="categories" 
                                                value="{{ $category->id }}"
                                                id="category-{{ $category->id }}"
                                            />
                                            <div class="ml-2 flex items-center flex-1 min-w-0">
                                                <label 
                                                    for="category-{{ $category->id }}"
                                                    class="text-sm text-zinc-700 dark:text-zinc-300 truncate cursor-pointer flex-1"
                                                >{{ $category->name }}</label>
                                                @if($category->children->count() > 0)
                                                    <flux:button 
                                                        variant="ghost" 
                                                        icon="chevron-right"
                                                        size="xs"
                                                        square
                                                        @click="open = !open"
                                                        class="transform transition-transform"
                                                        ::class="{ 'rotate-90': open }"
                                                    />
                                                @endif
                                            </div>
                                            {{-- <flux:badge 
                                                color="zinc" 
                                                size="sm" 
                                                wire:key="cat-badge-{{ $category->id }}"
                                                class="ml-1 shrink-0"
                                            >{{ $category->prompts_count }}</flux:badge> --}}
                                        </div>

                                        <!-- Subcategories -->
                                        @if($category->children->count() > 0)
                                            <div x-show="open" x-collapse class="ml-5 space-y-1">
                                                @foreach($category->children as $subcategory)
                                                    <div class="space-y-1" x-data="{ subOpen: false }">
                                                        <div class="flex items-center group py-0.5">
                                                            <flux:checkbox 
                                                                wire:model.live="categories" 
                                                                value="{{ $subcategory->id }}"
                                                                id="subcategory-{{ $subcategory->id }}"
                                                            />
                                                            <div class="ml-2 flex items-center flex-1 min-w-0">
                                                                <label 
                                                                    for="subcategory-{{ $subcategory->id }}"
                                                                    class="text-xs text-zinc-600 dark:text-zinc-400 truncate cursor-pointer flex-1"
                                                                >{{ $subcategory->name }}</label>
                                                                @if($subcategory->children->count() > 0)
                                                                    <flux:button 
                                                                        variant="ghost" 
                                                                        icon="chevron-right"
                                                                        size="xs"
                                                                        square
                                                                        @click="subOpen = !subOpen"
                                                                        class="transform transition-transform"
                                                                        ::class="{ 'rotate-90': subOpen }"
                                                                    />
                                                                @endif
                                                            </div>
                                                            {{-- <flux:badge 
                                                                color="zinc" 
                                                                size="sm" 
                                                                wire:key="subcat-badge-{{ $subcategory->id }}"
                                                                class="ml-1 shrink-0"
                                                            >{{ $subcategory->prompts_count }}</flux:badge> --}}
                                                        </div>

                                                        <!-- Sub-subcategories -->
                                                        @if($subcategory->children->count() > 0)
                                                            <div x-show="subOpen" x-collapse class="ml-5 space-y-1">
                                                                @foreach($subcategory->children as $subSubcategory)
                                                                    <div class="flex items-center group py-0.5">
                                                                        <flux:checkbox 
                                                                            wire:model.live="categories" 
                                                                            value="{{ $subSubcategory->id }}"
                                                                            id="subsubcategory-{{ $subSubcategory->id }}"
                                                                        />
                                                                        <label 
                                                                            for="subsubcategory-{{ $subSubcategory->id }}"
                                                                            class="ml-2 text-xs text-zinc-500 dark:text-zinc-500 flex-1 truncate cursor-pointer"
                                                                        >{{ $subSubcategory->name }}</label>
                                                                        {{-- <flux:badge 
                                                                            color="zinc" 
                                                                            size="sm" 
                                                                            wire:key="subsubcat-badge-{{ $subSubcategory->id }}"
                                                                            class="ml-1 shrink-0"
                                                                        >{{ $subSubcategory->prompts_count }}</flux:badge> --}}
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
                                <flux:button 
                                    variant="ghost" 
                                    icon="chevron-down"
                                    size="xs"
                                    square
                                    class="transform transition-transform {{ $showPlatforms ? 'rotate-180' : '' }}"
                                />
                            </div>
                            
                            @if($showPlatforms)
                                @php
                                    $filteredPlatforms = $availablePlatforms->filter(function($platform) {
                                        return empty($this->platformSearch) || str_contains(strtolower($platform->name), strtolower($this->platformSearch));
                                    });
                                    $platformsToShow = empty($this->platformSearch) ? $availablePlatforms->take(10) : $filteredPlatforms;
                                @endphp
                                
                                <div class="relative mb-2">
                                    <flux:input 
                                        type="text" 
                                        wire:model.live="platformSearch"
                                        placeholder="Search platforms..."
                                        icon="magnifying-glass"
                                        size="sm"
                                    />
                                </div>
                                
                                <div class="space-y-1.5">
                                    @foreach($platformsToShow as $platform)
                                        <div class="flex items-center group py-0.5">
                                            <flux:checkbox 
                                                wire:model.live="platforms" 
                                                value="{{ $platform->id }}"
                                                id="platform-{{ $platform->id }}"
                                            />
                                            <label 
                                                for="platform-{{ $platform->id }}"
                                                class="ml-2 text-sm text-zinc-700 dark:text-zinc-300 flex-1 truncate cursor-pointer"
                                            >{{ $platform->name }}</label>
                                            {{-- <flux:badge 
                                                color="blue" 
                                                size="sm" 
                                                wire:key="platform-badge-{{ $platform->id }}"
                                                class="ml-1 shrink-0"
                                            >{{ $platform->prompts_count }}</flux:badge> --}}
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
                                <flux:button 
                                    variant="ghost" 
                                    icon="chevron-down"
                                    size="xs"
                                    square
                                    class="transform transition-transform {{ $showProviders ? 'rotate-180' : '' }}"
                                />
                            </div>
                            
                            @if($showProviders)
                                @php
                                    $filteredProviders = $availableProviders->filter(function($provider) {
                                        return empty($this->providerSearch) || str_contains(strtolower($provider->name), strtolower($this->providerSearch));
                                    });
                                    $providersToShow = empty($this->providerSearch) ? $availableProviders->take(10) : $filteredProviders;
                                @endphp
                                
                                <div class="relative mb-2">
                                    <flux:input 
                                        type="text" 
                                        wire:model.live="providerSearch"
                                        placeholder="Search providers..."
                                        icon="magnifying-glass"
                                        size="sm"
                                    />
                                </div>
                                
                                <div class="space-y-1.5">
                                    @foreach($providersToShow as $provider)
                                        <div class="space-y-1" x-data="{ open: false }">
                                            <!-- Provider -->
                                            <div class="flex items-center group py-0.5">
                                                <flux:checkbox 
                                                    wire:model.live="providers" 
                                                    value="{{ $provider->id }}"
                                                    id="provider-{{ $provider->id }}"
                                                />
                                                <div class="ml-2 flex items-center flex-1 min-w-0">
                                                    <label 
                                                        for="provider-{{ $provider->id }}"
                                                        class="text-sm text-zinc-700 dark:text-zinc-300 truncate cursor-pointer flex-1"
                                                    >{{ $provider->name }}</label>
                                                    @if($provider->aiModels->count() > 0)
                                                        <flux:button 
                                                            variant="ghost" 
                                                            icon="chevron-right"
                                                            size="xs"
                                                            square
                                                            @click="open = !open"
                                                            class="transform transition-transform"
                                                            ::class="{ 'rotate-90': open }"
                                                        />
                                                    @endif
                                                </div>
                                                {{-- <flux:badge 
                                                    color="amber" 
                                                    size="sm" 
                                                    wire:key="provider-badge-{{ $provider->id }}"
                                                    class="ml-1 shrink-0"
                                                >{{ $provider->total_prompts_count }}</flux:badge> --}}
                                            </div>

                                            <!-- AI Models under this Provider -->
                                            @if($provider->aiModels->count() > 0)
                                                <div x-show="open" x-collapse class="ml-5 space-y-1">
                                                    @foreach($provider->aiModels as $model)
                                                        <div class="flex items-center group py-0.5">
                                                            <flux:checkbox 
                                                                wire:model.live="models" 
                                                                value="{{ $model->id }}"
                                                                id="model-{{ $model->id }}"
                                                            />
                                                            <label 
                                                                for="model-{{ $model->id }}"
                                                                class="ml-2 text-xs text-zinc-600 dark:text-zinc-400 flex-1 truncate cursor-pointer"
                                                            >{{ $model->name }}</label>
                                                            {{-- <flux:badge 
                                                                color="green" 
                                                                size="sm" 
                                                                wire:key="provider-model-badge-{{ $model->id }}"
                                                                class="ml-1 shrink-0"
                                                            >{{ $model->prompts_count }}</flux:badge> --}}
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
                        <flux:button.group>
                            <flux:button wire:click="$set('activeTab', 'all')" variant="{{ $activeTab === 'all' ? 'filled' : 'subtle' }}" size="sm">All</flux:button>
                            <flux:button wire:click="$set('activeTab', 'trending')" variant="{{ $activeTab === 'trending' ? 'filled' : 'subtle' }}" size="sm">Trending</flux:button>
                            <flux:button wire:click="$set('activeTab', 'newest')" variant="{{ $activeTab === 'newest' ? 'filled' : 'subtle' }}" size="sm">Newest</flux:button>
                            <flux:button wire:click="$set('activeTab', 'popular')" variant="{{ $activeTab === 'popular' ? 'filled' : 'subtle' }}" size="sm">Popular</flux:button>
                        </flux:button.group>
                        <flux:button.group>
                            <flux:button 
                                wire:click="$set('viewMode', 'grid')"
                                variant="{{ $viewMode === 'grid' ? 'filled' : 'outline' }}"
                                icon="squares-2x2"
                                size="sm"
                                square
                            />
                            <flux:button 
                                wire:click="$set('viewMode', 'list')"
                                variant="{{ $viewMode === 'list' ? 'filled' : 'outline' }}"
                                icon="bars-3"
                                size="sm"
                                square
                            />
                        </flux:button.group>
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
                                <flux:button 
                                    wire:click="clearFilters" 
                                    variant="primary"
                                >
                                    Clear Filters
                                </flux:button>
                            </x-slot>
                        </x-empty-state>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
