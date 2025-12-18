<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Config;
use App\Models\ConfigType;
use App\Models\McpServer;
use App\Models\Prompt;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('home', [
            // Recent items
            'recentConfigs' => Config::query()
                ->with(['user', 'agent', 'configType'])
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(),
            'recentMcpServers' => McpServer::query()
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(),
            'recentPrompts' => Prompt::query()
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(),
            // Most liked items
            'topConfigs' => Config::query()
                ->with(['user', 'agent', 'configType'])
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
            'topMcpServers' => McpServer::query()
                ->with('user')
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
            'topPrompts' => Prompt::query()
                ->with('user')
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
            // Reference data
            'agents' => Agent::query()
                ->withCount('configs')
                ->orderBy('name')
                ->get(),
            'configTypes' => ConfigType::query()
                ->withCount('configs')
                ->orderBy('name')
                ->get(),
            'stats' => [
                'totalConfigs' => Config::count(),
                'totalMcpServers' => McpServer::count(),
                'totalPrompts' => Prompt::count(),
                'totalUsers' => User::count(),
            ],
        ]);
    }
}
