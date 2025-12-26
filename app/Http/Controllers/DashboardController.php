<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Favorite;
use App\Models\McpServer;
use App\Models\Prompt;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = Auth::user();

        $recentConfigs = Config::query()
            ->where('submitted_by', $user->id)
            ->with(['agent', 'configType', 'category'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentMcpServers = McpServer::query()
            ->where('submitted_by', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentPrompts = Prompt::query()
            ->where('submitted_by', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentFavorites = Favorite::query()
            ->where('user_id', $user->id)
            ->with(['favoritable'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return Inertia::render('dashboard', [
            'stats' => [
                'totalConfigs' => Config::where('submitted_by', $user->id)->count(),
                'totalMcpServers' => McpServer::where('submitted_by', $user->id)->count(),
                'totalPrompts' => Prompt::where('submitted_by', $user->id)->count(),
                'totalFavorites' => Favorite::where('user_id', $user->id)->count(),
                'totalUpvotes' => Config::where('submitted_by', $user->id)->sum('vote_score')
                    + McpServer::where('submitted_by', $user->id)->sum('vote_score')
                    + Prompt::where('submitted_by', $user->id)->sum('vote_score'),
            ],
            'recentConfigs' => $recentConfigs,
            'recentMcpServers' => $recentMcpServers,
            'recentPrompts' => $recentPrompts,
            'recentFavorites' => $recentFavorites,
        ]);
    }
}
