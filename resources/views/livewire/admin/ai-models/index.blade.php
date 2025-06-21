<?php

use App\Models\AiModel;
use App\Models\Provider;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[Layout('components.layouts.app')]
class extends Component {
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $filterProvider = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterProvider()
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

    public function delete($id)
    {
        // Admin can delete any AI model including unapproved ones
        $aiModel = AiModel::withUnapproved()->find($id);
        if ($aiModel && $aiModel->prompts()->count() === 0) {
            $aiModel->delete();
            session()->flash('success', 'AI Model deleted successfully.');
        } else {
            session()->flash('error', 'Cannot delete AI model with associated prompts.');
        }
    }

    public function with()
    {
        // Admin sees all AI models including unapproved
        $aiModels = AiModel::withUnapproved()
            ->with('provider')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterProvider, function($query) {
                $query->where('provider_id', $this->filterProvider);
            })
            ->withCount('prompts')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        // Admin sees all providers including unapproved
        $providers = Provider::withUnapproved()->orderBy('name')->get();

        return [
            'title' => 'AI Models',
            'aiModels' => $aiModels,
            'providers' => $providers,
        ];
    }
}; ?>

<div>
    <x-page-heading 
        title="AI Models" 
        description="Manage AI models and their configurations"
    >
        <x-slot name="actions">
            <flux:button wire:navigate href="{{ route('admin.ai-models.create') }}" variant="primary">
                <flux:icon.plus class="size-4" />
                Add Model
            </flux:button>
        </x-slot>
    </x-page-heading>

    <!-- Search and Filters -->
    <x-admin-search-filters 
        search-placeholder="Search models..."
        :filters="[
            [
                'model' => 'filterProvider',
                'placeholder' => 'All Providers',
                'items' => $providers,
                'label_field' => 'name',
                'value_field' => 'id'
            ]
        ]"
    />

    <!-- Results -->
    <x-admin-table 
        :items="$aiModels"
        empty-icon="computer-desktop"
        empty-title="No AI Models Found"
        :empty-description="($search || $filterProvider) ? 'No models match your current filters.' : 'Get started by adding your first AI model.'"
        :create-route="!$search && !$filterProvider ? route('admin.ai-models.create') : null"
        create-text="Add AI Model"
    >
        <x-slot name="header">
            <tr>
                <x-admin-table-header sortable field="name" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    Name
                </x-admin-table-header>
                <x-admin-table-header>
                    Provider
                </x-admin-table-header>
                <x-admin-table-header>
                    Description
                </x-admin-table-header>
                <x-admin-table-header sortable field="prompts_count" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    Prompts
                </x-admin-table-header>
                <x-admin-table-header sortable field="is_active" :sort-by="$sortBy" :sort-direction="$sortDirection">
                    Status
                </x-admin-table-header>
                <x-admin-table-header class="text-right">
                    Actions
                </x-admin-table-header>
            </tr>
        </x-slot>

        @foreach($aiModels as $model)
            <x-admin-table-row>
                <td class="px-6 py-4">
                    <div class="font-medium text-zinc-900 dark:text-white">{{ $model->name }}</div>
                </td>
                <td class="px-6 py-4">
                    <flux:badge variant="subtle" class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $model->provider->name }}</flux:badge>
                </td>
                <td class="px-6 py-4">
                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">
                        {{ Str::limit($model->description, 100) }}
                    </flux:text>
                </td>
                <td class="px-6 py-4">
                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $model->prompts_count }}</flux:text>
                </td>
                <td class="px-6 py-4">
                    @if($model->is_active)
                        <flux:badge variant="subtle" class="bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Active</flux:badge>
                    @else
                        <flux:badge variant="subtle" class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">Inactive</flux:badge>
                    @endif
                </td>
                <x-admin-table-actions 
                    :edit-route="route('admin.ai-models.edit', $model)"
                    :delete-action="$model->prompts_count === 0 ? 'delete(' . $model->id . ')' : null"
                    delete-confirm="Are you sure you want to delete this AI model?"
                    :can-delete="$model->prompts_count === 0"
                />
            </x-admin-table-row>
        @endforeach
    </x-admin-table>
</div>
