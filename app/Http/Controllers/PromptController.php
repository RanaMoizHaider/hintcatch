<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Services\SeoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PromptController extends Controller
{
    public function index(): Response
    {
        $search = request('search');
        $category = request('category');

        $prompts = Prompt::query()
            ->with('submitter')
            ->when($search, function ($query, $search) {
                $searchLower = strtolower($search);
                $query->where(function ($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchLower}%"]);
                });
            })
            ->when($category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->orderByDesc('vote_score')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('prompts/index', [
            'seo' => SeoService::forPromptIndex(),
            'prompts' => Inertia::scroll(fn () => $prompts),
            'filters' => [
                'search' => $search,
                'category' => $category,
            ],
            'featuredPrompts' => Prompt::query()
                ->with('submitter')
                ->where('is_featured', true)
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
            'categories' => Prompt::query()
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->orderBy('category')
                ->pluck('category'),
        ]);
    }

    public function show(Prompt $prompt): Response
    {
        $prompt->load(['submitter']);

        $user = Auth::user();

        $comments = $prompt->comments()
            ->with('submitter')
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
            'seo' => SeoService::forPrompt($prompt),
            'prompt' => $prompt,
            'relatedPrompts' => Prompt::query()
                ->where('category', $prompt->category)
                ->where('id', '!=', $prompt->id)
                ->with('submitter')
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
            'moreFromUser' => Prompt::query()
                ->where('submitted_by', $prompt->submitted_by)
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
