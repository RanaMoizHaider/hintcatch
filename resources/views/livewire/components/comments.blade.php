<div class="space-y-6">
    @foreach($comments as $comment)
        <div class="flex gap-4" wire:key="comment-{{ $comment->id }}">
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
                <textarea 
                    wire:model="newComment"
                    class="w-full p-3 border border-zinc-200 dark:border-zinc-700 rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder-zinc-500 dark:placeholder-zinc-400 focus:ring-2 focus:ring-zinc-500 focus:border-transparent resize-none" 
                    rows="3" 
                    placeholder="Share your thoughts..."
                ></textarea>
                @error('newComment') 
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                @enderror
                <div class="mt-3 flex justify-between items-center">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Be respectful and constructive in your comments.
                    </p>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-900 rounded-md hover:bg-zinc-700 dark:hover:bg-zinc-300 transition-colors disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Post Comment</span>
                        <span wire:loading>Posting...</span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="mt-6 p-4 bg-zinc-50 dark:bg-zinc-900 rounded-md text-center border-t border-zinc-200 dark:border-zinc-700 pt-6">
            <p class="text-zinc-600 dark:text-zinc-400">
                <a href="{{ route('login') }}" class="text-zinc-800 dark:text-zinc-200 hover:underline font-medium">Sign in</a> to leave a comment
            </p>
        </div>
    @endauth
</div>
