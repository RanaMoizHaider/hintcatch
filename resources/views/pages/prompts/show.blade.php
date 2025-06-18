<?php

use App\Models\Prompt;
use App\Models\Comment;
use App\Models\Like;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

new
#[Layout('components.layouts.web')]
class extends Component {
    public Prompt $prompt;
    public $isLiked = false;
    public $isSaved = false;
    public $activeTab = 'comments';
    public $relatedPrompts;

    protected $listeners = ['comment-added' => 'refreshPrompt'];

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
            ->withViewsCount()
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



    public function copyPrompt()
    {
        $this->dispatch('prompt-copied', content: $this->prompt->content);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function refreshPrompt()
    {
        $this->prompt->refresh();
        $this->prompt->load(['comments.user']);
    }

    public function getCommentCount()
    {
        return $this->prompt->comments()->count();
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
                        @if($prompt->user)
                            <flux:link href="{{ route('profile.show', $prompt->user) }}" variant="ghost" class="flex items-center gap-2">
                                <livewire:components.user-avatar 
                                    :user="$prompt->user" 
                                    size="md" 
                                    :show-name="true"
                                    wire:key="avatar-{{ $prompt->id }}-{{ $prompt->user->id }}"
                                />
                            </flux:link>
                        @else
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 bg-zinc-200 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                                    <flux:icon.user class="w-5 h-5 text-zinc-500" />
                                </div>
                                <span class="text-sm text-zinc-500 dark:text-zinc-400 font-medium">Unknown User</span>
                            </div>
                        @endif

                        <div class="flex items-center text-sm text-zinc-600 dark:text-zinc-400">
                            <flux:icon.calendar class="size-4 mr-1" />
                            <time datetime="{{ $prompt->created_at }}">{{ $prompt->created_at->format('M j, Y') }}</time>
                        </div>
                    </div>

                    @if($prompt->description)
                        <p class="text-zinc-600 dark:text-zinc-400 mb-6">{{ $prompt->description }}</p>
                    @endif

                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($prompt->tags as $tag)
                            <flux:badge color="zinc" size="sm">{{ $tag->name }}</flux:badge>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3 mb-8">
                        <flux:button 
                            wire:click="toggleLike"
                            variant="{{ $isLiked ? 'danger' : 'outline' }}"
                            icon="heart"
                            size="sm"
                        >
                            {{ $prompt->likes->count() }}
                        </flux:button>
                        
                        {{-- <flux:button 
                            variant="outline" 
                            size="sm" 
                            icon="bookmark"
                        >
                            Save
                        </flux:button> --}}
                        
                        <flux:modal.trigger name="share-prompt">
                            <flux:button 
                                variant="outline" 
                                size="sm" 
                                icon="share"
                            >
                                Share
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:spacer />
                        
                        <flux:button
                            x-data="{ copied: false }"
                            data-content="{{ e($prompt->content) }}"
                            @click="
                                navigator.clipboard.writeText($el.dataset.content)
                                    .then(() => { copied = true; setTimeout(() => copied = false, 2000); })
                                    .catch(() => alert('Could not copy to clipboard'));
                            "
                            variant="outline"
                            size="sm"
                        >
                            <span x-text="copied ? 'Copied!' : 'Copy Prompt'"></span>
                        </flux:button>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 mb-8">
                    <h2 class="font-medium text-lg mb-4">Prompt</h2>
                    <pre class="whitespace-pre-wrap text-sm font-mono bg-zinc-50 dark:bg-zinc-900 p-4 rounded-md overflow-auto">{{ $prompt->content }}</pre>
                    
                    @if($prompt->source)
                        <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                <flux:icon.link class="size-4" />
                                <span>Original source:</span>
                                <flux:link 
                                    href="{{ $prompt->source }}" 
                                    variant="ghost"
                                    target="_blank" 
                                    class="text-blue-600 dark:text-blue-400"
                                >
                                    {{ parse_url($prompt->source, PHP_URL_HOST) ?? $prompt->source }}
                                    <flux:icon.arrow-up-right class="size-3 inline ml-1" />
                                </flux:link>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 mb-8">
                    <flux:button 
                        wire:click="toggleLike"
                        variant="{{ $isLiked ? 'danger' : 'outline' }}"
                        icon="heart"
                        size="sm"
                    >
                        {{ $prompt->likes->count() }}
                    </flux:button>
                    
                    {{-- <flux:button 
                        variant="outline" 
                        size="sm" 
                        icon="bookmark"
                    >
                        Save
                    </flux:button> --}}
                    
                    <flux:modal.trigger name="share-prompt">
                        <flux:button 
                            variant="outline" 
                            size="sm" 
                            icon="share"
                        >
                            Share
                        </flux:button>
                    </flux:modal.trigger>

                    <flux:spacer />
                    
                    <flux:button
                        x-data="{ copied: false }"
                        data-content="{{ e($prompt->content) }}"
                        @click="
                            navigator.clipboard.writeText($el.dataset.content)
                                .then(() => { copied = true; setTimeout(() => copied = false, 2000); })
                                .catch(() => alert('Could not copy to clipboard'));
                        "
                        variant="outline"
                        size="sm"
                    >
                        <span x-text="copied ? 'Copied!' : 'Copy Prompt'"></span>
                    </flux:button>
                </div>

                <div class="space-y-4">
                    <flux:button.group>
                        <flux:button 
                            wire:click="setActiveTab('comments')" 
                            variant="{{ $activeTab === 'comments' ? 'filled' : 'subtle' }}"
                            size="sm"
                        >
                            Comments ({{ $prompt->comments->count() }})
                        </flux:button>
                        <flux:button 
                            wire:click="setActiveTab('related')" 
                            variant="{{ $activeTab === 'related' ? 'filled' : 'subtle' }}"
                            size="sm"
                        >
                            Related Prompts
                        </flux:button>
                    </flux:button.group>

                    @if($activeTab === 'comments')
                        <div>
                            <livewire:components.comments :commentable="$prompt" />
                        </div>
                    @endif

                    @if($activeTab === 'related')
                        <div class="space-y-4">
                            @forelse($relatedPrompts as $relatedPrompt)
                                <flux:link href="{{ route('prompts.show', $relatedPrompt) }}" variant="ghost" class="block">
                                    <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <h3 class="font-medium">{{ $relatedPrompt->title }}</h3>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">By {{ $relatedPrompt->user ? $relatedPrompt->user->name : 'Unknown User' }}</p>
                                    </div>
                                </flux:link>
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
                                <flux:link href="{{ route('categories.show', $prompt->category) }}">
                                    <flux:badge color="blue" size="sm">{{ $prompt->category->name }}</flux:badge>
                                </flux:link>
                            </div>
                        @endif

                        @if($prompt->platforms->count() > 0)
                            <div>
                                <h3 class="text-sm font-medium mb-2">Platforms</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($prompt->platforms as $platform)
                                        <flux:link href="{{ route('platforms.show', $platform) }}">
                                            <flux:badge color="zinc" size="sm">{{ $platform->name }}</flux:badge>
                                        </flux:link>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($prompt->aiModels->count() > 0)
                            <div>
                                <h3 class="text-sm font-medium mb-2">Models</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($prompt->aiModels as $model)
                                        <flux:link href="{{ route('models.show', $model) }}">
                                            <flux:badge color="zinc" size="sm">{{ $model->name }}</flux:badge>
                                        </flux:link>
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
                        <div class="flex flex-col col-span-full items-center p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <span class="text-lg font-semibold">{{ $prompt->comments->count() }}</span>
                            <span class="text-xs text-zinc-600 dark:text-zinc-400">Comments</span>
                        </div>
                        {{-- <div class="flex flex-col items-center p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md">
                            <span class="text-lg font-semibold">0</span>
                            <span class="text-xs text-zinc-600 dark:text-zinc-400">Saves</span>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <flux:modal name="share-prompt" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Share Prompt</flux:heading>
                <flux:text class="mt-2">Share "{{ $prompt->title }}" with others</flux:text>
            </div>

            <!-- Copy URL -->
            <div class="flex items-center gap-2">
                <flux:input 
                    type="text" 
                    value="{{ route('prompts.show', $prompt) }}" 
                    readonly 
                    class="flex-1"
                />
                <flux:tooltip content="Copy Link">
                    <flux:button 
                        icon="clipboard" 
                        icon:variant="outline" 
                        @click="
                            navigator.clipboard.writeText('{{ route('prompts.show', $prompt) }}')
                                .then(() => alert('Link copied to clipboard!'))
                                .catch(() => alert('Failed to copy link.'));
                        "
                    />
                </flux:tooltip>
            </div>

            <!-- Social Sharing Options -->
            <div class="space-y-3">
                <flux:text class="text-sm font-medium">Share on social media</flux:text>
                
                <!-- Twitter/X -->
                <flux:button 
                    variant="outline" 
                    class="w-full justify-start"
                    @click="
                        window.open(
                            'https://twitter.com/intent/tweet?text=' + 
                            encodeURIComponent('Check out this AI prompt: {{ e($prompt->title) }}') + 
                            '&url=' + encodeURIComponent('{{ route('prompts.show', $prompt) }}'),
                            '_blank',
                            'width=600,height=400'
                        )
                    "
                >
                    <flux:icon.twitter class="size-4 mr-2" />
                    Share on X (Twitter)
                </flux:button>

                <!-- LinkedIn -->
                {{-- <flux:button 
                    variant="outline" 
                    class="w-full justify-start"
                    @click="
                        window.open(
                            'https://www.linkedin.com/sharing/share-offsite/?url=' + 
                            encodeURIComponent('{{ route('prompts.show', $prompt) }}'),
                            '_blank',
                            'width=600,height=400'
                        )
                    "
                >
                    <flux:icon.linkedin class="size-4 mr-2" />
                    Share on LinkedIn
                </flux:button> --}}

                <!-- Facebook -->
                <flux:button 
                    variant="outline" 
                    class="w-full justify-start"
                    @click="
                        window.open(
                            'https://www.facebook.com/sharer/sharer.php?u=' + 
                            encodeURIComponent('{{ route('prompts.show', $prompt) }}'),
                            '_blank',
                            'width=600,height=400'
                        )
                    "
                >
                    <flux:icon.facebook class="size-4 mr-2" />
                    Share on Facebook
                </flux:button>

                <!-- Reddit -->
                <flux:button 
                    variant="outline" 
                    class="w-full justify-start"
                    @click="
                        window.open(
                            'https://www.reddit.com/submit?title=' + 
                            encodeURIComponent('{{ e($prompt->title) }}') + 
                            '&url=' + encodeURIComponent('{{ route('prompts.show', $prompt) }}'),
                            '_blank',
                            'width=600,height=400'
                        )
                    "
                >
                    <flux:icon.message-square class="size-4 mr-2" />
                    Share on Reddit
                </flux:button>
            </div>

            <div x-data="{ supportsWebShare: navigator.share !== undefined }" x-show="supportsWebShare">
                <flux:button 
                    variant="primary" 
                    class="w-full"
                    @click="
                        navigator.share({
                            title: '{{ e($prompt->title) }}',
                            text: '{{ e($prompt->description ?? Str::limit($prompt->content, 100)) }}',
                            url: '{{ route('prompts.show', $prompt) }}'
                        }).catch(console.log)
                    "
                    icon="share"
                >
                    Share via...
                </flux:button>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>
