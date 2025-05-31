<header class="w-full border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="mx-auto flex h-16 max-w-4xl items-center justify-between px-4 lg:px-0">
        <flux:link variant="ghost" href="{{ route('home') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
            <x-app-logo />
        </flux:link>
        <div class="flex items-center gap-2">
            <flux:navbar>
                <flux:navbar.item href="{{ route('explore') }}" wire:navigate>Explore</flux:navbar.item>
                <flux:navbar.item href="{{ route('platforms.index') }}" wire:navigate>Platforms</flux:navbar.item>
                <flux:navbar.item href="{{ route('models.index') }}" wire:navigate>Models</flux:navbar.item>
                <flux:navbar.item href="{{ route('categories.index') }}" wire:navigate>Categories</flux:navbar.item>
            </flux:navbar>

            <flux:separator vertical class="my-4" />

            <!-- Dark Mode Toggle -->
            <flux:button 
                x-data 
                @click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'"
                variant="ghost"
                square
                class="h-10 w-10"
                title="Toggle dark mode"
            >
                <!-- Moon icon (for light mode - shows when in light mode) -->
                <flux:icon.moon x-show="$flux.appearance === 'light'" x-cloak class="size-5" />
                <!-- Sun icon (for dark mode - shows when in dark mode) -->
                <flux:icon.sun x-show="$flux.appearance === 'dark'" x-cloak class="size-5" />
            </flux:button>
            
            @if (Route::has('login'))
                @auth
                    <flux:button href="{{ route('dashboard') }}" wire:navigate variant="filled">Dashboard</flux:button>
                @else
                    <flux:button href="{{ route('login') }}" wire:navigate variant="outline">Log in</flux:button>
                    @if (Route::has('register'))
                        <flux:button href="{{ route('register') }}" wire:navigate variant="outline">Register</flux:button>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</header>
