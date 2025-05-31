<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new
#[Layout('components.layouts.app')]
class extends Component {
    public $name = '';
    public $description = '';
    public $parent_id = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:categories,name',
        'description' => 'nullable|string|max:1000',
        'parent_id' => 'nullable|exists:categories,id',
    ];

    public function save()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'parent_id' => $this->parent_id,
        ]);

        session()->flash('success', 'Category created successfully.');
        return redirect()->route('admin.categories.index');
    }

    public function with()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        
        return [
            'title' => 'Create Category',
            'parentCategories' => $parentCategories,
        ];
    }
}; ?>

<div class="space-y-6">
    <x-page-heading 
    title="Create Category" 
    description="Add a new category for organizing prompts"
>
    <x-slot name="actions">
        <flux:button wire:navigate href="{{ route('admin.categories.index') }}" variant="ghost" icon="arrow-left">
            Back to Categories
        </flux:button>
    </x-slot>
</x-page-heading>

    <!-- Form -->
    <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <flux:field>
                    <flux:label>Category Name</flux:label>
                    <flux:input 
                        wire:model.live="name" 
                        placeholder="Enter category name"
                        required
                    />
                    <flux:error name="name" />
                </flux:field>

                <!-- Parent Category -->
                <flux:field>
                    <flux:label>Parent Category (optional)</flux:label>
                    <flux:select wire:model="parent_id">
                        <option value="">No Parent Category</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="parent_id" />
                </flux:field>
            </div>

            <!-- Description -->
            <flux:field>
                <flux:label>Description (optional)</flux:label>
                <flux:textarea 
                    wire:model="description"
                    rows="3"
                    placeholder="Enter category description"
                />
                <flux:error name="description" />
            </flux:field>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 border-t border-zinc-200 dark:border-zinc-700 pt-6">
                <flux:button wire:navigate href="{{ route('admin.categories.index') }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary" icon="plus">
                    Create Category
                </flux:button>
            </div>
        </form>
    </div>

    <!-- Preview -->
    @if($name)
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Preview</flux:heading>
            <div class="flex items-center space-x-3 rounded-lg border border-zinc-100 dark:border-zinc-600 p-4">
                <div class="flex size-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-700">
                    <flux:icon.folder class="size-5 text-zinc-500" />
                </div>
                <div>
                    <flux:text class="font-medium">{{ $name }}</flux:text>
                    @if($description)
                        <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400">{{ $description }}</flux:text>
                    @endif
                    @if($parent_id && $parentCategories->find($parent_id))
                        <flux:text size="sm" class="text-zinc-500">Parent: {{ $parentCategories->find($parent_id)->name }}</flux:text>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
