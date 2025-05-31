<?php

use App\Models\Prompt;
use App\Models\Category;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[Layout('components.layouts.app')]
class extends Component {
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $filterCategory = '';
    public $filterUser = '';
    public $filterStatus = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function updatingFilterUser()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updateStatus($id, $status)
    {
        $prompt = Prompt::find($id);
        if ($prompt) {
            $prompt->update(['status' => $status]);
            session()->flash('success', 'Prompt status updated successfully.');
        }
    }

    public function updateVisibility($id, $visibility)
    {
        $prompt = Prompt::find($id);
        if ($prompt) {
            $prompt->update(['visibility' => $visibility]);
            session()->flash('success', 'Prompt visibility updated successfully.');
        }
    }

    public function delete($id)
    {
        $prompt = Prompt::find($id);
        if ($prompt) {
            $prompt->delete();
            session()->flash('success', 'Prompt deleted successfully.');
        }
    }

    public function with()
    {
        $prompts = Prompt::query()
            ->with(['user', 'category'])
            ->when($this->search, function($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterCategory, function($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterUser, function($query) {
                $query->where('user_id', $this->filterUser);
            })
            ->when($this->filterStatus, function($query) {
                $query->where('status', $this->filterStatus);
            })
            ->withCount(['likes', 'comments'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        $categories = Category::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return [
            'title' => 'Prompts',
            'prompts' => $prompts,
            'categories' => $categories,
            'users' => $users,
        ];
    }
}; ?>

<div>
    <x-page-heading 
        title="Prompts" 
        description="Manage user prompts and their visibility"
    >
        <x-slot name="actions">
            <flux:button wire:navigate href="{{ route('admin.prompts.create') }}" variant="primary" icon="plus">
                Add Prompt
            </flux:button>
        </x-slot>
    </x-page-heading>

    <!-- Search and Filters -->
    <x-admin-search-filters 
        search-placeholder="Search prompts..."
        :filters="[
            [
                'model' => 'filterCategory',
                'placeholder' => 'All Categories',
                'items' => $categories,
                'label_field' => 'name',
                'value_field' => 'id'
            ],
            [
                'model' => 'filterUser',
                'placeholder' => 'All Users',
                'items' => $users,
                'label_field' => 'name',
                'value_field' => 'id'
            ],
            [
                'model' => 'filterStatus',
                'placeholder' => 'All Status',
                'options' => [
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived'
                ]
            ]
        ]"
    />

    <!-- Results -->
    <x-admin-table 
        :items="$prompts"
        empty-icon="document-text"
        empty-title="No prompts found"
        :empty-description="($search || $filterCategory || $filterUser || $filterStatus) ? 'No prompts match your current filters.' : 'Get started by creating a new prompt.'"
        :create-route="(!$search && !$filterCategory && !$filterUser && !$filterStatus) ? route('admin.prompts.create') : null"
        create-text="Add Prompt"
    >
        <x-slot name="header">
            <tr>
                <x-admin-table-header :sortBy="$sortBy" :sortDirection="$sortDirection" field="title" :sortable="true">
                    Title
                </x-admin-table-header>
                
                <x-admin-table-header :sortBy="$sortBy" :sortDirection="$sortDirection" field="user_id" :sortable="true">
                    Author
                </x-admin-table-header>
                
                <x-admin-table-header>
                    Category
                </x-admin-table-header>
                
                <x-admin-table-header :sortBy="$sortBy" :sortDirection="$sortDirection" field="likes_count" :sortable="true">
                    Likes
                </x-admin-table-header>
                
                <x-admin-table-header>
                    Status
                </x-admin-table-header>
                
                <x-admin-table-header>
                    Visibility
                </x-admin-table-header>
                
                <x-admin-table-header class="text-right">
                    Actions
                </x-admin-table-header>
            </tr>
        </x-slot>

        @foreach($prompts as $prompt)
            <x-admin-table-row>
                <td class="px-6 py-4">
                    <div>
                        <div class="font-medium text-zinc-900 dark:text-white">{{ $prompt->title }}</div>
                        <flux:text size="sm" class="text-zinc-500">{{ $prompt->created_at->format('M d, Y') }}</flux:text>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $prompt->user->name }}</flux:text>
                </td>
                <td class="px-6 py-4">
                    @if($prompt->category)
                        <flux:badge variant="subtle" class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $prompt->category->name }}</flux:badge>
                    @else
                        <span class="text-zinc-400">No category</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-1 text-sm text-zinc-600 dark:text-zinc-400">
                        <flux:icon.heart class="size-4" />
                        <span>{{ $prompt->likes_count }}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <flux:select wire:change="updateStatus({{ $prompt->id }}, $event.target.value)" size="sm">
                        <option value="draft" {{ $prompt->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $prompt->status === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ $prompt->status === 'archived' ? 'selected' : '' }}>Archived</option>
                    </flux:select>
                </td>
                <td class="px-6 py-4">
                    <flux:select wire:change="updateVisibility({{ $prompt->id }}, $event.target.value)" size="sm">
                        <option value="public" {{ $prompt->visibility === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ $prompt->visibility === 'private' ? 'selected' : '' }}>Private</option>
                    </flux:select>
                </td>
                <x-admin-table-actions 
                    :edit-route="route('admin.prompts.edit', $prompt)"
                    :delete-action="'delete(' . $prompt->id . ')'"
                    delete-confirm="Are you sure you want to delete this prompt?"
                />
            </x-admin-table-row>
        @endforeach
    </x-admin-table>
</div>
