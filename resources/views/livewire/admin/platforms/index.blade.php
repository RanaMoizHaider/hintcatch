<?php

use App\Models\Platform;
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

    public function sort($field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function delete($id): void
    {
        $platform = Platform::find($id);
        if ($platform && $platform->prompts()->count() === 0) {
            $platform->delete();
            session()->flash('success', 'Platform deleted successfully.');
        } else {
            session()->flash('error', 'Cannot delete platform with associated prompts.');
        }
    }

    public function with(): array
    {
        $platforms = Platform::query()
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withCount('prompts')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return [
            'platforms' => $platforms,
            'title' => 'Platforms Management'
        ];
    }
}; ?>

<div class="space-y-6">
    <x-page-heading 
        title="Platforms" 
        description="Manage AI platforms and their information"
    >
        <x-slot name="actions">
            <flux:button href="{{ route('admin.platforms.create') }}" variant="primary" icon="plus" wire:navigate>
                Add Platform
            </flux:button>
        </x-slot>
    </x-page-heading>

    <!-- Search and Filters -->
    <x-admin-search-filters 
        search-placeholder="Search platforms..."
    />

    <!-- Platforms Table -->
    <x-admin-table 
        :items="$platforms"
        empty-icon="device-tablet"
        empty-title="No platforms"
        empty-description="Get started by creating a new platform."
        :create-route="route('admin.platforms.create')"
        create-text="Add Platform"
    >
        <x-slot name="header">
            <tr>
                <x-admin-table-header sortable field="name" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    Name
                </x-admin-table-header>
                <x-admin-table-header>
                    Description
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

        @foreach($platforms as $platform)
            <x-admin-table-row>
                <td class="px-6 py-4">
                    <div class="font-medium text-zinc-900 dark:text-white">{{ $platform->name }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ Str::limit($platform->description, 100) }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <flux:badge variant="outline">{{ $platform->prompts_count }}</flux:badge>
                </td>
                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ $platform->created_at->format('M j, Y') }}
                </td>
                <x-admin-table-actions 
                    :edit-route="route('admin.platforms.edit', $platform)"
                    :delete-action="'delete(' . $platform->id . ')'"
                    delete-confirm="Are you sure you want to delete this platform?"
                />
            </x-admin-table-row>
        @endforeach
    </x-admin-table>
</div>
