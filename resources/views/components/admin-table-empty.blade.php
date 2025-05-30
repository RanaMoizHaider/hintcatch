@props([
    'icon' => 'document-text',
    'title' => 'No items found',
    'description' => 'No items match your current filters.',
    'createRoute' => null,
    'createText' => 'Add Item'
])

<div class="px-6 py-12 text-center">
    <flux:icon name="{{ $icon }}" variant="outline" class="mx-auto h-12 w-12 text-zinc-400" />
    <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">{{ $title }}</h3>
    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
    
    @if($createRoute)
        <div class="mt-6">
            <flux:button href="{{ $createRoute }}" variant="primary" icon="plus" wire:navigate>
                {{ $createText }}
            </flux:button>
        </div>
    @endif
</div>
