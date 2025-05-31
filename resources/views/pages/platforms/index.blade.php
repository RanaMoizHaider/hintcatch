<?php

use App\Models\Platform;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.web')]
class extends Component {
    public $search = '';
    public $sortBy = 'popular'; // popular, newest, name

    public function getPlatformsProperty()
    {
        return Platform::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->sortBy === 'newest', function ($query) {
                $query->latest();
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

        <!-- Search and Filters/Sort -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="relative flex-1 max-w-md">
                    <flux:input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search platforms..." 
                        icon="magnifying-glass"
                    />
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="text-sm text-zinc-700 dark:text-zinc-400">Sort by:</span>
                    <flux:select wire:model.live="sortBy" size="sm">
                        <flux:select.option value="popular">Popular</flux:select.option>
                        <flux:select.option value="newest">Newest</flux:select.option>
                        <flux:select.option value="name">Name</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Platforms Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->platforms as $platform)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-md transition-all duration-200 hover:border-zinc-400 dark:hover:border-zinc-500 h-full flex flex-col" wire:key="category-{{ $platform->id }}">
                    <div class="flex items-start justify-between mb-3">
                        <h2 class="text-xl font-medium text-zinc-900 dark:text-white transition-colors">
                            <flux:link href="{{ route('platforms.show', $platform->slug) }}" variant="ghost" class="hover:text-zinc-700 dark:hover:text-zinc-300">
                                {{ $platform->name }}
                            </flux:link>
                        </h2>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 ml-2 shrink-0">
                            {{ $platform->prompts_count }}
                        </span>
                    </div>
                        
                    @if($platform->description)
                        <p class="text-sm text-zinc-700 dark:text-zinc-400 mb-4 line-clamp-3 flex-1">
                            {{ $platform->description }}
                        </p>
                    @endif

                    <div class="mt-auto">
                        @if($platform->features && is_array($platform->features))
                            <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                                <h3 class="text-sm font-medium mb-2 text-zinc-700 dark:text-zinc-200">Key Features:</h3>
                                <ul class="text-xs text-zinc-700 dark:text-zinc-400 space-y-1">
                                    @foreach(collect($platform->features)->take(2) as $feature)
                                        <li class="flex items-start">
                                            <flux:icon.check class="size-3 text-green-500 mr-1 mt-0.5 shrink-0" />
                                            {{ Str::limit($feature, 60) }}
                                        </li>
                                    @endforeach
                                    @if(count($platform->features) > 2)
                                        <li class="text-zinc-500 dark:text-zinc-500">
                                            +{{ count($platform->features) - 2 }} more {{ Str::plural('feature', count($platform->features) - 2) }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
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
