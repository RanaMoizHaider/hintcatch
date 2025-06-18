<?php

use App\Models\Prompt;
use App\Models\Category;
use App\Models\AiModel;
use App\Models\Platform;
use App\Models\Provider;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new
#[Layout('components.layouts.app')]
class extends Component {
    public string $title = '';
    public string $description = '';
    public string $content = '';
    public ?int $category_id = null;
    public string $visibility = 'public';
    public string $status = 'draft';
    public array $selectedAiModels = [];
    public array $selectedPlatforms = [];
    public array $tags = [];
    public string $newTag = '';
    public string $aiModelSearch = '';
    public string $platformSearch = '';

    // Suggestion properties
    public string $newCategory = '';
    public string $newPlatform = '';
    public string $newAiModel = '';
    public $newAiModelProviderId = null;
    public string $newProvider = '';
    public bool $showNewProviderInput = false;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'visibility' => 'required|in:public,private,unlisted',
            'status' => 'required|in:draft,published',
            'selectedAiModels' => 'nullable|array',
            'selectedPlatforms' => 'nullable|array',
            'tags' => 'nullable|array',
        ];
    }

    public function addTag(): void
    {
        if ($this->newTag && !in_array($this->newTag, $this->tags)) {
            $this->tags[] = $this->newTag;
            $this->newTag = '';
        }
    }

    public function removeTag(int $index): void
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function suggestCategory(): void
    {
        $this->validate(['newCategory' => 'required|string|max:255|unique:categories,name']);
        $category = Category::create([
            'name' => $this->newCategory,
            'is_approved' => false,
            'user_id' => auth()->id(),
        ]);
        $this->category_id = $category->id;
        $this->newCategory = '';
        session()->flash('success', 'Category suggested and selected.');
    }

    public function updatedNewAiModelProviderId($value): void
    {
        $this->showNewProviderInput = ($value === 'new');
        if ($this->showNewProviderInput) {
            $this->newAiModelProviderId = null;
        }
    }

    public function suggestAiModel(): void
    {
        $this->validate([
            'newAiModel' => 'required|string|max:255|unique:ai_models,name',
            'newAiModelProviderId' => 'required_if:showNewProviderInput,false|nullable|exists:providers,id',
            'newProvider' => 'required_if:showNewProviderInput,true|string|max:255|unique:providers,name',
        ]);

        $providerId = $this->newAiModelProviderId;

        if ($this->showNewProviderInput) {
            $provider = Provider::create([
                'name' => $this->newProvider,
                'is_approved' => false,
                'user_id' => auth()->id()
            ]);
            $providerId = $provider->id;
        }

        $aiModel = AiModel::create([
            'name' => $this->newAiModel,
            'provider_id' => $providerId,
            'is_approved' => false,
            'user_id' => auth()->id(),
        ]);

        if (!in_array($aiModel->id, $this->selectedAiModels)) {
            $this->selectedAiModels[] = $aiModel->id;
        }
        
        $this->newAiModel = '';
        $this->newAiModelProviderId = null;
        $this->newProvider = '';
        session()->flash('success', 'AI Model suggested and selected.');
    }

    public function suggestPlatform(): void
    {
        $this->validate(['newPlatform' => 'required|string|max:255|unique:platforms,name']);
        $platform = Platform::create([
            'name' => $this->newPlatform,
            'is_approved' => false,
            'user_id' => auth()->id(),
        ]);
        if (!in_array($platform->id, $this->selectedPlatforms)) {
            $this->selectedPlatforms[] = $platform->id;
        }
        $this->newPlatform = '';
        session()->flash('success', 'Platform suggested and selected.');
    }

    public function save(): void
    {
        $this->validate();

        $prompt = Prompt::create([
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'description' => $this->description,
            'content' => $this->content,
            'category_id' => $this->category_id,
            'user_id' => auth()->id(),
            'visibility' => $this->visibility,
            'status' => $this->status,
        ]);

        $prompt->aiModels()->attach($this->selectedAiModels);
        $prompt->platforms()->attach($this->selectedPlatforms);
        $prompt->attachTags($this->tags);

        session()->flash('success', 'Prompt created successfully.');
        $this->redirect(route('user.prompts.index'), navigate: true);
    }

    public function getCategoriesProperty()
    {
        return Category::where(function ($query) {
            $query->where('is_approved', true)
                  ->orWhere('user_id', auth()->id());
        })->orderBy('name')->get();
    }

    public function getAiModelsProperty()
    {
        $query = AiModel::with('provider')
            ->where(function ($query) {
                $query->where('is_approved', true)
                      ->orWhere('user_id', auth()->id());
            });
        
        if ($this->aiModelSearch) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->aiModelSearch . '%')
                  ->orWhereHas('provider', function($providerQuery) {
                      $providerQuery->where('name', 'like', '%' . $this->aiModelSearch . '%');
                  });
            });
        }
        
        return $query->orderBy('name')->get();
    }

    public function getPlatformsProperty()
    {
        $query = Platform::where(function ($query) {
            $query->where('is_approved', true)
                  ->orWhere('user_id', auth()->id());
        });
        
        if ($this->platformSearch) {
            $query->where('name', 'like', '%' . $this->platformSearch . '%');
        }
        
        return $query->orderBy('name')->get();
    }

    public function getProvidersProperty()
    {
        return Provider::where(function ($query) {
            $query->where('is_approved', true)
                  ->orWhere('user_id', auth()->id());
        })->orderBy('name')->get();
    }
}; ?>

<div>
    <x-page-heading 
        title="Create New Prompt" 
        description="Share your prompt with the community"
    >
        <x-slot name="actions">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('user.prompts.index') }}">My Prompts</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Create</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </x-slot>
    </x-page-heading>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Basic Information</flux:heading>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label badge="Required">Title</flux:label>
                    <flux:input wire:model="title" placeholder="Enter prompt title" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Required">Category</flux:label>
                    <flux:select wire:model="category_id" placeholder="Select category">
                        @foreach($this->categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>

                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label>Description</flux:label>
                        <flux:textarea wire:model="description" placeholder="Brief description of the prompt" rows="3" />
                        <flux:error name="description" />
                    </flux:field>
                </div>

                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label badge="Required">Content</flux:label>
                        <flux:textarea wire:model="content" placeholder="Enter the prompt content" rows="8" />
                        <flux:error name="content" />
                    </flux:field>
                </div>
            </div>

            <!-- Category Suggestion -->
            <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                <flux:text size="sm" class="font-medium mb-2">Don't see the category you need?</flux:text>
                <div class="flex gap-2">
                    <flux:input 
                        wire:model="newCategory" 
                        wire:keydown.enter.prevent="suggestCategory" 
                        placeholder="Suggest a new category" 
                        class="flex-1"
                    />
                    <flux:button type="button" wire:click="suggestCategory" variant="outline" icon="plus">
                        Suggest
                    </flux:button>
                </div>
                <flux:error name="newCategory" />
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Tags</flux:heading>
            
            <div class="space-y-4">
                <div class="flex gap-2">
                    <flux:input wire:model="newTag" placeholder="Add a tag" wire:keydown.enter.prevent="addTag" />
                    <flux:button type="button" wire:click="addTag" variant="outline" icon="plus">
                        Add
                    </flux:button>
                </div>

                @if(count($tags) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $index => $tag)
                            <flux:badge variant="subtle" class="bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $tag }}
                                <flux:badge.close wire:click="removeTag({{ $index }})" />
                            </flux:badge>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">AI Models & Platforms</flux:heading>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Compatible AI Models</flux:label>
                    <div class="space-y-3">
                        <flux:input 
                            wire:model.live="aiModelSearch" 
                            placeholder="Search AI models..." 
                            icon="magnifying-glass"
                        />
                        <div class="space-y-2 max-h-48 overflow-y-auto border border-zinc-200 rounded-lg p-3 dark:border-zinc-700">
                            @forelse($this->aiModels as $model)
                                <label class="flex items-center hover:bg-zinc-50 dark:hover:bg-zinc-800 p-2 rounded">
                                    <flux:checkbox wire:model="selectedAiModels" value="{{ $model->id }}" class="mr-3" />
                                    <flux:text size="sm">{{ $model->name }} ({{ $model->provider->name }})</flux:text>
                                </label>
                            @empty
                                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 p-2">
                                    @if($aiModelSearch)
                                        No AI models found matching "{{ $aiModelSearch }}"
                                    @else
                                        No AI models available
                                    @endif
                                </flux:text>
                            @endforelse
                        </div>
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label>Target Platforms</flux:label>
                    <div class="space-y-3">
                        <flux:input 
                            wire:model.live="platformSearch" 
                            placeholder="Search platforms..." 
                            icon="magnifying-glass"
                        />
                        <div class="space-y-2 max-h-48 overflow-y-auto border border-zinc-200 rounded-lg p-3 dark:border-zinc-700">
                            @forelse($this->platforms as $platform)
                                <label class="flex items-center hover:bg-zinc-50 dark:hover:bg-zinc-800 p-2 rounded">
                                    <flux:checkbox wire:model="selectedPlatforms" value="{{ $platform->id }}" class="mr-3" />
                                    <flux:text size="sm">{{ $platform->name }}</flux:text>
                                </label>
                            @empty
                                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 p-2">
                                    @if($platformSearch)
                                        No platforms found matching "{{ $platformSearch }}"
                                    @else
                                        No platforms available
                                    @endif
                                </flux:text>
                            @endforelse
                        </div>
                    </div>
                </flux:field>
            </div>
        </div>

        <!-- Suggestions Section -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Can't Find What You Need?</flux:heading>
            <flux:description class="mb-6">Suggest new AI models or platforms to help expand our database.</flux:description>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- AI Model Suggestion -->
                <div class="space-y-4">
                    <flux:heading size="md" class="text-zinc-700 dark:text-zinc-300">Suggest AI Model</flux:heading>
                    <flux:field>
                        <flux:label>Model Name</flux:label>
                        <flux:input wire:model="newAiModel" placeholder="e.g., GPT-4 Turbo" />
                        <flux:error name="newAiModel" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:label>Provider</flux:label>
                        <flux:select wire:model.live="newAiModelProviderId" placeholder="Select or add provider">
                            @foreach($this->providers as $provider)
                                <flux:select.option value="{{ $provider->id }}">{{ $provider->name }}</flux:select.option>
                            @endforeach
                            <flux:select.option value="new">+ Add New Provider</flux:select.option>
                        </flux:select>
                        <flux:error name="newAiModelProviderId" />
                    </flux:field>
                    
                    @if($showNewProviderInput)
                        <flux:field>
                            <flux:label>New Provider Name</flux:label>
                            <flux:input wire:model="newProvider" placeholder="e.g., OpenAI" />
                            <flux:error name="newProvider" />
                        </flux:field>
                    @endif
                    
                    <flux:button type="button" wire:click="suggestAiModel" variant="outline" icon="plus" class="w-full">
                        Suggest AI Model
                    </flux:button>
                </div>

                <!-- Platform Suggestion -->
                <div class="space-y-4">
                    <flux:heading size="md" class="text-zinc-700 dark:text-zinc-300">Suggest Platform</flux:heading>
                    <flux:field>
                        <flux:label>Platform Name</flux:label>
                        <flux:input 
                            wire:model="newPlatform" 
                            wire:keydown.enter.prevent="suggestPlatform" 
                            placeholder="e.g., Claude Console" 
                        />
                        <flux:error name="newPlatform" />
                    </flux:field>
                    
                    <flux:button type="button" wire:click="suggestPlatform" variant="outline" icon="plus" class="w-full">
                        Suggest Platform
                    </flux:button>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Visibility & Status</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:radio.group wire:model="visibility" label="Visibility">
                        <flux:radio value="public" label="Public" description="Everyone can see this prompt" />
                        <flux:radio value="private" label="Private" description="Only you can see this prompt" />
                        <flux:radio value="unlisted" label="Unlisted" description="Only accessible with direct link" />
                    </flux:radio.group>
                </flux:field>

                <flux:field>
                    <flux:radio.group wire:model="status" label="Status">
                        <flux:radio value="published" label="Published" description="Ready for public viewing" />
                        <flux:radio value="draft" label="Draft" description="Work in progress, not yet published" />
                    </flux:radio.group>
                </flux:field>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button variant="outline" href="{{ route('user.prompts.index') }}">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="plus">
                Create Prompt
            </flux:button>
        </div>
    </form>
</div>