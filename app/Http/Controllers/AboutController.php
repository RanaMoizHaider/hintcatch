<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\McpServer;
use App\Models\Prompt;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('about', [
            'stats' => [
                'totalConfigs' => Config::count(),
                'totalMcpServers' => McpServer::count(),
                'totalPrompts' => Prompt::count(),
                'totalContributors' => User::count(),
            ],
        ]);
    }
}
