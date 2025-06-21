<?php

use App\Models\AiModel;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Provider;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Model;

new
#[Layout('components.layouts.app')]
class extends Component {
    public function approve(string $model, int $id): void
    {
        $this->getModel($model, $id)->update(['is_approved' => true]);
        session()->flash('success', 'Item approved successfully.');
    }

    public function deny(string $model, int $id): void
    {
        $this->getModel($model, $id)->delete();
        session()->flash('success', 'Item denied and removed successfully.');
    }

    private function getModel(string $model, int $id): Model
    {
        // Admin needs to access unapproved items for approval/denial
        return match ($model) {
            'Category' => Category::withUnapproved()->findOrFail($id),
            'AiModel' => AiModel::withUnapproved()->findOrFail($id),
            'Platform' => Platform::withUnapproved()->findOrFail($id),
            'Provider' => Provider::withUnapproved()->findOrFail($id),
            default => abort(404),
        };
    }

    // Number of items per page for pagination
    public int $perPage = 10;

    // Current page for each section
    public int $categoryPage = 1;
    public int $aiModelPage = 1;
    public int $platformPage = 1;
    public int $providerPage = 1;

    // Computed properties for paginated results
    public function getCategoriesProperty()
    {
        // Admin sees only unapproved categories for the approval queue
        return Category::unapproved()
            ->with('user')
            ->latest()
            ->paginate($this->perPage, ['*'], 'categoryPage');
    }

    public function getAiModelsProperty()
    {
        // Admin sees only unapproved AI models for the approval queue
        return AiModel::unapproved()
            ->with('user')
            ->latest()
            ->paginate($this->perPage, ['*'], 'aiModelPage');
    }

    public function getPlatformsProperty()
    {
        // Admin sees only unapproved platforms for the approval queue
        return Platform::unapproved()
            ->with('user')
            ->latest()
            ->paginate($this->perPage, ['*'], 'platformPage');
    }

    public function getProvidersProperty()
    {
        // Admin sees only unapproved providers for the approval queue
        return Provider::unapproved()
            ->with('user')
            ->latest()
            ->paginate($this->perPage, ['*'], 'providerPage');
    }

    public function with(): array
    {
        return [
            'categories' => $this->categories,
            'aiModels' => $this->aiModels,
            'platforms' => $this->platforms,
            'providers' => $this->providers,
            'title' => 'Approval Queue'
        ];
    }
}; ?>

<div class="space-y-8">
    <x-page-heading 
        title="Approval Queue" 
        description="Approve or deny user-suggested categories, AI models, and platforms."
    />

    <!-- Unapproved Categories -->
    <div>
        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Unapproved Categories</h3>
        <x-admin-table
            :items="$categories"
            empty-icon="tag"
            empty-title="No categories to approve"
            empty-description="There are no user-suggested categories waiting for approval."
        >
            <x-slot name="header">
                <tr>
                    <x-admin-table-header>Name</x-admin-table-header>
                    <x-admin-table-header>Suggested by</x-admin-table-header>
                    <x-admin-table-header>Created</x-admin-table-header>
                    <x-admin-table-header class="text-right">Actions</x-admin-table-header>
                </tr>
            </x-slot>

            @foreach($categories as $item)
                <x-admin-table-row>
                    <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $item->name }}</td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->created_at->format('M j, Y') }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <flux:button wire:click="approve('Category', {{ $item->id }})" variant="primary" size="sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Approve</span>
                            <span wire:loading>Processing...</span>
                        </flux:button>
                        <flux:button wire:click="deny('Category', {{ $item->id }})" variant="danger" size="sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Deny</span>
                            <span wire:loading>Processing...</span>
                        </flux:button>
                    </td>
                </x-admin-table-row>
            @endforeach
        </x-admin-table>
    </div>

    <!-- Unapproved AI Models -->
    <div>
        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Unapproved AI Models</h3>
        <x-admin-table
            :items="$aiModels"
            empty-icon="cpu-chip"
            empty-title="No AI models to approve"
            empty-description="There are no user-suggested AI models waiting for approval."
        >
            <x-slot name="header">
                <tr>
                    <x-admin-table-header>Name</x-admin-table-header>
                    <x-admin-table-header>Suggested by</x-admin-table-header>
                    <x-admin-table-header>Created</x-admin-table-header>
                    <x-admin-table-header class="text-right">Actions</x-admin-table-header>
                </tr>
            </x-slot>

            @foreach($aiModels as $item)
                <x-admin-table-row>
                    <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $item->name }}</td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->created_at->format('M j, Y') }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <flux:button wire:click="approve('AiModel', {{ $item->id }})" variant="primary" size="sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Approve</span>
                            <span wire:loading>Processing...</span>
                        </flux:button>
                        <flux:button wire:click="deny('AiModel', {{ $item->id }})" variant="danger" size="sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Deny</span>
                            <span wire:loading>Processing...</span>
                        </flux:button>
                    </td>
                </x-admin-table-row>
            @endforeach
        </x-admin-table>
    </div>

    <!-- Unapproved Platforms -->
    <div>
        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Unapproved Platforms</h3>
        <x-admin-table
            :items="$platforms"
            empty-icon="device-tablet"
            empty-title="No platforms to approve"
            empty-description="There are no user-suggested platforms waiting for approval."
        >
            <x-slot name="header">
                <tr>
                    <x-admin-table-header>Name</x-admin-table-header>
                    <x-admin-table-header>Suggested by</x-admin-table-header>
                    <x-admin-table-header>Created</x-admin-table-header>
                    <x-admin-table-header class="text-right">Actions</x-admin-table-header>
                </tr>
            </x-slot>

            @foreach($platforms as $item)
                <x-admin-table-row>
                    <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $item->name }}</td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->created_at->format('M j, Y') }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <flux:button wire:click="approve('Platform', {{ $item->id }})" variant="primary" size="sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Approve</span>
                            <span wire:loading>Processing...</span>
                        </flux:button>
                        <flux:button wire:click="deny('Platform', {{ $item->id }})" variant="danger" size="sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Deny</span>
                            <span wire:loading>Processing...</span>
                        </flux:button>
                    </td>
                </x-admin-table-row>
            @endforeach
        </x-admin-table>
    </div>

    <!-- Unapproved Providers -->
    <div>
        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-4">Unapproved Providers</h3>
        <x-admin-table
            :items="$providers"
            empty-icon="building-office"
            empty-title="No providers to approve"
            empty-description="There are no user-suggested providers waiting for approval."
        >
            <x-slot name="header">
                <tr>
                    <x-admin-table-header>Name</x-admin-table-header>
                    <x-admin-table-header>Website</x-admin-table-header>
                    <x-admin-table-header>Suggested by</x-admin-table-header>
                    <x-admin-table-header>Created</x-admin-table-header>
                    <x-admin-table-header class="text-right">Actions</x-admin-table-header>
                </tr>
            </x-slot>


            @foreach($providers as $item)
                <x-admin-table-row>
                    <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                        <div class="flex items-center space-x-3">
                            @if($item->logo)
                                <img src="{{ $item->logo }}" alt="{{ $item->name }} logo" class="h-8 w-8 rounded-full">
                            @endif
                            <span>{{ $item->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                        @if($item->website)
                            <a href="{{ $item->website }}" target="_blank" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                {{ parse_url($item->website, PHP_URL_HOST) }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->user->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $item->created_at->format('M j, Y') }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <flux:button wire:click="approve('Provider', {{ $item->id }})" variant="primary" size="sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Approve</span>
                            <span wire:loading>Processing...</span>
                        </flux:button>
                        <flux:button wire:click="deny('Provider', {{ $item->id }})" variant="danger" size="sm" wire:loading.attr="disabled">
                            <span wire:loading.remove>Deny</span>
                            <span wire:loading>Processing...</span>
                        </flux:button>
                    </td>
                </x-admin-table-row>
            @endforeach
        </x-admin-table>
    </div>
</div>
