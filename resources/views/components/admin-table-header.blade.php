@props([
    'sortBy' => null,
    'sortDirection' => 'asc',
    'field' => null,
    'sortable' => false
])

<th {{ $attributes->merge(['class' => 'px-6 py-3 text-left']) }}>
    @if($sortable && $field)
        <button wire:click="sort('{{ $field }}')" class="flex items-center space-x-1 text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider hover:text-zinc-700 dark:hover:text-zinc-200">
            <span>{{ $slot }}</span>
            @if($sortBy === $field)
                <flux:icon variant="micro" name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" />
            @endif
        </button>
    @else
        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
            {{ $slot }}
        </span>
    @endif
</th>
