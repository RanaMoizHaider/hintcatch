@props([
    'title',
    'description' => null,
    'actions' => null,
])

<div class="relative mb-6 w-full">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex-1">
            <flux:heading size="xl" level="1">{{ $title }}</flux:heading>
            @if($description)
                <flux:subheading size="lg" class="mb-6">{{ $description }}</flux:subheading>
            @endif
        </div>
        
        @if($actions ?? false)
            <div class="flex-shrink-0">
                {{ $actions }}
            </div>
        @endif
    </div>
    
    @if(!$description)
        <div class="mb-6"></div>
    @endif
    
    <flux:separator variant="subtle" />
</div>
