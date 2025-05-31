<footer class="w-full border-t border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand Section -->
            <div class="md:col-span-1">
                <flux:brand href="{{ route('home') }}" name="Hint Catch" class="mb-4" />
                <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-4">
                    Discover and share the best AI prompts for your creative and professional needs.
                </flux:text>
            </div>

            <!-- Quick Links -->
            <div>
                <flux:navlist>
                    <flux:navlist.group heading="Explore">
                        <flux:navlist.item href="{{ route('categories.index') }}">All Categories</flux:navlist.item>
                        <flux:navlist.item href="{{ route('models.index') }}">All AI Models</flux:navlist.item>
                        <flux:navlist.item href="{{ route('platforms.index') }}">All Platforms</flux:navlist.item>
                        @auth
                            <flux:navlist.item href="{{ route('user.prompts.create') }}">Create Prompt</flux:navlist.item>
                        @else
                            <flux:navlist.item href="{{ route('register') }}">Join Community</flux:navlist.item>
                        @endauth
                    </flux:navlist.group>
                </flux:navlist>
            </div>

            <!-- Top Categories -->
            <div>
                <flux:navlist>
                    <flux:navlist.group heading="Popular Categories">
                        @php
                            $topCategories = \App\Models\Category::withCount('prompts')
                                ->orderBy('prompts_count', 'desc')
                                ->limit(4)
                                ->get();
                        @endphp
                        @forelse($topCategories as $category)
                            <flux:navlist.item href="{{ route('categories.show', $category->slug) }}">
                                {{ $category->name }}
                            </flux:navlist.item>
                        @empty
                            <flux:navlist.item href="{{ route('categories.index') }}">Browse Categories</flux:navlist.item>
                        @endforelse
                    </flux:navlist.group>
                </flux:navlist>
            </div>

            <!-- Top Platforms & Models -->
            <div>
                <flux:navlist>
                    <flux:navlist.group heading="Popular Platforms">
                        @php
                            $topPlatforms = \App\Models\Platform::withCount('prompts')
                                ->orderBy('prompts_count', 'desc')
                                ->limit(4)
                                ->get();
                        @endphp
                        @forelse($topPlatforms as $platform)
                            <flux:navlist.item href="{{ route('platforms.show', $platform->slug) }}">
                                {{ $platform->name }}
                            </flux:navlist.item>
                        @empty
                            <flux:navlist.item href="{{ route('platforms.index') }}">Browse Platforms</flux:navlist.item>
                        @endforelse
                    </flux:navlist.group>
                </flux:navlist>
            </div>
        </div>

        <!-- Bottom Section -->
        <flux:separator class="my-8" />
        
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <div class="flex items-center space-x-4">
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                    &copy; {{ date('Y') }} Hint Catch. All rights reserved.
                </flux:text>
            </div>
            
            <div class="flex items-center space-x-4">
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                    Made with ❤️ and 🤖 for the AI community
                </flux:text>
            </div>
        </div>
    </div>
</footer>
