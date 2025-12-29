<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Config;
use App\Models\ConfigType;
use App\Models\McpServer;
use App\Models\Skill;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    public function index(): Response
    {
        $agents = Agent::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('agents/index', [
            'seo' => SeoService::forAgentIndex(),
            'agents' => $agents,
            'agentCounts' => Inertia::defer(function () {
                $mcpServerCount = McpServer::count();
                $skillsCount = Skill::count();

                return Agent::query()
                    ->orderBy('name')
                    ->withCount(['configs'])
                    ->get()
                    ->mapWithKeys(function ($agent) use ($mcpServerCount, $skillsCount) {
                        return [
                            $agent->id => [
                                'configs_count' => $agent->configs_count,
                                'mcp_servers_count' => $agent->supports_mcp ? $mcpServerCount : 0,
                                'skills_count' => $agent->supportsSkills() ? $skillsCount : 0,
                            ],
                        ];
                    });
            }),
        ]);
    }

    public function show(Agent $agent): Response
    {
        $configTypes = ConfigType::query()
            ->whereIn('slug', $agent->supported_config_types ?? [])
            ->withCount(['configs' => fn ($q) => $q->where('agent_id', $agent->id)])
            ->get();

        $configsByType = [];
        foreach ($configTypes as $configType) {
            $configsByType[$configType->slug] = [
                'configType' => $configType,
                'recent' => Config::query()
                    ->where('agent_id', $agent->id)
                    ->where('config_type_id', $configType->id)
                    ->with(['submitter', 'configType', 'category', 'agent'])
                    ->orderByDesc('created_at')
                    ->limit(6)
                    ->get(),
                'top' => Config::query()
                    ->where('agent_id', $agent->id)
                    ->where('config_type_id', $configType->id)
                    ->with(['submitter', 'configType', 'category', 'agent'])
                    ->orderByDesc('vote_score')
                    ->limit(6)
                    ->get(),
            ];
        }

        $props = [
            'seo' => SeoService::forAgent($agent),
            'agent' => $agent,
            'agentConfigsCount' => $agent->configs()->count(),
            'configTypes' => $configTypes,
            'configsByType' => $configsByType,
        ];

        if ($agent->supports_mcp) {
            $props['mcpServerCount'] = McpServer::count();
        }

        if ($agent->supportsSkills()) {
            $props['skillsCount'] = Skill::count();
        }

        return Inertia::render('agents/show', $props);
    }

    public function configs(Request $request, Agent $agent, ConfigType $configType): Response
    {
        $sort = $request->get('sort', 'recent');

        $query = Config::query()
            ->where('agent_id', $agent->id)
            ->where('config_type_id', $configType->id)
            ->with(['submitter', 'configType', 'category']);

        if ($sort === 'top') {
            $query->orderByDesc('vote_score');
        } else {
            $query->orderByDesc('created_at');
        }

        return Inertia::render('agents/configs', [
            'seo' => SeoService::forAgentConfigs($agent, $configType),
            'agent' => $agent,
            'configType' => $configType,
            'configs' => Inertia::scroll(fn () => $query->paginate(12)->withQueryString()),
            'filters' => [
                'sort' => $sort,
            ],
            'totalCount' => Config::query()
                ->where('agent_id', $agent->id)
                ->where('config_type_id', $configType->id)
                ->count(),
        ]);
    }
}
