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

    public function with(): array
    {
        return [
            'title' => 'Create Prompt',
            'categories' => Category::where(function ($query) {
                    $query->where('is_approved', true)
                          ->orWhere('user_id', auth()->id());
                })->orderBy('name')->get(),
            'aiModels' => AiModel::with('provider')
                ->where(function ($query) {
                    $query->where('is_approved', true)
                          ->orWhere('user_id', auth()->id());
                })
                ->orderBy('name')->get(),
            'platforms' => Platform::where(function ($query) {
                    $query->where('is_approved', true)
                          ->orWhere('user_id', auth()->id());
                })->orderBy('name')->get(),
            'providers' => Provider::where(function ($query) {
                    $query->where('is_approved', true)
                          ->orWhere('user_id', auth()->id());
                })->orderBy('name')->get(),
        ];
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

    <!-- Form -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
        <form wire:submit="save" class="space-y-6">
            <!-- Title -->
            <flux:field>
                <flux:label for="title" badge="Required">Prompt Title</flux:label>
                <flux:input 
                    wire:model="title" 
                    id="title" 
                    placeholder="Enter a descriptive title for your prompt"
                    required
                />
                <flux:error name="title" />
            </flux:field>

            <!-- Description -->
            <flux:field>
                <flux:label for="description" badge="Optional">Description</flux:label>
                <flux:textarea 
                    wire:model="description" 
                    id="description" 
                    rows="3"
                    placeholder="Brief description of what this prompt does..."
                />
                <flux:error name="description" />
            </flux:field>

            <!-- Content -->
            <flux:field>
                <flux:label for="content" badge="Required">Prompt Content</flux:label>
                <flux:textarea 
                    wire:model="content" 
                    id="content" 
                    rows="8"
                    placeholder="Enter your prompt content here..."
                    required
                />
                <flux:error name="content" />
            </flux:field>

            <!-- Category -->
            <flux:field>
                <flux:label for="category_id" badge="Required">Category</flux:label>
                <flux:select wire:model.live="category_id" id="category_id" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>
                <div class="flex space-x-2 mt-2">
                    <flux:input wire:model="newCategory" wire:keydown.enter.prevent="suggestCategory" placeholder="Or suggest a new one" class="flex-1" />
                    <flux:button type="button" wire:click="suggestCategory" variant="outline">Suggest</flux:button>
                </div>
                <flux:error name="category_id" />
                <flux:error name="newCategory" />
            </flux:field>

            <!-- AI Models -->
            <flux:field>
                <flux:label badge="Optional">Compatible AI Models</flux:label>
                <flux:description>Select which AI models work well with this prompt</flux:description>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($aiModels as $model)
                        <label class="flex items-center space-x-2 p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer">
                            <flux:checkbox 
                                wire:model="selectedAiModels" 
                                value="{{ $model->id }}"
                            />
                            <div class="flex-1">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $model->name }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $model->provider->name }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
                <div class="flex flex-col space-y-2 mt-2 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Don't see the model you're looking for?</div>
                    <flux:description>You can suggest a new AI model below.</flux:description>
                    <div class="flex space-x-2 pt-2">
                        <flux:input wire:model="newAiModel" placeholder="Suggest a new AI Model" class="flex-1" />
                        <flux:select wire:model.live="newAiModelProviderId" class="flex-1">
                            <option value="">Select a Provider</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                            @endforeach
                            <option value="new">New Provider</option>
                        </flux:select>
                    </div>
                    @if($showNewProviderInput)
                    <div class="flex space-x-2">
                        <flux:input wire:model="newProvider" placeholder="Enter new provider name" class="flex-1" required />
                    </div>
                    @endif
                    <div class="flex justify-end pt-2">
                        <flux:button type="button" wire:click="suggestAiModel" variant="outline">Suggest Model</flux:button>
                    </div>
                </div>
                <flux:error name="newAiModel" />
                <flux:error name="newAiModelProviderId" />
                <flux:error name="newProvider" />
            </flux:field>

            <!-- Platforms -->
            <flux:field>
                <flux:label badge="Optional">Compatible Platforms</flux:label>
                <flux:description>Select which platforms this prompt works on</flux:description>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($platforms as $platform)
                        <label class="flex items-center space-x-2 p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer">
                            <flux:checkbox 
                                wire:model="selectedPlatforms" 
                                value="{{ $platform->id }}"
                            />
                            <div class="flex-1">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $platform->name }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
                <div class="flex space-x-2 mt-2">
                    <flux:input wire:model="newPlatform" wire:keydown.enter.prevent="suggestPlatform" placeholder="Or suggest a new one" class="flex-1" />
                    <flux:button type="button" wire:click="suggestPlatform" variant="outline">Suggest</flux:button>
                </div>
                <flux:error name="newPlatform" />
            </flux:field>

            <!-- Visibility and Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Visibility -->
                <flux:field>
                    <flux:radio.group wire:model="visibility" label="Visibility">
                        <flux:radio value="public" label="Public" description="Everyone can see this prompt" />
                        <flux:radio value="unlisted" label="Unlisted" description="Only accessible with direct link" />
                        <flux:radio value="private" label="Private" description="Only you can see this prompt" />
                    </flux:radio.group>
                    <flux:error name="visibility" />
                </flux:field>

                <!-- Status -->
                <flux:field>
                    <flux:radio.group wire:model="status" label="Status">
                        <flux:radio value="draft" label="Draft" description="Work in progress, not published yet" />
                        <flux:radio value="published" label="Published" description="Ready to share with others" />
                    </flux:radio.group>
                    <flux:error name="status" />
                </flux:field>
            </div>

            <!-- Tags -->
            <flux:field>
                <flux:label badge="Optional">Tags</flux:label>
                <flux:description>Add tags to help others discover your prompt</flux:description>
                <div class="flex space-x-2">
                    <flux:input 
                        wire:model="newTag"
                        wire:keydown.enter.prevent="addTag"
                        placeholder="Enter a tag and press Enter"
                        class="flex-1"
                    />
                    <flux:button type="button" wire:click="addTag" variant="outline">
                        Add Tag
                    </flux:button>
                </div>
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($tags as $index => $tag)
                        <flux:badge color="zinc">
                            {{ $tag }}
                            <flux:badge.close wire:click="removeTag({{ $index }})" />
                        </flux:badge>
                    @endforeach
                </div>
            </flux:field>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button wire:navigate href="{{ route('user.prompts.index') }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit">
                    Create Prompt
                </flux:button>
            </div>
        </form>
    </div>
</div>