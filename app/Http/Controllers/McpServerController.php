<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\McpServer;
use Inertia\Inertia;
use Inertia\Response;

class McpServerController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('mcp-servers/index', [
            'mcpServers' => McpServer::query()
                ->with('user')
                ->orderByDesc('vote_score')
                ->orderByDesc('created_at')
                ->get(),
            'featuredMcpServers' => McpServer::query()
                ->with('user')
                ->where('is_featured', true)
                ->orderByDesc('vote_score')
                ->limit(6)
                ->get(),
        ]);
    }

    public function show(McpServer $mcpServer): Response
    {
        $mcpServer->load([
            'user',
            'comments' => fn ($q) => $q->with('user')->latest()->limit(20),
        ]);

        // Get all agents that support MCP and generate integrations for each
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
            'mcpServer' => $mcpServer,
            'agentIntegrations' => $agentIntegrations,
            'moreFromUser' => McpServer::query()
                ->where('user_id', $mcpServer->user_id)
                ->where('id', '!=', $mcpServer->id)
                ->orderByDesc('vote_score')
                ->limit(4)
                ->get(),
        ]);
    }
}
