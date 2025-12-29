<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Config;
use App\Models\ConfigType;
use App\Models\McpServer;
use App\Models\Prompt;
use App\Models\Skill;
use App\Models\User;
use App\Services\SeoService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('home', [
            'seo' => SeoService::forHome(),
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
            'topConfigs' => Inertia::defer(fn () => Config::query()
                ->with(['submitter', 'agent', 'configType'])
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get()),
            'topMcpServers' => Inertia::defer(fn () => McpServer::query()
                ->with('submitter')
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get()),
            'topSkills' => Inertia::defer(fn () => Skill::query()
                ->with('submitter')
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get()),
            'topPrompts' => Inertia::defer(fn () => Prompt::query()
                ->with('submitter')
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get()),
            'agents' => Inertia::defer(fn () => Agent::query()
                ->withCount('configs')
                ->orderBy('name')
                ->get()),
            'configTypes' => Inertia::defer(fn () => ConfigType::query()
                ->withCount('configs')
                ->orderBy('name')
                ->get()),
            'stats' => Inertia::defer(fn () => [
                'totalConfigs' => Config::count(),
                'totalMcpServers' => McpServer::count(),
                'totalSkills' => Skill::count(),
                'totalPrompts' => Prompt::count(),
                'totalUsers' => User::count(),
            ]),
        ]);
    }
}
