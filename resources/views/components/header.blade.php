<header class="w-full border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="mx-auto flex h-16 max-w-4xl items-center justify-between px-4 lg:px-0">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-lg text-zinc-900 dark:text-white">
            <x-app-logo-icon class="h-7 w-7 fill-current text-black dark:text-white" />
            <span class="hidden sm:inline">{{ config('app.name', 'Hint Catch') }}</span>
        </a>
        <nav class="flex items-center gap-2 text-sm">
            <a href="{{ route('explore') }}" class="px-3 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-zinc-700 dark:text-zinc-200">Explore</a>
            <a href="{{ route('platforms.index') }}" class="px-3 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-zinc-700 dark:text-zinc-200">Platforms</a>
            <a href="{{ route('models.index') }}" class="px-3 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-zinc-700 dark:text-zinc-200">Models</a>
            <a href="{{ route('categories.index') }}" class="px-3 py-1.5 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-zinc-700 dark:text-zinc-200">Categories</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-md bg-zinc-800 text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-800 dark:hover:bg-zinc-200 transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-md border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-md border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">Register</a>
                    @endif
                @endauth
            @endif
        </nav>
    </div>
</header>
