<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\McpServer;
use App\Services\SeoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class McpServerController extends Controller
{
    public function index(): Response
    {
        $search = request('search');

        $mcpServers = McpServer::query()
            ->with('submitter')
            ->when($search, function ($query, $search) {
                $searchLower = mb_strtolower($search);
                $query->where(function ($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchLower}%"]);
                });
            })
            ->orderByDesc('vote_score')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('mcp-servers/index', [
            'seo' => SeoService::forMcpServerIndex(),
            'mcpServers' => Inertia::scroll(fn () => $mcpServers),
            'filters' => ['search' => $search],
            'featuredMcpServers' => McpServer::query()
                ->with('submitter')
                ->where('is_featured', true)
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
        ]);
    }

    public function show(McpServer $mcpServer): Response
    {
        $mcpServer->load(['submitter']);

        $user = Auth::user();

        $comments = $mcpServer->comments()
            ->with('submitter')
            ->whereNull('parent_id')
            ->withCount(['votes as vote_score' => fn ($q) => $q->select(DB::raw('COALESCE(SUM(value), 0)'))])
            ->latest()
            ->get()
            ->map(fn ($comment) => $this->formatComment($comment, $user?->id));

        $userVote = $user
            ? $mcpServer->votes()->where('user_id', $user->id)->first()?->value
            : null;

        $isFavorited = $user
            ? $mcpServer->favorites()->where('user_id', $user->id)->exists()
            : false;

        $agents = Agent::query()
            ->where('supports_mcp', true)
            ->whereNotNull('mcp_config_template')
            ->orderBy('name')
            ->get();

        $agentIntegrations = $agents
            ->mapWithKeys(fn (Agent $agent) => [
                $agent->slug => [
                    'agent' => $agent,
                    'integration' => $mcpServer->generateIntegrationForAgent($agent),
                ],
            ])
            ->filter(fn ($data) => ! empty($data['integration']));

        return Inertia::render('mcp-servers/show', [
            'seo' => SeoService::forMcpServer($mcpServer),
            'mcpServer' => $mcpServer,
            'agentIntegrations' => $agentIntegrations,
            'moreFromUser' => McpServer::query()
                ->where('submitted_by', $mcpServer->submitted_by)
                ->where('id', '!=', $mcpServer->id)
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
            'comments' => $comments,
            'interaction' => [
                'user_vote' => $userVote,
                'is_favorited' => $isFavorited,
                'favorites_count' => $mcpServer->favorites()->count(),
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
