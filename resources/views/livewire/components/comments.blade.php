<div class="space-y-6">
    @foreach($comments as $comment)
        <div class="flex gap-4" wire:key="comment-{{ $comment->id }}">
            @if($comment->user)
                <livewire:components.user-avatar :user="$comment->user" size="sm" :key="'avatar-'.$comment->id" />
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-medium">{{ $comment->user->name }}</span>
                        <span class="text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $comment->body }}</p>
                </div>
            @else
                <div class="w-8 h-8 bg-zinc-200 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                    <flux:icon.user class="w-4 h-4 text-zinc-500" />
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-medium text-zinc-500">Unknown User</span>
                        <span class="text-xs text-zinc-600 dark:text-zinc-400">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $comment->body }}</p>
                </div>
            @endif
        </div>
    @endforeach

    @if($comments->isEmpty())
        <div class="text-center py-8">
            <p class="text-zinc-500 dark:text-zinc-400">No comments yet. Be the first to share your thoughts!</p>
        </div>
    @endif

    @auth
        <div class="mt-6 border-t border-zinc-200 dark:border-zinc-700 pt-6">
            <h3 class="font-medium mb-3">Add a comment</h3>
            <form wire:submit="addComment">
                <flux:field>
                    <flux:textarea 
                        wire:model="newComment"
                        rows="3" 
                        placeholder="Share your thoughts..."
                    />
                    <flux:error name="newComment" />
                </flux:field>
                <div class="mt-3 flex justify-between items-center">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Be respectful and constructive in your comments.
                    </p>
                    <flux:button 
                        type="submit" 
                        variant="primary"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Post Comment</span>
                        <span wire:loading>Posting...</span>
                    </flux:button>
                </div>
            </form>
        </div>
    @else
        <div class="mt-6 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-md text-center border-t border-zinc-200 dark:border-zinc-700 pt-6">
            <p class="text-zinc-600 dark:text-zinc-400">
                <flux:link href="{{ route('login') }}" variant="ghost" class="text-zinc-800 dark:text-zinc-200 hover:underline font-medium">Sign in</flux:link> to leave a comment
            </p>
        </div>
    @endauth
</div>
