<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.web')] class extends Component {
    public $search = '';
    public $sortBy = 'name'; // name, prompts_count

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

        return $query->orderBy($this->sortBy === 'prompts_count' ? 'prompts_count' : 'name')
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
                        placeholder="Search categories..." 
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

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->categories as $category)
                <a href="{{ route('categories.show', $category->slug) }}" class="block group">
                    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-md transition-all duration-200 group-hover:border-zinc-400 dark:group-hover:border-zinc-500">
                        <div class="flex items-start justify-between mb-3">
                            <h2 class="text-xl font-medium text-zinc-900 dark:text-white group-hover:text-zinc-700 dark:group-hover:text-zinc-300 transition-colors">
                                {{ $category->name }}
                            </h2>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 ml-2 shrink-0">
                                {{ $category->prompts_count }}
                            </span>
                        </div>
                        
                        @if($category->description)
                            <p class="text-sm text-zinc-700 dark:text-zinc-400 mb-4 line-clamp-3">
                                {{ $category->description }}
                            </p>
                        @endif

                        @if($category->children->count() > 0)
                            <div class="space-y-2">
                                <h3 class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Subcategories:</h3>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($category->children->take(3) as $child)
                                        <livewire:components.badge variant="primary" size="xs" text="{{ $child->name }}" />
                                    @endforeach
                                    @if($category->children->count() > 3)
                                        <livewire:components.badge variant="secondary" size="xs" text="+{{ $category->children->count() - 3 }} more" />
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </a>
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
