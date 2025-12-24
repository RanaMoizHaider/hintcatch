<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ConfigController extends Controller
{
    public function show(Config $config): Response
    {
        $config->load([
            'user',
            'agent',
            'configType',
            'category',
            'files',
        ]);

        $user = Auth::user();

        $comments = $config->comments()
            ->with('user')
            ->whereNull('parent_id')
            ->withCount(['votes as vote_score' => fn ($q) => $q->select(DB::raw('COALESCE(SUM(value), 0)'))])
            ->latest()
            ->get()
            ->map(fn ($comment) => $this->formatComment($comment, $user?->id));

        $userVote = $user
            ? $config->votes()->where('user_id', $user->id)->first()?->value
            : null;

        $isFavorited = $user
            ? $config->favorites()->where('user_id', $user->id)->exists()
            : false;

        return Inertia::render('configs/show', [
            'config' => $config,
            'relatedConfigs' => $config->allConnections()
                ->load(['user', 'agent', 'configType'])
                ->take(6),
            'moreFromUser' => Config::query()
                ->where('user_id', $config->user_id)
                ->where('id', '!=', $config->id)
                ->with(['agent', 'configType'])
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
            'comments' => $comments,
            'interaction' => [
                'user_vote' => $userVote,
                'is_favorited' => $isFavorited,
                'favorites_count' => $config->favorites()->count(),
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
