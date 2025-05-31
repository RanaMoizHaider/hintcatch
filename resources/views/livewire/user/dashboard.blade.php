<?php

use App\Models\Prompt;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.app')]
class extends Component {
    public $totalPrompts;
    public $publicPrompts;
    public $favoritePrompts;
    public $categoriesUsed;
    public $recentPrompts;
    public $topCategories;
    public $topAiModels;

    public function mount(): void
    {
        $user = auth()->user();
        
        $this->totalPrompts = $user->prompts()->count();
        $this->publicPrompts = $user->prompts()->where('visibility', 'public')->where('status', 'published')->count();
        // For favorites, we'll need to use the likes relationship or create a separate favorites feature
        $this->favoritePrompts = $user->prompts()->whereHas('likes', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        $this->categoriesUsed = $user->prompts()->whereNotNull('category_id')->distinct('category_id')->count('category_id');
        
        $this->recentPrompts = $user->prompts()
            ->with(['category', 'aiModels', 'platforms'])
            ->latest()
            ->take(5)
            ->get();
            
        $this->topCategories = $user->prompts()
            ->select('category_id')
            ->selectRaw('count(*) as prompts_count')
            ->with('category')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->orderByDesc('prompts_count')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->category->name ?? 'Uncategorized',
                    'prompts_count' => $item->prompts_count
                ];
            });
            
        // Since AI models have many-to-many relationship, we need to count differently
        $this->topAiModels = $user->prompts()
            ->with('aiModels')
            ->get()
            ->flatMap(function ($prompt) {
                return $prompt->aiModels;
            })
            ->groupBy('id')
            ->map(function ($models) {
                $model = $models->first();
                return (object) [
                    'name' => $model->name,
                    'prompts_count' => $models->count()
                ];
            })
            ->sortByDesc('prompts_count')
            ->take(5)
            ->values();
    }

    public function with(): array
    {
        return [
            'title' => 'Dashboard'
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
<x-page-heading 
    title="Welcome back, {{ auth()->user()->name }}!" 
    description="Here's what's happening with your prompts"
>
    <x-slot name="actions">
        <flux:button wire:navigate href="{{ route('user.prompts.create') }}" variant="primary" icon="plus">
            Create Prompt
        </flux:button>
    </x-slot>
</x-page-heading>

<!-- Statistics Cards -->
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
            <div>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Total Prompts</flux:text>
                <flux:heading size="2xl" class="mt-1">{{ $totalPrompts }}</flux:heading>
            </div>
            <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/20">
                <flux:icon.chat-bubble-left-right class="size-6 text-blue-600 dark:text-blue-400" />
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
            <div>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Public Prompts</flux:text>
                <flux:heading size="2xl" class="mt-1">{{ $publicPrompts }}</flux:heading>
            </div>
            <div class="rounded-lg bg-green-100 p-3 dark:bg-green-900/20">
                <flux:icon.eye class="size-6 text-green-600 dark:text-green-400" />
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
            <div>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Favorite Prompts</flux:text>
                <flux:heading size="2xl" class="mt-1">{{ $favoritePrompts }}</flux:heading>
            </div>
            <div class="rounded-lg bg-yellow-100 p-3 dark:bg-yellow-900/20">
                <flux:icon.heart class="size-6 text-yellow-600 dark:text-yellow-400" />
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between">
            <div>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Categories Used</flux:text>
                <flux:heading size="2xl" class="mt-1">{{ $categoriesUsed }}</flux:heading>
            </div>
            <div class="rounded-lg bg-purple-100 p-3 dark:bg-purple-900/20">
                <flux:icon.folder class="size-6 text-purple-600 dark:text-purple-400" />
            </div>
        </div>
    </div>
</div>

<!-- Recent Prompts -->
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Recent Prompts -->
    <div class="lg:col-span-2">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <flux:heading size="lg">Recent Prompts</flux:heading>
                <flux:button wire:navigate href="{{ route('user.prompts.index') }}" variant="ghost" size="sm">
                    View All
                </flux:button>
            </div>
            <div class="space-y-4">
                @forelse($recentPrompts as $prompt)
                    <div class="flex items-start justify-between rounded-lg border border-zinc-100 p-4 dark:border-zinc-700">
                        <div class="flex-1">
                            <flux:heading size="sm" class="font-medium">{{ Str::limit($prompt->title, 50) }}</flux:heading>
                            <flux:text size="sm" class="mt-1 text-zinc-600 dark:text-zinc-400">{{ Str::limit($prompt->content, 100) }}</flux:text>
                            <div class="mt-2 flex items-center space-x-4 text-xs text-zinc-500 dark:text-zinc-500">
                                <flux:text size="xs">{{ $prompt->category->name ?? 'Uncategorized' }}</flux:text>
                                <flux:text size="xs">{{ $prompt->aiModels->first()->name ?? 'No AI Model' }}</flux:text>
                                <flux:text size="xs">{{ $prompt->created_at->diffForHumans() }}</flux:text>
                            </div>
                        </div>
                        <div class="ml-4 flex items-center space-x-2">
                            @if($prompt->visibility === 'public' && $prompt->status === 'published')
                                <flux:badge variant="subtle" class="bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400">Public</flux:badge>
                            @else
                                <flux:badge variant="subtle" class="bg-zinc-50 text-zinc-700 dark:bg-zinc-900/20 dark:text-zinc-400">Private</flux:badge>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border-2 border-dashed border-zinc-200 p-8 text-center dark:border-zinc-700">
                        <flux:icon.chat-bubble-left-right class="mx-auto size-12 text-zinc-400" />
                        <flux:heading size="lg" class="mt-2">No prompts yet</flux:heading>
                        <flux:text size="sm" class="mt-1 text-zinc-500 dark:text-zinc-400">Get started by creating your first prompt.</flux:text>
                        <div class="mt-6">
                            <flux:button wire:navigate href="{{ route('user.prompts.create') }}" variant="primary" size="sm" icon="plus">
                                Create Prompt
                            </flux:button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="space-y-6">
        <!-- Popular Categories -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Your Top Categories</flux:heading>
            <div class="space-y-3">
                @forelse($topCategories as $category)
                    <div class="flex items-center justify-between">
                        <flux:text size="sm" class="font-medium">{{ $category->name }}</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $category->prompts_count }}</flux:text>
                    </div>
                @empty
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">No categories used yet.</flux:text>
                @endforelse
            </div>
        </div>

        <!-- Popular AI Models -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Your Top AI Models</flux:heading>
            <div class="space-y-3">
                @forelse($topAiModels as $aiModel)
                    <div class="flex items-center justify-between">
                        <flux:text size="sm" class="font-medium">{{ $aiModel->name }}</flux:text>
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $aiModel->prompts_count }}</flux:text>
                    </div>
                @empty
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">No AI models used yet.</flux:text>
                @endforelse
            </div>
        </div>
    </div>
</div>
    </div>
</div>
