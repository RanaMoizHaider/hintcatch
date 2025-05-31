<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\AiModel;
use App\Models\Provider;
use Illuminate\Support\Str;

new
#[Layout('components.layouts.app')]
class extends Component {
    public $name = '';
    public $description = '';
    public $provider_id = '';
    public $image = '';
    public $color = '';
    public $icon = '';
    public $release_date = '';
    public $features = [];

    protected $rules = [
        'name' => 'required|string|max:255|unique:ai_models,name',
        'description' => 'nullable|string|max:1000',
        'provider_id' => 'required|exists:providers,id',
        'image' => 'nullable|url|max:255',
        'color' => 'nullable|string|max:7',
        'icon' => 'nullable|string|max:255',
        'release_date' => 'nullable|date',
    ];

    public function save()
    {
        $this->validate();

        AiModel::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'provider_id' => $this->provider_id,
            'image' => $this->image,
            'color' => $this->color,
            'icon' => $this->icon,
            'release_date' => $this->release_date,
            'features' => $this->features,
        ]);

        session()->flash('success', 'AI Model created successfully.');
        return redirect()->route('admin.ai-models.index');
    }

    public function with(): array
    {
        $providers = Provider::where('is_active', true)->orderBy('name')->get();
        
        return [
            'title' => 'Create AI Model',
            'providers' => $providers
        ];
    }
}; ?>

<div class="max-w-2xl mx-auto">
    <x-page-heading 
        title="Create AI Model" 
        description="Add a new AI model to the system"
    />

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 p-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <flux:field>
                        <flux:label badge="Required">Model Name</flux:label>
                        <flux:input wire:model="name" placeholder="e.g., GPT-4, Claude 3.5" />
                        <flux:error name="name" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label badge="Required">Provider</flux:label>
                        <flux:select wire:model="provider_id" placeholder="Select provider">
                            @foreach($providers as $provider)
                                <flux:select.option value="{{ $provider->id }}">{{ $provider->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="provider_id" />
                    </flux:field>
                </div>

                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label badge="Optional">Description</flux:label>
                        <flux:textarea wire:model="description" placeholder="Brief description of the AI model" rows="3" />
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

                <div>
                    <flux:field>
                        <flux:label badge="Optional">Icon</flux:label>
                        <flux:input wire:model="icon" placeholder="e.g., fa-robot, heroicon-cpu" />
                        <flux:error name="icon" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label badge="Optional">Release Date</flux:label>
                        <flux:input wire:model="release_date" type="date" />
                        <flux:error name="release_date" />
                    </flux:field>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button variant="ghost" href="{{ route('admin.ai-models.index') }}">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="plus">
                Create AI Model
            </flux:button>
        </div>
            </flux:button>
        </div>
    </form>
</div>
