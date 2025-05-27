<?php

namespace App\Livewire\Components;

use Livewire\Component;

class UserAvatar extends Component
{
    public $user;
    public $size = 'md'; // 'xs', 'sm', 'md', 'lg', 'xl'
    public $showName = false;
    public $showOnline = false;
    public $online = false;
    public $linkable = false;
    public $avatarSize = '';
    public $avatarSrc = '';

    public function mount(
        $user,
        $size = 'md',
        $showName = false,
        $showOnline = false,
        $online = false,
        $linkable = false
    ) {
        $this->user = $user;
        $this->size = $size;
        $this->showName = $showName;
        $this->showOnline = $showOnline;
        $this->online = $online;
        $this->linkable = $linkable;
        $this->avatarSize = $this->getAvatarSize();
    }

    public function getAvatarSize()
    {
        $sizeClasses = [
            'xs' => 'w-4 h-4',
            'sm' => 'w-6 h-6', 
            'md' => 'w-8 h-8',
            'lg' => 'w-10 h-10',
            'xl' => 'w-12 h-12'
        ];
        
        return $sizeClasses[$this->size];
    }

    public function render()
    {
        return view('livewire.components.user-avatar');
    }
}
