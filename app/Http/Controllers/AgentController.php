<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Category;
use App\Models\ConfigType;
use App\Models\McpServer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('agents/index', [
            'agents' => Agent::query()
                ->withCount('configs')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Agent $agent): Response
    {
        // Get config types this agent supports with counts
        $configTypes = ConfigType::query()
            ->whereIn('slug', $agent->supported_config_types ?? [])
            ->withCount(['configs' => fn ($q) => $q->where('agent_id', $agent->id)])
            ->get();

        // Build configs grouped by config type (recent + top for each)
        $configsByType = [];
        foreach ($configTypes as $configType) {
            $configsByType[$configType->slug] = [
                'configType' => $configType,
                'recent' => $agent->configs()
                    ->where('config_type_id', $configType->id)
                    ->with(['user', 'configType', 'category', 'agent'])
                    ->orderByDesc('created_at')
                    ->limit(6)
                    ->get(),
                'top' => $agent->configs()
                    ->where('config_type_id', $configType->id)
                    ->with(['user', 'configType', 'category', 'agent'])
                    ->orderByDesc('vote_score')
                    ->limit(6)
                    ->get(),
            ];
        }

        $props = [
            'agent' => $agent->loadCount('configs'),
            'configTypes' => $configTypes,
            'configsByType' => $configsByType,
        ];

        // Only include MCP server count if agent supports MCP
        if ($agent->supports_mcp) {
            $props['mcpServerCount'] = McpServer::count();
        }

        return Inertia::render('agents/show', $props);
    }

    /**
     * Show configs for a specific agent and config type.
     * URL: /agents/{agent}/configs/{configType}
     * Example: /agents/opencode/configs/rules
     */
    public function configs(Request $request, Agent $agent, ConfigType $configType): Response
    {
        $sort = $request->get('sort', 'recent');
        $categorySlug = $request->get('category');

        $query = $agent->configs()
            ->where('config_type_id', $configType->id)
            ->with(['user', 'configType', 'category']);

        // Filter by category if provided
        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        // Sort
        if ($sort === 'top') {
            $query->orderByDesc('vote_score');
        } else {
            $query->orderByDesc('created_at');
        }

        $configs = $query->paginate(24)->withQueryString();

        // Get categories for this config type
        $categories = Category::query()
            ->where('config_type_id', $configType->id)
            ->withCount(['configs' => fn ($q) => $q->where('agent_id', $agent->id)])
            ->orderBy('name')
            ->get();

        return Inertia::render('agents/configs', [
            'agent' => $agent,
            'configType' => $configType,
            'configs' => $configs,
            'categories' => $categories,
            'filters' => [
                'sort' => $sort,
                'category' => $categorySlug,
            ],
            'totalCount' => $agent->configs()->where('config_type_id', $configType->id)->count(),
        ]);
    }
}
