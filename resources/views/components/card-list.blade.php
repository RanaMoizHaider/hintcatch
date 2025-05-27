@props([
    'spacing' => 'space-y-6',
    'class' => '',
])

<div class="flex flex-col {{ $spacing }} {{ $class }}">
    {{ $slot }}
</div>