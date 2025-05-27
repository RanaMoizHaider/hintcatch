<?php

use App\Models\AiModel;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.web')] class extends Component {
    public $search = '';
    public $sortBy = 'name'; // name, prompts_count
    public $provider = ''; // filter by provider

    public function getProvidersProperty()
    {
        return AiModel::distinct('provider')
            ->pluck('provider')
            ->filter()
            ->sort()
            ->values();
    }

    public function getModelsProperty()
    {
        return AiModel::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('provider', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->provider, function ($query) {
                $query->where('provider', $this->provider);
            })
            ->when($this->sortBy === 'name', function ($query) {
                $query->orderBy('name');
            })
            ->when($this->sortBy === 'latest', function ($query) {
                $query->latest();
            })
            ->when($this->sortBy === 'prompts', function ($query) {
                $query->withCount('prompts')->orderBy('prompts_count', 'desc');
            })
            ->withCount('prompts')
            ->get();
    }

    public function updatedSearch()
    {
        // This will trigger re-rendering when search changes
    }

    public function updatedSortBy()
    {
        // This will trigger re-rendering when sort changes
    }

    public function updatedProvider()
    {
        // This will trigger re-rendering when provider changes
    }
}; ?>

<div>
    <x-slot name="title">AI Models - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <section class="mb-8">
            <h1 class="text-3xl font-bold mb-4">AI Models</h1>
            <p class="text-zinc-600 dark:text-zinc-400 max-w-2xl mb-6">
                Browse prompts by AI model to find ones optimized for your preferred language model.
            </p>
        </section>

        <!-- Search and Filters -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-zinc-400 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search models..." 
                        class="w-full pl-10 pr-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder-zinc-500 dark:placeholder-zinc-400 focus:ring-2 focus:ring-zinc-500 focus:border-transparent"
                    >
                </div>
                
                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Provider Filter -->
                    <div class="flex items-center gap-2">
                        <label for="provider" class="text-sm text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Provider:</label>
                        <select wire:model.live="provider" id="provider" class="px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-sm">
                            <option value="">All Providers</option>
                            @foreach($this->providers as $providerOption)
                                <option value="{{ $providerOption }}">{{ $providerOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="flex items-center gap-2">
                        <label for="sortBy" class="text-sm text-zinc-600 dark:text-zinc-400 whitespace-nowrap">Sort by:</label>
                        <select wire:model.live="sortBy" id="sortBy" class="px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-sm">
                            <option value="name">Name</option>
                            <option value="prompts_count">Prompt Count</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            @if($search || $provider)
                <div class="flex flex-wrap items-center gap-2 mt-4">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Active filters:</span>
                    
                    @if($search)
                        <livewire:components.badge 
                            variant="primary" 
                            size="sm" 
                            removable="true"
                            removeAction="$set('search', '')"
                            text="Search: {{ $search }}"
                        />
                    @endif

                    @if($provider)
                        <livewire:components.badge 
                            variant="success" 
                            size="sm" 
                            removable="true"
                            removeAction="$set('provider', '')"
                            text="Provider: {{ $provider }}"
                        />
                    @endif
                </div>
            @endif
        </div>

        <!-- Models Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->models as $model)
                <a href="{{ route('models.show', $model->slug) }}" class="block group">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-md transition-all duration-200 group-hover:border-zinc-300 dark:group-hover:border-zinc-600">
                        <div class="flex items-start justify-between mb-2">
                            <h2 class="text-xl font-medium group-hover:text-zinc-800 dark:group-hover:text-zinc-200 transition-colors">
                                {{ $model->name }}
                            </h2>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 ml-2 shrink-0">
                                {{ $model->prompts_count }}
                            </span>
                        </div>

                        @if($model->provider)
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium mb-2">{{ $model->provider }}</p>
                        @endif

                        @if($model->description)
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 line-clamp-3">
                                {{ $model->description }}
                            </p>
                        @endif

                        @if($model->features && is_array($model->features))
                            <div class="space-y-3">
                                <div>
                                    <h3 class="text-sm font-medium mb-2 text-zinc-700 dark:text-zinc-300">Key Features:</h3>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(collect($model->features)->take(4) as $feature)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                                                {{ $feature }}
                                            </span>
                                        @endforeach
                                        @if(count($model->features) > 4)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                                +{{ count($model->features) - 4 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <x-empty-state 
                    icon="folder"
                    title="No models found"
                    :description="($search || $provider) ? 'No models match your search criteria.' : 'No AI models are available yet.'"
                    class="col-span-full"
                />
            @endforelse
        </div>

        @if($this->models->count() > 0)
            <div class="mt-8 text-center">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Showing {{ $this->models->count() }} 
                    {{ Str::plural('model', $this->models->count()) }}
                </p>
            </div>
        @endif
    </div>
</div>
