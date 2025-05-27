@props([
    'type' => 'cards', // 'cards', 'list', 'skeleton', 'spinner', 'pattern'
    'count' => 6,
    'columns' => 3,
    'class' => '',
])

@php
    $gridClass = match($columns) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
        5 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5',
        6 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6',
        default => is_string($columns) ? $columns : 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
    };
@endphp

<div {{ $attributes->merge(['class' => $class]) }}>
    @if($type === 'cards')
        <div class="grid {{ $gridClass }} gap-6">
            @for($i = 0; $i < $count; $i++)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 animate-pulse">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 bg-zinc-200 dark:bg-zinc-700 rounded-full"></div>
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-24"></div>
                    </div>
                    <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded mb-2"></div>
                    <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-4/5 mb-4"></div>
                    <div class="space-y-2 mb-4">
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-full"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2"></div>
                    </div>
                    <div class="flex gap-2 mb-4">
                        <div class="h-6 bg-zinc-200 dark:bg-zinc-700 rounded-full w-16"></div>
                        <div class="h-6 bg-zinc-200 dark:bg-zinc-700 rounded-full w-20"></div>
                    </div>
                    <div class="flex justify-between">
                        <div class="flex gap-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-8"></div>
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-8"></div>
                        </div>
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-16"></div>
                    </div>
                </div>
            @endfor
        </div>
    @elseif($type === 'list')
        <div class="space-y-4">
            @for($i = 0; $i < $count; $i++)
                <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 animate-pulse">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 bg-zinc-200 dark:bg-zinc-700 rounded-lg flex-shrink-0"></div>
                        <div class="flex-1">
                            <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded mb-2"></div>
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    @elseif($type === 'pattern')
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-8 animate-pulse">
            <div class="relative h-64 overflow-hidden rounded-lg">
                <x-placeholder-pattern class="absolute inset-0 opacity-10 stroke-zinc-400 dark:stroke-zinc-600" />
                <div class="relative z-10 flex items-center justify-center h-full">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-zinc-200 dark:bg-zinc-700 rounded-full mx-auto mb-4"></div>
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-32 mx-auto mb-2"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-24 mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($type === 'skeleton')
        <div class="animate-pulse">
            {{ $slot }}
        </div>
    @elseif($type === 'spinner')
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-zinc-600"></div>
        </div>
    @endif
</div>