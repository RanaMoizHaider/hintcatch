<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[Layout('components.layouts.app')]
class extends Component {
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(int $id): void
    {
        // Admin can delete any category including unapproved ones
        $category = Category::withUnapproved()->find($id);
        if ($category && $category->prompts()->count() === 0) {
            $category->delete();
            session()->flash('success', 'Category deleted successfully.');
        } else {
            session()->flash('error', 'Cannot delete category with associated prompts.');
        }
    }

    public function with(): array
    {
        // Admin sees all categories including unapproved
        $categories = Category::withUnapproved()
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withCount('prompts')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return [
            'categories' => $categories,
            'title' => 'Categories Management'
        ];
    }
}; ?>

<div class="space-y-6">
    <x-page-heading 
    title="Categories" 
    description="Manage prompt categories"
>
    <x-slot name="actions">
        <flux:button wire:navigate href="{{ route('admin.categories.create') }}" variant="primary">
            <flux:icon.plus class="size-4" />
            Create Category
        </flux:button>
    </x-slot>
</x-page-heading>

    <!-- Search and Filters -->
    <x-admin-search-filters 
        search-placeholder="Search categories..."
    />

    <!-- Categories Table -->
    <x-admin-table 
        :items="$categories"
        empty-icon="folder"
        empty-title="No categories found"
        empty-description="Get started by creating a new category."
        :create-route="route('admin.categories.create')"
        create-text="Create Category"
    >
        <x-slot name="header">
            <tr>
                <x-admin-table-header sortable field="name" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    Name
                </x-admin-table-header>
                <x-admin-table-header>
                    Description
                </x-admin-table-header>
                <x-admin-table-header>
                    Status
                </x-admin-table-header>
                <x-admin-table-header sortable field="prompts_count" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    Prompts
                </x-admin-table-header>
                <x-admin-table-header sortable field="created_at" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    Created
                </x-admin-table-header>
                <x-admin-table-header class="text-right">
                    Actions
                </x-admin-table-header>
            </tr>
        </x-slot>

        @foreach($categories as $category)
            <x-admin-table-row>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 size-8">
                            <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-700">
                                <flux:icon.folder class="size-4 text-zinc-500" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <flux:text class="font-medium">{{ $category->name }}</flux:text>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $category->slug }}</flux:text>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <flux:text size="sm">{{ Str::limit($category->description, 50) ?? 'No description' }}</flux:text>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($category->is_active ?? true)
                        <flux:badge color="green">Active</flux:badge>
                    @else
                        <flux:badge color="red">Inactive</flux:badge>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <flux:text size="sm">{{ $category->prompts_count }}</flux:text>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $category->created_at->format('M j, Y') }}</flux:text>
                </td>
                <x-admin-table-actions 
                    :edit-route="route('admin.categories.edit', $category)"
                    :delete-action="$category->prompts_count == 0 ? 'delete(' . $category->id . ')' : null"
                    delete-confirm="Are you sure you want to delete this category?"
                    :can-delete="$category->prompts_count == 0"
                />
            </x-admin-table-row>
        @endforeach
    </x-admin-table>
</div>
