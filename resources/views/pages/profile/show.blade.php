<?php

use App\Models\User;
use App\Models\Prompt;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.web')] class extends Component {
    use WithPagination;

    public User $user;
    public $activeTab = 'prompts';

    public function mount(User $user)
    {
        $this->user = $user->load(['prompts' => function($query) {
            $query->where('status', 'published')
                  ->where('visibility', 'public')
                  ->whereNotNull('published_at')
                  ->where('published_at', '<=', now())
                  ->latest();
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
        return $this->user->prompts()
            ->with(['tags', 'category', 'likes', 'user'])
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest()
            ->paginate(12);
    }

    public function getLikedPromptsProperty()
    {
        return Prompt::with(['user', 'tags', 'category', 'likes'])
            ->whereHas('likes', function($query) {
                $query->where('user_id', $this->user->id);
            })
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest()
            ->paginate(12);
    }

    public function getStatsProperty()
    {
        return [
            'prompts_count' => $this->user->prompts()
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->count(),
            'likes_received' => $this->user->prompts()
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->withCount('likes')
                ->get()
                ->sum('likes_count'),
            'total_views' => $this->user->prompts()
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->get()
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
                <img src="{{ $user->gravatar ?? '/placeholder.svg' }}" alt="{{ $user->name }}" class="w-24 h-24 md:w-32 md:h-32 rounded-full">

                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                        <div>
                            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
                            <p class="text-zinc-600 dark:text-zinc-400">{{ $user->username ? '@' . $user->username : '' }}</p>
                        </div>

                        <div class="flex gap-3">
                            @auth
                                @if(auth()->id() !== $user->id)
                                    <button class="px-4 py-2 bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-900 rounded-md hover:bg-zinc-700 dark:hover:bg-zinc-300">Follow</button>
                                    <button class="px-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800">Message</button>
                                @else
                                    <a href="{{ route('dashboard') }}" class="px-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800">Edit Profile</a>
                                @endif
                            @else
                                <button class="px-4 py-2 bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-900 rounded-md hover:bg-zinc-700 dark:hover:bg-zinc-300">Follow</button>
                                <button class="px-4 py-2 border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800">Message</button>
                            @endauth
                        </div>
                    </div>

                    <p class="mb-4">{{ $user->bio ?? 'No bio available.' }}</p>

                    <div class="flex flex-wrap gap-y-2 gap-x-4 text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                        @if($user->location ?? false)
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $user->location }}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Joined {{ $user->created_at->format('F Y') }}</span>
                        </div>

                        @if($user->twitter ?? false)
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"></path>
                                </svg>
                                <a href="https://twitter.com/{{ $user->twitter }}" target="_blank" class="hover:underline">@{{ $user->twitter }}</a>
                            </div>
                        @endif

                        @if($user->website ?? false)
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <a href="{{ $user->website }}" target="_blank" class="hover:underline">{{ parse_url($user->website, PHP_URL_HOST) ?? $user->website }}</a>
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
            <div class="flex space-x-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg mb-6 w-fit">
                <button 
                    wire:click="setActiveTab('prompts')"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200 ease-in-out {{ $activeTab === 'prompts' ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}"
                >
                    Prompts ({{ $this->stats['prompts_count'] }})
                </button>
                <button 
                    wire:click="setActiveTab('liked')"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200 ease-in-out {{ $activeTab === 'liked' ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}"
                >
                    Liked
                </button>
                <button 
                    wire:click="setActiveTab('collections')"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200 ease-in-out {{ $activeTab === 'collections' ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}"
                >
                    Collections
                </button>
            </div>

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
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">Collections coming soon</h3>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                            Collections feature will be available soon.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
