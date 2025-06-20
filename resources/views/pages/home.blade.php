<?php

use App\Models\Prompt;
use App\Models\AiModel;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.web')]
class extends Component {
    public $trendingPrompts;
    public $newestPrompts;
    public $popularPrompts;
    public $featuredPrompts;
    public $viewMode = 'grid';
    public $activeTab = 'trending';
    
    public $totalPrompts;
    public $totalAiModels;
    public $totalUsers;

    public function mount()
    {
        // Frontend - automatically gets only public + published prompts due to global scopes
        $this->trendingPrompts = Prompt::with(['user', 'tags', 'category'])
            ->withViewsCount()
            ->orderByDesc('views_count')
            ->take(12)
            ->get();
            
        $this->newestPrompts = Prompt::with(['user', 'tags', 'category'])
            ->withViewsCount()
            ->latest()
            ->take(12)
            ->get();
            
        $this->popularPrompts = Prompt::with(['user', 'tags', 'category'])
            ->withViewsCount()
            ->withCount('likes')
            ->orderByDesc('likes_count')
            ->take(12)
            ->get();
            
        $this->featuredPrompts = Prompt::featured()
            ->with(['user', 'tags', 'category'])
            ->withViewsCount()
            ->take(6)
            ->get();
            
        // Get real statistics - only count approved/published content for public display
        $this->totalPrompts = Prompt::count(); // Gets only public + published due to global scopes
        $this->totalAiModels = AiModel::count(); // Gets only approved due to global scope
        $this->totalUsers = User::count(); // No global scopes, gets all users
    }
}; ?>

<div>
    <x-slot name="title">Hint Catch - Modern AI Prompts Directory</x-slot>
    
    <!-- Hero Section -->
    <section class="py-16 md:py-24">
        <div class="text-center max-w-4xl mx-auto">
            <!-- Main Heading -->
            <flux:heading size="xl" class="md:text-5xl lg:text-6xl tracking-tight mb-6">
                Share Your Genius Prompts with the World
            </flux:heading>
            
            <!-- Subtitle -->
            <flux:subheading size="lg" class="md:text-xl max-w-3xl mx-auto mb-12 leading-relaxed">
                Join our community of creators—curate your favorite AI prompts, inspire others, and get recognition for your best ideas.
            </flux:subheading>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                <flux:button 
                    href="{{ route('explore') }}" 
                    variant="filled" 
                    size="base"
                    class="px-8 py-4"
                    icon:trailing=arrow-right
                    wire:navigate
                >
                    Explore Prompts
                </flux:button>
                
                @auth
                    <flux:button 
                        href="{{ route('user.prompts.create') }}" 
                        variant="outline" 
                        size="base"
                        class="px-8 py-4"
                        wire:navigate
                    >
                        Submit a Prompt
                    </flux:button>
                @else
                    <flux:button 
                        href="{{ route('register') }}" 
                        variant="outline" 
                        size="base"
                        class="px-8 py-4"
                        wire:navigate
                    >
                        Join Community
                    </flux:button>
                @endauth
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-8 max-w-2xl mx-auto">
                <div class="text-center">
                    <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-100 mb-2">{{ number_format($totalPrompts) }}</flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400">Prompts Shared</flux:text>
                </div>
                <div class="text-center">
                    <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-100 mb-2">{{ number_format($totalAiModels) }}</flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400">AI Models</flux:text>
                </div>
                <div class="text-center">
                    <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-100 mb-2">{{ number_format($totalUsers) }}</flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400">Active Contributors</flux:text>
                </div>
            </div>
        </div>
    </section>

    <div class="container mx-auto px-4 py-8">

        @if($featuredPrompts->count() > 0)
        <section class="mb-12">
            <flux:heading size="2xl" class="mb-6">Featured Prompts</flux:heading>
            <x-card-grid>
                @foreach($featuredPrompts as $prompt)
                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" wire:key="featured-{{ $prompt->id }}" />
                @endforeach
            </x-card-grid>
        </section>
        @endif

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Main Content -->
            <div class="flex-1">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <flux:button.group>
                            <flux:button wire:click="$set('activeTab', 'trending')" variant="{{ $activeTab === 'trending' ? 'filled' : 'subtle' }}" size="sm">Trending</flux:button>
                            <flux:button wire:click="$set('activeTab', 'newest')" variant="{{ $activeTab === 'newest' ? 'filled' : 'subtle' }}" size="sm">Newest</flux:button>
                            <flux:button wire:click="$set('activeTab', 'popular')" variant="{{ $activeTab === 'popular' ? 'filled' : 'subtle' }}" size="sm">Popular</flux:button>
                        </flux:button.group>
                        <flux:button.group>
                            <flux:button 
                                wire:click="$set('viewMode', 'grid')"
                                variant="{{ $viewMode === 'grid' ? 'filled' : 'outline' }}"
                                icon="squares-2x2"
                                size="sm"
                                square
                            />
                            <flux:button 
                                wire:click="$set('viewMode', 'list')"
                                variant="{{ $viewMode === 'list' ? 'filled' : 'outline' }}"
                                icon="bars-3"
                                size="sm"
                                square
                            />
                        </flux:button.group>
                    </div>
                    
                    @if($activeTab === 'trending')
                        @if($viewMode === 'grid')
                            <x-card-grid>
                                @foreach($trendingPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" wire:key="trending-{{ $prompt->id }}" />
                                @endforeach
                            </x-card-grid>
                        @else
                            <div class="space-y-4">
                                @foreach($trendingPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" :layout="'list'" wire:key="trending-list-{{ $prompt->id }}" />
                                @endforeach
                            </div>
                        @endif
                    @elseif($activeTab === 'newest')
                        @if($viewMode === 'grid')
                            <x-card-grid>
                                @foreach($newestPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" wire:key="newest-{{ $prompt->id }}" />
                                @endforeach
                            </x-card-grid>
                        @else
                            <div class="space-y-4">
                                @foreach($newestPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" :layout="'list'" wire:key="newest-list-{{ $prompt->id }}" />
                                @endforeach
                            </div>
                        @endif
                    @elseif($activeTab === 'popular')
                        @if($viewMode === 'grid')
                            <x-card-grid>
                                @foreach($popularPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" wire:key="popular-{{ $prompt->id }}" />
                                @endforeach
                            </x-card-grid>
                        @else
                            <div class="space-y-4">
                                @foreach($popularPrompts as $prompt)
                                    <livewire:components.prompt-card :prompt="$prompt" :linkable="true" :show-featured-badge="false" :layout="'list'" wire:key="popular-list-{{ $prompt->id }}" />
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
