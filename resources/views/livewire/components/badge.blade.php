<span class="{{ $computedClasses }}">
    {{ $text ?: $slot }}
    
    @if($removable && $removeAction)
        <button wire:click="{{ $removeAction }}" class="ml-1 -mr-1 p-0.5 rounded-full hover:bg-black/10 dark:hover:bg-white/20 transition-colors focus:outline-none focus:ring-2 focus:ring-current focus:ring-offset-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif
</span>
