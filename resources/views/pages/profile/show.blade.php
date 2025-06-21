<?php

use App\Models\User;
use App\Models\Prompt;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[Layout('components.layouts.web')]
class extends Component {
    use WithPagination;

    public User $user;
    public $activeTab = 'prompts';

    public function mount(User $user)
    {
        // Frontend - uses global scopes automatically (published + public only)
        $this->user = $user->load(['prompts' => function($query) {
            $query->latest();
        }]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function getPromptsProperty()
    {
        // Frontend - uses global scopes automatically (published + public only)
        return $this->user->prompts()
            ->with(['tags', 'category', 'likes', 'user'])
            ->latest()
            ->paginate(12);
    }

    public function getLikedPromptsProperty()
    {
        // Frontend - uses global scopes automatically (published + public only)
        return Prompt::with(['user', 'tags', 'category', 'likes'])
            ->whereHas('likes', function($query) {
                $query->where('user_id', $this->user->id);
            })
            ->latest()
            ->paginate(12);
    }

    public function getStatsProperty()
    {
        return [
            'prompts_count' => $this->user->prompts()
                ->count(), // Uses global scopes - only public + published
            'likes_received' => $this->user->prompts()
                ->withCount('likes')
                ->get() // Uses global scopes - only public + published
                ->sum('likes_count'),
            'total_views' => $this->user->prompts()
                ->get() // Uses global scopes - only public + published
                ->sum(function($prompt) {
                    return views($prompt)->count();
                }),
            'followers_count' => 0, // Implement if you have followers functionality
        ];
    }
}; ?>

<div>
    <x-slot name="title">{{ $user->name }} - Profile - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-8 items-start">
                <img src="{{ $user->avatar ?? '/placeholder.svg' }}" alt="{{ $user->name }}" class="w-24 h-24 md:w-32 md:h-32 rounded-full">

                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                        <div>
                            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                            <p class="text-zinc-600 dark:text-zinc-400">{{ $user->username ? '@' . $user->username : '' }}</p>
                        </div>

                        <div class="flex gap-3">
                            @auth
                                @if(auth()->id() !== $user->id)
                                    <flux:button variant="primary">Follow</flux:button>
                                    <flux:button variant="outline">Message</flux:button>
                                @else
                                    <flux:button variant="outline" href="{{ route('dashboard') }}">Edit Profile</flux:button>
                                @endif
                            @else
                                <flux:button variant="primary">Follow</flux:button>
                                <flux:button variant="outline">Message</flux:button>
                            @endauth
                        </div>
                    </div>

                    <p class="mb-4">{{ $user->bio ?? 'No bio available.' }}</p>

                    <div class="flex flex-wrap gap-y-2 gap-x-4 text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                        @if($user->location ?? false)
                            <div class="flex items-center gap-1">
                                <flux:icon.map-pin class="size-4" />
                                <span>{{ $user->location }}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-1">
                            <flux:icon.calendar class="size-4" />
                            <span>Joined {{ $user->created_at->format('F Y') }}</span>
                        </div>

                        @if($user->twitter ?? false)
                            <div class="flex items-center gap-1">
                                <flux:icon.twitter class="size-4" />
                                <flux:link href="https://twitter.com/{{ $user->twitter }}" target="_blank" variant="ghost" class="hover:underline">@{{ $user->twitter }}</flux:link>
                            </div>
                        @endif

                        @if($user->website ?? false)
                            <div class="flex items-center gap-1">
                                <flux:icon.link class="size-4" />
                                <flux:link href="{{ $user->website }}" target="_blank" class="hover:underline">{{ parse_url($user->website, PHP_URL_HOST) ?? $user->website }}</flux:link>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <div class="text-lg font-semibold">{{ $this->stats['prompts_count'] }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Prompts</div>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <div class="text-lg font-semibold">{{ $this->stats['likes_received'] }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Likes</div>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <div class="text-lg font-semibold">{{ number_format($this->stats['total_views']) }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Views</div>
                        </div>
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <div class="text-lg font-semibold">{{ $this->stats['followers_count'] }}</div>
                            <div class="text-sm text-zinc-600 dark:text-zinc-400">Followers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        <div>
            <flux:button.group class="mb-6">
                <flux:button 
                    wire:click="setActiveTab('prompts')"
                    variant="{{ $activeTab === 'prompts' ? 'filled' : 'subtle' }}"
                    size="sm"
                >
                    Prompts ({{ $this->stats['prompts_count'] }})
                </flux:button>
                <flux:button 
                    wire:click="setActiveTab('liked')"
                    variant="{{ $activeTab === 'liked' ? 'filled' : 'subtle' }}"
                    size="sm"
                >
                    Liked
                </flux:button>
                <flux:button 
                    wire:click="setActiveTab('collections')"
                    variant="{{ $activeTab === 'collections' ? 'filled' : 'subtle' }}"
                    size="sm"
                >
                    Collections
                </flux:button>
            </flux:button.group>

            <div class="min-h-[400px]" wire:loading.class="opacity-50" wire:target="setActiveTab">
                @if($activeTab === 'prompts')
                    @if($this->prompts->count() > 0)
                        <x-card-grid :columns="3">
                            @foreach($this->prompts as $prompt)
                                <livewire:components.prompt-card 
                                    :prompt="$prompt"
                                    :show-user="false"
                                    :show-stats="true" 
                                    :show-featured-badge="true"
                                    :show-tags="true"
                                    :tag-limit="2"
                                    wire:key="user-prompt-{{ $prompt->id }}"
                                />
                            @endforeach
                        </x-card-grid>

                        <div class="mt-8">
                            {{ $this->prompts->links() }}
                        </div>
                    @else
                        <x-empty-state 
                            icon="document"
                            title="No prompts yet"
                            :description="$user->name . ' hasn\'t created any prompts yet.'"
                        />
                    @endif
                @endif

                @if($activeTab === 'liked')
                    @if($this->likedPrompts->count() > 0)
                        <x-card-grid :columns="3">
                            @foreach($this->likedPrompts as $prompt)
                                <livewire:components.prompt-card 
                                    :prompt="$prompt"
                                    :show-user="true"
                                    :show-stats="false" 
                                    :show-tags="true"
                                    :tag-limit="2"
                                    wire:key="liked-prompt-{{ $prompt->id }}"
                                />
                            @endforeach
                        </x-card-grid>

                        <div class="mt-8">
                            {{ $this->likedPrompts->links() }}
                        </div>
                    @else
                        <x-empty-state 
                            icon="heart"
                            title="No liked prompts"
                            :description="$user->name . ' hasn\'t liked any prompts yet.'"
                        />
                    @endif
                @endif

                @if($activeTab === 'collections')
                    <x-empty-state 
                        icon="archive-box"
                        title="Collections coming soon"
                        description="Collections feature will be available soon."
                    />
                @endif
            </div>
        </div>
    </div>
</div>
