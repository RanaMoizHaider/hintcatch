<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Provider;
use Illuminate\Support\Str;

new
#[Layout('components.layouts.app')]
class extends Component {
    public $name = '';
    public $description = '';
    public $website = '';
    public $api_endpoint = '';
    public $logo = '';
    public $color = '';
    public $is_active = true;
    public $supported_features = [];
    public $pricing_model = [];

    protected $rules = [
        'name' => 'required|string|max:255|unique:providers,name',
        'description' => 'nullable|string|max:1000',
        'website' => 'nullable|url|max:255',
        'api_endpoint' => 'nullable|url|max:255',
        'logo' => 'nullable|url|max:255',
        'color' => 'nullable|string|max:7',
        'is_active' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        Provider::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'website' => $this->website,
            'api_endpoint' => $this->api_endpoint,
            'logo' => $this->logo,
            'color' => $this->color,
            'is_active' => $this->is_active,
            'supported_features' => $this->supported_features,
            'pricing_model' => $this->pricing_model,
        ]);

        session()->flash('success', 'Provider created successfully.');
        return redirect()->route('admin.providers.index');
    }

    public function with(): array
    {
        return [
            'title' => 'Create Provider'
        ];
    }
}; ?>

<div class="max-w-2xl mx-auto">
    <x-page-heading 
    title="Create Provider" 
    description="Add a new AI provider to the system"
/>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <flux:field>
                        <flux:label badge="Required">Provider Name</flux:label>
                        <flux:input wire:model="name" placeholder="e.g., OpenAI, Anthropic" />
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
                        <flux:textarea wire:model="description" placeholder="Brief description of the provider" rows="3" />
                        <flux:error name="description" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label badge="Optional">API Endpoint</flux:label>
                        <flux:input wire:model="api_endpoint" type="url" placeholder="https://api.example.com" />
                        <flux:error name="api_endpoint" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label badge="Optional">Logo URL</flux:label>
                        <flux:input wire:model="logo" type="url" placeholder="https://example.com/logo.png" />
                        <flux:error name="logo" />
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label badge="Optional">Brand Color</flux:label>
                        <flux:input wire:model="color" type="color" />
                        <flux:error name="color" />
                    </flux:field>
                </div>

                <div class="flex items-center">
                    <flux:checkbox wire:model="is_active" />
                    <flux:label class="ml-2">Provider is active</flux:label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button variant="ghost" href="{{ route('admin.providers.index') }}">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="plus">
                Create Provider
            </flux:button>
        </div>
    </form>
</div>
