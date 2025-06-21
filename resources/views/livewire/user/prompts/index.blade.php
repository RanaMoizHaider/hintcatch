<?php

use App\Models\{Prompt, Category};
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[Layout('components.layouts.app')]
class extends Component {
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public string $filterCategory = '';
    public string $filterVisibility = '';
    public string $filterStatus = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatingFilterVisibility(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        // Reset sort direction when changing sort field
        if ($this->sortBy === 'title') {
            $this->sortDirection = 'asc'; // A-Z for title
        } else {
            $this->sortDirection = 'desc'; // Most recent/highest count first for others
        }
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            if ($field === 'title') {
                $this->sortDirection = 'asc'; // A-Z for title
            } else {
                $this->sortDirection = 'desc'; // Most recent/highest count first for others
            }
        }
    }

    public function delete(int $id): void
    {
        // Author sees ALL their own prompts regardless of status or visibility
        $prompt = Prompt::withAll()->where('user_id', auth()->id())->find($id);
        if ($prompt) {
            $prompt->delete();
            session()->flash('success', 'Prompt deleted successfully.');
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterCategory = '';
        $this->filterVisibility = '';
        $this->filterStatus = '';
        $this->resetPage();
    }

    public function with(): array
    {
        // Author sees ALL their own prompts regardless of status or visibility
        $prompts = Prompt::withAll()
            ->where('user_id', auth()->id())
            ->with(['category', 'aiModels', 'platforms', 'tags'])
            ->when($this->search, function($query) {
                $searchTerm = strtolower($this->search);
                $query->where(function($subQuery) use ($searchTerm) {
                    $subQuery->whereRaw('LOWER(title) LIKE ?', ['%' . $searchTerm . '%'])
                             ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $searchTerm . '%'])
                             ->orWhereRaw('LOWER(content) LIKE ?', ['%' . $searchTerm . '%']);
                });
            })
            ->when($this->filterCategory, function($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterVisibility, function($query) {
                $query->where('visibility', $this->filterVisibility);
            })
            ->when($this->filterStatus, function($query) {
                $query->where('status', $this->filterStatus);
            })
            ->withCount(['likes', 'comments'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        // Get approved categories + author's own unapproved categories
        $categories = Category::withUnapproved()
            ->where(function ($query) {
                $query->where('is_approved', true)
                      ->orWhere('user_id', auth()->id());
            })
            ->orderBy('name')->get();

        return [
            'prompts' => $prompts,
            'categories' => $categories,
            'title' => 'My Prompts'
        ];
    }
}; ?>

<div class="space-y-6">
    <x-page-heading 
        title="My Prompts" 
        description="Manage your personal prompt collection"
    >
        <x-slot name="actions">
            <flux:button wire:navigate href="{{ route('user.prompts.create') }}" variant="primary" icon="plus">
                Create Prompt
            </flux:button>
        </x-slot>
    </x-page-heading>

    <!-- Search and Filters -->
    <x-admin-search-filters 
        search-placeholder="Search your prompts..."
        :filters="[
            [
                'model' => 'filterCategory',
                'placeholder' => 'All Categories',
                'items' => $categories,
                'label_field' => 'name',
                'value_field' => 'id'
            ],
            [
                'model' => 'filterVisibility',
                'placeholder' => 'All Visibility',
                'options' => [
                    'public' => 'Public',
                    'private' => 'Private',
                    'unlisted' => 'Unlisted'
                ]
            ],
            [
                'model' => 'filterStatus',
                'placeholder' => 'All Status',
                'options' => [
                    'draft' => 'Draft',
                    'published' => 'Published'
                ]
            ]
        ]"
    />

    {{-- <!-- Sorting Controls -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                {{ $prompts->total() }} {{ Str::plural('prompt', $prompts->total()) }}
            </flux:text>
        </div>
        <div class="flex items-center gap-2">
            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">Sort by:</flux:text>
            <flux:select wire:model.live="sortBy" class="min-w-32">
                <option value="created_at">Newest first</option>
                <option value="title">Title (A-Z)</option>
                <option value="updated_at">Recently updated</option>
                <option value="likes_count">Most liked</option>
                <option value="comments_count">Most comments</option>
            </flux:select>
            <flux:button 
                wire:click="sortBy('{{ $sortBy }}')" 
                variant="ghost" 
                size="sm"
                title="Change sort direction"
                class="p-2"
            >
                <flux:icon 
                    name="{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" 
                    class="size-4 text-zinc-400 hover:text-zinc-600" 
                />
            </flux:button>
        </div>
    </div> --}}

    <!-- Prompts Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($prompts as $prompt)
            <div class="group relative rounded-xl border border-zinc-200 bg-white p-6 transition-all hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
                <!-- Header -->
                <div class="mb-4 flex items-start justify-between">
                    <div class="flex-1">
                        <flux:heading size="sm" class="font-semibold">
                            <flux:link href="{{ route('prompts.show', $prompt) }}" wire:navigate variant="ghost" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ Str::limit($prompt->title, 30) }}
                            </flux:link>
                        </flux:heading>
                        <flux:text size="sm" class="mt-1 text-zinc-600 dark:text-zinc-400">
                            {{ Str::limit($prompt->content, 100) }}
                        </flux:text>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Publishing Status Icon -->
                        @if($prompt->status === 'published')
                            <flux:icon.check-circle class="size-4 text-green-500" title="Published" />
                        @else
                            <flux:icon.clock class="size-4 text-amber-500" title="Draft" />
                        @endif
                        
                        <!-- Visibility Icon -->
                        @if($prompt->visibility === 'public')
                            <flux:icon.eye class="size-4 text-green-500" title="Public" />
                        @elseif($prompt->visibility === 'unlisted')
                            <flux:icon.eye class="size-4 text-zinc-400" title="Unlisted" />
                        @else
                            <flux:icon.eye-slash class="size-4 text-zinc-400" title="Private" />
                        @endif
                    </div>
                </div>

                <!-- Metadata -->
                <div class="mb-4 flex flex-wrap items-center gap-4 text-sm text-zinc-600 dark:text-zinc-400">
                    <div class="flex items-center space-x-1">
                        <flux:icon.folder class="size-3" />
                        <flux:text size="sm">{{ $prompt->category->name ?? 'Uncategorized' }}</flux:text>
                    </div>
                    @if($prompt->aiModels->count() > 0)
                    <div class="flex items-center space-x-1">
                        <flux:icon.cpu-chip class="size-3" />
                        <flux:text size="sm">{{ $prompt->aiModels->first()->name }}</flux:text>
                    </div>
                    @endif
                    @if($prompt->platforms->count() > 0)
                    <div class="flex items-center space-x-1">
                        <flux:icon.device-tablet class="size-3" />
                        <flux:text size="sm">{{ $prompt->platforms->first()->name }}</flux:text>
                    </div>
                    @endif
                </div>

                <!-- Tags -->
                @if($prompt->tags->count() > 0)
                    <div class="mb-4 flex flex-wrap gap-1">
                        @foreach($prompt->tags->take(3) as $tag)
                            <flux:badge variant="subtle" size="sm" class="bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                                {{ $tag->name }}
                            </flux:badge>
                        @endforeach
                        @if($prompt->tags->count() > 3)
                            <flux:badge variant="subtle" size="sm" class="bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                                +{{ $prompt->tags->count() - 3 }}
                            </flux:badge>
                        @endif
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <div class="text-xs text-zinc-500 dark:text-zinc-500">
                        {{ $prompt->created_at->diffForHumans() }}
                    </div>
                    <div class="flex items-center space-x-1">
                        <flux:button wire:navigate href="{{ route('user.prompts.edit', $prompt) }}" variant="ghost" size="sm" icon="pencil" />
                        <flux:button 
                            wire:click="delete({{ $prompt->id }})"
                            wire:confirm="Are you sure you want to delete this prompt?"
                            variant="ghost" 
                            size="sm"
                            class="text-red-600 hover:text-red-500"
                            icon="trash"
                        />
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 lg:col-span-3">
                <div class="rounded-xl border-2 border-dashed border-zinc-200 p-12 text-center dark:border-zinc-700">
                    <flux:icon.chat-bubble-left-right class="mx-auto size-16 text-zinc-400" />
                    <h3 class="mt-4 text-lg font-medium text-zinc-900 dark:text-white">No prompts found</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        @if($search || $filterCategory || $filterVisibility || $filterStatus)
                            Try adjusting your search or filters to find what you're looking for.
                        @else
                            Get started by creating your first prompt.
                        @endif
                    </p>
                    <div class="mt-6">
                        @if($search || $filterCategory || $filterVisibility || $filterStatus)
                            <flux:button wire:click="clearFilters" variant="ghost">
                                Clear Filters
                            </flux:button>
                        @else
                            <flux:button wire:navigate href="{{ route('user.prompts.create') }}" variant="primary" icon="plus">
                                Create Your First Prompt
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($prompts->hasPages())
        <div class="flex justify-center">
            {{ $prompts->links() }}
        </div>
    @endif
</div>
