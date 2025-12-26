<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Category;
use App\Models\ConfigType;
use Inertia\Inertia;
use Inertia\Response;

class ConfigTypeController extends Controller
{
    public function index(): Response
    {
        $configTypes = ConfigType::query()
            ->whereNotIn('slug', ['mcp-servers', 'prompts'])
            ->withCount(['configs', 'categories'])
            ->orderBy('name')
            ->get();

        return Inertia::render('config-types/index', [
            'configTypes' => $configTypes,
        ]);
    }

    public function show(ConfigType $configType): Response
    {
        return Inertia::render('config-types/show', [
            'configType' => $configType->loadCount(['configs', 'categories']),
            'configs' => $configType->configs()
                ->with(['submitter', 'agent', 'category'])
                ->orderByDesc('vote_score')
                ->limit(20)
                ->get(),
            'categories' => Category::query()
                ->where('config_type_id', $configType->id)
                ->withCount(['configs' => fn ($q) => $q->where('config_type_id', $configType->id)])
                ->orderBy('name')
                ->get(),
            'agents' => Agent::query()
                ->whereJsonContains('supported_config_types', $configType->slug)
                ->withCount(['configs' => fn ($q) => $q->where('config_type_id', $configType->id)])
                ->get(),
        ]);
    }
}
