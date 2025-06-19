<div>
    @if($user)
        @if($linkable && $user->username)
            <flux:link href="{{ route('profile.show', $user->username) }}" variant="ghost" class="hover:opacity-80 transition-opacity">
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <img 
                            src="{{ $user->avatar }}" 
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
            </flux:link>
        @else
            <div class="flex items-center gap-2">
                <div class="relative">
                    <img 
                        src="{{ $user->avatar }}" 
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
    @else
        {{-- Fallback for null user --}}
        <div class="flex items-center gap-2">
            <div class="relative {{ $avatarSize }} bg-zinc-200 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                <flux:icon.user class="w-1/2 h-1/2 text-zinc-500" />
            </div>
            
            @if($showName)
                <span class="text-sm text-zinc-500 dark:text-zinc-400 font-medium">Unknown User</span>
            @endif
        </div>
    @endif
</div>