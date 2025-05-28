<header class="w-full border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="mx-auto flex h-16 max-w-4xl items-center justify-between px-4 lg:px-0">
        <a href="{{ route('home') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
            <x-app-logo />
        </a>
        <nav class="flex items-center gap-2 text-sm">
            <a href="{{ route('explore') }}" class="px-3 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-zinc-700 dark:text-zinc-200" wire:navigate>Explore</a>
            <a href="{{ route('platforms.index') }}" class="px-3 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-zinc-700 dark:text-zinc-200" wire:navigate>Platforms</a>
            <a href="{{ route('models.index') }}" class="px-3 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-zinc-700 dark:text-zinc-200" wire:navigate>Models</a>
            <a href="{{ route('categories.index') }}" class="px-3 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-zinc-700 dark:text-zinc-200" wire:navigate>Categories</a>
            
            <!-- Dark Mode Toggle -->
            <button 
                x-data 
                @click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'"
                class="flex items-center justify-center h-10 w-10 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-zinc-700 dark:text-zinc-200"
                title="Toggle dark mode"
            >
                <!-- Moon icon (for light mode - shows when in light mode) -->
                <svg x-show="$flux.appearance === 'light'" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <!-- Sun icon (for dark mode - shows when in dark mode) -->
                <svg x-show="$flux.appearance === 'dark'" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <!-- Default moon icon (fallback when Alpine hasn't loaded yet) -->
                <svg x-show="!$flux.appearance || $flux.appearance === 'system'" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </button>
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-md bg-zinc-800 text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-800 dark:hover:bg-zinc-200 transition-colors" wire:navigate>Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-md border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" wire:navigate>Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-md border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" wire:navigate>Register</a>
                    @endif
                @endauth
            @endif
        </nav>
    </div>
</header>
