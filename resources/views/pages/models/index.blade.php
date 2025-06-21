<?php

use App\Models\AiModel;
use App\Models\Provider;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.web')]
class extends Component {
    public $search = '';
    public $sortBy = 'popular'; // popular, newest, oldest, name
    public $provider = ''; // filter by provider

    public function getProvidersProperty()
    {
        // Frontend - only get approved providers due to global scope
        return Provider::whereHas('aiModels')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');
    }

    public function getModelsProperty()
    {
        // Frontend - only get approved AI models due to global scope
        return AiModel::query()
            ->with('provider')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('provider', function ($providerQuery) {
                          $providerQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->provider, function ($query) {
                $query->where('provider_id', $this->provider);
            })
            ->when($this->sortBy === 'newest', function ($query) {
                $query->orderByDesc('release_date');
            })
            ->when($this->sortBy === 'oldest', function ($query) {
                $query->orderBy('release_date');
            })
            ->when($this->sortBy === 'name', function ($query) {
                $query->orderByRaw('LOWER(name)');
            })
            ->when($this->sortBy === 'popular', function ($query) {
                $query->orderByDesc('prompts_count');
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

    public function getSelectedProviderNameProperty()
    {
        if (!$this->provider) return null;
        return $this->providers[$this->provider] ?? 'Unknown';
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

        <!-- Search and Filters/Sort -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="relative flex-1 max-w-md">
                    <flux:input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search models..." 
                        icon="magnifying-glass"
                    />
                </div>
                
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Provider Filter -->
                    <div class="flex items-center gap-2">
                        <label for="provider" class="text-sm text-zinc-700 dark:text-zinc-400 whitespace-nowrap">Provider:</label>
                        <flux:select wire:model.live="provider" id="provider" size="sm">
                            <flux:select.option value="">All Providers</flux:select.option>
                            @foreach($this->providers as $providerId => $providerName)
                                <flux:select.option value="{{ $providerId }}">{{ $providerName }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <!-- Sort -->
                    <div class="flex items-center gap-2">
                        <label for="sortBy" class="text-sm text-zinc-700 dark:text-zinc-400 whitespace-nowrap">Sort by:</label>
                        <flux:select wire:model.live="sortBy" id="sortBy" size="sm">
                            <flux:select.option value="popular">Popular</flux:select.option>
                            <flux:select.option value="newest">Newest</flux:select.option>
                            <flux:select.option value="oldest">Oldest</flux:select.option>
                            <flux:select.option value="name">Name</flux:select.option>
                        </flux:select>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            @if($search || $provider)
                <div class="flex flex-wrap items-center gap-2 mt-4">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Active filters:</span>
                    
                    @if($search)
                        <flux:badge 
                            color="blue" 
                            size="sm"
                        >
                            Search: {{ $search }}
                            <flux:badge.close wire:click="$set('search', '')" />
                        </flux:badge>
                    @endif

                    @if($provider)
                        <flux:badge 
                            color="green" 
                            size="sm"
                            wire:key="provider-filter-{{ $provider }}"
                        >
                            Provider: {{ $this->selectedProviderName }}
                            <flux:badge.close wire:click="$set('provider', '')" />
                        </flux:badge>
                    @endif
                </div>
            @endif
        </div>

        <!-- Models Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->models as $model)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-md transition-all duration-200 hover:border-zinc-300 dark:hover:border-zinc-600 h-full flex flex-col">
                    <div class="flex items-start justify-between mb-2">
                        <h2 class="text-xl font-medium transition-colors">
                            <flux:link href="{{ route('models.show', $model->slug) }}" variant="ghost" class="hover:text-zinc-800 dark:hover:text-zinc-200">
                                {{ $model->name }}
                            </flux:link>
                        </h2>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 ml-2 shrink-0">
                            {{ $model->prompts_count }}
                        </span>
                    </div>

                    @if($model->provider)
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium mb-2">{{ $model->provider->name }}</p>
                    @endif

                    @if($model->release_date)
                        <p class="text-xs text-zinc-500 dark:text-zinc-500 mb-2">Released {{ $model->release_date->format('M j, Y') }}</p>
                    @endif

                    @if($model->description)
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 line-clamp-3 flex-1">
                            {{ $model->description }}
                        </p>
                    @endif

                    @if($model->features && is_array($model->features))
                        <div class="space-y-3 mt-auto">
                            <div>
                                <h3 class="text-sm font-medium mb-2 text-zinc-700 dark:text-zinc-300">Key Features:</h3>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(collect($model->features)->take(4) as $feature)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                    @if(count($model->features) > 4)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-zinc-50 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                            +{{ count($model->features) - 4 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
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
