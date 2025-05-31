<?php

use App\Models\AiModel;
use App\Models\Category;
use App\Models\Platform;
use App\Models\Prompt;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.app')]
class extends Component {
    public Prompt $prompt;
    public $title;
    public $content;
    public $category_id;
    public $ai_model_id;
    public $platform_id;
    public $is_public = false;
    public $is_favorite = false;
    public $tags = '';

    public function mount(Prompt $prompt)
    {
        // Ensure user can only edit their own prompts
        if ($prompt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this prompt.');
        }

        $this->prompt = $prompt;
        $this->title = $prompt->title;
        $this->content = $prompt->content;
        $this->category_id = $prompt->category_id;
        $this->ai_model_id = $prompt->ai_model_id;
        $this->platform_id = $prompt->platform_id;
        $this->is_public = $prompt->is_public;
        $this->is_favorite = $prompt->is_favorite;
        $this->tags = $prompt->tags->pluck('name')->implode(', ');
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'ai_model_id' => 'required|exists:ai_models,id',
            'platform_id' => 'required|exists:platforms,id',
            'is_public' => 'boolean',
            'is_favorite' => 'boolean',
            'tags' => 'nullable|string|max:500',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->prompt->update([
            'title' => $this->title,
            'content' => $this->content,
            'category_id' => $this->category_id,
            'ai_model_id' => $this->ai_model_id,
            'platform_id' => $this->platform_id,
            'is_public' => $this->is_public,
            'is_favorite' => $this->is_favorite,
        ]);

        // Handle tags
        if ($this->tags) {
            $tagNames = array_map('trim', explode(',', $this->tags));
            $tagNames = array_filter($tagNames); // Remove empty strings
            $this->prompt->syncTagsWithType($tagNames, 'default');
        } else {
            $this->prompt->syncTagsWithType([], 'default');
        }

        session()->flash('message', 'Prompt updated successfully!');
        return redirect()->route('user.prompts.index');
    }

    public function duplicate()
    {
        $duplicatedPrompt = $this->prompt->replicate();
        $duplicatedPrompt->title = $this->prompt->title . ' (Copy)';
        $duplicatedPrompt->user_id = Auth::id();
        $duplicatedPrompt->save();

        // Copy tags
        $tagNames = $this->prompt->tags->pluck('name')->toArray();
        if (!empty($tagNames)) {
            $duplicatedPrompt->syncTagsWithType($tagNames, 'default');
        }

        session()->flash('message', 'Prompt duplicated successfully!');
        return redirect()->route('user.prompts.edit', $duplicatedPrompt);
    }

    public function with()
    {
        $categories = Category::orderBy('name')->get();
        $aiModels = AiModel::with('provider')->orderBy('name')->get();
        $platforms = Platform::orderBy('name')->get();

        return [
            'title' => 'Edit Prompt',
            'categories' => $categories,
            'aiModels' => $aiModels,
            'platforms' => $platforms,
        ];
    }
}; ?>

<div>
    <x-page-heading 
        title="Edit Prompt" 
        description="Update your prompt details"
    >
        <x-slot name="actions">
            <div class="flex items-center space-x-3">
                <flux:button wire:click="duplicate" variant="ghost" icon="document-duplicate">
                    Duplicate
                </flux:button>
                <flux:button wire:navigate href="{{ route('user.prompts.index') }}" variant="ghost" icon="arrow-left">
                    Back to Prompts
                </flux:button>
            </div>
        </x-slot>
    </x-page-heading>

    <!-- Form -->
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
        <form wire:submit="save" class="space-y-6">
            <!-- Title -->
            <div>
                <flux:label for="title">Prompt Title</flux:label>
                <flux:input 
                    wire:model="title" 
                    id="title" 
                    placeholder="Enter a descriptive title for your prompt"
                    required
                />
                @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Content -->
            <div>
                <flux:label for="content">Prompt Content</flux:label>
                <flux:textarea 
                    wire:model="content" 
                    id="content" 
                    rows="6"
                    placeholder="Enter your prompt content here..."
                    required
                />
                @error('content') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                <flux:text size="sm" class="mt-1 text-zinc-500 dark:text-zinc-400">
                    Write clear, specific instructions for the AI model.
                </flux:text>
            </div>

            <!-- Category, AI Model, Platform -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <flux:label for="category_id">Category</flux:label>
                    <flux:select wire:model="category_id" id="category_id" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </flux:select>
                    @error('category_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <flux:label for="ai_model_id">AI Model</flux:label>
                    <flux:select wire:model="ai_model_id" id="ai_model_id" required>
                        <option value="">Select an AI model</option>
                        @foreach($aiModels as $aiModel)
                            <option value="{{ $aiModel->id }}">
                                {{ $aiModel->name }} ({{ $aiModel->provider->name }})
                            </option>
                        @endforeach
                    </flux:select>
                    @error('ai_model_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <flux:label for="platform_id">Platform</flux:label>
                    <flux:select wire:model="platform_id" id="platform_id" required>
                        <option value="">Select a platform</option>
                        @foreach($platforms as $platform)
                            <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                        @endforeach
                    </flux:select>
                    @error('platform_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Tags -->
            <div>
                <flux:label for="tags">Tags (optional)</flux:label>
                <flux:input 
                    wire:model="tags" 
                    id="tags" 
                    placeholder="Enter tags separated by commas (e.g., creative, marketing, technical)"
                />
                @error('tags') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                <flux:text size="sm" class="mt-1 text-zinc-500 dark:text-zinc-400">
                    Separate tags with commas to help organize and find your prompts.
                </flux:text>
            </div>

            <!-- Settings -->
            <div class="space-y-4 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">Prompt Settings</flux:heading>
                
                <div class="flex items-center space-x-2">
                    <flux:checkbox wire:model="is_public" id="is_public" />
                    <flux:label for="is_public">Make this prompt public</flux:label>
                </div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                    Public prompts can be discovered and used by other users.
                </flux:text>

                <div class="flex items-center space-x-2">
                    <flux:checkbox wire:model="is_favorite" id="is_favorite" />
                    <flux:label for="is_favorite">Mark as favorite</flux:label>
                </div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                    Favorite prompts are easier to find in your collection.
                </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 border-t border-zinc-200 pt-6">
                <flux:button wire:navigate href="{{ route('user.prompts.index') }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary" icon="check">
                    Update Prompt
                </flux:button>
            </div>
        </form>
    </div>

    <!-- Preview -->
    @if($title && $content)
        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6 mt-6">
            <flux:heading size="lg" class="mb-4 text-zinc-900 dark:text-zinc-100">Preview</flux:heading>
            <div class="rounded-lg border border-zinc-100 dark:border-zinc-700 p-4">
                <div class="mb-3 flex items-center justify-between">
                    <flux:heading size="lg" class="text-zinc-900 dark:text-zinc-100">{{ $title }}</flux:heading>
                    <div class="flex items-center space-x-2">
                        @if($is_favorite)
                            <flux:icon name="heart" class="w-4 h-4 text-yellow-500" />
                        @endif
                        @if($is_public)
                            <flux:badge variant="success">Public</flux:badge>
                        @else
                            <flux:badge variant="outline">Private</flux:badge>
                        @endif
                    </div>
                </div>
                
                <flux:text class="mb-3 text-zinc-700 dark:text-zinc-300">
                    {{ $content }}
                </flux:text>
                
                <div class="flex flex-wrap gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                    @if($category_id && $categories->find($category_id))
                        <span class="flex items-center space-x-1">
                            <flux:icon name="folder" class="w-4 h-4" />
                            <span>{{ $categories->find($category_id)->name }}</span>
                        </span>
                    @endif
                    @if($ai_model_id && $aiModels->find($ai_model_id))
                        <span class="flex items-center space-x-1">
                            <flux:icon name="cpu-chip" class="w-4 h-4" />
                            <span>{{ $aiModels->find($ai_model_id)->name }}</span>
                        </span>
                    @endif
                    @if($platform_id && $platforms->find($platform_id))
                        <span class="flex items-center space-x-1">
                            <flux:icon name="device-tablet" class="w-4 h-4" />
                            <span>{{ $platforms->find($platform_id)->name }}</span>
                        </span>
                    @endif
                </div>

                @if($tags)
                    <div class="mt-3 flex flex-wrap gap-1">
                        @foreach(array_filter(array_map('trim', explode(',', $tags))) as $tag)
                            <flux:badge variant="outline">{{ $tag }}</flux:badge>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
