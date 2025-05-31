<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.web')]
class extends Component {
    public $search = '';
    public $sortBy = 'popular'; // popular, newest, name

    public function getCategoriesProperty()
    {
        $query = Category::query()
            ->withCount('prompts')
            ->whereNull('parent_id'); // Only top-level categories

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return $query->when($this->sortBy === 'newest', function ($q) {
                    $q->latest();
                }, function ($q) {
                    if ($this->sortBy === 'name') {
                        $q->orderByRaw('LOWER(name)');
                    } else { // popular - highest prompt count first
                        $q->orderByDesc('prompts_count');
                    }
                })
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
    <x-slot name="title">Browse Categories - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <section class="mb-8">
            <h1 class="text-3xl font-bold mb-4">Browse Categories</h1>
            <p class="text-zinc-700 dark:text-zinc-400 max-w-2xl mb-6">
                Explore prompts by category to find exactly what you need for your specific use case.
            </p>
        </section>

        <!-- Search and Filters/Sort -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="relative flex-1 max-w-md">
                    <flux:input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search categories..." 
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

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->categories as $category)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-md transition-all duration-200 hover:border-zinc-400 dark:hover:border-zinc-500 h-full flex flex-col" wire:key="category-{{ $category->id }}">
                    <div class="flex items-start justify-between mb-3">
                        <h2 class="text-xl font-medium text-zinc-900 dark:text-white transition-colors">
                            <flux:link href="{{ route('categories.show', $category->slug) }}" variant="ghost" class="hover:text-zinc-700 dark:hover:text-zinc-300">
                                {{ $category->name }}
                            </flux:link>
                        </h2>
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 ml-2 shrink-0">
                            {{ $category->prompts_count }}
                        </span>
                    </div>
                        
                    @if($category->description)
                        <p class="text-sm text-zinc-700 dark:text-zinc-400 mb-4 line-clamp-3 flex-1">
                            {{ $category->description }}
                        </p>
                    @endif

                    @if($category->children->count() > 0)
                        <div class="space-y-2 mt-auto">
                            <h3 class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Subcategories:</h3>
                            <div class="flex flex-wrap gap-1">
                                @foreach($category->children->take(3) as $child)
                                    <flux:badge 
                                        color="blue" 
                                        size="sm" 
                                        wire:key="subcategory-{{ $category->id }}-{{ $child->id }}"
                                    >{{ $child->name }}</flux:badge>
                                @endforeach
                                @if($category->children->count() > 3)
                                    <flux:badge 
                                        color="zinc" 
                                        size="sm" 
                                        wire:key="subcategory-more-{{ $category->id }}"
                                    >+{{ $category->children->count() - 3 }} more</flux:badge>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <x-empty-state 
                    icon="tag"
                    title="No categories found"
                    :description="$search ? 'No categories match your search criteria.' : 'No categories are available yet.'"
                    class="col-span-full"
                />
            @endforelse
        </div>

        @if($this->categories->count() > 0)
            <div class="mt-8 text-center">
                <p class="text-sm text-zinc-700 dark:text-zinc-400">
                    Showing {{ $this->categories->count() }} 
                    {{ Str::plural('category', $this->categories->count()) }}
                </p>
            </div>
        @endif
    </div>
</div>
