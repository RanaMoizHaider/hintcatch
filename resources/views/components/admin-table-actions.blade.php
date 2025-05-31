@props([
    'editRoute',
    'deleteAction' => null,
    'deleteConfirm' => 'Are you sure you want to delete this item?',
    'canDelete' => true
])

<td class="px-6 py-4 text-right space-x-2">
    <div class="flex items-center justify-end space-x-2">
        <!-- Edit Button -->
        <flux:button 
            wire:navigate 
            href="{{ $editRoute }}" 
            size="sm" 
            variant="ghost"
            icon="pencil"
        />
        
        <!-- Delete Button (conditional) -->
        @if($canDelete && $deleteAction)
            <flux:button 
                wire:click="{{ $deleteAction }}"
                wire:confirm="{{ $deleteConfirm }}"
                size="sm"
                variant="ghost"
                class="text-red-600 hover:text-red-700"
                icon="trash"
            />
        @endif
    </div>
</td>
