<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Config;
use App\Models\ConfigType;
use App\Models\McpServer;
use App\Models\Prompt;
use App\Models\Skill;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('home', [
            'recentConfigs' => Config::query()
                ->with(['submitter', 'agent', 'configType'])
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(),
            'recentMcpServers' => McpServer::query()
                ->with('submitter')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(),
            'recentSkills' => Skill::query()
                ->with('submitter')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(),
            'recentPrompts' => Prompt::query()
                ->with('submitter')
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(),
            'topConfigs' => Config::query()
                ->with(['submitter', 'agent', 'configType'])
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
            'topMcpServers' => McpServer::query()
                ->with('submitter')
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
            'topSkills' => Skill::query()
                ->with('submitter')
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
            'topPrompts' => Prompt::query()
                ->with('submitter')
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
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
                'totalSkills' => Skill::count(),
                'totalPrompts' => Prompt::count(),
                'totalUsers' => User::count(),
            ],
        ]);
    }
}
