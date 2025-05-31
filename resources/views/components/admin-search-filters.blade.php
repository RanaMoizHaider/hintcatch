@props([
    'searchModel' => 'search',
    'searchPlaceholder' => 'Search...',
    'filters' => []
])

<div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-6 mb-6">
    <!-- Left side: Search -->
    <div class="w-full md:flex-1 md:max-w-md">
        <flux:input 
            wire:model.live.debounce.300ms="{{ $searchModel }}"
            placeholder="{{ $searchPlaceholder }}"
            icon="magnifying-glass"
            clearable
        />
    </div>

    <!-- Spacer - only visible on large screens and up -->
    <div class="hidden 2xl:flex flex-1"></div>

    <!-- Right side: Filters -->
    @if(count($filters) > 0)
        <div class="flex flex-col md:flex-row md:items-center gap-3 md:gap-4">
            @foreach($filters as $filter)
                <flux:select wire:model.live="{{ $filter['model'] }}" class="w-full md:min-w-40">
                    <option value="">{{ $filter['placeholder'] ?? 'All' }}</option>
                    @if(isset($filter['options']))
                        @foreach($filter['options'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    @elseif(isset($filter['items']))
                        @foreach($filter['items'] as $item)
                            <option value="{{ $item->{$filter['value_field'] ?? 'id'} }}">
                                {{ $item->{$filter['label_field'] ?? 'name'} }}
                            </option>
                        @endforeach
                    @endif
                </flux:select>
            @endforeach
        </div>
    @endif
</div>
