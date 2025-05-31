<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Platform;
use Illuminate\Support\Str;

new
#[Layout('components.layouts.app')]
class extends Component {
    public $name = '';
    public $description = '';
    public $website = '';
    public $image = '';
    public $color = '';
    public $icon = '';
    public $features = [];
    public $best_practices = [];

    protected $rules = [
        'name' => 'required|string|max:255|unique:platforms,name',
        'description' => 'nullable|string|max:1000',
        'website' => 'nullable|url|max:255',
        'image' => 'nullable|url|max:255',
        'color' => 'nullable|string|max:7',
        'icon' => 'nullable|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        Platform::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'website' => $this->website,
            'image' => $this->image,
            'color' => $this->color,
            'icon' => $this->icon,
            'features' => $this->features,
            'best_practices' => $this->best_practices,
        ]);

        session()->flash('success', 'Platform created successfully.');
        return redirect()->route('admin.platforms.index');
    }

    public function with(): array
    {
        return [
            'title' => 'Create Platform'
        ];
    }
}; ?>

<div class="max-w-2xl mx-auto">
    <x-page-heading 
    title="Create Platform" 
    description="Add a new platform to the system"
/>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 p-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <flux:field>
                        <flux:label>Platform Name *</flux:label>
                        <flux:input wire:model="name" placeholder="e.g., WordPress, React, Next.js" />
                        <flux:error name="name" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Website</flux:label>
                        <flux:input wire:model="website" type="url" placeholder="https://example.com" />
                        <flux:error name="website" />
                    </flux:field>
                </div>

                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label>Description</flux:label>
                        <flux:textarea wire:model="description" placeholder="Brief description of the platform" rows="3" />
                        <flux:error name="description" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Image URL</flux:label>
                        <flux:input wire:model="image" type="url" placeholder="https://example.com/image.png" />
                        <flux:error name="image" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Brand Color</flux:label>
                        <flux:input wire:model="color" type="color" />
                        <flux:error name="color" />
                    </flux:field>
                </div>

                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label>Icon</flux:label>
                        <flux:input wire:model="icon" placeholder="e.g., fa-wordpress, heroicon-react" />
                        <flux:error name="icon" />
                    </flux:field>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button variant="ghost" href="{{ route('admin.platforms.index') }}">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="plus">
                Create Platform
            </flux:button>
        </div>
    </form>
</div>
