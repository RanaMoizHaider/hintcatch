<?php

use App\Models\Platform;
use App\Models\Prompt;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[Layout('components.layouts.web')]
class extends Component {
    use WithPagination;

    public Platform $platform;
    public $activeTab = 'trending';
    public $viewMode = 'grid';

    public function mount(Platform $platform)
    {
        $this->platform = $platform;
    }

    public function getPromptsProperty()
    {
        $query = $this->platform->prompts()
            ->with(['user', 'category', 'platforms', 'aiModels'])
            ->withCount(['likes', 'comments'])
            ->withViewsCount();

        return match($this->activeTab) {
            'newest' => $query->latest()->paginate(12),
            'trending' => $query->orderByDesc('views_count')->paginate(12),
            default => $query->orderByDesc('likes_count')->paginate(12),
        };
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }
}; ?>

<div>
    <x-slot name="title">{{ $platform->name }} - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('platforms.index') }}">Platforms</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $platform->name }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </nav>

        <!-- Platform Header -->
        <section class="mb-8">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $platform->name }}</h1>
                    @if($platform->description)
                        <p class="text-zinc-600 dark:text-zinc-400 max-w-3xl mb-4">{{ $platform->description }}</p>
                    @endif
                </div>
                <div class="text-right shrink-0 ml-4">
                    <div class="text-2xl font-bold text-zinc-800 dark:text-zinc-200">{{ $platform->prompts()->count() }}</div>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ Str::plural('prompt', $platform->prompts()->count()) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if($platform->features && is_array($platform->features))
                    <div>
                        <h2 class="text-lg font-medium mb-3 text-zinc-900 dark:text-zinc-100">Key Features</h2>
                        <div class="space-y-2">
                            @foreach($platform->features as $feature)
                                <div class="flex items-start">
                                    <flux:icon.check class="size-4 text-zinc-500 mr-2 mt-0.5 shrink-0" />
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($platform->best_practices && is_array($platform->best_practices))
                    <div>
                        <h2 class="text-lg font-medium mb-3 text-zinc-900 dark:text-zinc-100">Best Practices for Prompts</h2>
                        <div class="space-y-2">
                            @foreach($platform->best_practices as $practice)
                                <div class="flex items-start">
                                    <flux:icon.check class="size-4 text-green-500 mr-2 mt-0.5 shrink-0" />
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $practice }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Prompts Section -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Prompts for {{ $platform->name }}</h2>
            </div>

            <!-- Tab Navigation -->
            <div class="flex items-center justify-between mb-6">
                <flux:button.group>
                    <flux:button 
                        wire:click="setActiveTab('trending')"
                        variant="{{ $activeTab === 'trending' ? 'filled' : 'subtle' }}"
                        size="sm"
                    >
                        Trending
                    </flux:button>
                    <flux:button 
                        wire:click="setActiveTab('newest')"
                        variant="{{ $activeTab === 'newest' ? 'filled' : 'subtle' }}"
                        size="sm"
                    >
                        Newest
                    </flux:button>
                    <flux:button 
                        wire:click="setActiveTab('popular')"
                        variant="{{ $activeTab === 'popular' ? 'filled' : 'subtle' }}"
                        size="sm"
                    >
                        Popular
                    </flux:button>
                </flux:button.group>
                <flux:button.group>
                    <flux:button 
                        wire:click="$set('viewMode', 'grid')"
                        variant="{{ $viewMode === 'grid' ? 'filled' : 'outline' }}"
                        icon="squares-2x2"
                        size="sm"
                        square
                    />
                    <flux:button 
                        wire:click="$set('viewMode', 'list')"
                        variant="{{ $viewMode === 'list' ? 'filled' : 'outline' }}"
                        icon="bars-3"
                        size="sm"
                        square
                    />
                </flux:button.group>
            </div>
            
            <!-- Prompts Grid/List -->
            @if($viewMode === 'grid')
                <x-card-grid :columns="3" wire:loading.class="opacity-50" class="grid-auto-rows-[1fr]">
                    @forelse($this->prompts as $prompt)
                        <livewire:components.prompt-card 
                            :prompt="$prompt"
                            :show-user="false"
                            :show-stats="true" 
                            :show-category="true"
                            :show-platforms="false"
                            :show-models="true"
                            :model-limit="2"
                            class="h-full"
                            wire:key="prompt-{{ $prompt->id }}"
                        />
                    @empty
                        <x-empty-state 
                            icon="document"
                            title="No prompts found"
                            description="No prompts are available for this platform yet."
                            class="col-span-full"
                        />
                    @endforelse
                </x-card-grid>
            @else
                <div class="space-y-4" wire:loading.class="opacity-50">
                    @forelse($this->prompts as $prompt)
                        <livewire:components.prompt-card 
                            :prompt="$prompt"
                            :show-user="false"
                            :show-stats="true" 
                            :show-category="true"
                            :show-platforms="false"
                            :show-models="true"
                            :model-limit="2"
                            :layout="'list'"
                            wire:key="prompt-list-{{ $prompt->id }}"
                        />
                    @empty
                        <x-empty-state 
                            icon="document"
                            title="No prompts found"
                            description="No prompts are available for this platform yet."
                        />
                    @endforelse
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-8">
                {{ $this->prompts->links() }}
            </div>
        </section>
    </div>
</div>
