<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Config;
use App\Models\McpServer;
use App\Models\Prompt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /** @var array<string, class-string> */
    private array $commentableTypes = [
        'config' => Config::class,
        'prompt' => Prompt::class,
        'mcp-server' => McpServer::class,
    ];

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'commentable_type' => ['required', 'string', 'in:config,prompt,mcp-server'],
            'commentable_id' => ['required', 'integer'],
            'body' => ['required', 'string', 'min:1', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        $user = Auth::user();
        $modelClass = $this->commentableTypes[$validated['commentable_type']];

        $modelClass::findOrFail($validated['commentable_id']);

        if (! empty($validated['parent_id'])) {
            $parentComment = Comment::findOrFail($validated['parent_id']);
            if ($parentComment->commentable_type !== $modelClass || $parentComment->commentable_id !== (int) $validated['commentable_id']) {
                return back()->withErrors(['parent_id' => 'Invalid parent comment.']);
            }
        }

        Comment::create([
            'user_id' => $user->id,
            'commentable_type' => $modelClass,
            'commentable_id' => $validated['commentable_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
        ]);

        return back();
    }

    public function update(Request $request, Comment $comment): RedirectResponse
    {
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        $comment->update([
            'body' => $validated['body'],
        ]);
        $comment->markAsEdited();

        return back();
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            abort(403);
        }

        $comment->replies()->delete();
        $comment->delete();

        return back();
    }
}
