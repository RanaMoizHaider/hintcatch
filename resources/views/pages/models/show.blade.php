<?php

use App\Models\AiModel;
use App\Models\Prompt;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.web')] class extends Component {
    use WithPagination;

    public AiModel $model;
    public $activeTab = 'trending';
    public $viewMode = 'grid';

    public function mount(AiModel $model)
    {
        $this->model = $model->load('provider');
    }

    public function getPromptsProperty()
    {
        $query = $this->model->prompts()
            ->with(['user', 'category', 'platforms', 'aiModels'])
            ->withCount(['likes', 'comments'])
            ->withViewsCount();

        return match($this->activeTab) {
            'newest' => $query->latest()->paginate(12),
            'trending' => $query->orderByViews('desc')->paginate(12),
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
    <x-slot name="title">{{ $model->name }} - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <a href="{{ route('models.index') }}" class="inline-flex items-center text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Models
            </a>
        </nav>

        <!-- Model Header -->
        <section class="mb-8">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold">{{ $model->name }}</h1>
                    @if($model->provider)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                            {{ $model->provider->name }}
                        </span>
                    @endif
                </div>
                <div class="text-right shrink-0 ml-4">
                    <div class="text-2xl font-bold text-zinc-800 dark:text-zinc-200">{{ $model->prompts()->count() }}</div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ Str::plural('prompt', $model->prompts()->count()) }}</div>
                </div>
            </div>

            @if($model->description)
                <p class="text-zinc-600 dark:text-zinc-400 max-w-3xl mb-6">{{ $model->description }}</p>
            @endif

            @if($model->features && is_array($model->features))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h2 class="text-lg font-medium mb-3 text-zinc-900 dark:text-zinc-100">Key Features</h2>
                        <div class="space-y-2">
                            @foreach($model->features as $feature)
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-medium mb-3 text-zinc-900 dark:text-zinc-100">Model Information</h2>
                        <div class="space-y-3">
                            @if($model->provider)
                                <div>
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Provider:</span>
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400 ml-2">{{ $model->provider->name }}</span>
                                </div>
                            @endif
                            @if($model->release_date)
                                <div>
                                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Release Date:</span>
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400 ml-2">{{ $model->release_date->format('M j, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <!-- Prompts Section -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Prompts for {{ $model->name }}</h2>
            </div>

            <!-- Tab Navigation -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex space-x-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg">
                    <button 
                        wire:click="setActiveTab('trending')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'trending' ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'hover:bg-white/50 dark:hover:bg-zinc-700/50' }}">
                        Trending
                    </button>
                    <button 
                        wire:click="setActiveTab('newest')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'newest' ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'hover:bg-white/50 dark:hover:bg-zinc-700/50' }}">
                        Newest
                    </button>
                    <button 
                        wire:click="setActiveTab('popular')"
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'popular' ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'hover:bg-white/50 dark:hover:bg-zinc-700/50' }}">
                        Popular
                    </button>
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
            
            <!-- Prompts Grid/List -->
            @if($viewMode === 'grid')
                <x-card-grid :columns="3" wire:loading.class="opacity-50" class="grid-auto-rows-[1fr]">
                    @forelse($this->prompts as $prompt)
                        <livewire:components.prompt-card 
                            :prompt="$prompt"
                            :show-user="false"
                            :show-stats="true" 
                            :show-category="true"
                            :show-platforms="true"
                            :show-models="false"
                            :platform-limit="2"
                            class="h-full"
                            wire:key="prompt-{{ $prompt->id }}"
                        />
                    @empty
                        <x-empty-state 
                            icon="document"
                            title="No prompts found"
                            description="No prompts are available for this model yet."
                            class="col-span-full"
                        />
                    @endforelse
                </x-card-grid>
            @else
                <div class="space-y-4" wire:loading.class="opacity-50">
                    @forelse($this->prompts as $prompt)
                        <livewire:components.prompt-card 
                            :prompt="$prompt"
                            :show-user="false"
                            :show-stats="true" 
                            :show-category="true"
                            :show-platforms="true"
                            :show-models="false"
                            :platform-limit="2"
                            :layout="'list'"
                            wire:key="prompt-list-{{ $prompt->id }}"
                        />
                    @empty
                        <x-empty-state 
                            icon="document"
                            title="No prompts found"
                            description="No prompts are available for this model yet."
                        />
                    @endforelse
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-8">
                {{ $this->prompts->links() }}
            </div>
        </section>
    </div>
</div>
