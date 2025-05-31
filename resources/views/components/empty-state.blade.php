@props([
    'title' => 'No results found',
    'description' => 'Try adjusting your search or filter criteria.',
    'icon' => 'search',
    'action' => null,
    'actionText' => null,
    'actionUrl' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12']) }}>
    <div class="flex justify-center mx-auto mb-4 text-zinc-400">
        @if($icon === 'search')
            <flux:icon.magnifying-glass class="size-16" />
        @elseif($icon === 'folder')
            <flux:icon.folder class="size-16" />
        @elseif($icon === 'document' || $icon === 'document-text')
            <flux:icon.document-text class="size-16" />
        @elseif($icon === 'heart')
            <flux:icon.heart class="size-16" />
        @elseif($icon === 'star')
            <flux:icon.star class="size-16" />
        @elseif($icon === 'tag')
            <flux:icon.tag class="size-16" />
        @elseif($icon === 'archive')
            <flux:icon.archive-box class="size-16" />
        @else
            <flux:icon.question-mark-circle class="size-16" />
        @endif
    </div>
    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white mb-2">{{ $title }}</h3>
    <p class="text-zinc-600 dark:text-zinc-400 max-w-sm mx-auto mb-6">{{ $description }}</p>
    
    <?php if ($action || $actionUrl): ?>
        <?php if ($actionUrl): ?>
            <flux:button href="{{ $actionUrl }}" variant="filled">
                {{ $actionText ?? 'Take Action' }}
            </flux:button>
        <?php elseif($action): ?>
            <flux:button {{ $action }} variant="primary">
                {{ $actionText ?? 'Take Action' }}
            </flux:button>
        <?php endif; ?>
    {{ $slot }}
</div>