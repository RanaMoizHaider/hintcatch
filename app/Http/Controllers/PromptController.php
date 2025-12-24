<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PromptController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('prompts/index', [
            'prompts' => Prompt::query()
                ->with('user')
                ->orderByDesc('vote_score')
                ->paginate(24),
            'featuredPrompts' => Prompt::query()
                ->with('user')
                ->where('is_featured', true)
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
            'categories' => Prompt::query()
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->pluck('category'),
        ]);
    }

    public function show(Prompt $prompt): Response
    {
        $prompt->load(['user']);

        $user = Auth::user();

        $comments = $prompt->comments()
            ->with('user')
            ->whereNull('parent_id')
            ->withCount(['votes as vote_score' => fn ($q) => $q->select(DB::raw('COALESCE(SUM(value), 0)'))])
            ->latest()
            ->get()
            ->map(fn ($comment) => $this->formatComment($comment, $user?->id));

        $userVote = $user
            ? $prompt->votes()->where('user_id', $user->id)->first()?->value
            : null;

        $isFavorited = $user
            ? $prompt->favorites()->where('user_id', $user->id)->exists()
            : false;

        return Inertia::render('prompts/show', [
            'prompt' => $prompt,
            'relatedPrompts' => Prompt::query()
                ->where('category', $prompt->category)
                ->where('id', '!=', $prompt->id)
                ->with('user')
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
            'moreFromUser' => Prompt::query()
                ->where('user_id', $prompt->user_id)
                ->where('id', '!=', $prompt->id)
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
            'comments' => $comments,
            'interaction' => [
                'user_vote' => $userVote,
                'is_favorited' => $isFavorited,
                'favorites_count' => $prompt->favorites()->count(),
            ],
        ]);
    }

    private function formatComment(object $comment, ?int $userId): array
    {
        $userVote = $userId
            ? $comment->votes()->where('user_id', $userId)->first()?->value
            : null;

        return [
            'id' => $comment->id,
            'user_id' => $comment->user_id,
            'parent_id' => $comment->parent_id,
            'body' => $comment->body,
            'is_edited' => $comment->is_edited,
            'edited_at' => $comment->edited_at,
            'created_at' => $comment->created_at,
            'updated_at' => $comment->updated_at,
            'vote_score' => (int) $comment->vote_score,
            'user_vote' => $userVote,
            'user' => $comment->user ? [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
                'username' => $comment->user->username,
                'avatar' => $comment->user->avatar,
            ] : null,
            'replies' => $comment->replies?->map(fn ($reply) => $this->formatComment($reply, $userId))->toArray() ?? [],
        ];
    }
}
