<div>
    @if($linkable)
        <a href="{{ route('profile.show', $user) }}" class="hover:opacity-80 transition-opacity">
            <div class="flex items-center gap-2">
                <div class="relative">
                    <img 
                        src="{{ $user->gravatar }}" 
                        alt="{{ $user->name }}" 
                        class="{{ $avatarSize }} rounded-full object-cover"
                    >
                    @if($showOnline)
                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dark:border-zinc-800 {{ $online ? 'bg-green-400' : 'bg-zinc-400' }}"></div>
                    @endif
                </div>
                
                @if($showName)
                    <span class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">{{ $user->name }}</span>
                @endif
            </div>
        </a>
    @else
        <div class="flex items-center gap-2">
            <div class="relative">
                <img 
                    src="{{ $user->gravatar }}" 
                    alt="{{ $user->name }}" 
                    class="{{ $avatarSize }} rounded-full object-cover"
                >
                @if($showOnline)
                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white dark:border-zinc-800 {{ $online ? 'bg-green-400' : 'bg-zinc-400' }}"></div>
                @endif
            </div>
            
            @if($showName)
                <span class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">{{ $user->name }}</span>
            @endif
        </div>
    @endif
</div>