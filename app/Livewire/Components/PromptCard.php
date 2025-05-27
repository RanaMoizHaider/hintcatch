<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Str;

class PromptCard extends Component
{
    public $prompt;
    public $showUser = true;
    public $showStats = true;
    public $showFeaturedBadge = true;
    public $showCategory = true;
    public $showPlatforms = true;
    public $showModels = true;
    public $showTags = true;
    public $tagLimit = 2;
    public $platformLimit = 2;
    public $modelLimit = 1;
    public $contentLimit = 150;
    public $linkable = true;
    public $layout = 'card'; // 'card', 'list', 'horizontal'

    public function mount(
        $prompt,
        $showUser = true,
        $showStats = true,
        $showFeaturedBadge = true,
        $showCategory = true,
        $showPlatforms = true,
        $showModels = true,
        $showTags = true,
        $tagLimit = 2,
        $platformLimit = 2,
        $modelLimit = 1,
        $contentLimit = 150,
        $linkable = true,
        $layout = 'card'
    ) {
        $this->prompt = $prompt;
        $this->showUser = $showUser;
        $this->showStats = $showStats;
        $this->showFeaturedBadge = $showFeaturedBadge;
        $this->showCategory = $showCategory;
        $this->showPlatforms = $showPlatforms;
        $this->showModels = $showModels;
        $this->showTags = $showTags;
        $this->tagLimit = $tagLimit;
        $this->platformLimit = $platformLimit;
        $this->modelLimit = $modelLimit;
        $this->contentLimit = $contentLimit;
        $this->linkable = $linkable;
        $this->layout = $layout;
    }

    public function render()
    {
        return view('livewire.components.prompt-card');
    }
}
