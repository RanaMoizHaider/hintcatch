<?php

use App\Models\Prompt;
use App\Models\Category;
use App\Models\AiModel;
use App\Models\Platform;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new
#[Layout('components.layouts.app')]
class extends Component {
    public $title = '';
    public $description = '';
    public $content = '';
    public $category_id = '';
    public $visibility = 'public';
    public $status = 'draft';
    public $selectedAiModels = [];
    public $selectedPlatforms = [];
    public $tags = [];
    public $newTag = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'content' => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'visibility' => 'required|in:public,private,unlisted',
        'status' => 'required|in:draft,published',
    ];

    public function addTag()
    {
        if ($this->newTag && !in_array($this->newTag, $this->tags)) {
            $this->tags[] = $this->newTag;
            $this->newTag = '';
        }
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function save()
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

        // Attach AI models and platforms
        if (!empty($this->selectedAiModels)) {
            $prompt->aiModels()->attach($this->selectedAiModels);
        }
        
        if (!empty($this->selectedPlatforms)) {
            $prompt->platforms()->attach($this->selectedPlatforms);
        }

        // Add tags
        if (!empty($this->tags)) {
            $prompt->attachTags($this->tags);
        }

        session()->flash('success', 'Prompt created successfully.');
        return redirect()->route('user.prompts.index');
    }

    public function with()
    {
        $categories = Category::orderBy('name')->get();
        $aiModels = AiModel::with('provider')->orderBy('name')->get();
        $platforms = Platform::orderBy('name')->get();

        return [
            'title' => 'Create Prompt',
            'categories' => $categories,
            'aiModels' => $aiModels,
            'platforms' => $platforms,
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
                <flux:select wire:model="category_id" id="category_id" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="category_id" />
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
                                @if($platform->description)
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $platform->description }}</div>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
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
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($tags as $index => $tag)
                        <flux:badge color="zinc">
                            {{ $tag }}
                            <flux:badge.close wire:click="removeTag({{ $index }})" />
                        </flux:badge>
                    @endforeach
                </div>
            </flux:field>

            <!-- Submit -->
            <div class="flex items-center justify-end space-x-3">
                <flux:button wire:navigate href="{{ route('user.prompts.index') }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check">Create Prompt</flux:button>
            </div>
        </form>
    </div>
</div>
