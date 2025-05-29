<?php

namespace App\Livewire\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Comments extends Component
{
    public Model $commentable;
    public string $newComment = '';
    public $comments;

    public function mount(Model $commentable)
    {
        $this->commentable = $commentable;
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->comments = $this->commentable->comments()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'newComment' => 'required|min:3|max:1000'
        ]);

        $this->commentable->comments()->create([
            'user_id' => Auth::id(),
            'body' => $this->newComment
        ]);

        $this->newComment = '';
        
        // Reload just the comments instead of refreshing the entire model
        $this->loadComments();

        // Dispatch event to notify parent components
        $this->dispatch('comment-added');
    }

    public function render()
    {
        return view('livewire.components.comments');
    }
}
