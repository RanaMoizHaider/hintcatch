<div class="{{ $layout !== 'list' ? 'h-full' : '' }}">
    @if($layout === 'list')
        <!-- List Layout -->
        <article class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-md transition-shadow">
            <div class="flex gap-6">
                <div class="flex-1">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-semibold text-lg leading-tight flex-1 mr-2">
                            @if($linkable)
                                <flux:link href="{{ route('prompts.show', $prompt) }}" variant="ghost" class="hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors">
                                    {{ $prompt->title }}
                                </flux:link>
                            @else
                                {{ $prompt->title }}
                            @endif
                        </h3>
                        @if($showFeaturedBadge && $prompt->featured)
                            <flux:badge color="green" size="sm" class="flex-shrink-0">Featured</flux:badge>
                        @endif
                    </div>
                    
                    @if($showUser && $prompt->user)
                        <div class="mb-3">
                            <livewire:components.user-avatar 
                                :user="$prompt->user" 
                                size="sm" 
                                :show-name="true"
                                wire:key="avatar-{{ $prompt->id }}-{{ $prompt->user->id }}"
                            />
                        </div>
                    @endif
                    
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4 line-clamp-2">
                        {{ $prompt->description ?? Str::limit($prompt->content, $contentLimit) }}
                    </p>
                    
                    <!-- Tags and Categories -->
                    @if($showCategory || $showPlatforms || $showModels || $showTags)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($showCategory && $prompt->category)
                                <flux:badge color="zinc" size="sm">{{ $prompt->category->name }}</flux:badge>
                            @endif
                            
                            @if($showPlatforms)
                                @foreach($prompt->platforms->take($platformLimit) as $platform)
                                    <flux:badge color="blue" size="sm">{{ $platform->name }}</flux:badge>
                                @endforeach
                            @endif
                            
                            @if($showModels)
                                @foreach($prompt->aiModels->take($modelLimit) as $model)
                                    <flux:badge color="green" size="sm">{{ $model->name }}</flux:badge>
                                @endforeach
                            @endif
                            
                            @if($showTags)
                                @foreach($prompt->tags->take($tagLimit) as $tag)
                                    <flux:badge color="zinc" size="sm">{{ $tag->name }}</flux:badge>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
                
                @if($showStats)
                    <!-- Right side stats -->
                    <div class="flex flex-col items-end justify-between min-w-0 text-sm text-zinc-500 dark:text-zinc-400">
                        <div class="flex items-center space-x-4 mb-2">
                            <span class="flex items-center gap-1">
                                <flux:icon.heart class="size-4" />
                                {{ $prompt->likes->count() ?? 0 }}
                            </span>
                            <span class="flex items-center gap-1">
                                <flux:icon.eye class="size-4" />
                                {{ views($prompt)->count() }}
                            </span>
                        </div>
                        <div class="text-xs text-zinc-400 whitespace-nowrap">
                            {{ $prompt->created_at->diffForHumans() }}
                        </div>
                    </div>
                @endif
            </div>
        </article>
    @elseif($layout === 'horizontal')
        <!-- Horizontal Layout -->
        <article class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="font-semibold text-base leading-tight truncate">
                            @if($linkable)
                                <flux:link href="{{ route('prompts.show', $prompt) }}" variant="ghost" class="hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors">
                                    {{ $prompt->title }}
                                </flux:link>
                            @else
                                {{ $prompt->title }}
                            @endif
                        </h3>
                        @if($showFeaturedBadge && $prompt->featured)
                            <flux:badge color="green" size="sm" class="flex-shrink-0">Featured</flux:badge>
                        @endif
                    </div>
                    
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm line-clamp-1 mb-2">
                        {{ $prompt->description ?? Str::limit($prompt->content, 100) }}
                    </p>
                    
                    <!-- Inline tags -->
                    @if($showCategory || $showPlatforms || $showModels || $showTags)
                        <div class="flex flex-wrap gap-1">
                            @if($showCategory && $prompt->category)
                                <flux:badge color="zinc" size="sm">{{ $prompt->category->name }}</flux:badge>
                            @endif
                            
                            @if($showPlatforms)
                                @foreach($prompt->platforms->take($platformLimit) as $platform)
                                    <flux:badge color="blue" size="sm">{{ $platform->name }}</flux:badge>
                                @endforeach
                            @endif
                            
                            @if($showModels)
                                @foreach($prompt->aiModels->take($modelLimit) as $model)
                                    <flux:badge color="green" size="sm">{{ $model->name }}</flux:badge>
                                @endforeach
                            @endif
                            
                            @if($showTags)
                                @foreach($prompt->tags->take($tagLimit) as $tag)
                                    <flux:badge color="zinc" size="sm">{{ $tag->name }}</flux:badge>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
                
                @if($showStats)
                    <!-- Compact stats -->
                    <div class="flex items-center space-x-3 text-sm text-zinc-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1">
                            <flux:icon.heart class="size-4" />
                            {{ $prompt->likes->count() ?? 0 }}
                        </span>
                        <span class="flex items-center gap-1">
                            <flux:icon.eye class="size-4" />
                            {{ views($prompt)->count() }}
                        </span>
                        <span class="text-xs">{{ $prompt->created_at->diffForHumans() }}</span>
                    </div>
                @endif
            </div>
        </article>
    @else
        <!-- Default Card Layout -->
        <article class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-md transition-shadow h-full flex flex-col">
            <div class="flex items-start justify-between mb-3">
                <h3 class="font-semibold text-lg leading-tight flex-1 mr-2">
                    @if($linkable)
                        <flux:link href="{{ route('prompts.show', $prompt) }}" variant="ghost" class="hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors" wire:navigate>
                            {{ $prompt->title }}
                        </flux:link>
                    @else
                        {{ $prompt->title }}
                    @endif
                </h3>
                @if($showFeaturedBadge && $prompt->featured)
                    <flux:badge color="green" size="sm" class="flex-shrink-0" wire:key="featured-{{ $prompt->id }}">Featured</flux:badge>
                @endif
            </div>
            
            @if($showUser && $prompt->user)
                <div class="mb-3">
                    <livewire:components.user-avatar 
                        :user="$prompt->user" 
                        size="sm" 
                        :show-name="true"
                        wire:key="avatar-{{ $prompt->id }}-{{ $prompt->user->id }}"
                    />
                </div>
            @endif
            
            <p class="text-zinc-600 dark:text-zinc-400 text-sm mb-4 line-clamp-3 flex-1">
                {{ $prompt->description ?? Str::limit($prompt->content, $contentLimit) }}
            </p>
            
            <!-- Tags and Categories -->
            @if($showCategory || $showPlatforms || $showModels || $showTags)
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($showCategory && $prompt->category)
                    <div>
                        <flux:badge color="zinc" size="sm" wire:key="category-{{ $prompt->id }}-{{ $prompt->user->id }}">{{ $prompt->category->name }}</flux:badge>
                    </div>
                    @endif

                    @if($showPlatforms)
                        @foreach($prompt->platforms->take($platformLimit) as $platform)
                        <div>
                            <flux:badge color="blue" size="sm" wire:key="platform-{{ $prompt->id }}-{{ $platform->id }}">{{ $platform->name }}</flux:badge>
                        </div>
                        @endforeach
                    @endif
                    
                    @if($showModels)
                        @foreach($prompt->aiModels->take($modelLimit) as $model)
                        <div>
                            <flux:badge color="green" size="sm" wire:key="model-{{ $prompt->id }}-{{ $model->id }}">{{ $model->name }}</flux:badge>
                        </div>
                        @endforeach
                    @endif
                    
                    @if($showTags)
                        @foreach($prompt->tags->take($tagLimit) as $tag)
                        <div>
                            <flux:badge color="zinc" size="sm" wire:key="tag-{{ $prompt->id }}-{{ $tag->id }}">{{ $tag->name }}</flux:badge>
                        </div>
                        @endforeach
                    @endif
                </div>
            @endif
            
            @if($showStats)
                <!-- Footer Stats -->
                <div class="flex items-center justify-between text-sm text-zinc-500 dark:text-zinc-400 mt-auto">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center gap-1">
                            <flux:icon.heart class="size-4" />
                            {{ $prompt->likes->count() ?? 0 }}
                        </span>
                        <span class="flex items-center gap-1">
                            <flux:icon.eye class="size-4" />
                            {{ views($prompt)->count() }}
                        </span>
                    </div>
                    <div class="text-xs text-zinc-400">
                        {{ $prompt->created_at->diffForHumans() }}
                    </div>
                </div>
            @endif
        </article>
    @endif
</div>