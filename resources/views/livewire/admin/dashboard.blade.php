<?php

use App\Models\{AiModel, Category, Platform, Prompt, Provider, User};
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public $totalPrompts;
    public $totalUsers;
    public $publicPrompts;
    public $activeAiModels;
    public $recentPrompts;
    public $recentUsers;

    public function mount(): void
    {
        $this->totalPrompts = Prompt::count();
        $this->totalUsers = User::count();
        $this->publicPrompts = Prompt::where('visibility', 'public')->where('status', 'published')->count();
        $this->activeAiModels = AiModel::count();
        
        $this->recentPrompts = Prompt::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();
            
        $this->recentUsers = User::withCount('prompts')
            ->latest()
            ->take(5)
            ->get();
    }

    public function with(): array
    {
        return [
            'title' => 'Admin Dashboard'
        ];
    }
}; ?>

<div class="space-y-6">
    <x-page-heading 
        title="Admin Dashboard" 
        description="Manage your platform's content and settings" 
    />

    <!-- Statistics Cards -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Total Prompts</flux:text>
                    <flux:heading size="xl" class="mt-1">{{ $totalPrompts }}</flux:heading>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                    <flux:icon.chat-bubble-left-right class="size-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Total Users</flux:text>
                    <flux:heading size="xl" class="mt-1">{{ $totalUsers }}</flux:heading>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-50 dark:bg-green-900/20">
                    <flux:icon.users class="size-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Public Prompts</flux:text>
                    <flux:heading size="xl" class="mt-1">{{ $publicPrompts }}</flux:heading>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-900/20">
                    <flux:icon.eye class="size-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Active AI Models</flux:text>
                    <flux:heading size="xl" class="mt-1">{{ $activeAiModels }}</flux:heading>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-900/20">
                    <flux:icon.cpu-chip class="size-6 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Recent Prompts -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Recent Prompts</flux:heading>
            <div class="space-y-4">
                @forelse($recentPrompts as $prompt)
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <flux:text class="font-medium">{{ Str::limit($prompt->title, 40) }}</flux:text>
                            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">by {{ $prompt->user->name }}</flux:text>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-500">{{ $prompt->created_at->diffForHumans() }}</flux:text>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($prompt->visibility === 'public' && $prompt->status === 'published')
                                <flux:badge color="green">Public</flux:badge>
                            @else
                                <flux:badge>Private</flux:badge>
                            @endif
                        </div>
                    </div>
                @empty
                    <flux:text class="text-zinc-500 dark:text-zinc-400">No prompts yet.</flux:text>
                @endforelse
            </div>
        </div>

        <!-- Recent Users -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Recent Users</flux:heading>
            <div class="space-y-4">
                @forelse($recentUsers as $user)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <flux:avatar size="sm" alt="{{ $user->name }}" class="bg-zinc-100 dark:bg-zinc-700">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </flux:avatar>
                            <div>
                                <flux:text class="font-medium">{{ $user->name }}</flux:text>
                                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $user->email }}</flux:text>
                            </div>
                        </div>
                        <div class="text-right">
                            <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $user->prompts_count }} prompts</flux:text>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-500">{{ $user->created_at->diffForHumans() }}</flux:text>
                        </div>
                    </div>
                @empty
                    <flux:text class="text-zinc-500 dark:text-zinc-400">No users yet.</flux:text>
                @endforelse
            </div>
        </div>
    </div>
</div>