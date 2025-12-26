<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\McpServer;
use App\Models\Prompt;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public function show(User $user): Response
    {
        return Inertia::render('users/show', [
            'profileUser' => $user,
            'configs' => Config::query()
                ->where('submitted_by', $user->id)
                ->with(['agent', 'configType', 'category'])
                ->orderByDesc('vote_score')
                ->limit(12)
                ->get(),
            'mcpServers' => McpServer::query()
                ->where('submitted_by', $user->id)
                ->orderByDesc('vote_score')
                ->limit(12)
                ->get(),
            'prompts' => Prompt::query()
                ->where('submitted_by', $user->id)
                ->orderByDesc('vote_score')
                ->limit(12)
                ->get(),
            'stats' => [
                'totalConfigs' => Config::where('submitted_by', $user->id)->count(),
                'totalMcpServers' => McpServer::where('submitted_by', $user->id)->count(),
                'totalPrompts' => Prompt::where('submitted_by', $user->id)->count(),
                'totalVotes' => Config::where('submitted_by', $user->id)->sum('vote_score')
                    + McpServer::where('submitted_by', $user->id)->sum('vote_score')
                    + Prompt::where('submitted_by', $user->id)->sum('vote_score'),
            ],
        ]);
    }
}
