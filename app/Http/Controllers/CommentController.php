<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Config;
use App\Models\McpServer;
use App\Models\Prompt;
use Illuminate\Http\JsonResponse;
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

    public function store(Request $request): JsonResponse
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
                return response()->json(['message' => 'Invalid parent comment.'], 422);
            }
        }

        $comment = Comment::create([
            'user_id' => $user->id,
            'commentable_type' => $modelClass,
            'commentable_id' => $validated['commentable_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
        ]);

        $comment->load('user');

        return response()->json([
            'comment' => $this->formatComment($comment, $user->id),
        ], 201);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        $comment->update([
            'body' => $validated['body'],
        ]);
        $comment->markAsEdited();

        $comment->load('user');

        return response()->json([
            'comment' => $this->formatComment($comment, $user->id),
        ]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $comment->replies()->delete();
        $comment->delete();

        return response()->json(['message' => 'Comment deleted.']);
    }

    /** @return array<string, mixed> */
    private function formatComment(Comment $comment, int $currentUserId): array
    {
        $userVote = $comment->votes()
            ->where('user_id', $currentUserId)
            ->first();

        return [
            'id' => $comment->id,
            'user_id' => $comment->user_id,
            'body' => $comment->body,
            'is_edited' => $comment->is_edited,
            'edited_at' => $comment->edited_at?->toIso8601String(),
            'created_at' => $comment->created_at->toIso8601String(),
            'updated_at' => $comment->updated_at->toIso8601String(),
            'vote_score' => $comment->vote_score,
            'user_vote' => $userVote?->value,
            'user' => $comment->user ? [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'username' => $comment->user->username,
                'avatar' => $comment->user->avatar,
            ] : null,
            'replies' => [],
        ];
    }
}
