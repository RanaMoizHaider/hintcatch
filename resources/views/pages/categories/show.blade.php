<?php

use App\Models\Category;
use App\Models\Prompt;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[Layout('components.layouts.web')]
class extends Component {
    use WithPagination;

    public Category $category;
    public $activeTab = 'trending';
    public $viewMode = 'grid';

    public function mount(Category $category)
    {
        $this->category = $category->load(['children', 'parent']);
    }

    public function getPromptsProperty()
    {
        $query = $this->category->prompts()
            ->with(['user', 'category', 'platforms', 'aiModels'])
            ->withCount(['likes', 'comments'])
            ->withViewsCount();

        return match($this->activeTab) {
            'newest' => $query->latest()->paginate(12),
            'trending' => $query->orderByDesc('views_count')->paginate(12),
            default => $query->orderByDesc('likes_count')->paginate(12),
        };
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }
}; ?>

<div>
    <x-slot name="title">{{ $category->name }} - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('categories.index') }}">Categories</flux:breadcrumbs.item>
                @if($category->parent)
                    <flux:breadcrumbs.item href="{{ route('categories.show', $category->parent->slug) }}">{{ $category->parent->name }}</flux:breadcrumbs.item>
                @endif
                <flux:breadcrumbs.item>{{ $category->name }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
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
                        <div class="px-3 py-2 text-sm border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors text-center">
                            <flux:link href="{{ route('categories.show', $subcategory->slug) }}" variant="ghost" class="font-medium text-zinc-700 dark:text-zinc-300 hover:underline">
                                {{ $subcategory->name }}
                            </flux:link>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">{{ $subcategory->prompts()->count() }}</div>
                        </div>
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
                <flux:button.group>
                    <flux:button 
                        wire:click="setActiveTab('trending')"
                        variant="{{ $activeTab === 'trending' ? 'filled' : 'subtle' }}"
                        size="sm"
                    >
                        Trending
                    </flux:button>
                    <flux:button 
                        wire:click="setActiveTab('newest')"
                        variant="{{ $activeTab === 'newest' ? 'filled' : 'subtle' }}"
                        size="sm"
                    >
                        Newest
                    </flux:button>
                    <flux:button 
                        wire:click="setActiveTab('popular')"
                        variant="{{ $activeTab === 'popular' ? 'filled' : 'subtle' }}"
                        size="sm"
                    >
                        Popular
                    </flux:button>
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
            
            <!-- Prompts Grid/List -->
            @if($viewMode === 'grid')
                <x-card-grid :columns="3" wire:loading.class="opacity-50" class="grid-auto-rows-[1fr]">
                    @forelse($this->prompts as $prompt)
                        <livewire:components.prompt-card 
                            :prompt="$prompt"
                            :show-user="false"
                            :show-stats="true" 
                            :show-platforms="true"
                            :show-models="true"
                            :platform-limit="2"
                            :model-limit="1"
                            wire:key="prompt-{{ $prompt->id }}"
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
            @else
                <div class="space-y-4" wire:loading.class="opacity-50">
                    @forelse($this->prompts as $prompt)
                        <livewire:components.prompt-card 
                            :prompt="$prompt"
                            :show-user="true"
                            :show-stats="true" 
                            :show-platforms="true"
                            :show-models="true"
                            :platform-limit="3"
                            :model-limit="2"
                            :layout="'horizontal'"
                            wire:key="prompt-list-{{ $prompt->id }}"
                        />
                    @empty
                        <x-empty-state 
                            icon="document"
                            title="No prompts found"
                            description="This category doesn't have any prompts yet."
                            class="w-full"
                        />
                    @endforelse
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-8">
                {{ $this->prompts->links() }}
            </div>
        </div>
    </div>
</div>
