<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Prompt;
use App\Models\Category;
use App\Models\AiModel;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Support\Str;

new
#[Layout('components.layouts.app')]
class extends Component {
    public $title = '';
    public $description = '';
    public $content = '';
    public $category_id = '';
    public $visibility = 'public';
    public $status = 'published';
    public $user_id = '';
    public $selectedAiModels = [];
    public $selectedPlatforms = [];
    public $tags = [];
    public $newTag = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'content' => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'user_id' => 'required|exists:users,id',
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
            'user_id' => $this->user_id,
            'visibility' => $this->visibility,
            'status' => $this->status,
            'tags' => $this->tags,
        ]);

        if (!empty($this->selectedAiModels)) {
            $prompt->aiModels()->attach($this->selectedAiModels);
        }

        if (!empty($this->selectedPlatforms)) {
            $prompt->platforms()->attach($this->selectedPlatforms);
        }

        session()->flash('success', 'Prompt created successfully.');
        return redirect()->route('admin.prompts.index');
    }

    public function with(): array
    {
        $categories = Category::orderBy('name')->get();
        $aiModels = AiModel::with('provider')->orderBy('name')->get();
        $platforms = Platform::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return [
            'title' => 'Create Prompt',
            'categories' => $categories,
            'aiModels' => $aiModels,
            'platforms' => $platforms,
            'users' => $users
        ];
    }
}; ?>

<div>
    <x-page-heading 
        title="Create Prompt" 
        description="Add a new prompt to the system"
    />

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Basic Information</flux:heading>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label badge="Required">Title</flux:label>
                        <flux:input wire:model="title" placeholder="Enter prompt title" />
                        <flux:error name="title" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label badge="Required">Category</flux:label>
                    <flux:select wire:model="category_id" placeholder="Select category">
                        @foreach($categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>

                <flux:field>
                    <flux:label badge="Required">Author</flux:label>
                    <flux:select wire:model="user_id" placeholder="Select author">
                        @foreach($users as $user)
                            <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="user_id" />
                </flux:field>

                <flux:field>
                    <flux:radio.group wire:model="visibility" label="Visibility">
                        <flux:radio value="public" label="Public" description="Everyone can see this prompt" />
                        <flux:radio value="private" label="Private" description="Only you can see this prompt" />
                        <flux:radio value="unlisted" label="Unlisted" description="Only accessible with direct link" />
                    </flux:radio.group>
                </flux:field>

                <flux:field>
                    <flux:radio.group wire:model="status" label="Status">
                        <flux:radio value="draft" label="Draft" description="Work in progress, not yet published" />
                        <flux:radio value="published" label="Published" description="Ready for public viewing" />
                    </flux:radio.group>
                </flux:field>

                <div class="md:col-span-2">
                    <flux:field>
                        <flux:label badge="Optional">Description</flux:label>
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
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Tags</flux:heading>
            
            <div class="space-y-4">
                <div class="flex gap-2">
                    <flux:input wire:model="newTag" placeholder="Add a tag" wire:keydown.enter.prevent="addTag" />
                    <flux:button type="button" wire:click="addTag" variant="ghost" icon="plus">
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
                    <div class="space-y-2 max-h-48 overflow-y-auto border border-zinc-200 rounded-lg p-3 dark:border-zinc-700">
                        @foreach($aiModels as $model)
                            <label class="flex items-center hover:bg-zinc-50 dark:hover:bg-zinc-800 p-2 rounded">
                                <flux:checkbox wire:model="selectedAiModels" value="{{ $model->id }}" class="mr-3" />
                                <flux:text size="sm">{{ $model->name }} ({{ $model->provider->name }})</flux:text>
                            </label>
                        @endforeach
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label>Target Platforms</flux:label>
                    <div class="space-y-2 max-h-48 overflow-y-auto border border-zinc-200 rounded-lg p-3 dark:border-zinc-700">
                        @foreach($platforms as $platform)
                            <label class="flex items-center hover:bg-zinc-50 dark:hover:bg-zinc-800 p-2 rounded">
                                <flux:checkbox wire:model="selectedPlatforms" value="{{ $platform->id }}" class="mr-3" />
                                <flux:text size="sm">{{ $platform->name }}</flux:text>
                            </label>
                        @endforeach
                    </div>
                </flux:field>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button variant="ghost" href="{{ route('admin.prompts.index') }}">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" icon="plus">
                Create Prompt
            </flux:button>
        </div>
    </form>
</div>
