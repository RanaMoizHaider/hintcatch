@props([
    'columns' => 3,
    'gap' => 'gap-6',
    'class' => '',
])

@php
    $gridClasses = is_numeric($columns) 
        ? "grid-cols-1 md:grid-cols-2 lg:grid-cols-{$columns}"
        : $columns;
@endphp

<div class="grid {{ $gridClasses }} {{ $gap }} {{ $class }}">
    {{ $slot }}
</div>