@props([
    'title',
    'description' => null,
    'actions' => null,
])

<div class="relative mb-4 sm:mb-6 w-full">
    <div class="flex flex-row items-center justify-between gap-3 sm:gap-4">
        <div class="flex-1 min-w-0">
            <flux:heading size="lg" class="sm:text-xl break-words">{{ $title }}</flux:heading>
            @if($description)
                <flux:subheading size="base" class="sm:text-lg mt-1 sm:mt-2 break-words">{{ $description }}</flux:subheading>
            @endif
        </div>
        
        @if($actions ?? false)
            <div class="flex-shrink-0">
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 [&_button]:text-xs [&_button]:sm:text-sm [&_button]:px-2 [&_button]:sm:px-3 [&_button]:py-1 [&_button]:sm:py-2 [&_a]:text-xs [&_a]:sm:text-sm [&_a]:px-2 [&_a]:sm:px-3 [&_a]:py-1 [&_a]:sm:py-2">
                    {{ $actions }}
                </div>
            </div>
        @endif
    </div>
    
    <div class="mt-4 sm:mt-6">
        <flux:separator variant="subtle" />
    </div>
</div>
