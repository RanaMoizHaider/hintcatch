<?php

use App\Models\Prompt;
use App\Models\Comment;
use App\Models\Like;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.web')] class extends Component {
    public Prompt $prompt;
    public $newComment = '';
    public $isLiked = false;
    public $isSaved = false;
    public $activeTab = 'comments';
    public $relatedPrompts;

    public function mount(Prompt $prompt)
    {
        $this->prompt = $prompt->load([
            'user', 
            'tags', 
            'category', 
            'platforms', 
            'aiModels', 
            'comments.user',
            'likes'
        ]);

        // Record view
        views($this->prompt)->record();

        // Check if user has liked or saved this prompt
        if (Auth::check()) {
            $this->isLiked = $this->prompt->likes()->where('user_id', Auth::id())->exists();
            // Assuming you have a saves relationship
            // $this->isSaved = $this->prompt->saves()->where('user_id', Auth::id())->exists();
        }

        // Get related prompts
        $this->relatedPrompts = Prompt::with(['user', 'category'])
            ->where('id', '!=', $this->prompt->id)
            ->where('category_id', $this->prompt->category_id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('visibility', 'public')
            ->take(5)
            ->get();
    }

    public function toggleLike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $like = $this->prompt->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
            $this->isLiked = false;
        } else {
            $this->prompt->likes()->create(['user_id' => Auth::id()]);
            $this->isLiked = true;
        }

        $this->prompt->refresh();
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'newComment' => 'required|min:3|max:1000'
        ]);

        $this->prompt->comments()->create([
            'user_id' => Auth::id(),
            'content' => $this->newComment
        ]);

        $this->newComment = '';
        $this->prompt->refresh();
        $this->prompt->load('comments.user');
    }

    public function copyPrompt()
    {
        $this->dispatch('prompt-copied', content: $this->prompt->content);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
}; ?>

<div>
    <x-slot name="title">{{ $prompt->title }} - Hint Catch</x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold mb-4">{{ $prompt->title }}</h1>

                    <div class="flex items-center gap-4 mb-6">
                        <a href="{{ route('profile.show', $prompt->user) }}" class="flex items-center gap-2">
                            <livewire:components.user-avatar :user="$prompt->user" size="md" />
                            <span class="font-medium">{{ $prompt->user->name }}</span>
                        </a>

                        <div class="flex items-center text-sm text-zinc-600 dark:text-zinc-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <time datetime="{{ $prompt->created_at }}">{{ $prompt->created_at->format('M j, Y') }}</time>
                        </div>
                    </div>

                    @if($prompt->description)
                        <p class="text-zinc-600 dark:text-zinc-400 mb-6">{{ $prompt->description }}</p>
                    @endif

                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($prompt->tags as $tag)
                            <livewire:components.badge variant="secondary" size="sm" text="{{ $tag->name }}" />
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3 mb-8">
                        <button 
                            wire:click="toggleLike"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-sm border rounded-md transition-colors {{ $isLiked ? 'border-red-300 bg-red-50 text-red-700 dark:border-red-600 dark:bg-red-900 dark:text-red-300' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}"
                        >
                            <svg class="w-4 h-4" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span>{{ $prompt->likes->count() }}</span>
                        </button>
                        
                        <button class="inline-flex items-center gap-1 px-3 py-1.5 text-sm border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                            <span>Save</span>
                        </button>
                        
                        <button class="inline-flex items-center gap-1 px-3 py-1.5 text-sm border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            <span>Share</span>
                        </button>
                        
                        <button 
                            wire:click="copyPrompt"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-sm border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800"
                            x-data
                            @prompt-copied.window="
                                navigator.clipboard.writeText($event.detail.content);
                                $el.textContent = 'Copied!';
                                setTimeout(() => $el.querySelector('span').textContent = 'Copy', 2000);
                            "
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Copy</span>
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                    <h2 class="font-medium text-lg mb-4">Prompt</h2>
                    <pre class="whitespace-pre-wrap text-sm font-mono bg-zinc-50 dark:bg-zinc-900 p-4 rounded-md overflow-auto">{{ $prompt->content }}</pre>
                </div>

                <div class="space-y-4">
                    <div class="flex space-x-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg w-fit">
                        <button 
                            wire:click="setActiveTab('comments')" 
                            class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'comments' ? 'bg-white dark:bg-zinc-700 shadow-sm' : '' }}"
                        >
                            Comments ({{ $prompt->comments->count() }})
                        </button>
                        <button 
                            wire:click="setActiveTab('related')" 
                            class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'related' ? 'bg-white dark:bg-zinc-700 shadow-sm' : '' }}"
                        >
                            Related Prompts
                        </button>
                    </div>

                    @if($activeTab === 'comments')
                        <div class="space-y-6">
                            @foreach($prompt->comments as $comment)
                                <div class="flex gap-4">
                                    <livewire:components.user-avatar :user="$comment->user" size="sm" />
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-medium">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-zinc-600 dark:text-zinc-400">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-sm">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            @endforeach

                            @auth
                                <div class="mt-6">
                                    <h3 class="font-medium mb-2">Add a comment</h3>
                                    <form wire:submit="addComment">
                                        <textarea 
                                            wire:model="newComment"
                                            class="w-full p-3 border border-zinc-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder-zinc-500 dark:placeholder-zinc-400" 
                                            rows="3" 
                                            placeholder="Share your thoughts about this prompt..."
                                        ></textarea>
                                        @error('newComment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        <div class="mt-2 flex justify-end">
                                            <button type="submit" class="px-4 py-2 bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-900 rounded-md hover:bg-zinc-700 dark:hover:bg-zinc-300">Post Comment</button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="mt-6 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-md text-center">
                                    <p class="text-zinc-600 dark:text-zinc-400">
                                        <a href="{{ route('login') }}" class="text-zinc-800 dark:text-zinc-200 hover:underline">Sign in</a> to leave a comment
                                    </p>
                                </div>
                            @endauth
                        </div>
                    @endif

                    @if($activeTab === 'related')
                        <div class="space-y-4">
                            @forelse($relatedPrompts as $relatedPrompt)
                                <a href="{{ route('prompts.show', $relatedPrompt) }}" class="block">
                                    <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <h3 class="font-medium">{{ $relatedPrompt->title }}</h3>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">By {{ $relatedPrompt->user->name }}</p>
                                    </div>
                                </a>
                            @empty
                                <p class="text-zinc-600 dark:text-zinc-400">No related prompts found.</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                    <h2 class="font-medium text-lg mb-4">Compatibility</h2>

                    <div class="space-y-4">
                        @if($prompt->category)
                            <div>
                                <h3 class="text-sm font-medium mb-2">Category</h3>
                                <a href="{{ route('categories.show', $prompt->category) }}">
                                    <livewire:components.badge variant="primary" size="sm" text="{{ $prompt->category->name }}" />
                                </a>
                            </div>
                        @endif

                        @if($prompt->platforms->count() > 0)
                            <div>
                                <h3 class="text-sm font-medium mb-2">Platforms</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($prompt->platforms as $platform)
                                        <a href="{{ route('platforms.show', $platform) }}">
                                            <livewire:components.badge variant="secondary" size="sm" text="{{ $platform->name }}" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($prompt->aiModels->count() > 0)
                            <div>
                                <h3 class="text-sm font-medium mb-2">Models</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($prompt->aiModels as $model)
                                        <a href="{{ route('models.show', $model) }}">
                                            <livewire:components.badge variant="default" size="sm" text="{{ $model->name }}" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                    <h2 class="font-medium text-lg mb-4">Stats</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col items-center p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <span class="text-lg font-semibold">{{ $prompt->likes->count() }}</span>
                            <span class="text-xs text-zinc-600 dark:text-zinc-400">Likes</span>
                        </div>
                        <div class="flex flex-col items-center p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <span class="text-lg font-semibold">{{ views($prompt)->count() }}</span>
                            <span class="text-xs text-zinc-600 dark:text-zinc-400">Views</span>
                        </div>
                        <div class="flex flex-col items-center p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <span class="text-lg font-semibold">{{ $prompt->comments->count() }}</span>
                            <span class="text-xs text-zinc-600 dark:text-zinc-400">Comments</span>
                        </div>
                        <div class="flex flex-col items-center p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <span class="text-lg font-semibold">0</span>
                            <span class="text-xs text-zinc-600 dark:text-zinc-400">Saves</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
