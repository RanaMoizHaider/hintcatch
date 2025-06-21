<?php

use App\Models\Provider;
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
        // Admin can delete any provider including unapproved ones
        $provider = Provider::withUnapproved()->find($id);
        if ($provider && $provider->aiModels()->count() === 0) {
            $provider->delete();
            session()->flash('success', 'Provider deleted successfully.');
        } else {
            session()->flash('error', 'Cannot delete provider with associated AI models.');
        }
    }

    public function with(): array
    {
        // Admin sees all providers including unapproved
        $providers = Provider::withUnapproved()
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withCount('aiModels')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return [
            'providers' => $providers,
            'title' => 'Providers Management'
        ];
    }
}; ?>

<div class="space-y-6">
    <x-page-heading 
        title="Providers" 
        description="Manage AI providers and their information"
    >
        <x-slot name="actions">
            <flux:button href="{{ route('admin.providers.create') }}" variant="primary" icon="plus" wire:navigate>
                Add Provider
            </flux:button>
        </x-slot>
    </x-page-heading>

    <!-- Search and Filters -->
    <x-admin-search-filters 
        search-placeholder="Search providers..."
    />

    <!-- Providers Table -->
    <x-admin-table 
        :items="$providers"
        empty-icon="building-office"
        empty-title="No providers"
        empty-description="Get started by creating a new provider."
        :create-route="route('admin.providers.create')"
        create-text="Add Provider"
    >
        <x-slot name="header">
            <tr>
                <x-admin-table-header sortable field="name" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    Name
                </x-admin-table-header>
                <x-admin-table-header>
                    Description
                </x-admin-table-header>
                <x-admin-table-header sortable field="ai_models_count" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    AI Models
                </x-admin-table-header>
                <x-admin-table-header sortable field="created_at" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    Created
                </x-admin-table-header>
                <x-admin-table-header class="text-right">
                    Actions
                </x-admin-table-header>
            </tr>
        </x-slot>

        @foreach($providers as $provider)
            <x-admin-table-row>
                <td class="px-6 py-4">
                    <div class="font-medium text-zinc-900 dark:text-white">{{ $provider->name }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        {{ Str::limit($provider->description, 100) }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <flux:badge variant="outline">{{ $provider->ai_models_count }}</flux:badge>
                </td>
                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ $provider->created_at->format('M j, Y') }}
                </td>
                <x-admin-table-actions 
                    :edit-route="route('admin.providers.edit', $provider)"
                    :delete-action="'delete(' . $provider->id . ')'"
                    delete-confirm="Are you sure you want to delete this provider?"
                />
            </x-admin-table-row>
        @endforeach
    </x-admin-table>
</div>
