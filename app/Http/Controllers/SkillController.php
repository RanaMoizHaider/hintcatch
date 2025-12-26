<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Skill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SkillController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('skills/index', [
            'skills' => Skill::query()
                ->with('submitter')
                ->orderByDesc('vote_score')
                ->orderByDesc('created_at')
                ->get(),
            'featuredSkills' => Skill::query()
                ->with('submitter')
                ->where('is_featured', true)
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
        ]);
    }

    public function show(Skill $skill): Response
    {
        $skill->load(['submitter', 'category']);

        $user = Auth::user();

        $comments = $skill->comments()
            ->with('submitter')
            ->whereNull('parent_id')
            ->withCount(['votes as vote_score' => fn ($q) => $q->select(DB::raw('COALESCE(SUM(value), 0)'))])
            ->latest()
            ->get()
            ->map(fn ($comment) => $this->formatComment($comment, $user?->id));

        $userVote = $user
            ? $skill->votes()->where('user_id', $user->id)->first()?->value
            : null;

        $isFavorited = $user
            ? $skill->favorites()->where('user_id', $user->id)->exists()
            : false;

        $agents = Agent::query()
            ->whereNotNull('skills_config_template')
            ->orderBy('name')
            ->get();

        $agentIntegrations = $agents
            ->mapWithKeys(fn (Agent $agent) => [
                $agent->slug => [
                    'agent' => $agent,
                    'integration' => $skill->generateIntegrationForAgent($agent),
                ],
            ])
            ->filter(fn ($data) => ! empty($data['integration']));

        return Inertia::render('skills/show', [
            'skill' => $skill,
            'agentIntegrations' => $agentIntegrations,
            'moreFromUser' => Skill::query()
                ->where('submitted_by', $skill->submitted_by)
                ->where('id', '!=', $skill->id)
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
            'comments' => $comments,
            'interaction' => [
                'user_vote' => $userVote,
                'is_favorited' => $isFavorited,
                'favorites_count' => $skill->favorites()->count(),
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
