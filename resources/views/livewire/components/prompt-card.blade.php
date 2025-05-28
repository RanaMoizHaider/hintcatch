<div class="{{ $layout !== 'list' ? 'h-full' : '' }}">
    @if($layout === 'list')
        <!-- List Layout -->
        <article class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-md transition-shadow">
            <div class="flex gap-6">
                <div class="flex-1">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-semibold text-lg leading-tight flex-1 mr-2">
                            @if($linkable)
                                <a href="{{ route('prompts.show', $prompt) }}" class="hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors">
                                    {{ $prompt->title }}
                                </a>
                            @else
                                {{ $prompt->title }}
                            @endif
                        </h3>
                        @if($showFeaturedBadge && $prompt->featured)
                            <livewire:components.badge variant="success" size="sm" text="Featured" class="flex-shrink-0" />
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
                                <livewire:components.badge variant="secondary" size="sm" :text="$prompt->category->name" />
                            @endif
                            
                            @if($showPlatforms)
                                @foreach($prompt->platforms->take($platformLimit) as $platform)
                                    <livewire:components.badge variant="primary" size="sm" :text="$platform->name" />
                                @endforeach
                            @endif
                            
                            @if($showModels)
                                @foreach($prompt->aiModels->take($modelLimit) as $model)
                                    <livewire:components.badge variant="success" size="sm" :text="$model->name" />
                                @endforeach
                            @endif
                            
                            @if($showTags)
                                @foreach($prompt->tags->take($tagLimit) as $tag)
                                    <livewire:components.badge variant="default" size="sm" :text="$tag->name" />
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
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                {{ $prompt->likes->count() ?? 0 }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
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
                                <a href="{{ route('prompts.show', $prompt) }}" class="hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors">
                                    {{ $prompt->title }}
                                </a>
                            @else
                                {{ $prompt->title }}
                            @endif
                        </h3>
                        @if($showFeaturedBadge && $prompt->featured)
                            <livewire:components.badge variant="success" size="sm" text="Featured" class="flex-shrink-0" />
                        @endif
                    </div>
                    
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm line-clamp-1 mb-2">
                        {{ $prompt->description ?? Str::limit($prompt->content, 100) }}
                    </p>
                    
                    <!-- Inline tags -->
                    @if($showCategory || $showPlatforms || $showModels || $showTags)
                        <div class="flex flex-wrap gap-1">
                            @if($showCategory && $prompt->category)
                                <livewire:components.badge variant="secondary" size="xs" :text="$prompt->category->name" />
                            @endif
                            
                            @if($showPlatforms)
                                @foreach($prompt->platforms->take($platformLimit) as $platform)
                                    <livewire:components.badge variant="primary" size="xs" :text="$platform->name" />
                                @endforeach
                            @endif
                            
                            @if($showModels)
                                @foreach($prompt->aiModels->take($modelLimit) as $model)
                                    <livewire:components.badge variant="success" size="xs" :text="$model->name" />
                                @endforeach
                            @endif
                            
                            @if($showTags)
                                @foreach($prompt->tags->take($tagLimit) as $tag)
                                    <livewire:components.badge variant="default" size="xs" :text="$tag->name" />
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
                
                @if($showStats)
                    <!-- Compact stats -->
                    <div class="flex items-center space-x-3 text-sm text-zinc-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            {{ $prompt->likes->count() ?? 0 }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
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
                        <a href="{{ route('prompts.show', $prompt) }}" class="hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors" wire:navigate>
                            {{ $prompt->title }}
                        </a>
                    @else
                        {{ $prompt->title }}
                    @endif
                </h3>
                @if($showFeaturedBadge && $prompt->featured)
                    <livewire:components.badge variant="success" size="sm" text="Featured" class="flex-shrink-0" wire:key="featured-{{ $prompt->id }}" />
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
                        <livewire:components.badge variant="secondary" size="sm" :text="$prompt->category->name" wire:key="category-{{ $prompt->id }}-{{ $prompt->user->id }}" />
                    </div>
                    @endif

                    @if($showPlatforms)
                        @foreach($prompt->platforms->take($platformLimit) as $platform)
                        <div>
                            <livewire:components.badge variant="primary" size="sm" :text="$platform->name" wire:key="platform-{{ $prompt->id }}-{{ $platform->id }}" />
                        </div>
                        @endforeach
                    @endif
                    
                    @if($showModels)
                        @foreach($prompt->aiModels->take($modelLimit) as $model)
                        <div>
                            <livewire:components.badge variant="success" size="sm" :text="$model->name" wire:key="model-{{ $prompt->id }}-{{ $model->id }}" />
                        </div>
                        @endforeach
                    @endif
                    
                    @if($showTags)
                        @foreach($prompt->tags->take($tagLimit) as $tag)
                        <div>
                            <livewire:components.badge variant="default" size="sm" :text="$tag->name" wire:key="tag-{{ $prompt->id }}-{{ $tag->id }}" />
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            {{ $prompt->likes->count() ?? 0 }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
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