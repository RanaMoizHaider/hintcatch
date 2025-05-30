@props([
    'items',
    'emptyIcon' => 'document-text',
    'emptyTitle' => 'No items found',
    'emptyDescription' => 'No items match your current filters.',
    'createRoute' => null,
    'createText' => 'Add Item'
])

<div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 overflow-hidden">
    @if($items->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    {{ $header }}
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-900 dark:divide-zinc-700">
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $items->links() }}
            </div>
        @endif
    @else
        <x-admin-table-empty 
            :icon="$emptyIcon"
            :title="$emptyTitle"
            :description="$emptyDescription"
            :createRoute="$createRoute"
            :createText="$createText"
        />
    @endif
</div>
