<?php

use App\Models\Platform;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.web')] class extends Component {
    public $search = '';
    public $sortBy = 'name'; // name, prompts_count

    public function getPlatformsProperty()
    {
        return Platform::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
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
}; ?>

<div>
    <x-slot name="title">AI Platforms - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <section class="mb-8">
            <h1 class="text-3xl font-bold mb-4">AI Platforms</h1>
            <p class="text-zinc-700 dark:text-zinc-400 max-w-2xl mb-6">
                Browse prompts by AI platform to find ones optimized for your preferred tools and services.
            </p>
        </section>

        <!-- Search and Sort -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search platforms..." 
                        class="w-full pl-10 pr-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-800 focus:ring-2 focus:ring-zinc-500 focus:border-transparent text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500"
                    >
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="text-sm text-zinc-700 dark:text-zinc-400">Sort by:</span>
                    <select wire:model.live="sortBy" class="px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-800 text-sm text-zinc-900 dark:text-zinc-100">
                        <option value="name">Name</option>
                        <option value="prompts_count">Prompt Count</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Platforms Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->platforms as $platform)
                <a href="{{ route('platforms.show', $platform->slug) }}" class="block group">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-md transition-all duration-200 group-hover:border-zinc-400 dark:group-hover:border-zinc-500">
                        <div class="flex items-start justify-between mb-3">
                            <h2 class="text-xl font-medium text-zinc-900 dark:text-white group-hover:text-zinc-700 dark:group-hover:text-zinc-300 transition-colors">
                                {{ $platform->name }}
                            </h2>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 ml-2 shrink-0">
                                {{ $platform->prompts_count }}
                            </span>
                        </div>
                        
                        @if($platform->description)
                            <p class="text-sm text-zinc-700 dark:text-zinc-400 mb-4 line-clamp-3">
                                {{ $platform->description }}
                            </p>
                        @endif

                        @if($platform->features && is_array($platform->features))
                            <div class="space-y-3">
                                <div>
                                    <h3 class="text-sm font-medium mb-2 text-zinc-700 dark:text-zinc-200">Key Features:</h3>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(collect($platform->features)->take(4) as $feature)
                                            <livewire:components.badge variant="primary" size="xs" text="{{ $feature }}" />
                                        @endforeach
                                        @if(count($platform->features) > 4)
                                            <livewire:components.badge variant="secondary" size="xs" text="+{{ count($platform->features) - 4 }} more" />
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($platform->best_practices && is_array($platform->best_practices))
                            <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                                <h3 class="text-sm font-medium mb-2 text-zinc-700 dark:text-zinc-200">Best Practices:</h3>
                                <ul class="text-xs text-zinc-700 dark:text-zinc-400 space-y-1">
                                    @foreach(collect($platform->best_practices)->take(2) as $practice)
                                        <li class="flex items-start">
                                            <svg class="w-3 h-3 text-green-500 mr-1 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ Str::limit($practice, 60) }}
                                        </li>
                                    @endforeach
                                    @if(count($platform->best_practices) > 2)
                                        <li class="text-zinc-500 dark:text-zinc-500">
                                            +{{ count($platform->best_practices) - 2 }} more practices
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <x-empty-state 
                    icon="folder"
                    title="No platforms found"
                    :description="$search ? 'No platforms match your search criteria.' : 'No AI platforms are available yet.'"
                    class="col-span-full"
                />
            @endforelse
        </div>

        @if($this->platforms->count() > 0)
            <div class="mt-8 text-center">
                <p class="text-sm text-zinc-700 dark:text-zinc-400">
                    Showing {{ $this->platforms->count() }} 
                    {{ Str::plural('platform', $this->platforms->count()) }}
                </p>
            </div>
        @endif
    </div>
</div>
