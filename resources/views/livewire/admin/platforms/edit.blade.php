<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Platform;
use Illuminate\Support\Str;

new
#[Layout('components.layouts.app')]
class extends Component {
    public Platform $platform;
    public $name = '';
    public $description = '';
    public $website = '';
    public $image = '';
    public $color = '';
    public $icon = '';
    public $features = [];
    public $best_practices = [];

    public function mount(Platform $platform)
    {
        $this->platform = $platform;
        $this->name = $platform->name;
        $this->description = $platform->description;
        $this->website = $platform->website;
        $this->image = $platform->image;
        $this->color = $platform->color;
        $this->icon = $platform->icon;
        $this->features = $platform->features ?? [];
        $this->best_practices = $platform->best_practices ?? [];
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:platforms,name,' . $this->platform->id,
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'image' => 'nullable|url|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:255',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->platform->update([
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

        session()->flash('success', 'Platform updated successfully.');
        return redirect()->route('admin.platforms.index');
    }

    public function with(): array
    {
        return [
            'title' => 'Edit Platform'
        ];
    }
}; ?>

<div class="max-w-2xl mx-auto">
    <x-page-heading 
    title="Edit Platform" 
    description="Update platform information"
/>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 p-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <flux:field>
                        <flux:label badge="Required">Platform Name</flux:label>
                        <flux:input wire:model="name" placeholder="e.g., WordPress, React, Next.js" />
                        <flux:error name="name" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label badge="Optional">Website</flux:label>
                        <flux:input wire:model="website" type="url" placeholder="https://example.com" />
                        <flux:error name="website" />
                    </flux:field>
                </div>

                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label badge="Optional">Description</flux:label>
                        <flux:textarea wire:model="description" placeholder="Brief description of the platform" rows="3" />
                        <flux:error name="description" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label badge="Optional">Image URL</flux:label>
                        <flux:input wire:model="image" type="url" placeholder="https://example.com/image.png" />
                        <flux:error name="image" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label badge="Optional">Brand Color</flux:label>
                        <flux:input wire:model="color" type="color" />
                        <flux:error name="color" />
                    </flux:field>
                </div>

                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label badge="Optional">Icon</flux:label>
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
            <flux:button type="submit" variant="primary" icon="check">
                Update Platform
            </flux:button>
        </div>
    </form>
</div>
